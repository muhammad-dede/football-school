<?php

namespace App\Http\Controllers;

use App\Models\Period;
use App\Traits\HasPermissionCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class PeriodController extends Controller
{
    use HasPermissionCheck;

    protected $attributes = [
        'name' => 'Nama',
        'start_date' => 'Tanggal Mulai',
        'end_date' => 'Tanggal Berakhir',
    ];

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->checkPermission('period-index');

        $search = $request->search;
        $per_page = $request->per_page ?? "5";
        $filter = $request->filter ?? 'desc';

        $periods = Period::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                });
            })
            ->when($filter, function ($query) use ($filter) {
                $query->orderBy('id', $filter);
            })
            ->paginate($per_page)
            ->withQueryString();

        return Inertia::render('period/Index', [
            'periods' => $periods,
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
        $this->checkPermission('period-create');

        return Inertia::render('period/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkPermission('period-create');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
        ], [], $this->attributes);

        try {
            DB::beginTransaction();
            Period::create([
                'name' => strtoupper($request->name),
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'is_active' => false,
            ]);
            DB::commit();
            return redirect()->route('period.index')->with('success', 'Periode berhasil ditambahkan');
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
        $this->checkPermission('period-edit');

        $period = Period::findOrFail($id);
        return Inertia::render('period/Edit', [
            'period' => $period,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->checkPermission('period-edit');

        $period = Period::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
        ], [], $this->attributes);

        try {
            DB::beginTransaction();
            $period->update([
                'name' => strtoupper($request->name),
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);
            DB::commit();
            return redirect()->route('period.index')->with('success', 'Periode berhasil diubah');
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
        //
    }

    /**
     * Change Status the specified resource from storage.
     */
    public function status(string $id)
    {
        $this->checkPermission('period-status');

        $period = Period::findOrFail($id);

        try {
            DB::beginTransaction();
            Period::where('is_active', true)->update([
                'is_active' => false,
            ]);
            $period->update([
                'is_active' => $period->is_active ? false : true,
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Status Periode berhasil diubah');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
