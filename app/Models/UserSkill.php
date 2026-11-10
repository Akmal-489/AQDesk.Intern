<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSkill extends Model
{
    protected $table = 'user_skills';
    protected $primaryKey = 'user_skill_id';
    public $timestamps = false;
    protected $fillable = ['user_id', 'skill_id', 'percentage', 'updated_at'];

    public function skill()
    {
        return $this->belongsTo(Skill::class, 'skill_id', 'skill_id');
    }
}