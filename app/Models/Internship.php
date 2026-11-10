<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Internship extends Model
{
    protected $table = 'internships';
    protected $primaryKey = 'internship_id';
    protected $fillable = ['company_name','specifications','application_deadline','start_date','end_date', 'work_mode','location','apply_link','hosting_type','status', 'posted_by', 'title', 'category', 'description', 'duration_weeks', 'required_skill_id', 'is_paid', 'stipend', 'required_percentage' ];
    protected $casts = [
        'is_paid' => 'boolean',
        'application_deadline' => 'date:Y-m-d',
        'start_date'           => 'date:Y-m-d',
        'end_date'             => 'date:Y-m-d',];

    public function applications()
        {
            return $this->hasMany(InternshipApplication::class, 'internship_id', 'internship_id');
        }

    public function required_skill()
    {
        return $this->belongsTo(Skill::class, 'required_skill_id', 'skill_id');
    }
}