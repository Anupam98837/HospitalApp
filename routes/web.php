<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landingPage/pages/department');
});

// Admin Routes 

Route::get('/admin/login', function () {
    return view('users/admin/pages/common/login');
});
Route::get('/admin/dashboard', function () {
    return view('users/admin/pages/common/dashboard');
});
Route::get('/admin/department/manage', function () {
    return view('users/admin/pages/department/manageDepartment');
});
Route::get('/admin/doctor/manage', function () {
    return view('users/admin/pages/doctor/manageDoctor');
});
Route::get('/admin/appointment/create', function () {
    return view('users/admin/pages/doctor/createAppointment');
});
Route::get('/admin/prescription/manage', function () {
    return view('modules/prescription/managePrescription');
});

Route::view('/doctors', 'landingPage/pages/bookDoctor')
     ->name('doctors.page');
