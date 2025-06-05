<?php

namespace App\Http\Controllers;

use App\Enums\Attendance;
use App\Models\Coach;
use App\Models\Group;
use App\Models\MatchEvent;
use App\Models\Period;
use App\Models\Student;
use App\Traits\HasPermissionCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MatchEventController extends Controller
{
    use HasPermissionCheck;

    protected $groups = [];
    protected $periods = [];
    protected $coaches = [];
    protected $students = [];
    protected $attendances = [];
    protected $attributes = [
        'group_code' => 'Grup',
        'period_id' => 'Periode',
        'coach_id' => 'Pelatih',
        'opponent' => 'Nama Tim Lawan',
        'match_date' => 'Tanggal Pertandingan',
        'start_time' => 'Waktu Mulai',
        'end_time' => 'Waktu Selesai',
        'group_score' => 'Skor Tim',
        'opponent_score' => 'Skor Lawan',
        'location' => 'Lokasi',
        'description' => 'Deskripsi',
        'participants.*.student_id' => 'Pemain',
    ];

    public function __construct()
    {
        $this->groups = Group::where('is_active', true)->get();
        $this->periods = Period::where('is_active', true)->get();
        $this->coaches = Coach::where('is_active', true)->get();
        $this->students = Student::with(['enrollment'])->whereHas('enrollment', function ($query) {
            $query->where('is_active', true);
        })->get();
        $this->attendances = Attendance::options();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->checkPermission('match-event-index');

        $search = $request->search;
        $per_page = $request->per_page ?? "5";
        $filter = $request->filter ?? 'desc';

        $match_events = MatchEvent::query()
            ->with(['group', 'period', 'coach', 'participants'])
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

        return Inertia::render('match-event/Index', [
            'match_events' => $match_events,
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
        $this->checkPermission('match-event-create');

        return Inertia::render('match-event/Create', [
            'groups' => $this->groups,
            'periods' => $this->periods,
            'coaches' => $this->coaches,
            'students' => $this->students,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkPermission('match-event-create');

        $request->validate([
            'group_code' => ['required', 'exists:group,code'],
            'period_id' => ['required', 'exists:period,id'],
            'coach_id' => ['required', 'exists:coach,id'],
            'opponent' => ['required', 'string', 'max:255'],
            'match_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time'   => ['required', 'date_format:H:i', 'after:start_time'],
            'group_score' => ['required', 'numeric'],
            'opponent_score' => ['required', 'numeric'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'participants' => ['nullable', 'array'],
            'participants.*.student_id' => ['nullable', 'exists:student,id'],
        ], [], $this->attributes);

        try {
            DB::beginTransaction();
            $match_event = MatchEvent::create([
                'group_code' => $request->group_code,
                'period_id' => $request->period_id,
                'coach_id' => $request->coach_id,
                'opponent' => strtoupper($request->opponent),
                'match_date' => $request->match_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'group_score' => $request->group_score,
                'opponent_score' => $request->opponent_score,
                'location' => $request->location,
                'description' => $request->description,
            ]);
            if ($request->participants) {
                foreach ($request->participants as $key => $value) {
                    if (!empty($value['student_id'])) {
                        $match_event->participants()->create([
                            'match_event_id' => $match_event->id,
                            'student_id' => $value['student_id'],
                            'attendance' => null,
                            'notes' => null,
                        ]);
                    }
                }
            }
            DB::commit();
            return redirect()->route('match-event.show', $match_event->id)->with('success', 'Pertandingan berhasil ditambahkan');
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
        $this->checkPermission('match-event-show');

        $match_event = MatchEvent::with(['group', 'period', 'coach', 'participants', 'participants.student'])->findOrFail($id);
        return Inertia::render('match-event/Show', [
            'match_event' => $match_event,
            'attendances' => $this->attendances,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->checkPermission('match-event-edit');
        $match_event = MatchEvent::with(['group', 'period', 'coach', 'participants', 'participants.student'])->findOrFail($id);
        return Inertia::render('match-event/Edit', [
            'groups' => $this->groups,
            'periods' => $this->periods,
            'coaches' => $this->coaches,
            'students' => $this->students,
            'match_event' => $match_event,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->checkPermission('match-event-edit');

        $request->validate([
            'group_code' => ['required', 'exists:group,code'],
            'period_id' => ['required', 'exists:period,id'],
            'coach_id' => ['required', 'exists:coach,id'],
            'opponent' => ['required', 'string', 'max:255'],
            'match_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time'   => ['required', 'date_format:H:i', 'after:start_time'],
            'group_score' => ['required', 'numeric'],
            'opponent_score' => ['required', 'numeric'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'participants' => ['nullable', 'array'],
            'participants.*.student_id' => ['nullable', 'exists:student,id'],
        ], [], $this->attributes);

        try {
            DB::beginTransaction();
            $match_event = MatchEvent::findOrFail($id);
            $match_event->update([
                'group_code' => $request->group_code,
                'period_id' => $request->period_id,
                'coach_id' => $request->coach_id,
                'opponent' => strtoupper($request->opponent),
                'match_date' => $request->match_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'group_score' => $request->group_score,
                'opponent_score' => $request->opponent_score,
                'location' => $request->location,
                'description' => $request->description,
            ]);
            // Sinkronisasi peserta
            $new_student_ids = collect($request->participants)
                ->pluck('student_id')
                ->filter() // buang null/kosong
                ->unique()
                ->values()
                ->all();
            $existing_student_ids = $match_event->participants()->pluck('student_id')->all();
            // Hapus peserta yang tidak lagi ada
            $match_event->participants()
                ->whereNotIn('student_id', $new_student_ids)
                ->delete();
            // Tambah peserta baru
            $toInsert = array_diff($new_student_ids, $existing_student_ids);
            foreach ($toInsert as $student_id) {
                $match_event->participants()->create([
                    'student_id' => $student_id,
                ]);
            }
            DB::commit();
            return redirect()->route('match-event.show', $match_event->id)->with('success', 'Pertandingan berhasil diubah');
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
        $this->checkPermission('match-event-delete');

        try {
            DB::beginTransaction();
            $match_event = MatchEvent::findOrFail($id);
            $match_event->delete();
            DB::commit();
            return redirect()->route('match-event.index')->with('success', 'Pertandingan berhasil dihapus');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Change the specified attendance.
     */
    public function attendance(Request $request, string $match_event_id)
    {
        $this->checkPermission('match-event-attendance');

        try {
            DB::beginTransaction();
            $match_event = MatchEvent::findOrFail($match_event_id);
            if ($request->attendances) {
                foreach ($request->attendances as $key => $value) {
                    $match_event->participants()->updateOrCreate([
                        'match_event_id' => $match_event_id,
                        'student_id' => $value['student_id'],
                    ], [
                        'match_event_id' => $match_event_id,
                        'student_id' => $value['student_id'],
                        'attendance' => $value['attendance'],
                        'notes' => $value['notes'],
                    ]);
                }
            }
            DB::commit();
            return redirect()->route('match-event.show', $match_event_id)->with('success', 'Kehadiran berhasil diperbaharui');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
