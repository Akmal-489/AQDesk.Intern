<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\SkillTestAttempt;
use App\Models\TalentRefferal;
use App\Models\User;
use App\Models\UserSkill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class TalentController extends Controller
{
    // The Talent Program criteria. Change these two numbers to change the rule.
    const MIN_AVERAGE = 80;   // average skill percentage
    const MIN_TESTS = 3;      // minimum number of tests taken

    private function stats($userId)
    {
        $skills = UserSkill::with('skill')->where('user_id', $userId)->get();
        $average = $skills->count() ? (int) round($skills->avg('percentage')) : 0;
        $attempts = SkillTestAttempt::where('user_id', $userId)->count();
        $qualifies = $attempts >= self::MIN_TESTS && $average >= self::MIN_AVERAGE;

        return ['skills' => $skills, 'average' => $average, 'attempts' => $attempts, 'qualifies' => $qualifies];
    }

    // For the internee's own dashboard
    public function myStatus(Request $request)
    {
        $user = $request->user();
        $s = $this->stats($user->id);

        return response()->json([
            'average' => $s['average'],
            'attempts' => $s['attempts'],
            'qualifies' => $s['qualifies'],
            'consent' => (bool) $user->talent_consent,
            'min_average' => self::MIN_AVERAGE,
            'min_tests' => self::MIN_TESTS,
            'referrals' => TalentRefferal::with('company')->where('user_id', $user->id)->get(),
        ]);
    }

    public function setConsent(Request $request)
    {
        $validator = Validator::make($request->all(), ['consent' => 'required|boolean']);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $user->talent_consent = $request->boolean('consent');
        $user->save();

        return response()->json(['message' => 'Preference saved.', 'consent' => (bool) $user->talent_consent]);
    }

    // ---------- Admin only ----------

    public function candidates()
    {
        $result = [];

        foreach (User::where('talent_consent', 1)->get() as $u) {
            $s = $this->stats($u->id);
            if (!$s['qualifies']) {
                continue;
            }
            $result[] = [
                'user_id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'bio' => $u->bio,
                'average' => $s['average'],
                'attempts' => $s['attempts'],
                'skills' => $s['skills']->map(fn ($x) => [
                    'skill' => optional($x->skill)->skill_name,
                    'percentage' => $x->percentage,
                ])->values(),
            ];
        }

        usort($result, fn ($a, $b) => $b['average'] <=> $a['average']);

        return response()->json($result);
    }

    public function companies()
    {
        return response()->json(Company::orderBy('name')->get());
    }

    public function storeCompany(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150',
            'contact_email' => 'required|email|max:150',
            'industry' => 'nullable|string|max:100',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $company = Company::create($request->only(['name', 'contact_email', 'industry']));

        return response()->json(['message' => 'Company added.', 'company' => $company], 201);
    }

    public function refer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'company_id' => 'required|exists:companies,company_id',
            'note' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $candidate = User::findOrFail($request->user_id);
        $stats = $this->stats($candidate->id);

        // Re-check on the server, never trust the browser
        if (!$candidate->talent_consent || !$stats['qualifies']) {
            return response()->json(['message' => 'This candidate has not consented or does not meet the criteria.'], 422);
        }

        if (TalentRefferal::where('user_id', $candidate->id)->where('company_id', $request->company_id)->exists()) {
            return response()->json(['message' => 'This candidate was already referred to that company.'], 422);
        }

        $company = Company::findOrFail($request->company_id);

        $referral = TalentRefferal::create([
            'user_id' => $candidate->id,
            'company_id' => $company->company_id,
            'referred_by' => $request->user()->id,
            'note' => $request->note,
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        $skillLines = $stats['skills']
            ->map(fn ($s) => '- ' . optional($s->skill)->skill_name . ': ' . $s->percentage . '%')
            ->implode("\n");

        $body = "AQDesk recommends a high-performing candidate.\n\n"
            . "Name: {$candidate->name}\n"
            . "Email: {$candidate->email}\n"
            . 'Bio: ' . ($candidate->bio ?: 'N/A') . "\n"
            . "Average score: {$stats['average']}% across {$stats['attempts']} tests\n\n"
            . "Skills:\n{$skillLines}\n"
            . ($request->note ? "\nNote from AQDesk: {$request->note}\n" : '');

        try {
            Mail::raw($body, function ($m) use ($company) {
                $m->to($company->contact_email)->subject('AQDesk Talent Recommendation');
            });
        } catch (\Throwable $e) {
            Log::warning('Referral email failed: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Profile sent to ' . $company->name . '.', 'referral' => $referral], 201);
    }
}