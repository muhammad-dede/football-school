<?php

namespace App\Http\Controllers;

use App\Enums\StatusBilling;
use App\Enums\StatusPayment;
use App\Models\BankAccount;
use App\Models\Billing;
use App\Models\Payment;
use App\Models\StudentProgram;
use App\Traits\HasPermissionCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class BillingController extends Controller
{
    use HasPermissionCheck;

    protected $bank_accounts = [];

    protected $attributes = [
        'amount' => 'Jumlah Pembayaran',
        'payment_date' => 'Tanggal Pembayaran',
        'method' => 'Metode Pembayaran',
        'receiver_id' => 'Bank Tujuan',
        'sender_bank_name' => 'Nama Bank Pengirim',
        'sender_account_number' => 'No Rekening Pengirim',
        'sender_account_holder_name' => 'Atas Nama Pengirim',
        'proof_file' => 'Bukti Transfer',
        'reference_number' => 'No Referensi',
        'notes' => 'Catatan',
    ];

    public function __construct()
    {
        $this->bank_accounts = BankAccount::all();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->checkPermission('billing-index');

        $search = $request->search;
        $per_page = $request->per_page ?? "5";
        $filter = $request->filter ?? 'desc';

        $billings = Billing::query()->with(['student', 'period', 'billingType', 'payment'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('student', function ($sub) use ($search) {
                        $sub->where('name', 'like', '%' . $search . '%');
                    })->orWhereHas('period', function ($sub) use ($search) {
                        $sub->where('name', 'like', '%' . $search . '%');
                    });
                });
            })
            ->when($filter, function ($query) use ($filter) {
                $query->orderBy('id', $filter);
            })
            ->paginate($per_page)
            ->withQueryString();

        return Inertia::render('billing/Index', [
            'billings' => $billings,
            'search_term' => $search,
            'per_page_term' => $per_page,
            'filter_term' => $filter,
        ]);
    }

    /**
     * Show the form for creating a new payment.
     */
    public function paymentCreate(string $billing_id)
    {
        $this->checkPermission('billing-payment-create');

        $billing = Billing::with(['student', 'period', 'billingType'])->findOrFail($billing_id);

        return Inertia::render('billing/payment/Create', [
            'billing' => $billing,
            'bank_accounts' => $this->bank_accounts,
        ]);
    }

    /**
     * Store a newly created payment in storage.
     */
    public function paymentStore(Request $request, string $billing_id)
    {
        $this->checkPermission('billing-payment-create');

        $billing = Billing::with(['student', 'period', 'billingType'])->findOrFail($billing_id);

        $request->validate([
            'amount' => ['required', 'numeric'],
            'payment_date' => ['required', 'date'],
            'method' => ['required', 'string', 'in:CASH,TRANSFER'],
            'receiver_id' => [Rule::requiredIf($request->method === 'TRANSFER'), 'nullable', 'exists:bank_account,id'],
            'sender_bank_name' => [Rule::requiredIf($request->method === 'TRANSFER'), 'nullable', 'string', 'max:255'],
            'sender_account_number' => [Rule::requiredIf($request->method === 'TRANSFER'), 'nullable', 'string', 'max:255'],
            'sender_account_holder_name' => [Rule::requiredIf($request->method === 'TRANSFER'), 'nullable', 'string', 'max:255'],
            'proof_file' => [Rule::requiredIf($request->method === 'TRANSFER'), 'nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ], [], $this->attributes);

        try {
            DB::beginTransaction();
            $payment = Payment::create([
                'billing_id' => $billing->id,
                'amount' => $billing->amount,
                'payment_date' => $request->payment_date,
                'method' => $request->method,
                'notes' => $request->notes,
                'status' => StatusPayment::PAID,
            ]);
            if ($request->method === 'TRANSFER') {
                $bank_account = BankAccount::findOrFail($request->receiver_id);
                $payment->update([
                    'receiver_bank_name' => $bank_account->bank_name,
                    'receiver_account_number' => $bank_account->account_number,
                    'receiver_account_holder_name' => $bank_account->account_holder_name,
                    'sender_bank_name' => $request->sender_bank_name,
                    'sender_account_number' => $request->sender_account_number,
                    'sender_account_holder_name' => $request->sender_account_holder_name,
                    'reference_number' => $request->reference_number,
                ]);
                if ($request->hasFile('proof_file')) {
                    $path = Storage::disk('public')->put('payment', $request->proof_file);
                    $payment->update([
                        'proof_file' => $path,
                    ]);
                }
            }
            $billing->update([
                'status' => StatusBilling::PAID,
            ]);
            StudentProgram::where('student_id', $billing->student_id)->where('period_id', $billing->period_id)->update([
                'is_active' => true,
            ]);
            DB::commit();
            return redirect()->route('billing.index')->with('success', 'Tagihan berhasil dibayarkan');
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
