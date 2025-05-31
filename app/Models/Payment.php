<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payment';

    protected $guarded = [];

    public function billing()
    {
        return $this->belongsTo(Billing::class, 'billing_id', 'id');
    }
}
