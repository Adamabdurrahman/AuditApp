<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Models\User;
use App\Http\Controllers\User\UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    // Cek jika user yang login adalah admin atau superadmin
    if ($user instanceof User && ($user->isAdmin() || $user->isSuperAdmin())) {
        return redirect()->route('admin.dashboard');
    }
    // Jika tidak, tampilkan dasbor user biasa
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'can:access-admin-area'])
    ->prefix('admin') // Memberi awalan URL /admin pada semua rute di dalam grup
    ->name('admin.') // Memberi awalan nama rute admin. pada semua rute di dalam grup
    ->group(function () {
        
    // URL: /admin/dashboard -> Nama Rute: admin.dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // URL: /admin/findings -> Nama Rute: admin.findings
    Route::get('/findings', [AdminController::class, 'findings'])->name('findings');

    // URL: /admin/report -> Nama Rute: admin.report
    Route::get('/report', [AdminController::class, 'report'])->name('report');

    // URL: /admin/users -> Nama Rute: admin.users
    Route::get('/users', [AdminController::class, 'manageUsers'])->name('users');
});

Route::middleware(['auth', 'can:access-user-area'])
    ->name('user.') // Memberi awalan nama rute user.
    ->group(function() {
    
    // URL: /my-open-findings -> Nama Rute: user.my-open-findings
    Route::get('/my-open-findings', [UserController::class, 'myOpenFindings'])->name('my-open-findings');
});

require __DIR__.'/auth.php';
