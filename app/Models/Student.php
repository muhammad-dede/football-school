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

    public function enrollments()
    {
        return $this->hasMany(StudentEnrollment::class, 'student_id', 'id');
    }

    public function enrollment()
    {
        return $this->hasOne(StudentEnrollment::class, 'student_id', 'id')->latest();
    }

    public function enrollmentActive()
    {
        return $this->hasOne(StudentEnrollment::class, 'student_id', 'id')
            ->where('is_active', true)
            ->latest();
    }

    public function billings()
    {
        return $this->hasMany(Billing::class, 'student_id', 'id');
    }
}
