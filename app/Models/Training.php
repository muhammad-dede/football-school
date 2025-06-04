<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    protected $table = 'training';

    protected $guarded = [];

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_code', 'code');
    }

    public function period()
    {
        return $this->belongsTo(Period::class, 'period_id', 'id');
    }

    public function coach()
    {
        return $this->belongsTo(Coach::class, 'coach_id', 'id');
    }

    public function attendances()
    {
        return $this->hasMany(TrainingAttendance::class, 'training_id', 'id');
    }
}
