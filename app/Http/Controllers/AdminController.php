<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Psikiater;
use App\Models\Psikolog;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $counts = [
            'users'     => 0,
            'psikiater' => 0,
            'psikolog'  => 0,
            'therapy_group' => 0,
        ];

        try {
            $counts['users']     = DB::table('users')->where('role', 'user')->count();
            $counts['psikiater'] = DB::table('users')->where('role', 'psikiater')->count();
            $counts['psikolog']  = DB::table('users')->where('role', 'psikolog')->count();
            $counts['therapy_group'] = DB::table('therapy_groups')->count();
        } catch (\Throwable $e) {
        }
        $lists = [
            'users'     => [],
            'psikiater' => [],
            'psikolog'  => [],
        ];

        try {
            $lists['users'] = DB::table('users')
                ->where('role', 'user')
                ->select('id', 'name', 'email', 'created_at')
                ->orderBy('id', 'desc')
                ->limit(200)
                ->get();

            $lists['psikiater'] = DB::table('users')
                ->where('role', 'psikiater')
                ->select('id', 'name', 'email', 'created_at')
                ->orderBy('id', 'desc')
                ->limit(200)
                ->get();

            $lists['psikolog'] = DB::table('users')
                ->where('role', 'psikolog')
                ->select('id', 'name', 'email', 'created_at')
                ->orderBy('id', 'desc')
                ->limit(200)
                ->get();
        } catch (\Throwable $e) {
        }
        $bookings = [];
        try {
            $bookings = DB::table('bookings')
                ->leftJoin('users as u', 'bookings.user_id', '=', 'u.id')
                ->leftJoin('psikiaters as ps', 'bookings.psikiater_id', '=', 'ps.id')
                ->where('bookings.type', 'psikiater')
                ->select(
                    'bookings.id',
                    'bookings.service',
                    'bookings.status',
                    'bookings.scheduled_at',
                    'u.name as user_name',
                    'ps.name as psikiater_name',
                    'bookings.created_at'
                )
                ->orderBy('bookings.created_at', 'desc')
                ->limit(200)
                ->get();
        } catch (\Throwable $e) {
        }

        return view('admin.dashboard', compact('counts', 'lists', 'bookings'));
    }
    public function users()
    {
        $users = DB::table('users')->where('role', 'user')->get();
        return view('admin.list-users', compact('users'));
    }

    public function destroyUser($id)
    {
        $user = User::where('role', 'user')->findOrFail($id);
        $user->delete();

        return redirect()
            ->route('admin.users')
            ->with('success', 'User berhasil dihapus.');
    }
    public function psikiater()
    {
        $psikiater = DB::table('users')->where('role', 'psikiater')->get();
        return view('admin.list-psikiater', compact('psikiater'));
    }

    public function createPsikiater()
    {
        return view('admin.create-psikiater');
    }

    public function storePsikiater(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|string|min:6',
            'hospital'    => 'nullable|string|max:255',
            'work_start'  => 'nullable|string|max:50',
            'work_end'    => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'photo'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::transaction(function () use ($request) {

            $hashedPassword = Hash::make($request->password);
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => $hashedPassword,
                'role'     => 'psikiater',
            ]);
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('psikiaters', 'public');
            }
            Psikiater::create([
                'user_id'     => $user->id,
                'name'        => $request->name,
                'hospital'    => $request->hospital,
                'work_start'  => $request->work_start,
                'work_end'    => $request->work_end,
                'description' => $request->description,
                'photo'       => $photoPath,
            ]);
        });

        return redirect()
            ->route('admin.psikiater')
            ->with('success', 'Psikiater berhasil ditambahkan dan bisa langsung login.');
    }

    public function destroyPsikiater($id)
    {
        $user = User::where('role', 'psikiater')->findOrFail($id);
        $user->delete();

        return redirect()
            ->route('admin.psikiater')
            ->with('success', 'Psikiater berhasil dihapus.');
    }
    public function psikolog()
    {
        $psikolog = DB::table('users')->where('role', 'psikolog')->get();
        return view('admin.list-psikolog', compact('psikolog'));
    }

    public function createPsikolog()
    {
        return view('admin.create-psikolog');
    }

    public function storePsikolog(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|string|min:6',
            'hospital'    => 'nullable|string|max:255',
            'work_start'  => 'nullable|string|max:50',
            'work_end'    => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'photo'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::transaction(function () use ($request) {

            $hashedPassword = Hash::make($request->password);
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => $hashedPassword,
                'role'     => 'psikolog',
            ]);
            $photoPath = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('psikologs', 'public');
            }
            Psikolog::create([
                'user_id'     => $user->id,
                'name'        => $request->name,
                'hospital'    => $request->hospital,
                'work_start'  => $request->work_start,
                'work_end'    => $request->work_end,
                'description' => $request->description,
                'photo'       => $photoPath,
            ]);
        });

        return redirect()
            ->route('admin.psikolog')
            ->with('success', 'Psikolog berhasil ditambahkan dan bisa langsung login.');
    }

    public function destroyPsikolog($id)
    {
        $user = User::where('role', 'psikolog')->findOrFail($id);
        $user->delete();

        return redirect()
            ->route('admin.psikolog')
            ->with('success', 'Psikolog berhasil dihapus');
    }
    public function bookingMenu()
    {
        return view('admin.booking-menu');
    }

    public function bookingsPsikiater()
    {
        $bookings = DB::table('bookings')
            ->leftJoin('users as u', 'bookings.user_id', '=', 'u.id')
            ->leftJoin('psikiaters as ps', 'bookings.psikiater_id', '=', 'ps.id')
            ->where('bookings.type', 'psikiater')
            ->select(
                'bookings.*',
                'u.name as user_name',
                'ps.name as psikiater_name'
            )
            ->orderBy('bookings.id', 'desc')
            ->get();

        return view('admin.list-bookings-psikiater', compact('bookings'));
    }

    public function bookingsPsikolog()
    {
        $bookings = DB::table('bookings')
            ->leftJoin('users as u', 'bookings.user_id', '=', 'u.id')
            ->leftJoin('psikologs as ps', 'bookings.psikolog_id', '=', 'ps.id')
            ->where('bookings.type', 'psikolog')
            ->select(
                'bookings.*',
                'u.name as user_name',
                'ps.name as psikolog_name'
            )
            ->orderBy('bookings.id', 'desc')
            ->get();

        return view('admin.list-bookings-psikolog', compact('bookings'));
    }

    public function therapyGroups(Request $request)
    {
        $groups = DB::table('therapy_groups')
            ->orderBy('created_at', 'asc')
            ->get();
        $periods = DB::table('group_messages')
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month')
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('year DESC, month DESC')
            ->get();
        if ($periods->isEmpty()) {
            $topUsers      = collect();
            $selectedYear  = null;
            $selectedMonth = null;

            return view('admin.therapy-groups.index', compact(
                'groups', 'topUsers', 'periods', 'selectedYear', 'selectedMonth'
            ));
        }
        $selectedYear  = $request->input('year',  $periods->first()->year);
        $selectedMonth = $request->input('month', $periods->first()->month);
        $selectedYear  = (int) $selectedYear;
        $selectedMonth = (int) $selectedMonth;
        $topUsers = DB::table('group_messages as gm')
            ->leftJoin('users as u', 'gm.user_id', '=', 'u.id')
            ->whereNotNull('gm.user_id')
            ->whereYear('gm.created_at', $selectedYear)
            ->whereMonth('gm.created_at', $selectedMonth)
            ->groupBy('gm.user_id', 'u.name', 'u.email')
            ->select(
                'gm.user_id',
                DB::raw('COUNT(*) as total_messages'),
                DB::raw('COALESCE(u.name, "(user dihapus)") as name'),
                DB::raw('COALESCE(u.email, "-") as email')
            )
            ->orderByDesc('total_messages')
            ->limit(10)
            ->get();

        return view('admin.therapy-groups.index', compact(
            'groups', 'topUsers', 'periods', 'selectedYear', 'selectedMonth'
        ));
    }
    public function createTherapyGroup()
    {
        return view('admin.therapy-groups.create');
    }
    public function storeTherapyGroup(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:200',
            'slug'        => 'required|string|max:200|unique:therapy_groups,slug',
            'description' => 'nullable|string',
        ]);

        DB::table('therapy_groups')->insert([
            'title'       => $request->title,
            'slug'        => $request->slug,
            'description' => $request->description,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return redirect()
            ->route('admin.therapy.groups')
            ->with('success', 'Therapy group berhasil dibuat.');
    }
    public function therapyGroupsDestroy($slug)
    {
        $group = DB::table('therapy_groups')->where('slug', $slug)->first();

        if (!$group) {
            abort(404);
        }

        DB::table('group_messages')->where('therapy_group_id', $group->id)->delete();
        DB::table('therapy_groups')->where('id', $group->id)->delete();

        return redirect()
            ->route('admin.therapy.groups')
            ->with('success', 'Therapy group berhasil dihapus.');
    }
}
