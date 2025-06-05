<?php

namespace App\Http\Controllers;

use App\Enums\Attendance;
use App\Models\Coach;
use App\Models\Group;
use App\Models\Period;
use App\Models\Student;
use App\Models\Training;
use App\Traits\HasPermissionCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TrainingController extends Controller
{
    use HasPermissionCheck;

    protected $groups = [];
    protected $periods = [];
    protected $coaches = [];
    protected $attendances = [];
    protected $attributes = [
        'group_code' => 'Grup',
        'period_id' => 'Periode',
        'coach_id' => 'Pelatih',
        'training_date' => 'Tanggal Pelatihan',
        'start_time' => 'Waktu Mulai',
        'end_time' => 'Waktu Selesai',
        'location' => 'Lokasi',
        'description' => 'Deskripsi',
    ];

    public function __construct()
    {
        $this->groups = Group::where('is_active', true)->get();
        $this->periods = Period::where('is_active', true)->get();
        $this->coaches = Coach::where('is_active', true)->get();
        $this->attendances = Attendance::options();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->checkPermission('training-index');

        $search = $request->search;
        $per_page = $request->per_page ?? "5";
        $filter = $request->filter ?? 'desc';

        $trainings = Training::query()
            ->with(['group', 'period', 'coach', 'attendances'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('group', function ($sub) use ($search) {
                        $sub->where('name', 'like', '%' . $search . '%')
                            ->orWhere('code', 'like', '%' . $search . '%');
                    })->orWhereHas('period', function ($sub) use ($search) {
                        $sub->where('name', 'like', '%' . $search . '%');
                    })->orWhereHas('coach', function ($sub) use ($search) {
                        $sub->where('name', 'like', '%' . $search . '%');
                    });
                });
            })
            ->when($filter, function ($query) use ($filter) {
                $query->orderBy('id', $filter);
            })
            ->paginate($per_page)
            ->withQueryString();

        return Inertia::render('training/Index', [
            'trainings' => $trainings,
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
        $this->checkPermission('training-create');

        return Inertia::render('training/Create', [
            'groups' => $this->groups,
            'periods' => $this->periods,
            'coaches' => $this->coaches,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkPermission('training-create');

        $request->validate([
            'group_code' => ['required', 'exists:group,code'],
            'period_id' => ['required', 'exists:period,id'],
            'coach_id' => ['required', 'exists:coach,id'],
            'training_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time'   => ['required', 'date_format:H:i', 'after:start_time'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ], [], $this->attributes);

        try {
            DB::beginTransaction();
            $training = Training::create([
                'group_code' => $request->group_code,
                'period_id' => $request->period_id,
                'coach_id' => $request->coach_id,
                'training_date' => $request->training_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'location' => $request->location,
                'description' => $request->description,
            ]);
            DB::commit();
            return redirect()->route('training.show', $training->id)->with('success', 'Pelatihan berhasil ditambahkan');
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
        $this->checkPermission('training-show');

        $training = Training::with(['group', 'period', 'coach', 'attendances'])->findOrFail($id);
        $students = Student::whereHas('currentProgram', function ($query) use ($training) {
            $query->where('period_id', $training->period_id)
                ->where('group_code', $training->group_code);
        })->get();
        return Inertia::render('training/Show', [
            'attendances' => $this->attendances,
            'training' => $training,
            'students' => $students,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->checkPermission('training-edit');

        $training = Training::with(['group', 'period', 'coach', 'attendances', 'attendances.student'])->findOrFail($id);
        return Inertia::render('training/Edit', [
            'groups' => $this->groups,
            'periods' => $this->periods,
            'coaches' => $this->coaches,
            'training' => $training,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->checkPermission('training-edit');

        $request->validate([
            'group_code' => ['required', 'exists:group,code'],
            'period_id' => ['required', 'exists:period,id'],
            'coach_id' => ['required', 'exists:coach,id'],
            'training_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time'   => ['required', 'date_format:H:i', 'after:start_time'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ], [], $this->attributes);

        try {
            $training = Training::findOrFail($id);
            DB::beginTransaction();
            $training->update([
                'coach_id' => $request->coach_id,
                'training_date' => $request->training_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'location' => $request->location,
                'description' => $request->description,
            ]);
            DB::commit();
            return redirect()->route('training.show', $training->id)->with('success', 'Pelatihan berhasil diubah');
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
        $this->checkPermission('training-delete');

        try {
            DB::beginTransaction();
            $training = Training::findOrFail($id);
            $training->delete();
            DB::commit();
            return redirect()->route('training.index')->with('success', 'Pelatihan berhasil dihapus');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Change the specified attendance.
     */
    public function attendance(Request $request, string $training_id)
    {
        $this->checkPermission('training-attendance');

        try {
            DB::beginTransaction();
            $training = Training::findOrFail($training_id);
            if ($request->attendances) {
                foreach ($request->attendances as $key => $value) {
                    $training->attendances()->updateOrCreate([
                        'training_id' => $training_id,
                        'student_id' => $value['student_id'],
                    ], [
                        'training_id' => $training_id,
                        'student_id' => $value['student_id'],
                        'attendance' => $value['attendance'],
                        'notes' => $value['notes'],
                    ]);
                }
            }
            DB::commit();
            return redirect()->route('training.show', $training_id)->with('success', 'Kehadiran berhasil diperbaharui');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
