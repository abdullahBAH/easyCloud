<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Auth;
use App\Models\UserFile;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
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

Route::get('/main', function () {
    // احصل على جميع الملفات للمستخدم
    $files = UserFile::where('user_id', Auth::id())->get();

    // تحويل الملفات وتعيين الأيقونة بناءً على نوع الملف
    $formattedFiles = $files->map(function ($file) {
        $fileExtension = pathinfo($file->file_name, PATHINFO_EXTENSION);

        // تحديد النوع والأيقونة بناءً على الامتداد
        $fileType = '';
        $icon = '';

        switch (strtolower($fileExtension)) {
            case 'mp4':
            case 'mov':
                $fileType = 'video';
                $icon = 'video_file';
                break;
            case 'pdf':
                $fileType = 'pdf';
                $icon = 'picture_as_pdf';
                break;
            case 'mp3':
                $fileType = 'audio';
                $icon = 'audio_file';
                break;
            default:
                $fileType = 'file';
                $icon = 'insert_drive_file';
        }

        return [
            'id' => $file->id,
            'name' => $file->file_name,
            'type' => $fileType,
            'preview' => null,
            'icon' => $icon,
            'downloadPath' => route('file.download', ['id' => $file->id]), // مسار التنزيل
            'created_at' => \Carbon\Carbon::parse($file->created_at)->format('Y-m-d'),
        ];
    });
    return view('main', ['files' => $formattedFiles]);
});

Route::get('/download/{id}', function ($id) {

    $file = UserFile::findOrFail($id);

    // التحقق مما إذا كان المستخدم هو مالك الملف أو لديه صلاحية الوصول
    if ($file->user_id !== Auth::id()) {
        abort(Response::HTTP_FORBIDDEN, 'ليس لديك صلاحية لتنزيل هذا الملف.');
    }

    $filePath = public_path("uploads/{$file->file_name}");

    if (file_exists($filePath)) {
        return response()->download($filePath);
    }

    abort(Response::HTTP_NOT_FOUND);
})->middleware('auth')->name('file.download');

require __DIR__ . '/auth.php';
