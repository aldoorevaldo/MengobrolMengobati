<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Message;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    // tampilkan halaman chat (psikolog view)
    public function show(Request $request, Booking $booking)
    {
        $user = $request->user();

        // only allow psikolog pemilik booking or the booking user
        $isOwner = ($booking->user_id === $user->id);
        $isPsikolog = false;
        try {
            $psikologRow = \DB::table('psikologs')->where('user_id', $user->id)->first();
            if ($psikologRow && $booking->psikolog_id == $psikologRow->id) {
                $isPsikolog = true;
            }
        } catch (\Throwable $t) {
            // ignore
        }

        if (!($isOwner || $isPsikolog)) {
            abort(403, 'Unauthorized to access chat for this booking.');
        }

        // Only allow chat when booking confirmed
        if ($booking->status !== 'confirmed') {
            return redirect()->back()->with('error', 'Chat hanya tersedia untuk booking yang telah dikonfirmasi.');
        }

        // pass a few things to view
        return view('psikolog.chat', [
            'booking' => $booking->load('user','psikolog','psikiater'),
            'currentUser' => $user,
            'isPsikolog' => $isPsikolog,
        ]);
    }

    // messages API — now includes sender_name
    public function messages(Request $request, Booking $booking)
    {
        $user = $request->user();

        // authorization: only participant psikolog or booking owner allowed
        $psikologRow = \DB::table('psikologs')->where('user_id', $user->id)->first();
        $isPsikolog = $psikologRow && $booking->psikolog_id == $psikologRow->id;
        $isOwner = $booking->user_id == $user->id;

        if (!($isOwner || $isPsikolog)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // eager-load sender to include name
        $messages = Message::with('sender')
            ->where('booking_id', $booking->id)
            ->orderBy('created_at','asc')
            ->get()
            ->map(function($m) {
                return [
                    'id' => $m->id,
                    'sender_type' => $m->sender_type,
                    'sender_id' => $m->sender_id,
                    'sender_name' => $m->sender ? $m->sender->name : null,
                    'content' => $m->content,
                    'created_at' => $m->created_at->toDateTimeString(),
                ];
            });

        return response()->json(['messages' => $messages]);
    }

    // send message
    public function send(Request $request, Booking $booking)
    {
        $request->validate([
            'content' => 'required|string|max:4000'
        ]);

        $user = $request->user();

        // authorization
        $psikologRow = \DB::table('psikologs')->where('user_id', $user->id)->first();
        $isPsikolog = $psikologRow && $booking->psikolog_id == $psikologRow->id;
        $isOwner = $booking->user_id == $user->id;

        if (!($isOwner || $isPsikolog)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Only allow chat for confirmed bookings
        if ($booking->status !== 'confirmed') {
            return response()->json(['error' => 'Chat hanya tersedia untuk booking yang telah dikonfirmasi.'], 422);
        }

        $senderType = $isPsikolog ? 'psikolog' : 'user';

        $msg = Message::create([
            'booking_id' => $booking->id,
            'sender_type' => $senderType,
            'sender_id' => $user->id,
            'content' => $request->input('content'),
        ]);

        // load sender name optionally
        $msg->load('sender');

        Log::info("CHAT: new message for booking {$booking->id} by {$senderType}: {$user->id}");

        // return message payload including sender_name
        return response()->json([
            'message' => [
                'id' => $msg->id,
                'sender_type' => $msg->sender_type,
                'sender_id' => $msg->sender_id,
                'sender_name' => $msg->sender ? $msg->sender->name : null,
                'content' => $msg->content,
                'created_at' => $msg->created_at->toDateTimeString(),
            ]
        ], 201);
    }

    public function endSession(Request $request, Booking $booking)
    {
        $user = $request->user();

        // pastikan pengguna adalah psikolog yang punya booking ini
        $psikologRow = \DB::table('psikologs')->where('user_id', $user->id)->first();
        if (! $psikologRow || $booking->psikolog_id != $psikologRow->id) {
            abort(403, 'Unauthorized');
        }

        if ($booking->status !== 'confirmed') {
            return redirect()->back()->with('error', 'Booking tidak bisa diakhiri karena status bukan confirmed.');
        }

        try {
            $booking->status = 'finished';
            $booking->save();

            \Log::info("CHAT: booking {$booking->id} finished by psikolog_user_id={$user->id}");

            return redirect()->route('psikolog.dashboard')->with('success', 'Sesi telah diakhiri. Slot waktu kini tersedia kembali.');
        } catch (\Throwable $e) {
            \Log::error("CHAT: gagal mengakhiri sesi booking {$booking->id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengakhiri sesi. Cek log.');
        }
    }
}
