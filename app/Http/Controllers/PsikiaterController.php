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
            $psikiaters = Psikiater::select('id','name','photo','hospital','work_start','work_end','description')->get()
                ->map(function($p){
                    $p->photo = $p->photo ? asset('storage/'.$p->photo) : null;
                    return $p;
                });
            return response()->json(['success'=>true,'data'=>$psikiaters], 200);
        } catch (\Throwable $e) {
            Log::error('Psikiater list error: '.$e->getMessage());
            return response()->json(['success'=>false,'message'=>'Server error'], 500);
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
            return redirect()->route('home')->with('error','Anda tidak memiliki akses ke dashboard psikiater.');
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
            'message' => $booking->status !== 'pending' ? 'Booking sudah diproses.' : null
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
            'message' => $booking->status !== 'pending' ? 'Booking sudah diproses.' : null
        ]);
    }

    public function approve(Request $request, Booking $booking)
    {
        $user = $request->user();
        $psikiater = $this->findPsikiaterByUser($user);
        if (! $psikiater || $booking->psikiater_id !== $psikiater->id) abort(403,'Unauthorized');

        if ($booking->status !== 'pending') {
            return redirect()->route('psikiater.dashboard')->with('error','Booking tidak dapat di-approve (bukan pending).');
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
            return redirect()->route('psikiater.dashboard')->with('success','Booking dikonfirmasi, tapi notifikasi email gagal. Cek log.');
        }

        return redirect()->route('psikiater.dashboard')->with('success','Booking berhasil dikonfirmasi.');
    }

    public function reject(Request $request, Booking $booking)
    {
        $user = $request->user();
        $psikiater = $this->findPsikiaterByUser($user);
        if (! $psikiater || $booking->psikiater_id !== $psikiater->id) abort(403,'Unauthorized');

        if ($booking->status !== 'pending') {
            return redirect()->route('psikiater.dashboard')->with('error','Booking tidak dapat ditolak (bukan pending).');
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

        return redirect()->route('psikiater.dashboard')->with('success','Booking telah ditolak (user diberi tahu bila email tersedia).');
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
            return redirect()->route('psikiater.dashboard')->with('success','Booking telah diselesaikan.');
        } catch (\Throwable $e) {
            Log::error("PSIKIATER FINISH: gagal menyelesaikan booking {$booking->id}: ".$e->getMessage());
            return redirect()->back()->with('error','Gagal menyelesaikan booking. Cek log.');
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
        $psikiater = \App\Models\Psikiater::where('user_id', $user->id)->firstOrFail();
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'hospital' => 'nullable|string|max:255',
            'work_start' => 'nullable|string',
            'work_end' => 'nullable|string',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'remove_photo' => 'nullable|in:1'
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $timesErrors = [];
        $workStart = null;
        $workEnd = null;

        if (!empty($data['work_start'])) {
            try {
                $cs = Carbon::createFromFormat('H:i', trim($data['work_start']));
                $workStart = $cs->format('H:i');
            } catch (\Throwable $e) {
                try {
                    $cs2 = Carbon::createFromFormat('H:i:s', trim($data['work_start']));
                    $workStart = $cs2->format('H:i');
                } catch (\Throwable $e2) {
                    $timesErrors['work_start'] = 'The work start field must match the format HH:MM.';
                }
            }
        }

        if (!empty($data['work_end'])) {
            try {
                $ce = Carbon::createFromFormat('H:i', trim($data['work_end']));
                $workEnd = $ce->format('H:i');
            } catch (\Throwable $e) {
                try {
                    $ce2 = Carbon::createFromFormat('H:i:s', trim($data['work_end']));
                    $workEnd = $ce2->format('H:i');
                } catch (\Throwable $e2) {
                    $timesErrors['work_end'] = 'The work end field must match the format HH:MM.';
                }
            }
        }

        if (!empty($timesErrors)) {
            return redirect()->back()->withErrors($timesErrors)->withInput();
        }

        if ($request->filled('remove_photo') && $request->input('remove_photo') == '1') {
            $psikiater->deletePhotoFile();
            $psikiater->photo = null;
        }

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $psikiater->deletePhotoFile();
            $file = $request->file('photo');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('psikiaters', $filename, 'public');
            $psikiater->photo = $path;
        }
        $psikiater->name = $data['name'];
        $psikiater->hospital = $data['hospital'] ?? null;
        $psikiater->work_start = $workStart; // null if not provided
        $psikiater->work_end = $workEnd;
        $psikiater->description = $data['description'] ?? null;

        $psikiater->save();

        return redirect()->route('psikiater.profile')->with('success', 'Profil berhasil diperbarui.');
    }

}
