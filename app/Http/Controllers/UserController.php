<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\HasPermissionCheck;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use HasPermissionCheck;

    protected $roles;

    public function __construct()
    {
        $this->roles = Role::whereNotIn('name', ['Super Admin', 'Student', 'Coach'])->get();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->checkPermission('user-index');

        $search = $request->search;
        $per_page = $request->per_page ?? "5";
        $filter = $request->filter ?? 'desc';

        $users = User::query()->with(['roles'])
            ->whereDoesntHave('roles', function ($query) {
                $query->whereIn('name', ['Super Admin', 'Student', 'Coach']);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->when($filter, function ($query) use ($filter) {
                $query->orderBy('id', $filter);
            })
            ->paginate($per_page)
            ->withQueryString();

        return Inertia::render('user/Index', [
            'users' => $users,
            'search_term' => $search,
            'per_page_term' => $per_page,
            'filter_term' => $filter,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->checkPermission('user-create');

        return Inertia::render('user/Create', [
            'roles' => $this->roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkPermission('user-create');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:user,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'max:255', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name' => strtoupper($request->name),
            'email' => strtolower($request->email),
            'password' => bcrypt($request->password),
        ]);

        $user->syncRoles($request->role);
        return redirect()->route('user.index')->with('success', 'Pengguna berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->checkPermission('user-edit');

        $user = User::with(['roles'])->findOrFail($id);
        return Inertia::render('user/Edit', [
            'roles' => $this->roles,
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->checkPermission('user-edit');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:user,email,' . $id . ',id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'string', 'max:255', 'exists:roles,name'],
        ]);

        $user = User::findOrFail($id);

        $user->update([
            'name' => strtoupper($request->name),
            'email' => strtolower($request->email),
            'password' => $request->password ? bcrypt($request->password) : $user->password,
        ]);

        $user->syncRoles($request->role);
        return redirect()->route('user.index')->with('success', 'Pengguna berhasil diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
