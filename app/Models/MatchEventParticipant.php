<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchEventParticipant extends Model
{
    protected $table = 'match_event_participant';

    protected $guarded = [];

    public function matchEvent()
    {
        return $this->belongsTo(MatchEvent::class, 'match_event_id', 'id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }
}
