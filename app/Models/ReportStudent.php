<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportStudent extends Model
{
    protected $table = 'report_student';

    protected $guarded = [];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

    public function period()
    {
        return $this->belongsTo(Period::class, 'period_id', 'id');
    }

    public function reportStages()
    {
        return $this->hasMany(ReportStudentStage::class, 'report_student_id', 'id');
    }
}
