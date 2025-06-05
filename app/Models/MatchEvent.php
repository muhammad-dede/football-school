<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MatchEvent extends Model
{
    protected $table = 'match_event';

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

    public function participants()
    {
        return $this->hasMany(MatchEventParticipant::class, 'match_event_id', 'id');
    }
}
