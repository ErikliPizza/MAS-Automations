<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\UserController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| checkUserStatus is must be next to the auth. It checks if the user status is active/inactive and expiry date
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home')->middleware('auth');


/*
 * List Users (GET)
 * List User (GET)
 * */
Route::middleware(['auth', 'checkUserStatus'])->group(function () {

    // list users
    Route::get('/users', [UserController::class, 'index'])
        ->name('user.index');

    // list user
    Route::get('/user/{user}', [UserController::class, 'show'])
        ->name('user.show');
});

/*
 * Create User Form (GET)
 * Store User (POST)
 *
 * Edit User Form (GET)
 * Update User (PUT)
 *
 * Delete User (POST)
 * */
Route::middleware(['auth', 'checkUserStatus', 'canAccessAdminOrRoot'])->group(function () {

    // create user
    Route::get('/users/new', [UserController::class, 'create'])
        ->name('user.create');
    Route::post('/users', [UserController::class, 'store'])
    ->name('user.store');

    // edit user
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])
        ->name('user.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])
        ->name('user.update');

    // delete user
    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->name('user.delete');

});

Route::middleware(['auth', 'checkUserStatus', 'checkAppointment'])->group(function () {
    // Services
    Route::get('/appointments/services', [ServiceController::class, 'index'])
        ->name('appointments.services.index');

    Route::get('/appointments/services/show/{slug}', [ServiceController::class, 'show'])
        ->name('appointments.services.show');

    Route::get('/appointments/services/create-service', [ServiceController::class, 'create'])
        ->name('appointments.services.create');

    Route::get('/appointments/services/edit/{service}', [ServiceController::class, 'edit'])
        ->name('appointments.services.edit');

    Route::post('/appointments/services/create-service', [ServiceController::class, 'store'])
        ->name('appointments.services.store');

    Route::put('/appointments/services/edit/{service}', [ServiceController::class, 'update'])
        ->name('appointments.services.update');

    Route::delete('/appointments/services/edit/{service}', [ServiceController::class, 'destroy'])
        ->name('appointments.services.destroy');

    // Appointments
    Route::get('/appointments/', [AppointmentController::class, 'index'])
        ->name('appointments.index');
    Route::get('/appointments/create-appointment', [AppointmentController::class, 'create'])
        ->name('appointments.create');
    Route::get('/appointments/edit-appointment/{appointment}', [AppointmentController::class, 'edit'])
        ->name('appointments.edit');

    Route::post('/appointments/create-appointment', [AppointmentController::class, 'store'])
        ->name('appointments.store');
    Route::put('/appointments/edit-appointment/{appointment}', [AppointmentController::class, 'update'])
        ->name('appointments.update');
    Route::put('/appointments/update-status/{appointment}', [AppointmentController::class, 'updateStatus'])
        ->name('appointments.updateStatus');


});


require __DIR__.'/auth.php';
