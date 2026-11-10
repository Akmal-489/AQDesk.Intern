<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkillTestAttempt extends Model
{
    protected $table = 'skill_test_attempts';
    protected $primaryKey = 'attempt_id';
    public $timestamps = false;
    protected $fillable = ['user_id', 'skill_id', 'score', 'attempted_at'];
}