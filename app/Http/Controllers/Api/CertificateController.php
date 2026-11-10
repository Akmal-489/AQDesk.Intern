<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Internship;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function mine(Request $r)
    {
        return Certificate::where('user_id', $r->user()->id)
            ->where('status', 'valid')->orderByDesc('issued_at')->get();
    }

    public function show(Request $r, $number)
    {
        $c = Certificate::where('certificate_no', $number)->firstOrFail();
        $u = $r->user();

        $isProvider = Internship::where('internship_id', $c->internship_id)
                        ->where('posted_by', $u->id)->exists();
        if (!($c->user_id == $u->id || $isProvider || $u->role_id == 6)) abort(404);
        return $c;
    }

    public function verify($number)
    {
        $c = Certificate::where('certificate_no', strtoupper(trim($number)))->first();
        if (!$c) return response()->json(['valid' => false], 404);

        return [
            'valid'            => $c->status === 'valid',
            'certificate_no'   => $c->certificate_no,
            'candidate_name'   => $c->candidate_name,
            'internship_title' => $c->internship_title,
            'company_name'     => $c->company_name,
            'completion_date'  => $c->completion_date->toDateString(),
            'status'           => $c->status,
        ];
    }
}