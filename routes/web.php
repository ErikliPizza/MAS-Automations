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
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if (auth()->check()) {
        return Inertia::render('Welcome');
    } else {
        return redirect()->to('/login');
    }
})->name('home');


Route::middleware(['auth', 'checkUserStatus'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])
        ->name('users.index');
});
Route::middleware(['auth', 'checkUserStatus', 'canAccessAdminOrRoot'])->group(function () {
    Route::get('/users/{user}', [UserController::class, 'create'])
        ->name('users.create');
    Route::put('/users/{user}', [UserController::class, 'update'])
        ->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->name('users.delete');
    Route::get('/create-user', [RegisteredUserController::class, 'create'])
        ->name('create-user');
    Route::post('create-user', [RegisteredUserController::class, 'store']);
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

    Route::post('/appointments/create-appointment', [AppointmentController::class, 'store'])
        ->name('appointments.store');
});


require __DIR__.'/auth.php';
