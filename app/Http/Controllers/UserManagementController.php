<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    private const CRUD_PERMISSION_OPTIONS = [
        'locally_funded_projects' => 'Locally Funded Projects',
        'fund_utilization_reports' => 'Fund Utilization Report',
        'local_project_monitoring_committee' => 'Local Project Monitoring Committee',
        'road_maintenance_status_reports' => 'Road Maintenance Status Report',
    ];

    private const CRUD_ACTION_OPTIONS = [
        'add' => 'ADD',
        'update' => 'UPDATE',
        'delete' => 'DELETE',
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('superadmin');
    }

    /**
     * Display a listing of all users.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $users = User::paginate(15);

        return view('admin.users.index', [
            'users' => $users,
            'crudPermissionOptions' => self::CRUD_PERMISSION_OPTIONS,
            'crudActionOptions' => self::CRUD_ACTION_OPTIONS,
        ]);
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fname' => ['required', 'string', 'max:255'],
            'lname' => ['required', 'string', 'max:255'],
            'emailaddress' => ['required', 'email', 'unique:tbusers,emailaddress'],
            'username' => ['required', 'string', 'unique:tbusers,username'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'agency' => ['required', 'string'],
            'position' => ['required', 'string'],
            'region' => ['required', 'string'],
            'province' => ['required', 'string'],
            'office' => ['nullable', 'string'],
            'mobileno' => ['required', 'digits:11'],
            'role' => ['required', 'in:user,admin,superadmin'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['email_verified_at'] = now();

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'User created successfully!');
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function show(User $user)
    {
        return view('admin.users.show', [
            'user' => $user,
            'crudPermissionOptions' => self::CRUD_PERMISSION_OPTIONS,
            'crudActionOptions' => self::CRUD_ACTION_OPTIONS,
        ]);
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'fname' => ['required', 'string', 'max:255'],
            'lname' => ['required', 'string', 'max:255'],
            'emailaddress' => ['required', 'email', "unique:tbusers,emailaddress,{$user->idno},idno"],
            'username' => ['required', 'string', "unique:tbusers,username,{$user->idno},idno"],
            'agency' => ['required', 'string'],
            'position' => ['required', 'string'],
            'region' => ['required', 'string'],
            'province' => ['required', 'string'],
            'office' => ['nullable', 'string'],
            'mobileno' => ['required', 'digits:11'],
            'role' => ['required', 'in:user,admin,superadmin'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => ['required', 'string', 'min:8', 'confirmed']]);
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'User updated successfully!');
    }

    /**
     * Delete the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->idno === Auth::id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted successfully!');
    }

    public function block(User $user)
    {
        if ($user->idno === Auth::id()) {
            return back()->with('error', 'You cannot change the status of your own account.');
        }

        $isInactive = strtolower(trim((string) $user->status)) === 'inactive';

        $user->update([
            'status' => $isInactive ? 'active' : 'inactive',
        ]);

        if ($isInactive) {
            return redirect()->route('users.index')->with('success', 'User unblocked successfully.');
        }

        return redirect()->route('users.index')->with('success', 'User blocked successfully.');
    }

    public function updateAccess(Request $request, User $user)
    {
        $redirectTo = $request->filled('redirect_to')
            ? $request->input('redirect_to')
            : route('users.index', ['tab' => 'access-grants']);

        if (strtolower(trim((string) $user->role)) === 'superadmin') {
            return redirect()
                ->to($redirectTo)
                ->with('error', 'Superadmin accounts always keep full access.');
        }

        $validated = $request->validate([
            'crud_permissions' => ['nullable', 'array'],
            'crud_permissions.*' => ['string'],
        ]);

        $validPermissionKeys = collect(array_keys(self::CRUD_PERMISSION_OPTIONS))
            ->flatMap(function ($aspect) {
                return collect(array_keys(self::CRUD_ACTION_OPTIONS))
                    ->map(fn ($action) => $aspect . '.' . $action);
            })
            ->all();

        $permissions = collect($validated['crud_permissions'] ?? [])
            ->map(fn ($permission) => strtolower(trim((string) $permission)))
            ->filter(fn ($permission) => in_array($permission, $validPermissionKeys, true))
            ->unique()
            ->values()
            ->all();

        if (count($permissions) === count($validPermissionKeys)) {
            $user->access = User::ACCESS_SCOPE_ALL;
        } elseif ($permissions === []) {
            $user->access = User::ACCESS_SCOPE_NONE;
        } else {
            $user->access = User::ACCESS_PERMISSION_PREFIX . implode(',', $permissions);
        }

        $user->save();

        return redirect()
            ->to($redirectTo)
            ->with('success', 'Access grants updated successfully.');
    }
}
