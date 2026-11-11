<?php

use Illuminate\Support\Facades\Route;

/*Route::get('/', function(){
    return redirect('/home');
});*/

Route::get('/', function(){
    return view('home');
});
Route ::redirect('/home', '/');

Route::get('/login', function(){
    return view('login');
});

Route::get('/ask-questions', function(){
    return view('ask-questions');
});

Route::get('/dashboard', function(){
    return view('dashboard');
});

Route::get('/jobs', function(){
    return view('jobs');
});

Route::get('/Profile', function(){
    return view('Profile');
});

Route::get('/register', function(){
    return view('register');
});

Route::get('/JobInterface', function(){
    return view('JobInterface');
});

Route::get('/internships', function(){
    return view('internships');
});

Route::get('/forgot-password', function () {
    return view('forgot-password');
});

Route::get('/reset-password', function () {
    return view('reset-password');
});

Route::get('/talent-admin', function () {
    return view('talent-admin');
});

Route::get('/opportunities', function () {
    return view('opportunities');
});

Route::view('/provider-internships', 'provider-internships');

Route::get('/certificate/{number}', fn ($number) => view('certificate', ['number' => $number]));

Route::get('/verify-certificate/{number?}', fn ($number = null) => view('verify-certificate', ['number' => $number]));

Route::view('/register/employee', 'register-employee');
Route::view('/register/employer', 'register-employer');

Route::redirect('/Login', '/login');                 // fixes the /Login vs /login mismatch

Route::view('/admin', 'admin');

Route::view('/employer', 'employer');