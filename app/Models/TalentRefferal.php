<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TalentRefferal extends Model
{
    protected $table = 'talent_referrals';
    protected $primaryKey = 'referral_id';
    public $timestamps = false;
    protected $fillable = ['user_id', 'company_id', 'referred_by', 'note', 'status', 'sent_at'];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'company_id');
    }
}