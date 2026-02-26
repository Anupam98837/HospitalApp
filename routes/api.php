<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\DoctorController;
use App\Http\Controllers\API\DepartmentController;
use App\Http\Controllers\API\AppointmentController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\DoctorBookingController;
use App\Http\Controllers\API\PrescriptionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Admin Routes 

Route::post('admin/login',  [AdminController::class, 'login']); 
Route::get('departments', [DepartmentController::class, 'index']);       
Route::post('admin/logout', [AdminController::class, 'logout'])
     ->middleware('CheckAuth:admin');                                     

// Department Routes 

Route::prefix('departments')
      ->controller(DepartmentController::class)
      ->middleware('CheckAuth:admin')                               // restrict to staff roles
      ->group(function () {
           
            Route::post('/',              'store');          // create
            Route::get('{id}',            'show');           // show single
            Route::put('{id}',            'update');         // full update
            Route::patch('{id}',          'update');         // partial update
            Route::delete('{id}',         'destroy');        // delete
            Route::patch('{id}/toggle-status', 'toggleStatus');
      });


// Doctor Routes 


Route::post('doctors/signup', [DoctorController::class, 'signup']);
Route::post('doctors/login',  [DoctorController::class, 'login']);
Route::get('doctors', [DoctorController::class, 'index']);
Route::get('departments/{id}/doctors', [DoctorController::class, 'doctorsByDepartment']);


Route::middleware('CheckAuth:doctor,admin')->prefix('doctors')
      ->controller(DoctorController::class)
      ->group(function () {
          Route::post('logout', 'logout');

          
          Route::get('{id}',            'show');
          Route::put('{id}',            'update');
          Route::patch('{id}',          'update');
          Route::delete('{id}',         'destroy');
          Route::patch('{id}/toggle-active', 'toggleActive');
      });


// Appoinment Routes 

Route::get('appointments/doctor/{doctorId}', [AppointmentController::class, 'index']);

Route::get(
    '/appointments/{slot}/slices',
    [AppointmentController::class, 'slices']
);

Route::prefix('appointments')
      ->controller(AppointmentController::class)
      ->middleware('CheckAuth:doctor,admin')   // doctors & admins only
      ->group(function () {
          Route::post('/',              'add');            // create slot

          Route::get('{id}',            'show');           // single slot
          Route::put('{id}',            'update');         // replace
          Route::delete('{id}',         'delete');         // remove
      });


// User Routes 

Route::get('users/me', [UserController::class, 'getUserDetails']);

Route::prefix('users')->controller(UserController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login',    'login');

});

/* ─── Protected: any valid user / doctor / admin token ─── */


Route::prefix('users')
    ->controller(UserController::class)
    ->middleware('CheckAuth:user,doctor,admin')
    ->group(function () {
        /* everyone */
        Route::post('logout', 'logout');

        /* self-service */
        Route::put('me',    'update');
        Route::patch('me',  'update');
        Route::delete('me', 'delete');
        
        /* admin powers */
        Route::put('{id}',    'update');   // admin only
        Route::patch('{id}',  'update');   // admin only
        Route::delete('{id}', 'delete');   // admin only
    });


// Booking Routes 

Route::prefix('bookings')
      ->controller(DoctorBookingController::class)
      ->middleware('CheckAuth:user,doctor,admin')
      ->group(function () {
          Route::post('/',                     'store');            // create
          Route::get('/',                      'index');            // my bookings
          Route::put('{booking_token}',        'update');           // update
          Route::patch('{booking_token}',      'update');           // partial
          Route::delete('{booking_token}',     'destroy');          // cancel
      });

//Prescription
Route::prefix('prescriptions')->group(function () {
    Route::get('/', [PrescriptionController::class, 'index']);      // all bookings + prescriptions
    Route::post('/', [PrescriptionController::class, 'store']);     // add prescription
    Route::get('{id}', [PrescriptionController::class, 'show']);    // view single prescription
    Route::put('{id}', [PrescriptionController::class, 'update']);  // edit prescription
    Route::delete('{id}', [PrescriptionController::class, 'destroy']); // delete prescription
});

      

