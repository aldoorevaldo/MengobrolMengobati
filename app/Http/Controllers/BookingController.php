<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Booking;
use App\Models\Psikiater;
use App\Models\Psikolog;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use App\Mail\BookingCreatedMail;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function create(Request $request, $providerId)
    {
        $type = $request->get('type', 'psikiater');
        if ($type === 'psikolog') {
            $ps = \DB::table('psikologs')->where('id', $providerId)->first();
        } else {
            $ps = \App\Models\Psikiater::find($providerId);
        }

        if (!$ps) {
            abort(404, "Provider not found.");
        }
        return view('booking.create', [
            'ps'    => $ps,
            'type'  => $type,
        ]);
    }

    public function availableTimes(Request $request, $providerId)
    {
        $request->validate([
            'date' => 'required|date_format:Y-m-d'
        ]);
        $date = $request->input('date');
        $type = $request->input('type', 'psikiater');
        if ($type === 'psikolog') {
            $ps = null;
            if (class_exists(\App\Models\Psikolog::class)) {
                $ps = Psikolog::findOrFail($providerId);
            } else {
                $psRow = DB::table('psikologs')->where('id', $providerId)->first();
                if (!$psRow) abort(404, 'Psikolog not found.');
                $ps = $psRow;
            }
        } else {
            $ps = Psikiater::findOrFail($providerId);
        }
        $slotMinutes = $ps->slot_minutes ?? 60;
        $ws = $ps->work_start ?? '09:00:00';
        $we = $ps->work_end ?? '17:00:00';
        if (strlen($ws) === 5) $ws .= ':00';
        if (strlen($we) === 5) $we .= ':00';

        $workStart = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $ws);
        $workEnd   = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $we);

        if ($workEnd->lte($workStart)) {
            return response()->json(['times' => []]);
        }
        $interval = CarbonInterval::minutes($slotMinutes);
        $slots = [];
        $cursor = $workStart->copy();
        while ($cursor->lte($workEnd->copy()->sub($interval))) {
            $slots[] = $cursor->format('H:i');
            $cursor->add($interval);
        }
        $now = Carbon::now();
        if ($date === $now->toDateString()) {
            $slots = array_values(array_filter($slots, function($t) use ($date, $now) {
                return Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $t)->gt($now);
            }));
        }
        if ($type === 'psikolog') {
            $booked = Booking::where('psikolog_id', $providerId)
                ->whereDate('scheduled_at', $date)
                ->whereIn('status', ['pending','confirmed'])
                ->pluck(DB::raw("TIME_FORMAT(scheduled_at, '%H:%i')"))
                ->map(fn($v) => (string)$v)
                ->toArray();
        } else {
            $booked = Booking::where('psikiater_id', $providerId)
                ->whereDate('scheduled_at', $date)
                ->whereIn('status', ['pending','confirmed'])
                ->pluck(DB::raw("TIME_FORMAT(scheduled_at, '%H:%i')"))
                ->map(fn($v) => (string)$v)
                ->toArray();
        }

        $available = array_values(array_filter($slots, function($t) use ($booked) {
            return !in_array($t, $booked);
        }));

        return response()->json(['times' => $available]);
    }

    public function store(Request $request)
    {
        $type = $request->input('type', 'psikiater');

        $rules = [
            'date' => 'required|date_format:Y-m-d',
            'time' => 'required|date_format:H:i',
            'service' => 'nullable|string|max:191',
            'notes' => 'nullable|string|max:2000',
        ];

        if ($type === 'psikolog') {
            $rules['psikolog_id'] = 'required|integer';
            if (\Schema::hasTable('psikologs')) {
                $rules['psikolog_id'] = 'required|exists:psikologs,id';
            }
        } else {
            $rules['psikiater_id'] = 'required|exists:psikiaters,id';
        }

        $request->validate($rules);

        $user = $request->user();
        $scheduled = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->time);

        if ($scheduled->lte(\Carbon\Carbon::now())) {
            return back()->withErrors(['time' => 'Schedule must be in the future.'])->withInput();
        }
        try {
            if ($type === 'psikolog') {
                $providerId = $request->input('psikolog_id');
                $provider = DB::table('psikologs')->where('id', $providerId)->first();
                if (!$provider) return back()->withErrors(['general' => 'Psikolog not found.'])->withInput();
            } else {
                $providerId = $request->input('psikiater_id');
                $provider = Psikiater::find($providerId);
                if (!$provider) return back()->withErrors(['general' => 'Psikiater not found.'])->withInput();
            }
        } catch (\Throwable $e) {
            \Log::error("STORE: error fetching provider: ".$e->getMessage(), ['ex'=>$e]);
            return back()->withErrors(['general' => 'An error occurred while retrieving provider data. Check the log.'])->withInput();
        }
        $ws = $provider->work_start ?? '09:00:00';
        $we = $provider->work_end ?? '17:00:00';
        if (strlen($ws) === 5) $ws .= ':00';
        if (strlen($we) === 5) $we .= ':00';

        $workStart = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $scheduled->toDateString() . ' ' . $ws);
        $workEnd   = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $scheduled->toDateString() . ' ' . $we);

        if ($scheduled->lt($workStart) || $scheduled->gte($workEnd)) {
            return back()->withErrors(['time' => 'Booking time is outside provider working hours.'])->withInput();
        }

        DB::beginTransaction();
        try {
            $q = Booking::where('scheduled_at', $scheduled->toDateTimeString())
                ->whereIn('status', ['pending','confirmed']);
            if ($type === 'psikolog') $q->where('psikolog_id', $providerId);
            else $q->where('psikiater_id', $providerId);

            if ($q->lockForUpdate()->exists()) {
                DB::rollBack();
                return back()->withErrors(['time' => 'Slot already booked. Please choose another time.'])->withInput();
            }

            $data = [
                'user_id' => $user->id,
                'service' => $request->input('service'),
                'scheduled_at' => $scheduled->toDateTimeString(),
                'notes' => $request->input('notes'),
                'status' => 'pending',
                'type' => $type,
            ];

            if ($type === 'psikolog') {
                $data['psikolog_id'] = $providerId;
                $data['psikiater_id'] = null;
            } else {
                $data['psikiater_id'] = $providerId;
                $data['psikolog_id'] = null;
            }
            $booking = Booking::create($data);

            DB::commit();
            $booking->load('user', 'psikiater.user', 'psikolog.user');
            try {
                $providerEmail = null;
                if ($type === 'psikolog') {
                    if (isset($booking->psikolog->user)) $providerEmail = $booking->psikolog->user->email ?? null;
                    elseif (isset($provider->email)) $providerEmail = $provider->email;
                } else {
                    $providerEmail = optional($booking->psikiater->user)->email ?? $booking->psikiater->email ?? null;
                }

                if ($providerEmail && filter_var($providerEmail, FILTER_VALIDATE_EMAIL)) {
                    Mail::to($providerEmail)->send(new BookingCreatedMail($booking));
                    \Log::info("STORE: sent BookingCreatedMail to {$providerEmail} for booking {$booking->id}");
                } else {
                    \Log::warning("STORE: provider email missing/invalid for booking {$booking->id}");
                }
            } catch (\Throwable $e) {
                \Log::error("STORE: mail send failed: ".$e->getMessage(), ['ex'=>$e, 'booking_id'=>$booking->id]);
            }

            return redirect()->route('profile.show')->with('success', 'Booking successfully created. Wait for provider confirmation.');

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error("STORE: failed to save booking: ".$e->getMessage(), [
                'ex' => $e,
                'input' => $request->all()
            ]);
            $msg = env('APP_DEBUG') ? $e->getMessage() : 'An error occurred while saving the booking.';
            return back()->withErrors(['general' => $msg])->withInput();
        }
    }


}

if (! function_exists('SchemaHasTable')) {
    function SchemaHasTable($table) {
        try {
            return \Schema::hasTable($table);
        } catch (\Throwable $t) {
            return false;
        }
    }
}
