<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'=>'required|string|max:100',
            'email' => ['required', 'email', 'max:150', 'unique:users,email', function ($attribute, $value, $fail) {
                $blockedDomains = ['example.com', 'test.com', 'mailinator.com', 'tempmail.com', 'fake.com'];
                $domain = strtolower(substr(strrchr($value, "@"), 1));
            if (in_array($domain, $blockedDomains)) {
                $fail('Please register with a real email address, or use Google/Facebook login.');
                }
            }],
            'password'=>'required|string|min:8|confirmed',
            'role_name'=>'nullable|string|exists:roles,role_name',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors'=>$validator->errors()],422);
        }
        $role = Role::where('role_name', $request->input('role_name', 'Student'))->first();

        $user = User::create([
            'role_id'=>$role->role_id,
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            'auth_provider'=>'email',
        ]);
        $token = $user->createToken('aqdesk-token')->plainTextToken;

        return response()->json([
            'message'=>'Registration successful.',
            'user'=>$user->load('role'),
            'token'=>$token,
        ], 201);
    }
    public function Login(Request $request)
        {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid email or password.'], 401);
        }

        $token = $user->createToken('aqdesk-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',
            'user' => $user->load('role'),
            'token' => $token,
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $user = User::where('email', $request->email)->first();

    if ($user) {
        $token = Str::random(64);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => Hash::make($token), 'created_at' => now()]
        );

        $link = url('/reset-password') . '?token=' . $token . '&email=' . urlencode($user->email);

        Mail::raw("Click this link to reset your AQDesk password:\n\n$link\n\nThis link expires in 60 minutes.", function ($m) use ($user) {
            $m->to($user->email)->subject('AQDesk Password Reset');
        });

        // Development helper: puts a clean copy of the link in laravel.log. Remove when using real email.
        Log::info('Password reset link for ' . $user->email . ': ' . $link);
    }

    // Same reply whether or not the email exists, so strangers can't test which emails are registered
    return response()->json(['message' => 'If that email is registered, a reset link has been sent.']);
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

    if (!$record
        || !Hash::check($request->token, $record->token)
        || Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
        return response()->json(['message' => 'This reset link is invalid or has expired.'], 422);
    }

    $user = User::where('email', $request->email)->first();
    if (!$user) {
        return response()->json(['message' => 'This reset link is invalid or has expired.'], 422);
    }

    $user->password = Hash::make($request->password);
    $user->save();

    $user->tokens()->delete();                                           // log out every old session
    DB::table('password_reset_tokens')->where('email', $user->email)->delete();   // the link works only once

    return response()->json(['message' => 'Password reset successful. You can now log in.']);
}

    public function profile(Request $request)
    {
        return response()->json($request->user()->load('role'));
    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:100',
            'bio' => 'sometimes|nullable|string',
        ]);

        if($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $user->fill($request->only(['name', 'bio']));
        $user->save();

        return response()->json([
            'message' => 'Profile updated.',
            'user' => $user->fresh('role'),
        ]);
    }
}
