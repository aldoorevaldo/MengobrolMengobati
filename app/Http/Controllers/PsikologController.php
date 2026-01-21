<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Booking;
use App\Models\Psikolog;
use App\Mail\BookingStatusUpdatedMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PsikologController extends Controller
{

    public function index()
    {
        if (class_exists(Psikolog::class)) {
            $psikologs = Psikolog::orderBy('name')->get();
        } else {
            $psikologs = DB::table('psikologs')->orderBy('name')->get();
        }

        return view('psikolog.index', compact('psikologs'));
    }

    public function list()
    {
        try {
            if (class_exists(Psikolog::class)) {
                $psikologs = Psikolog::select('id','name','photo','hospital','work_start','work_end','description')->get();
            } else {
                $psikologs = DB::table('psikologs')->select('id','name','photo','hospital','work_start','work_end','description')->get();
            }
            return response()->json(['success' => true, 'data' => $psikologs], 200);
        } catch (\Throwable $e) {
            Log::error('Psikolog list error: '.$e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    public function dashboard(Request $request)
    {
        $user = $request->user();
        try {
            if (class_exists(Psikolog::class)) {
                $psikolog = Psikolog::where('user_id', $user->id)->first();
            } else {
                $psikolog = DB::table('psikologs')->where('user_id', $user->id)->first();
            }
        } catch (\Throwable $e) {
            Log::error('PsikologController::dashboard fetch psikolog error: '.$e->getMessage());
            $psikolog = null;
        }

        if (! $psikolog) {
            return redirect()->route('home')->with('error', 'You do not have access to the psikolog dashboard.');
        }
        $bookings = Booking::with('user')
            ->where('psikolog_id', $psikolog->id)
            ->orderByRaw("FIELD(status, 'pending','confirmed','finished','rejected'), scheduled_at DESC")
            ->paginate(20);

        return view('psikolog.dashboard', compact('psikolog', 'bookings'));
    }

    public function approveConfirm(Booking $booking)
    {
        $user = request()->user();
        $psikolog = $this->findPsikologByUser($user);
        if (! $psikolog || $booking->psikolog_id !== $psikolog->id) {
            abort(403, 'Unauthorized');
        }

        return view('booking.confirm-action', [
            'booking' => $booking,
            'action' => 'approve',
            'disabled' => $booking->status !== 'pending',
            'message' => $booking->status !== 'pending' ? 'Booking has been processed.' : null,
        ]);
    }

    public function rejectConfirm(Booking $booking)
    {
        $user = request()->user();
        $psikolog = $this->findPsikologByUser($user);
        if (! $psikolog || $booking->psikolog_id !== $psikolog->id) {
            abort(403, 'Unauthorized');
        }

        return view('booking.confirm-action', [
            'booking' => $booking,
            'action' => 'reject',
            'disabled' => $booking->status !== 'pending',
            'message' => $booking->status !== 'pending' ? 'Booking has been processed.' : null,
        ]);
    }

    public function approve(Request $request, Booking $booking)
    {
        $user = $request->user();
        $psikolog = $this->findPsikologByUser($user);
        if (! $psikolog || $booking->psikolog_id !== $psikolog->id) {
            abort(403, 'Unauthorized');
        }

        if ($booking->status !== 'pending') {
            return redirect()->route('psikolog.dashboard')->with('error', 'Booking cannot be approved (not pending).');
        }
        if ($request->filled('notes')) {
            $booking->notes = $request->input('notes');
        }
        $booking->status = 'confirmed';
        $booking->save();
        $booking->loadMissing('user','psikolog','psikiater');

        try {
            $to = optional($booking->user)->email;
            if ($to && filter_var($to, FILTER_VALIDATE_EMAIL)) {
                Mail::to($to)->send(new BookingStatusUpdatedMail($booking));
                Log::info("Psikolog APPROVE: Mail sent to {$to} for booking {$booking->id}");
            } else {
                Log::warning("Psikolog APPROVE: invalid email for booking {$booking->id}");
            }
        } catch (\Throwable $e) {
            Log::error("Psikolog APPROVE: failed to send email for booking {$booking->id}: ".$e->getMessage());
            return redirect()->route('psikolog.dashboard')->with('success', 'Booking has been confirmed, but email notification failed. Check log.');
        }

        return redirect()->route('psikolog.dashboard')->with('success', 'Booking has been confirmed and user notified via email (if available).');
    }

    public function reject(Request $request, Booking $booking)
    {
        $user = $request->user();
        $psikolog = $this->findPsikologByUser($user);
        if (! $psikolog || $booking->psikolog_id !== $psikolog->id) {
            abort(403, 'Unauthorized');
        }

        if ($booking->status !== 'pending') {
            return redirect()->route('psikolog.dashboard')->with('error', 'Booking cannot be rejected (not pending).');
        }

        if ($request->filled('notes')) {
            $booking->notes = $request->input('notes');
        }

        $booking->status = 'rejected';
        $booking->save();

        $booking->loadMissing('user','psikolog','psikiater');

        try {
            $to = optional($booking->user)->email;
            if ($to && filter_var($to, FILTER_VALIDATE_EMAIL)) {
                Mail::to($to)->send(new BookingStatusUpdatedMail($booking));
                Log::info("Psikolog REJECT: Mail sent to {$to} for booking {$booking->id}");
            } else {
                Log::warning("Psikolog REJECT: invalid email for booking {$booking->id}");
            }
        } catch (\Throwable $e) {
            Log::warning("Psikolog REJECT: failed to send email for booking {$booking->id}: ".$e->getMessage());
        }

        return redirect()->route('psikolog.dashboard')->with('success', 'Booking has been rejected and user notified via email (if available).');
    }

    public function finish(Request $request, Booking $booking)
    {
        $user = $request->user();
        $psikolog = $this->findPsikologByUser($user);
        if (! $psikolog || $booking->psikolog_id !== $psikolog->id) {
            abort(403, 'Unauthorized');
        }

        if ($booking->status !== 'confirmed') {
            return redirect()->route('psikolog.dashboard')->with('error', 'Booking cannot be finished because status is not confirmed.');
        }

        try {
            $booking->status = 'finished';
            $booking->save();

            Log::info("Psikolog FINISH: booking_id={$booking->id} finished by psikolog_user_id={$user->id}");
            return redirect()->route('psikolog.dashboard')->with('success', 'The session has ended. The time slot is now available again.');
        } catch (\Throwable $e) {
            Log::error("Psikolog FINISH ERROR: ".$e->getMessage(), ['ex'=>$e]);
            return redirect()->route('psikolog.dashboard')->with('error', 'Failed to end the session. Check log.');
        }
    }

    protected function findPsikologByUser($user)
    {
        try {
            if (class_exists(Psikolog::class)) {
                return Psikolog::where('user_id', $user->id)->first();
            }
            return DB::table('psikologs')->where('user_id', $user->id)->first();
        } catch (\Throwable $e) {
            Log::error('findPsikologByUser error: '.$e->getMessage());
            return null;
        }
    }
    public function profile(Request $request)
    {
        $user = $request->user();
        $psikolog = Psikolog::where('user_id', $user->id)->firstOrFail();
        return view('psikolog.profile', compact('psikolog'));
    }
    public function editProfile(Request $request)
    {
        $user = $request->user();
        $psikolog = Psikolog::where('user_id', $user->id)->firstOrFail();
        return view('psikolog.edit-profile', compact('psikolog'));
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        $psikolog = Psikolog::where('user_id', $user->id)->firstOrFail();

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
            if ($psikolog->photo && Storage::disk('public')->exists($psikolog->photo)) {
                Storage::disk('public')->delete($psikolog->photo);
            }
            $psikolog->photo = null;
        }
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            if ($psikolog->photo && Storage::disk('public')->exists($psikolog->photo)) {
                Storage::disk('public')->delete($psikolog->photo);
            }

            $file     = $request->file('photo');
            $ext      = $file->getClientOriginalExtension();
            $filename = Str::random(20).'.'.$ext;
            $path = $file->storeAs('psikologs', $filename, 'public');
            $psikolog->photo = $path;
        }
        $psikolog->name        = $data['name'];
        $psikolog->hospital    = $data['hospital'] ?? null;
        $psikolog->work_start  = $workStart;
        $psikolog->work_end    = $workEnd;
        $psikolog->description = $data['description'] ?? null;
        $psikolog->save();

        return redirect()->route('psikolog.profile')->with('success', 'Profile has been updated.');
    }

}
