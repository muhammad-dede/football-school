<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingAttendance extends Model
{
    protected $table = 'training_attendance';

    protected $guarded = [];

    public function training()
    {
        return $this->belongsTo(Training::class, 'training_id', 'id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }
}
