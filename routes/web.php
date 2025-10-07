<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\SessionController;


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
// Session management routes
Route::middleware('auth')->group(function () {
    Route::post('/update-session', [SessionController::class, 'updateSession'])->name('session.update');
    Route::get('/check-active-sessions', [SessionController::class, 'checkActiveSessions'])->name('session.active');
});

// Admin routes - restricted to admin role only
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    // Dashboard routes
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])
        ->name('admin.dashboard');
        
    // Map data endpoint
    Route::get('/dashboard/map-data', [\App\Http\Controllers\Admin\DashboardController::class, 'getMapData'])
        ->name('admin.dashboard.map-data');
    
    // Enlistments management routes (admin only)
    Route::get('/enlistments', [\App\Http\Controllers\Admin\EnlistmentController::class, 'index'])->name('admin.enlistments');
    Route::delete('/enlistments/{boardingHouse}', [\App\Http\Controllers\Admin\EnlistmentController::class, 'destroy'])->name('admin.enlistments.destroy');
    
   
    // Owners management routes (admin only)
    Route::get('owners', [\App\Http\Controllers\Admin\OwnerController::class, 'index'])->name('admin.owners.index');
    Route::get('owners/create', [\App\Http\Controllers\Admin\OwnerController::class, 'create'])->name('admin.owners.create');
    Route::post('owners', [\App\Http\Controllers\Admin\OwnerController::class, 'store'])->name('admin.owners.store');
    Route::get('owners/{user}', [\App\Http\Controllers\Admin\OwnerController::class, 'show'])->name('admin.owners.show');
    Route::get('owners/{user}/edit', [\App\Http\Controllers\Admin\OwnerController::class, 'edit'])->name('admin.owners.edit');
    Route::match(['put', 'patch', 'post'], 'owners/{user}', [\App\Http\Controllers\Admin\OwnerController::class, 'update'])
        ->name('admin.owners.update');
    Route::delete('owners/{user}', [\App\Http\Controllers\Admin\OwnerController::class, 'destroy'])->name('admin.owners.destroy');
    
    // Sessions management
    Route::get('/sessions', [SessionController::class, 'index'])->name('admin.sessions');
});




// Owner routes
Route::prefix('owner')->middleware(['auth', 'role:owner'])->group(function () {
    Route::get('/home', [\App\Http\Controllers\Owner\HomeController::class, 'index'])->name('owner.home');
    
    // Boarding House routes for owners
    Route::resource('boarding-houses', \App\Http\Controllers\Owner\BoardingHouseController::class)
        ->names('owner.boarding-houses')
        ->except(['show']);
});

// Account routes accessible by both admin and owner
Route::middleware(['auth', 'role:admin,owner'])->group(function () {
    Route::get('/account', [\App\Http\Controllers\AccountController::class, 'index'])->name('account.index');
    Route::post('/account/update-username', [\App\Http\Controllers\AccountController::class, 'updateUsername'])->name('account.update-username');
    Route::post('/account/update-password', [\App\Http\Controllers\AccountController::class, 'updatePassword'])->name('account.update-password');
});

// Boarding house routes accessible by both admin and owner
Route::middleware(['auth', 'role:admin,owner'])->group(function () {
    // Create routes
    Route::get('admin/enlistments/create', [\App\Http\Controllers\Admin\EnlistmentController::class, 'create'])
        ->name('admin.enlistments.create');
    Route::post('admin/enlistments', [\App\Http\Controllers\Admin\EnlistmentController::class, 'store'])
        ->name('admin.enlistments.store');
        
    // Show, edit, update routes
    Route::get('admin/enlistments/{boardingHouse}', [\App\Http\Controllers\Admin\EnlistmentController::class, 'show'])
        ->name('admin.enlistments.show');
    Route::get('admin/enlistments/{boardingHouse}/edit', [\App\Http\Controllers\Admin\EnlistmentController::class, 'edit'])
        ->name('admin.enlistments.edit');
    Route::put('admin/enlistments/{boardingHouse}', [\App\Http\Controllers\Admin\EnlistmentController::class, 'update'])
        ->name('admin.enlistments.update');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Public routes
Route::get('/', [\App\Http\Controllers\PublicController::class, 'index'])->name('home');
Route::get('/map-data', [\App\Http\Controllers\PublicController::class, 'getMapData'])->name('public.map-data');
Route::get('/boarding-houses/{boardingHouse}', [\App\Http\Controllers\PublicController::class, 'show'])->name('public.boarding-houses.show');
