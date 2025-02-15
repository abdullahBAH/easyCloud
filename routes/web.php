<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Auth;
use App\Models\UserFile;
use App\Http\Controllers\FileController;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/main');
    }
    return redirect('/login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::post('/upload', [UploadController::class, 'store'])->name('upload');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/main', [FileController::class, 'index']);
    Route::get('/file/download/{id}', [FileController::class, 'download'])->name('file.download');
    Route::get('/file/delete/{id}', [FileController::class, 'delete'])->name('file.delete');
    Route::get('/file/share/{id}', [FileController::class, 'share'])->name('file.share');
});


Route::get('/file/share/download/{token}', [FileController::class, 'downloadSharedFile'])->name('file.shared.download');


require __DIR__ . '/auth.php';
