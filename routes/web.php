<?php

// use App\Http\Controllers\DashBoardController;

use App\Http\Controllers\DashboardController;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Booking\BookingController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboard;
use App\Http\Controllers\Admin\{AdminController, UserManagementController};
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\provider\DashboardController as ProviderDashboardController;
use App\Http\Controllers\Provider\ProviderController;


Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->isAdmin()) return redirect()->route('admin.dashboard');
        if ($user->isProvider()) return redirect()->route('provider.dashboard');
        if ($user->isStaff()) return redirect()->route('staff.dashboard');
    }
    return redirect()->route('login');
});

Route::get('login', [LoginController::class,'showLoginForm'])->name('login');
Route::post('login', [LoginController::class,'login'])->name('login.custom');
Route::post('logout', [LoginController::class,'logout'])->name('logout');



Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])
        ->name('dashboard');
    
    // Statistics
    Route::get('/statistics', [AdminController::class, 'statistics'])
        ->name('statistics');
    
    // User Management
    Route::resource('users', UserManagementController::class);
    Route::post('/users/{user}/toggle-activation', [UserManagementController::class, 'toggleActivation'])
        ->name('users.toggle-activation');
});
Route::get('/diagnoses', [AdminController::class, 'diagnoses'])->name('admin.diagnoses');

Route::middleware(['auth', 'role:staff'])->group(function () {
    Route::get('/', StaffDashboard::class)->name('staff.dashboard');
    
    // ✅ Calendar MUST come BEFORE generic routes
    Route::get('/bookings/calendar', [BookingController::class, 'calendar'])
        ->name('bookings.calendar');
    Route::get('/bookings/by-date', [BookingController::class, 'getBookings'])
        ->name('bookings.by-date');
    
    // Then other booking routes
    Route::get('/bookings', [BookingController::class, 'index'])
        ->name('bookings.index');
    Route::get('/bookings/create', [BookingController::class, 'create'])
        ->name('bookings.create');
    Route::post('/bookings', [BookingController::class, 'store'])
        ->name('bookings.store');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])
        ->name('bookings.show');
    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])
        ->name('bookings.destroy');
	
	Route::resource('clients', ClientController::class)->except(['destroy']);
    Route::get('/clients/{client}/medical-history', [ClientController::class, 'medicalHistory'])
        ->name('clients.medical-history');
});

Route::middleware(['auth', 'role:provider'])->prefix('provider')->name('provider.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [ProviderDashboardController::class, 'dashboard'])
        ->name('dashboard');
    
    // Bookings
    Route::get('/bookings', [ProviderController::class, 'bookings'])
        ->name('bookings.index');
    Route::get('/bookings/{booking}', [ProviderController::class, 'showBooking'])
        ->name('bookings.show');
    
    // Note
    Route::get('/bookings/{booking}/note/create', [ProviderController::class, 'createNote'])
        ->name('note.create');
    Route::post('/bookings/{booking}/note', [ProviderController::class, 'storeNote'])
        ->name('note.store');
    Route::get('/bookings/{booking}/note/edit', [ProviderController::class, 'editNote'])
        ->name('note.edit');
    Route::put('/bookings/{booking}/note', [ProviderController::class, 'updateNote'])
        ->name('note.update');
    
    // Client History
    Route::get('/clients/{client}/history', [ProviderController::class, 'clientHistory'])
        ->name('clients.history');
});
// clear routes

Route::get('/clear', function () {

    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    Artisan::call('route:clear');

    return "Cleared!";
});



use Illuminate\Support\Facades\App;

Route::get('/lang/{locale}', function ($locale) {
    if (!in_array($locale, ['en', 'ar'])) {
        abort(400);
    }

    App::setLocale($locale);
    session(['locale' => $locale]);
    return back();
});
