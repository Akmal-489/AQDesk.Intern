<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    // Roles a visitor may pick for themselves. admin and employer are NEVER included.
    private function selfRoles(): array
    {
        return Role::whereNotIn('role_name', ['admin', 'employer'])->pluck('role_name')->all();
    }

    // Feeds the "I am a..." dropdown on the employee form
    public function roles()
    {
        return collect($this->selfRoles())->map(fn ($r) => [
            'value' => $r,
            'label' => ucwords(str_replace('_', ' ', $r)),
        ])->values();
    }

    public function employee(Request $r)
    {
        $d = $r->validate([
            'name'           => 'required|string|max:100',
            'email'          => 'required|email|max:150|unique:users,email',
            'password'       => 'required|string|min:8|confirmed',
            'role_name'      => ['required', Rule::in($this->selfRoles())],
            'university'     => 'required|string|max:150',
            'field_of_study' => 'required|string|max:120',
            'phone'          => 'nullable|string|max:25',
            'city'           => 'nullable|string|max:80',
            'country'        => 'nullable|string|max:80',
            'terms'          => 'accepted',
        ], ['terms.accepted' => 'Please accept the terms to continue.']);

        $role = Role::where('role_name', $d['role_name'])->firstOrFail();

        $user = DB::transaction(function () use ($d, $role) {
            $u = User::create([
                'role_id'       => $role->role_id,
                'name'          => $d['name'],
                'email'         => $d['email'],
                'password'      => Hash::make($d['password']),
                'auth_provider' => 'email',
            ]);

            DB::table('employee_profiles')->insert([
                'user_id'        => $u->getKey(),
                'university'     => $d['university'],
                'field_of_study' => $d['field_of_study'],
                'phone'          => $d['phone']   ?? null,
                'city'           => $d['city']    ?? null,
                'country'        => $d['country'] ?? null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]);
            return $u;
        });

        return response()->json([
            'message' => 'Registration successful.',
            'user'    => $user->load('role'),
            'token'   => $user->createToken('aqdesk-token')->plainTextToken,
        ], 201);
    }

    public function employer(Request $r)
    {
        // friendly: add https:// if the person forgot it
        if ($r->filled('website') && !preg_match('~^https?://~i', $r->website)) {
            $r->merge(['website' => 'https://' . $r->website]);
        }

        $d = $r->validate([
            'name'            => 'required|string|max:100',
            'designation'     => 'required|string|max:80',
            'email'           => 'required|email|max:150|unique:users,email',
            'phone'           => 'required|string|max:25',
            'password'        => 'required|string|min:8|confirmed',
            'company_name'    => 'required|string|max:150',
            'website'         => 'required|url|max:200',
            'industry'        => 'required|string|max:80',
            'company_size'    => 'nullable|string|max:20',
            'city'            => 'required|string|max:80',
            'country'         => 'required|string|max:80',
            'address'         => 'nullable|string|max:255',
            'registration_no' => 'nullable|string|max:60',
            'description'     => 'required|string|min:20|max:1000',
            'terms'           => 'accepted',
        ], ['terms.accepted' => 'Please accept the terms to continue.']);

        $role = Role::where('role_name', 'employer')->first();
        if (!$role) {
            return response()->json(['message' => 'Employer accounts are not set up yet. Please contact the administrator.'], 500);
        }

        DB::transaction(function () use ($d, $role) {
            $u = User::create([
                'role_id'       => $role->role_id,
                'name'          => $d['name'],
                'email'         => $d['email'],
                'password'      => Hash::make($d['password']),
                'auth_provider' => 'email',
            ]);

            DB::table('employer_profiles')->insert([
                'user_id'             => $u->getKey(),
                'company_name'        => $d['company_name'],
                'contact_designation' => $d['designation'],
                'website'             => $d['website'],
                'industry'            => $d['industry'],
                'company_size'        => $d['company_size']    ?? null,
                'phone'               => $d['phone'],
                'city'                => $d['city'],
                'country'             => $d['country'],
                'address'             => $d['address']         ?? null,
                'registration_no'     => $d['registration_no'] ?? null,
                'description'         => $d['description'],
                'approval_status'     => 'pending',
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);
        });

        // No token: the company must be approved by an admin first
        return response()->json([
            'message' => 'Application submitted. An admin will verify your company.',
            'approval_status' => 'pending',
        ], 201);
    }
}