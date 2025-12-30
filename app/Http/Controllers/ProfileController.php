<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\Booking;


class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $psikiaterBookings = Booking::with(['psikiater'])
            ->where('user_id', $user->id)
            ->where('type', 'psikiater')
            ->orderBy('scheduled_at', 'desc')
            ->get();
        $psikologBookings = Booking::with(['psikolog'])
            ->where('user_id', $user->id)
            ->where('type', 'psikolog')
            ->orderBy('scheduled_at', 'desc')
            ->get();

        return view('profile.show', compact('user', 'psikiaterBookings', 'psikologBookings'));
    }
    public function edit(Request $request)
    {
        $user = $request->user();
        return view('profile.edit', compact('user'));
    }
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required','string','max:255'],
            'email' => ['required','email','max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable','confirmed','min:6'],
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        DB::table('users')->where('id', $user->id)->update($data);

        return redirect()->route('profile.show')->with('success', 'Profil berhasil diperbarui.');
    }
}
