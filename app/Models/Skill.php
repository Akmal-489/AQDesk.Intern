<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $table = 'skills';
    protected $primaryKey = 'skill_id';
    public $timestamps = false;
    protected $fillable = ['skill_name'];
}