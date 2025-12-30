<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TherapyGroup;
use App\Models\GroupMember;
use App\Models\GroupMessage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TherapyGroupController extends Controller
{
    public function index()
    {
        $groups = TherapyGroup::all();
        return view('therapy.index', compact('groups'));
    }

    public function show($slug)
    {
        // ambil group
        $group = TherapyGroup::where('slug', $slug)->firstOrFail();

        // current user & member check (untuk menampilkan tombol Join / pseudonym)
        $user = Auth::user();
        $member = null;
        if ($user) {
            $member = GroupMember::where('therapy_group_id', $group->id)
                        ->where('user_id', $user->id)
                        ->first();
        }

        // Ambil pesan bersama pseudonym menggunakan LEFT JOIN (satu query)
        $rows = DB::table('group_messages AS gm')
            ->leftJoin('group_members AS gm2', function ($join) {
                $join->on('gm.therapy_group_id', '=', 'gm2.therapy_group_id')
                    ->on('gm.user_id', '=', 'gm2.user_id');
            })
            ->where('gm.therapy_group_id', $group->id)
            ->orderBy('gm.created_at', 'asc')
            ->select(
                'gm.id',
                'gm.message',
                'gm.created_at',
                'gm.user_id',
                DB::raw('gm2.pseudonym as pseudonym')
            )
            ->get();

        // Normalisasi: pastikan tipe konsisten untuk client
        $messages = $rows->map(function ($r) {
            return (object) [
                'id' => isset($r->id) ? (int)$r->id : null,
                'message' => $r->message,
                'created_at' => isset($r->created_at) ? (string)$r->created_at : null,
                'pseudonym' => isset($r->pseudonym) ? $r->pseudonym : null,
                'user_id' => isset($r->user_id) ? (int)$r->user_id : null,
            ];
        });

        return view('therapy.show', compact('group', 'member', 'messages'));
    }

    // join group (create member row and pseudonym)
    public function join(Request $request, $slug)
    {
        $group = TherapyGroup::where('slug',$slug)->firstOrFail();
        $user = Auth::user();
        if (! $user) return redirect()->route('login');

        $existing = GroupMember::where('therapy_group_id',$group->id)
                               ->where('user_id',$user->id)->first();
        if ($existing) {
            return redirect()->route('therapy.show',$group->slug);
        }

        // generate deterministic pseudonym for consistency
        $base = substr(sha1(config('app.key') . $user->id . $group->id), 0, 8);
        $pseudonym = 'Anon-' . strtoupper($base);

        // ensure uniqueness within group (fallback)
        $attempt = 0;
        $p = $pseudonym;
        while (GroupMember::where('therapy_group_id',$group->id)->where('pseudonym',$p)->exists() && $attempt < 5) {
            $attempt++;
            $p = $pseudonym . '-' . $attempt;
        }

        GroupMember::create([
            'therapy_group_id' => $group->id,
            'user_id' => $user->id,
            'pseudonym' => $p,
        ]);

        return redirect()->route('therapy.show',$group->slug);
    }

    public function open($slug)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $group = TherapyGroup::where('slug', $slug)->firstOrFail();

        // Wrap in transaction to avoid race condition
        DB::transaction(function() use ($group, $user) {
            $exists = GroupMember::where('therapy_group_id', $group->id)
                        ->where('user_id', $user->id)
                        ->exists();
            if (! $exists) {
                // deterministic pseudonym: Anon- + first 8 hex of sha1(app_key + user_id + group_id)
                $base = strtoupper(substr(sha1(config('app.key') . $user->id . $group->id), 0, 8));
                $pseudonym = 'Anon-' . $base;

                // ensure uniqueness in group (append suffix if collision)
                $attempt = 0;
                $candidate = $pseudonym;
                while (GroupMember::where('therapy_group_id', $group->id)->where('pseudonym', $candidate)->exists() && $attempt < 20) {
                    $attempt++;
                    $candidate = $pseudonym . '-' . $attempt;
                }
                GroupMember::create([
                    'therapy_group_id' => $group->id,
                    'user_id' => $user->id,
                    'pseudonym' => $candidate,
                ]);
            }
        });

        // now redirect to show (which will render chat and detect member)
        return redirect()->route('therapy.show', $group->slug);
    }
}
