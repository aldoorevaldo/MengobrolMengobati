<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Models\Booking;
use App\Models\Psikiater;
use App\Mail\BookingStatusUpdatedMail;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PsikiaterController extends Controller
{
    public function index()
    {
        $psikiaters = Psikiater::orderBy('name')->get();
        return view('psikiater.index', compact('psikiaters'));
    }

    public function list()
    {
        try {
            if (class_exists(Psikiater::class)) {
                $psikiaters = Psikiater::select('id','name','photo','hospital','work_start','work_end','description')->get();
            } else {
                $psikiaters = DB::table('psikiaters')->select('id','name','photo','hospital','work_start','work_end','description')->get();
            }
            return response()->json(['success' => true, 'data' => $psikiaters], 200);
        } catch (\Throwable $e) {
            Log::error('Psikiater list error: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    protected function findPsikiaterByUser($user)
    {
        if (! $user) {
            return null;
        }

        return Psikiater::where('user_id', $user->id)->first();
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();
        $psikiater = Psikiater::where('user_id', $user->id)->first();

        if (! $psikiater) {
            return redirect()->route('home')->with('error','You do not have access to the psikiater dashboard.');
        }

        $bookings = Booking::with('user')
            ->where('psikiater_id', $psikiater->id)
            ->orderByRaw("FIELD(status, 'pending','confirmed','rejected','finished'), scheduled_at DESC")
            ->paginate(20);

        return view('psikiater.dashboard', compact('psikiater','bookings'));
    }

    public function approveConfirm(Booking $booking)
    {
        $user = request()->user();
        $psikiater = $this->findPsikiaterByUser($user);
        if (! $psikiater || $booking->psikiater_id !== $psikiater->id) abort(403,'Unauthorized');

        return view('booking.confirm-action', [
            'booking' => $booking,
            'action' => 'approve',
            'disabled' => $booking->status !== 'pending',
            'message' => $booking->status !== 'pending' ? 'Booking has been processed.' : null
        ]);
    }

    public function rejectConfirm(Booking $booking)
    {
        $user = request()->user();
        $psikiater = $this->findPsikiaterByUser($user);
        if (! $psikiater || $booking->psikiater_id !== $psikiater->id) abort(403,'Unauthorized');

        return view('booking.confirm-action', [
            'booking' => $booking,
            'action' => 'reject',
            'disabled' => $booking->status !== 'pending',
            'message' => $booking->status !== 'pending' ? 'Booking has been processed.' : null
        ]);
    }

    public function approve(Request $request, Booking $booking)
    {
        $user = $request->user();
        $psikiater = $this->findPsikiaterByUser($user);
        if (! $psikiater || $booking->psikiater_id !== $psikiater->id) abort(403,'Unauthorized');

        if ($booking->status !== 'pending') {
            return redirect()->route('psikiater.dashboard')->with('error','Booking cannot be approved (not pending).');
        }

        if ($request->filled('notes')) $booking->notes = $request->input('notes');
        $booking->status = 'confirmed';
        $booking->save();

        $booking->loadMissing('user','psikiater');

        try {
            $to = optional($booking->user)->email;
            if ($to && filter_var($to, FILTER_VALIDATE_EMAIL)) {
                Mail::to($to)->send(new BookingStatusUpdatedMail($booking));
                Log::info("PSIKIATER APPROVE: mail to {$to} for booking {$booking->id}");
            } else {
                Log::warning("PSIKIATER APPROVE: invalid email for booking {$booking->id}");
            }
        } catch (\Throwable $e) {
            Log::error("PSIKIATER APPROVE: mail exception: ".$e->getMessage());
            return redirect()->route('psikiater.dashboard')->with('success','Booking has been confirmed, but email notification failed. Check log.');
        }

        return redirect()->route('psikiater.dashboard')->with('success','Booking has been confirmed.');
    }

    public function reject(Request $request, Booking $booking)
    {
        $user = $request->user();
        $psikiater = $this->findPsikiaterByUser($user);
        if (! $psikiater || $booking->psikiater_id !== $psikiater->id) abort(403,'Unauthorized');

        if ($booking->status !== 'pending') {
            return redirect()->route('psikiater.dashboard')->with('error','Booking cannot be rejected (not pending).');
        }

        if ($request->filled('notes')) $booking->notes = $request->input('notes');
        $booking->status = 'rejected';
        $booking->save();

        $booking->loadMissing('user','psikiater');

        try {
            $to = optional($booking->user)->email;
            if ($to && filter_var($to, FILTER_VALIDATE_EMAIL)) {
                Mail::to($to)->send(new BookingStatusUpdatedMail($booking));
                Log::info("PSIKIATER REJECT: mail to {$to} for booking {$booking->id}");
            } else {
                Log::warning("PSIKIATER REJECT: invalid email for booking {$booking->id}");
            }
        } catch (\Throwable $e) {
            Log::warning("PSIKIATER REJECT: mail exception: ".$e->getMessage());
        }

        return redirect()->route('psikiater.dashboard')->with('success','Booking has been rejected (user notified if email is available).');
    }

    public function finish(Request $request, Booking $booking)
    {
        $user = $request->user();
        $psikiater = $this->findPsikiaterByUser($user);
        if (! $psikiater || $booking->psikiater_id !== $psikiater->id) abort(403,'Unauthorized');

        try {
            $booking->status = 'finished';
            $booking->save();

            Log::info("PSIKIATER FINISH: booking {$booking->id} finished by psikiater_user_id={$user->id}");
            return redirect()->route('psikiater.dashboard')->with('success','Booking has been finished.');
        } catch (\Throwable $e) {
            Log::error("PSIKIATER FINISH: failed to finish booking {$booking->id}: ".$e->getMessage());
            return redirect()->back()->with('error','Failed to finish booking. Check log.');
        }
    }
    public function profile(Request $request)
    {
        $user = $request->user();
        $psikiater = Psikiater::where('user_id', $user->id)->firstOrFail();
        return view('psikiater.profile', compact('psikiater'));
    }
    public function editProfile(Request $request)
    {
        $user = $request->user();
        $psikiater = Psikiater::where('user_id', $user->id)->firstOrFail();
        return view('psikiater.edit-profile', compact('psikiater'));
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $psikiater = Psikiater::where('user_id', $user->id)->firstOrFail();

        $validator = \Validator::make($request->all(), [
            'name'        => 'required|string|max:191',
            'hospital'    => 'nullable|string|max:255',
            'work_start'  => 'nullable|string',
            'work_end'    => 'nullable|string',
            'description' => 'nullable|string',
            'photo'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'remove_photo'=> 'nullable|in:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data = $validator->validated();
        $workStart  = null;
        $workEnd    = null;
        $timeErrors = [];

        if (!empty($data['work_start'])) {
            $s = trim($data['work_start']);
            try {
                $dt = Carbon::createFromFormat('H:i', $s);
                $workStart = $dt->format('H:i');
            } catch (\Throwable $e1) {
                try {
                    $dt2 = Carbon::createFromFormat('H:i:s', $s);
                    $workStart = $dt2->format('H:i');
                } catch (\Throwable $e2) {
                    $timeErrors['work_start'] = 'Work start must be in HH:MM format.';
                }
            }
        }

        if (!empty($data['work_end'])) {
            $s = trim($data['work_end']);
            try {
                $dt = Carbon::createFromFormat('H:i', $s);
                $workEnd = $dt->format('H:i');
            } catch (\Throwable $e1) {
                try {
                    $dt2 = Carbon::createFromFormat('H:i:s', $s);
                    $workEnd = $dt2->format('H:i');
                } catch (\Throwable $e2) {
                    $timeErrors['work_end'] = 'Work end must be in HH:MM format.';
                }
            }
        }

        if (!empty($timeErrors)) {
            return redirect()->back()->withErrors($timeErrors)->withInput();
        }
        if ($request->filled('remove_photo') && $request->input('remove_photo') == '1') {
            if ($psikiater->photo && Storage::disk('public')->exists($psikiater->photo)) {
                Storage::disk('public')->delete($psikiater->photo);
            }
            $psikiater->photo = null;
        }
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            if ($psikiater->photo && Storage::disk('public')->exists($psikiater->photo)) {
                Storage::disk('public')->delete($psikiater->photo);
            }

            $file     = $request->file('photo');
            $ext      = $file->getClientOriginalExtension();
            $filename = Str::random(20).'.'.$ext;
            $path = $file->storeAs('psikiaters', $filename, 'public');
            $psikiater->photo = $path;
        }
        $psikiater->name        = $data['name'];
        $psikiater->hospital    = $data['hospital'] ?? null;
        $psikiater->work_start  = $workStart;
        $psikiater->work_end    = $workEnd;
        $psikiater->description = $data['description'] ?? null;
        $psikiater->save();

        return redirect()->route('psikiater.profile')->with('success', 'Profile updated successfully.');
    }

}
