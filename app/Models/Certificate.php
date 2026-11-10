<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $table = 'certificates';
    protected $primaryKey = 'certificate_id';

    protected $fillable = [
        'certificate_no','application_id','user_id','internship_id','candidate_name',
        'internship_title','company_name','completion_date','issued_by','issued_at','status',
    ];

    protected $casts = [
        'completion_date' => 'date:Y-m-d',
        'issued_at'       => 'datetime:Y-m-d',
    ];
}