<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GroupMessage;
use App\Models\GroupMember;
use App\Models\TherapyGroup;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class GroupMessageController extends Controller
{
    public function store(Request $request, $slug)
    {
        $group = TherapyGroup::where('slug', $slug)->firstOrFail();

        $user = Auth::user();
        if (! $user) {
            return response()->json(['error' => 'unauthenticated'], 401);
        }

        $member = GroupMember::where('therapy_group_id', $group->id)
                             ->where('user_id', $user->id)
                             ->first();

        if (! $member) {
            return response()->json(['error' => 'not_joined'], 403);
        }

        $v = Validator::make($request->all(), [
            'message' => 'required|string|max:2000'
        ]);
        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        try {
            $msg = GroupMessage::create([
                'therapy_group_id' => $group->id,
                'user_id' => $user->id,
                'message' => $request->input('message'),
            ]);
            $pseudonym = DB::table('group_members')
                ->where('therapy_group_id', $group->id)
                ->where('user_id', $user->id)
                ->value('pseudonym');

            return response()->json([
                'id' => $msg->id,
                'message' => $msg->message,
                'created_at' => $msg->created_at->toDateTimeString(),
                'pseudonym' => $pseudonym,
                'user_id' => $msg->user_id,
            ], 201);

        } catch (\Throwable $e) {
            Log::error('GroupMessageController::store exception: ' . $e->getMessage(), [
                'group_id' => $group->id,
                'user_id' => $user->id,
                'exception' => $e,
            ]);
            return response()->json(['error' => 'server_error', 'message' => 'Failed to save message'], 500);
        }
    }

    public function fetch(Request $request, $slug)
    {
        $group = TherapyGroup::where('slug', $slug)->firstOrFail();
        $since = $request->query('since'); // optional ISO datetime
        $q = DB::table('group_messages AS gm')
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
            );

        if ($since) {
            $q->where('gm.created_at', '>', $since);
        }

        $rows = $q->get();
        $msgs = $rows->map(function ($r) {
            return [
                'id' => $r->id,
                'message' => $r->message,
                'created_at' => (string) $r->created_at,
                'pseudonym' => $r->pseudonym ?? null,
                'user_id' => $r->user_id,
            ];
        });

        return response()->json(['messages' => $msgs]);
    }
}
