<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportStudentStage extends Model
{
    protected $table = 'report_student_stage';

    protected $guarded = [];

    public function report()
    {
        return $this->belongsTo(ReportStudent::class, 'report_student_id', 'id');
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class, 'stage_code', 'code');
    }
}
