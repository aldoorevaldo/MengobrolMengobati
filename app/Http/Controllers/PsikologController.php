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
            return redirect()->route('home')->with('error', 'Anda tidak memiliki akses dashboard psikolog.');
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
            'message' => $booking->status !== 'pending' ? 'Booking sudah diproses.' : null,
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
            'message' => $booking->status !== 'pending' ? 'Booking sudah diproses.' : null,
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
            return redirect()->route('psikolog.dashboard')->with('error', 'Booking tidak dapat di-approve (bukan pending).');
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
                Log::info("Psikolog APPROVE: Mail sent to {$to} for booking_id={$booking->id}");
            } else {
                Log::warning("Psikolog APPROVE: user email kosong/invalid for booking_id={$booking->id}");
            }
        } catch (\Throwable $e) {
            Log::error("Psikolog APPROVE: gagal kirim email untuk booking_id={$booking->id}: ".$e->getMessage());
            return redirect()->route('psikolog.dashboard')->with('success', 'Booking telah dikonfirmasi, namun notifikasi email gagal dikirim. Cek log.');
        }

        return redirect()->route('psikolog.dashboard')->with('success', 'Booking telah dikonfirmasi dan pengguna diberi tahu via email (jika tersedia).');
    }

    public function reject(Request $request, Booking $booking)
    {
        $user = $request->user();
        $psikolog = $this->findPsikologByUser($user);
        if (! $psikolog || $booking->psikolog_id !== $psikolog->id) {
            abort(403, 'Unauthorized');
        }

        if ($booking->status !== 'pending') {
            return redirect()->route('psikolog.dashboard')->with('error', 'Booking tidak dapat ditolak (bukan pending).');
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
                Log::info("Psikolog REJECT: Mail sent to {$to} for booking_id={$booking->id}");
            } else {
                Log::warning("Psikolog REJECT: user email kosong/invalid for booking_id={$booking->id}");
            }
        } catch (\Throwable $e) {
            Log::warning("Psikolog REJECT: gagal kirim email untuk booking_id={$booking->id}: ".$e->getMessage());
        }

        return redirect()->route('psikolog.dashboard')->with('success', 'Booking telah ditolak dan pengguna diberi tahu via email (jika tersedia).');
    }

    public function finish(Request $request, Booking $booking)
    {
        $user = $request->user();
        $psikolog = $this->findPsikologByUser($user);
        if (! $psikolog || $booking->psikolog_id !== $psikolog->id) {
            abort(403, 'Unauthorized');
        }

        if ($booking->status !== 'confirmed') {
            return redirect()->route('psikolog.dashboard')->with('error', 'Booking tidak dapat diakhiri karena status bukan confirmed.');
        }

        try {
            $booking->status = 'finished';
            $booking->save();

            Log::info("Psikolog FINISH: booking_id={$booking->id} finished by psikolog_user_id={$user->id}");
            return redirect()->route('psikolog.dashboard')->with('success', 'Sesi telah diakhiri. Slot waktu kini tersedia kembali.');
        } catch (\Throwable $e) {
            Log::error("Psikolog FINISH ERROR: ".$e->getMessage(), ['ex'=>$e]);
            return redirect()->route('psikolog.dashboard')->with('error', 'Gagal mengakhiri sesi. Cek log.');
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
                    $timeErrors['work_start'] = 'Jam mulai harus berformat HH:MM.';
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
                    $timeErrors['work_end'] = 'Jam selesai harus berformat HH:MM.';
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

        return redirect()->route('psikolog.profile')->with('success', 'Profil berhasil diperbarui.');
    }

}
