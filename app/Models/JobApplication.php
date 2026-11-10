<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $primaryKey = 'application_id';
    public $timestamps = false;
    
    protected $fillable = ['job_id', 'applicant_id', 'resume_note', 'status'];

    public function job()
    {
        return $this->belongsTo(JobPosting::class, 'job_id', 'job_id');
    }

    public function applicant()
    {
        return $this->belongsTo(User::class, 'applicant_id', 'id');
    }
}