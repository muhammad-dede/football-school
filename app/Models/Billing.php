<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Billing extends Model
{
    protected $table = 'billing';

    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    public function period()
    {
        return $this->belongsTo(Period::class, 'period_id', 'id');
    }

    public function billingType()
    {
        return $this->belongsTo(BillingType::class, 'billing_type_code', 'code');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'billing_id', 'id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'billing_id', 'id');
    }
}
