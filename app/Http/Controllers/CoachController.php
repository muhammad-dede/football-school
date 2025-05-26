<?php

namespace App\Http\Controllers;

use App\Models\Coach;
use App\Models\User;
use App\Traits\HasPermissionCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CoachController extends Controller
{
    use HasPermissionCheck;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->checkPermission('coach-index');

        $search = $request->search;
        $per_page = $request->per_page ?? "5";
        $filter = $request->filter ?? 'desc';

        $coaches = Coach::query()->with(['user'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            })
            ->when($filter, function ($query) use ($filter) {
                $query->orderBy('created_at', $filter);
            })
            ->paginate($per_page)
            ->withQueryString();

        return Inertia::render('coach/Index', [
            'coaches' => $coaches,
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
        $this->checkPermission('coach-create');

        return Inertia::render('coach/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkPermission('coach-create');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'specialty' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['required', 'date', 'before:today'],
            'email' => ['required', 'email', 'max:255', 'unique:user,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [], [
            'name' => 'Nama',
            'specialty' => 'Spesialisasi',
            'phone' => 'Telepon',
            'birth_date' => 'Tanggal Lahir',
            'email' => 'Email',
            'password' => 'Password',
        ]);

        try {
            DB::beginTransaction();
            $user = User::create([
                'name' => strtoupper($request->name),
                'email' => strtolower($request->email),
                'password' => bcrypt($request->password),
            ]);
            $user->syncRoles('Coach');
            Coach::create([
                'name' => strtoupper($request->name),
                'specialty' => strtoupper($request->specialty),
                'phone' => $request->phone,
                'birth_date' => $request->birth_date,
                'is_active' => false,
                'user_id' => $user->id,
            ]);
            DB::commit();
            return redirect()->route('coach.index')->with('success', 'Pelatih berhasil ditambahkan');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $this->checkPermission('coach-show');

        $coach = Coach::with(['user'])->findOrFail($id);
        return Inertia::render('coach/Show', [
            'coach' => $coach,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->checkPermission('coach-edit');

        $coach = Coach::with(['user'])->findOrFail($id);
        return Inertia::render('coach/Edit', [
            'coach' => $coach,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->checkPermission('coach-edit');

        $coach = Coach::with('user')->findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'specialty' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['required', 'date', 'before:today'],
            'email' => ['required', 'email', 'max:255', 'unique:user,email,' . $coach->user?->id . ',id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [], [
            'name' => 'Nama',
            'specialty' => 'Spesialisasi',
            'phone' => 'Telepon',
            'birth_date' => 'Tanggal Lahir',
            'email' => 'Email',
            'password' => 'Password',
        ]);

        try {
            DB::beginTransaction();
            $coach->update([
                'name' => strtoupper($request->name),
                'specialty' => strtoupper($request->specialty),
                'phone' => $request->phone,
                'birth_date' => $request->birth_date,
            ]);
            $coach->user->update([
                'name' => strtoupper($request->name),
                'email' => strtolower($request->email),
                'password' => $request->password ? bcrypt($request->password) : $coach->user?->password,
            ]);
            DB::commit();
            return redirect()->route('coach.index')->with('success', 'Pelatih berhasil diubah');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->checkPermission('coach-delete');
        try {
            DB::beginTransaction();
            $coach = Coach::findOrFail($id);
            $user = User::findOrFail($coach->user_id);
            $user->delete();
            $coach->delete();
            DB::commit();
            return redirect()->back()->with('success', 'Pelatih berhasil dihapus');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Change Status the specified resource from storage.
     */
    public function status(string $id)
    {
        $this->checkPermission('coach-status');
        try {
            DB::beginTransaction();
            $coach = Coach::findOrFail($id);
            $coach->update([
                'is_active' => $coach->is_active ? false : true,
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Status Pelatih berhasil diubah');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
