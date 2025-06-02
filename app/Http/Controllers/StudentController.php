<?php

namespace App\Http\Controllers;

use App\Enums\StatusBilling;
use App\Models\BankAccount;
use App\Models\Billing;
use App\Models\Group;
use App\Models\Period;
use App\Models\Position;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\User;
use App\Traits\HasPermissionCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class StudentController extends Controller
{
    use HasPermissionCheck;

    protected $periods = [];
    protected $groups = [];
    protected $positions = [];
    protected $bank_accounts = [];

    protected $attributes = [
        'name' => 'Nama',
        'place_of_birth' => 'Tempat Lahir',
        'date_of_birth' => 'Tanggal Lahir',
        'gender' => 'Jenis Kelamin',
        'address' => 'Alamat',
        'phone' => 'Telepon',
        'national_id_number' => 'No. Identitas',
        'photo' => 'Foto',
        'dominant_foot' => 'Kaki Dominan',
        'height_cm' => 'Tinggi Badan',
        'weight_kg' => 'Berat Badan',
        'email' => 'Email',
        'password' => 'Password',
        // Enrollment
        'period_id' => 'Periode',
        'group_code' => 'Grup',
        'position_code' => 'Posisi',
        'alternative_position_code' => 'Alternatif Posisi',
        'jersey_number' => 'Nomor Punggung',
        'join_date' => 'Tanggal Bergabung',
    ];

    public function __construct()
    {
        $this->periods = Period::where('is_active', true)->get();
        $this->groups = Group::where('is_active', true)->get();
        $this->positions = Position::all();
        $this->bank_accounts = BankAccount::all();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->checkPermission('student-index');

        $search = $request->search;
        $per_page = $request->per_page ?? "5";
        $filter = $request->filter ?? 'desc';

        $students = Student::query()->with(['user', 'enrollment'])
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

        $students->getCollection()->transform(function ($student) {
            $student->photo_url = $student->photo ? asset('storage/' . $student->photo) : null;
            return $student;
        });

        return Inertia::render('student/Index', [
            'students' => $students,
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
        $this->checkPermission('student-create');

        return Inertia::render('student/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->checkPermission('student-create');

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'place_of_birth' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:MALE,FEMALE'],
            'address' => ['required', 'string'],
            'phone' => ['required', 'string', 'max:255'],
            'national_id_number' => ['required', 'string', 'max:255'],
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'dominant_foot' => ['required', 'string', 'in:RIGHT,LEFT,BOTH'],
            'height_cm' => ['required', 'numeric', 'min:100', 'max:300'],
            'weight_kg' => ['required', 'numeric', 'min:20', 'max:300'],
            'email' => ['required', 'email', 'max:255', 'unique:user,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [], $this->attributes);

        try {
            DB::beginTransaction();
            $user = User::create([
                'name' => strtoupper($request->name),
                'email' => strtolower($request->email),
                'password' => bcrypt($request->password),
            ]);
            $user->syncRoles('Student');
            $student = Student::create([
                'name' => strtoupper($request->name),
                'place_of_birth' => strtoupper($request->place_of_birth),
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => strtoupper($request->address),
                'phone' => $request->phone,
                'national_id_number' => $request->national_id_number,
                'photo' => null,
                'dominant_foot' => $request->dominant_foot,
                'height_cm' => $request->height_cm,
                'weight_kg' => $request->weight_kg,
                'user_id' => $user->id,
            ]);
            if ($request->hasFile('photo')) {
                $path = Storage::disk('public')->put('student', $request->photo);
                $student->update([
                    'photo' => $path,
                ]);
            }
            DB::commit();
            return redirect()->route('student.show', $student->id)->with('success', 'Siswa berhasil ditambahkan');
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
        $this->checkPermission('student-show');

        $student = Student::with(['user'])->findOrFail($id);
        $student->photo_url = asset('storage/' . $student->photo);
        $enrollments = StudentEnrollment::with(['student', 'period', 'group', 'position', 'alternativePosition'])->where('student_id', $id)->orderBy('created_at', 'desc')->get();
        $billings = Billing::with(['student', 'period', 'billingType', 'payments', 'payment'])->where('student_id', $id)->orderBy('created_at', 'desc')->get();

        return Inertia::render('student/Show', [
            'student' => $student,
            'enrollments' => $enrollments,
            'billings' => $billings,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->checkPermission('student-edit');

        $student = Student::with(['enrollments', 'user'])->findOrFail($id);
        $student->photo_url = asset('storage/' . $student->photo);
        return Inertia::render('student/Edit', [
            'student' => $student,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->checkPermission('student-edit');

        $student = Student::findOrFail($id);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'place_of_birth' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:MALE,FEMALE'],
            'address' => ['required', 'string'],
            'phone' => ['required', 'string', 'max:255'],
            'national_id_number' => ['required', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'dominant_foot' => ['required', 'string', 'in:RIGHT,LEFT,BOTH'],
            'height_cm' => ['required', 'numeric', 'min:100', 'max:300'],
            'weight_kg' => ['required', 'numeric', 'min:20', 'max:300'],
            'email' => ['required', 'email', 'max:255', 'unique:user,email,' . $student->user->id . ',id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [], $this->attributes);

        try {
            DB::beginTransaction();
            $student->update([
                'name' => strtoupper($request->name),
                'place_of_birth' => strtoupper($request->place_of_birth),
                'date_of_birth' => $request->date_of_birth,
                'gender' => $request->gender,
                'address' => strtoupper($request->address),
                'phone' => $request->phone,
                'national_id_number' => $request->national_id_number,
                'dominant_foot' => $request->dominant_foot,
                'height_cm' => $request->height_cm,
                'weight_kg' => $request->weight_kg,
            ]);
            if ($request->hasFile('photo')) {
                $path = Storage::disk('public')->put('student', $request->photo);
                $student->update([
                    'photo' => $path,
                ]);
            }
            $student->user()->update([
                'name' => strtoupper($request->name),
                'email' => strtolower($request->email),
                'password' => $request->password ? bcrypt($request->password) : $student->user->password,
            ]);
            DB::commit();
            return redirect()->route('student.show', $student->id)->with('success', 'Siswa berhasil diubah');
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
        $this->checkPermission('student-delete');
        try {
            DB::beginTransaction();
            $student = Student::findOrFail($id);
            $user = User::findOrFail($student->user_id);
            if ($student->photo && Storage::disk('public')->exists($student->photo)) {
                Storage::disk('public')->delete($student->photo);
            }
            $user->delete();
            $student->delete();
            DB::commit();
            return redirect()->route('student.index')->with('success', 'Siswa berhasil dihapus');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Show the form for creating a new enrollment.
     */
    public function enrollmentCreate(string $id)
    {
        $this->checkPermission('student-enrollment-create');

        $student = Student::findOrFail($id);

        return Inertia::render('student/enrollment/Create', [
            'periods' => $this->periods,
            'groups' => $this->groups,
            'positions' => $this->positions,
            'student' => $student,
        ]);
    }

    /**
     * Store a newly created enrollment in storage.
     */
    public function enrollmentStore(Request $request, string $id)
    {
        $this->checkPermission('student-enrollment-create');

        $student = Student::findOrFail($id);

        $request->validate([
            'period_id' => ['required', 'string', 'exists:period,id'],
            'group_code' => ['required', 'string', 'exists:group,code'],
            'position_code' => ['required', 'string', 'exists:position,code'],
            'alternative_position_code' => ['required', 'string', 'exists:position,code'],
            'jersey_number' => ['required', 'numeric', 'min:1', 'max:100'],
            'join_date' => ['required', 'date'],
        ], [], $this->attributes);

        try {
            DB::beginTransaction();
            $student->enrollment()->create([
                'period_id' => $request->period_id,
                'group_code' => $request->group_code,
                'position_code' => $request->position_code,
                'alternative_position_code' => $request->alternative_position_code,
                'jersey_number' => $request->jersey_number,
                'join_date' => $request->join_date,
                'is_active' => false,
            ]);
            $student->billings()->create([
                'period_id' => $request->period_id,
                'billing_type_code' => 'REGISTRATION',
                'amount' => 1000000,
                'due_date' => now()->addDays(7),
                'status' => StatusBilling::UNPAID,
            ]);
            DB::commit();
            return redirect()->route('student.show', $student->id)->with('success', 'Team berhasil ditambahkan');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Show the form for creating a new payment.
     */
    public function paymentCreate(string $id)
    {
        $this->checkPermission('student-payment-create');

        $billing = Billing::with(['student', 'period', 'billingType'])->findOrFail($id);

        return Inertia::render('student/payment/Create', [
            'billing' => $billing,
            'bank_accounts' => $this->bank_accounts,
        ]);
    }

    /**
     * Store a newly created payment in storage.
     */
    public function paymentStore(Request $request, string $id)
    {
        dd($request->all());
        $this->checkPermission('student-payment-create');

        $billing = Billing::with(['student', 'period', 'billingType'])->findOrFail($id);

        $request->validate([
            'amount' => ['required', 'numeric'],
        ], [], $this->attributes);

        try {
            DB::beginTransaction();
            //
            DB::commit();
            return redirect()->route('student.show', $billing->student_id)->with('success', 'Tagihan berhasil dibayarkan');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
