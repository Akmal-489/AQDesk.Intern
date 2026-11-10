<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InternshipApplication extends Model
{
    protected $table = 'internship_applications';
    protected $primaryKey = 'application_id';
    public $timestamps = false;
    protected $fillable = ['current_stage','completion_status','completed_at','internship_id', 'user_id', 'status', 'applied_at'];
    protected $casts = ['completed_at' => 'datetime'];

    public function applicant(){ return $this->belongsTo(User::class, 'user_id'); }
    public function internship()  { return $this->belongsTo(Internship::class, 'internship_id'); }
    public function stages()      { return $this->hasMany(InternshipStage::class, 'application_id')->orderBy('stage_no'); }
    public function certificate() { return $this->hasOne(Certificate::class, 'application_id'); }
}