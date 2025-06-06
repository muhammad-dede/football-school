<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'student';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function currentProgram()
    {
        return $this->hasOne(StudentProgram::class, 'student_id', 'id')
            ->where('is_active', true)
            ->latest();
    }

    public function programs()
    {
        return $this->hasMany(StudentProgram::class, 'student_id', 'id');
    }

    public function billings()
    {
        return $this->hasMany(Billing::class, 'student_id', 'id');
    }

    public function trainingAttendances()
    {
        return $this->hasMany(TrainingAttendance::class, 'student_id', 'id');
    }

    public function matchEventParticipants()
    {
        return $this->hasMany(MatchEventParticipant::class, 'student_id', 'id');
    }

    public function reports()
    {
        return $this->hasMany(ReportStudent::class, 'student_id', 'id');
    }
}
