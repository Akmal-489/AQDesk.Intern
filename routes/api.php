<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\Api\InternshipController;
use App\Http\Controllers\Api\ScheduleController;
use App\Http\Controllers\Api\TalentController;
use App\Http\Controllers\Api\ProviderController;
use App\Http\Controllers\Api\InternController;
use App\Http\Controllers\Api\CertificateController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\EmployerController;
use App\Http\Controllers\Api\AdminController;

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/overview', [AdminController::class, 'overview']);

    Route::get('/employers',                      [AdminController::class, 'employers']);
    Route::post('/employers/{userId}/approve',    [AdminController::class, 'approveEmployer']);
    Route::post('/employers/{userId}/reject',     [AdminController::class, 'rejectEmployer']);

    Route::get('/programs',                       [AdminController::class, 'programs']);
    Route::get('/programs/{id}',                  [AdminController::class, 'program']);
    Route::post('/programs/{id}/start',           [AdminController::class, 'startProgram']);
    Route::post('/programs/{id}/complete',        [AdminController::class, 'completeProgram']);
    Route::post('/programs/{id}/unsuccessful',    [AdminController::class, 'failProgram']);
    Route::post('/programs/{id}/certificate',     [AdminController::class, 'issueCertificate']);

    Route::put('/stages/{id}',                    [AdminController::class, 'updateStage']);
    Route::post('/stages/{id}/review',            [AdminController::class, 'reviewStage']);
    Route::post('/stages/{id}/reopen',            [AdminController::class, 'reopenStage']);

    Route::get('/certificates',                   [AdminController::class, 'certificates']);
    Route::post('/certificates/{id}/status',      [AdminController::class, 'setCertificateStatus']);

    Route::get('/internships',                    [AdminController::class, 'internships']);
    Route::post('/internships/{id}/status',       [AdminController::class, 'setInternshipStatus']);
    Route::delete('/internships/{id}',            [AdminController::class, 'deleteInternship']);

    Route::get('/users',                          [AdminController::class, 'users']);
    Route::post('/users/{id}/status',             [AdminController::class, 'setUserStatus']);
});

Route::middleware(['auth:sanctum', 'role:employer'])->prefix('employer')->group(function () {
    Route::get('/me',                          [EmployerController::class, 'me']);
    Route::get('/internships',                 [EmployerController::class, 'index']);
    Route::post('/internships',                [EmployerController::class, 'store']);
    Route::put('/internships/{id}',            [EmployerController::class, 'update']);
    Route::post('/internships/{id}/status',    [EmployerController::class, 'setStatus']);
    Route::get('/internships/{id}/applicants', [EmployerController::class, 'applicants']);
    Route::post('/applications/{id}/status',   [EmployerController::class, 'applicantStatus']);
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/questions', [QuestionController::class, 'store']);
Route::get('/questions', [QuestionController::class, 'index']);
Route::middleware('auth:sanctum')->post('/questions/{question_id}/answers', [QuestionController::class, 'storeAnswer']);
Route::get('/jobs', [JobController::class, 'index']);
Route::middleware('auth:sanctum')->post('/jobs', [JobController::class, 'store']);
Route::middleware('auth:sanctum')->post('/jobs/{job_id}/apply',[JobController::class, 'apply']);
Route::get('/skills', [SkillController::class, 'index']);
Route::middleware('auth:sanctum')->get('/my-skills', [SkillController::class, 'mySkills']);
Route::middleware('auth:sanctum')->post('/skills/{skill_id}/test', [SkillController::class, 'submitTest']);
Route::get('/internships', [InternshipController::class, 'index']);
Route::middleware('auth:sanctum')->post('/internships', [InternshipController::class, 'store']);
Route::middleware('auth:sanctum')->post('/internships/{internship_id}/apply-test', [InternshipController::class, 'applyForTest']);
Route::middleware('auth:sanctum')->post('/internships/{internship_id}/interview', [InternshipController::class, 'requestInterview']);
Route::middleware('auth:sanctum')->get('/my-applications', [InternshipController::class, 'myApplications']);
Route::get('/register/roles', [RegisterController::class, 'roles']);
Route::post('/register/employee', [RegisterController::class, 'employee'])->middleware('throttle:10,1');
Route::post('/register/employer', [RegisterController::class, 'employer'])->middleware('throttle:10,1');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->get('/profile', [AuthController::class, 'profile']);
Route::middleware('auth:sanctum')->put('/profile', [AuthController::class, 'updateProfile']);

Route::middleware(['auth:sanctum', 'role:admin'])->get('/admin/test', function () {
    return response()->json(['message' => 'You are an admin! Access granted.']);
});

Route::middleware('throttle:5,1')->post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::get('/test-schedules', [ScheduleController::class, 'index']);
Route::middleware(['auth:sanctum', 'role:admin,instructor'])->post('/test-schedules', [ScheduleController::class, 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/talent/status', [TalentController::class, 'myStatus']);
    Route::post('/talent/consent', [TalentController::class, 'setConsent']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/talent/candidates', [TalentController::class, 'candidates']);
    Route::get('/companies', [TalentController::class, 'companies']);
    Route::post('/companies', [TalentController::class, 'storeCompany']);
    Route::post('/talent/referrals', [TalentController::class, 'refer']);
});

// public verification (must come BEFORE /certificates/{number})
Route::get('/certificates/verify/{number}', [CertificateController::class, 'verify'])
    ->middleware('throttle:30,1');

Route::middleware('auth:sanctum')->group(function () {
    // interns
    Route::post('/internships/{internship}/apply-direct', [InternController::class, 'apply']);
    Route::get('/my-workflows',           [InternController::class, 'mine']);
    Route::post('/stages/{stage}/submit', [InternController::class, 'submit']);
    Route::get('/stages/{stage}/file',    [InternController::class, 'file']);
    Route::get('/my-certificates',        [CertificateController::class, 'mine']);
    Route::get('/certificates/{number}',  [CertificateController::class, 'show']);

    // providers (employer role) and admin
    Route::middleware('role:admin')->prefix('provider')->group(function () {
        Route::get('/internships',                    [ProviderController::class, 'index']);
        Route::post('/internships',                   [ProviderController::class, 'store']);
        Route::put('/internships/{id}',               [ProviderController::class, 'update']);
        Route::patch('/internships/{id}/status',      [ProviderController::class, 'setStatus']);
        Route::get('/internships/{id}/applications',  [ProviderController::class, 'applications']);
        Route::patch('/applications/{id}/status',     [ProviderController::class, 'updateApplicationStatus']);
        Route::post('/applications/{id}/start',       [ProviderController::class, 'startWorkflow']);
        Route::post('/applications/{id}/complete',    [ProviderController::class, 'completeApplication']);
        Route::post('/applications/{id}/certificate', [ProviderController::class, 'issueCertificate']);
        Route::put('/stages/{id}',                    [ProviderController::class, 'updateStage']);
        Route::post('/stages/{id}/review',            [ProviderController::class, 'reviewStage']);
    });
});