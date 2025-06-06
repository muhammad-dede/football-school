<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Period;
use App\Models\Stage;
use App\Models\StudentProgram;
use App\Traits\HasPermissionCheck;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportStudentController extends Controller
{
    use HasPermissionCheck;

    protected $periods = [];
    protected $groups = [];
    protected $stages = [];

    public function __construct()
    {
        $this->periods = Period::latest()->get();
        $this->groups = Group::all();
        $this->stages = Stage::where('is_active', true)->orderBy('order', 'asc')->get();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->checkPermission('report-student-index');

        $search = $request->search ?? null;
        $group_code = $request->group_code ?? null;
        $period_id = $request->period_id ?? null;
        $student_programs = [];
        $report_students = [];

        if (!is_null($period_id)) {
            $student_programs = StudentProgram::query()
                ->select('student_program.*')
                ->join('student', 'student.id', '=', 'student_program.student_id')
                ->with(['student', 'period', 'group'])
                ->when($search, function ($query) use ($search) {
                    $query->where('student.name', 'like', '%' . $search . '%');
                })
                ->when($group_code, function ($query) use ($group_code) {
                    $query->where('student_program.group_code', $group_code);
                })
                ->where('student_program.period_id', $period_id)
                ->orderBy('student.name', 'asc')
                ->get();
        }

        return Inertia::render('report-student/Index', [
            'periods' => $this->periods,
            'groups' => $this->groups,
            'search_term' => $search,
            'group_code_term' => $group_code,
            'period_id_term' => $period_id,
            'student_programs' => $student_programs,
            'report_students' => $report_students,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
