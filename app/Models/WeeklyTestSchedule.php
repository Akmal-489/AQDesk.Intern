<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyTestSchedule extends Model
{
    protected $table = 'weekly_test_schedules';
    protected $primaryKey = 'schedule_id';
    protected $fillable = ['skill_id', 'title', 'description', 'test_date'];

    public function skill()
    {
        return $this->belongsTo(Skill::class, 'skill_id', 'skill_id');
    }
}