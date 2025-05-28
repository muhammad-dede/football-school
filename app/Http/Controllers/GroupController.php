<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Traits\HasPermissionCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class GroupController extends Controller
{
    use HasPermissionCheck;

    protected $attributes = [
        'code' => 'Kode',
        'name' => 'Nama',
        'age_min' => 'Minimal Usia',
        'age_max' => 'Maksimal Usia',
        'description' => 'Deskripsi',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->checkPermission('group-index');

        $search = $request->search;
        $per_page = $request->per_page ?? "5";
        $filter = $request->filter ?? 'desc';

        $groups = Group::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('code', 'like', '%' . $search . '%');
                });
            })
            ->when($filter, function ($query) use ($filter) {
                $query->orderBy('id', $filter);
            })
            ->paginate($per_page)
            ->withQueryString();

        return Inertia::render('group/Index', [
            'groups' => $groups,
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
        $this->checkPermission('group-create');

        return Inertia::render('group/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkPermission('group-create');

        $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:group,code'],
            'name' => ['required', 'string', 'max:255'],
            'age_min' => ['required', 'numeric', 'lte:age_max'],
            'age_max' => ['required', 'numeric', 'gte:age_min'],
            'description' => ['nullable', 'string'],
        ], [], $this->attributes);

        try {
            DB::beginTransaction();
            Group::create([
                'code' => strtoupper($request->code),
                'name' => strtoupper($request->name),
                'age_min' => $request->age_min,
                'age_max' => $request->age_max,
                'description' => $request->description,
                'is_active' => false,
            ]);
            DB::commit();
            return redirect()->route('group.index')->with('success', 'Grup berhasil ditambahkan');
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->checkPermission('group-edit');

        $group = Group::findOrFail($id);
        return Inertia::render('group/Edit', [
            'group' => $group,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->checkPermission('group-edit');

        $group = Group::findOrFail($id);

        $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:group,code,' . $id . ',id'],
            'name' => ['required', 'string', 'max:255'],
            'age_min' => ['required', 'numeric', 'lte:age_max'],
            'age_max' => ['required', 'numeric', 'gte:age_min'],
            'description' => ['nullable', 'string'],
        ], [], $this->attributes);

        try {
            DB::beginTransaction();
            $group->update([
                'code' => strtoupper($request->code),
                'name' => strtoupper($request->name),
                'age_min' => $request->age_min,
                'age_max' => $request->age_max,
                'description' => $request->description,
            ]);
            DB::commit();
            return redirect()->route('group.index')->with('success', 'Grup berhasil diubah');
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
        $this->checkPermission('group-status');

        $group = Group::findOrFail($id);

        try {
            DB::beginTransaction();
            $group->update([
                'is_active' => $group->is_active ? false : true,
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Status Grup berhasil diubah');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
