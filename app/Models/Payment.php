<?php

namespace App\Models;

use App\Enums\StatusPayment;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payment';

    protected $guarded = [];

    protected $casts = [
        'status' => StatusPayment::class,
    ];

    public function billing()
    {
        return $this->belongsTo(Billing::class, 'billing_id', 'id');
    }
}
