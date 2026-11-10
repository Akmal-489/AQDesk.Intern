<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternshipStage extends Model
{
    protected $table = 'internship_stages';
    protected $primaryKey = 'stage_id';

    protected $fillable = [
        'application_id','stage_no','stage_type','title','instructions','due_date','status',
        'submission_text','submission_path','submitted_at','score','feedback','reviewed_at',
        'interview_at','interview_link','interview_result',
    ];

    protected $casts = [
        'stage_no'     => 'integer',
        'due_date'     => 'date:Y-m-d',
        'submitted_at' => 'datetime',
        'reviewed_at'  => 'datetime',
        'interview_at' => 'datetime:Y-m-d\TH:i',
    ];

    protected $hidden  = ['submission_path'];   // never expose the private file path
    protected $appends = ['has_file'];

    public function getHasFileAttribute()
    {
        return !empty($this->submission_path);
    }

    public function application()
    {
        return $this->belongsTo(InternshipApplication::class, 'application_id');
    }
}