<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Question extends Model
{
    protected $primaryKey = 'question_id';
    protected $fillable = ['user_id', 'title', 'body', 'category', 'views'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class, 'question_id', 'question_id');
    }
}