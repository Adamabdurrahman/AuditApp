<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Models\User;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\AuditPlanController;
use Illuminate\Support\Facades\Mail;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/logout/thank-you', 'thankyou')->name('logout.thankyou');

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

    // Tambahkan ini di dalam grup admin
    Route::get('/findings/create', [AdminController::class, 'createFinding'])->name('findings.create');

    // URL: /admin/findings -> Nama Rute: admin.findings
    Route::get('/findings', [AdminController::class, 'findings'])->name('findings');

    // Di dalam grup admin
    Route::post('/findings', [AdminController::class, 'storeFinding'])->name('findings.store');

    Route::delete('/findings/{id}', [AdminController::class, 'deleteFinding'])->name('findings.delete');

    Route::post('/findings/{id}/autosave', [AdminController::class, 'autoSaveFinding'])->name('findings.autosave');

    Route::post('/findlossdetail/{auditFormId}', [AdminController::class, 'addFindLossDetail'])
    ->name('findlossdetail.add');

    Route::put('/findlossdetail/{id}', [AdminController::class, 'updateFindLossDetail'])
    ->name('findlossdetail.update');

    Route::delete('/findlossdetail/{id}', [AdminController::class, 'deleteFindLossDetail'])
    ->name('findlossdetail.delete');

    Route::post('/findlossdetail/{detail}/recovery', [AdminController::class, 'storeFindLossRecovery'])
    ->name('findlossdetail.recovery.store');

    Route::put('/findlossdetail/recovery/{id}', [AdminController::class, 'updateFindLossRecovery'])
    ->name('findlossdetail.recovery.update');

    Route::delete('/findlossdetail/recovery/{id}', [AdminController::class, 'deleteFindLossRecovery'])
    ->name('findlossdetail.recovery.delete');

    Route::post('/findings/{id}/extend', [AdminController::class, 'extendDueDate'])
    ->name('findings.extend');

    Route::post('/findings/{id}/attachments', [AdminController::class, 'uploadAttachment'])
    ->name('findings.uploadAttachment');

    Route::delete('/attachments/{id}', [AdminController::class, 'deleteAttachment'])
    ->name('findings.deleteAttachment');

    Route::get('/plans', [AuditPlanController::class, 'index'])->name('plans.index');
    Route::get('/plans/create', [AuditPlanController::class, 'create'])->name('plans.create');
    Route::post('/plans', [AuditPlanController::class, 'store'])->name('plans.store');
    Route::get('/plans/{plan}/edit', [AuditPlanController::class, 'edit'])->name('plans.edit');
    Route::put('/plans/{plan}', [AuditPlanController::class, 'update'])->name('plans.update');
    Route::delete('/plans/{plan}', [AuditPlanController::class, 'destroy'])->name('plans.destroy');
    Route::get('/plans/{plan}', [AuditPlanController::class, 'show'])->name('plans.show');

    // Di dalam grup admin
    Route::get('/findings/{id}/assessment', [AdminController::class, 'showAssessment'])
    ->name('findings.assessment');
        
    // untuk assesments
    Route::post('/assessments/{auditFormId}', [AdminController::class, 'addAssessment'])
    ->name('assessments.add');

    Route::get('/assessments/{id}', [AdminController::class, 'getAssessment'])
    ->name('assessments.get');

    Route::patch('/assessments/{id}', [AdminController::class, 'updateAssessment'])
    ->name('assessments.update');

    // Recovery
    Route::post('/recovery/{assessmentId}', [AdminController::class, 'addRecovery'])->name('recovery.add');
    Route::delete('/recovery/{id}', [AdminController::class, 'deleteRecovery'])->name('recovery.delete');
    Route::get('/recovery/{assessmentId}', [AdminController::class, 'getRecovery'])->name('recovery.get');

    Route::delete('/assessments/{id}', [AdminController::class, 'deleteAssessment'])
    ->name('assessments.delete');

    // halaman konfirmasi setelah submit for review
    Route::get('/findings/{id}/confirm', [AdminController::class, 'confirmFinding'])
    ->name('findings.confirm');

    // Menutup audit form
    Route::post('/findings/{id}/close', [AdminController::class, 'closeFinding'])
        ->name('findings.close');
    
    Route::get('/users', [AdminController::class, 'manageUsers'])
        ->name('users')
        ->middleware('can:access-superadmin-area'); // <-- Gunakan nama Gate baru Anda

    Route::post('/users/{user}/update-role', [AdminController::class, 'updateUserRole'])
        ->name('users.updateRole')
        ->middleware('can:access-superadmin-area');

    // URL: /admin/report -> Nama Rute: admin.report
    // Route::get('/report', [AdminController::class, 'report'])->name('report');

});

Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard/data/finloss', [App\Http\Controllers\Admin\AdminController::class, 'getFinLossDonut'])
            ->name('dashboard.finloss');
        
        Route::get('/dashboard/data/improvement', [App\Http\Controllers\Admin\AdminController::class, 'getImprovementChart'])
            ->name('dashboard.improvement');

        Route::get('/dashboard/data/noncompliance', [App\Http\Controllers\Admin\AdminController::class, 'getNonComplianceChart'])
            ->name('dashboard.noncompliance');

        Route::get('/dashboard/data/status', [App\Http\Controllers\Admin\AdminController::class, 'getStatusChart'])
            ->name('dashboard.status');

        Route::get('/dashboard/data/finloss-global', [App\Http\Controllers\Admin\AdminController::class, 'getFinLossGlobalChart'])
            ->name('dashboard.finlossGlobal');

        Route::get('/dashboard/data/finloss-findings', [App\Http\Controllers\Admin\AdminController::class, 'getFinLossFindingBreakdown'])
            ->name('dashboard.finlossFindings');

        Route::get('/dashboard/data/report-titles', [App\Http\Controllers\Admin\AdminController::class, 'getReportTitleDistribution'])
            ->name('dashboard.reportTitles');
        
        Route::get('/dashboard/data/audit-recap', [App\Http\Controllers\Admin\AdminController::class, 'getAuditRecapChart'])
            ->name('dashboard.auditRecap');
    });


Route::middleware(['auth', 'can:access-user-area'])
    ->name('user.') // Memberi awalan nama rute user.
    ->group(function() {
    
    // URL: /my-open-findings -> Nama Rute: user.my-open-findings
    Route::get('/my-open-findings', [UserController::class, 'myOpenFindings'])->name('my-open-findings');
});

Route::middleware('auth')->group(function () {
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])
        ->name('notifications.read');
    
    Route::get('/notifications', [NotificationController::class, 'index'])
        ->name('notifications.index');
});


Route::get('/notifications-count', function () {
    $count = \App\Models\Notification::where('user_id', auth()->id())
        ->where('is_read', false)
        ->count();
    return response()->json(['count' => $count]);
})->middleware('auth');

Route::post('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])
    ->name('notifications.markAsRead');

Route::get('/test-email', function () {
    Mail::raw('Halo! Ini tes email dari AuditApp via Brevo SMTP.', function ($message) {
        $message->to('melvi09januari@gmail.com')
                ->subject('✅ Tes Email dari AuditApp');
    });
    return '✅ Email test berhasil dikirim ke melvi09januari@gmail.com! Silakan cek inbox/spam.';
});

require __DIR__.'/auth.php';
