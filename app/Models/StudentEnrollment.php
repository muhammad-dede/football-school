<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentEnrollment extends Model
{
    protected $table = 'student_enrollment';

    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    public function period()
    {
        return $this->belongsTo(Period::class, 'period_id', 'id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_code', 'code');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_code', 'code');
    }

    public function alternativePosition()
    {
        return $this->belongsTo(Position::class, 'alternative_position_code', 'code');
    }
}
