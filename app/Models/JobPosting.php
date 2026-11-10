<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPosting extends Model
{
    protected $table = 'job_postings';
    protected $primaryKey = 'job_id';

    protected $fillable = ['posted_by', 'title', 'type', 'description', 'deadline', 'registration_link', 'advertisement_pdf'];

    public function poster(){
        return $this->belongsTo(User::class, 'posted_by', 'id');
    }

    public function applications(){
        return $this->hasMany(JobApplication::class, 'job_id', 'job_id');
    }
}