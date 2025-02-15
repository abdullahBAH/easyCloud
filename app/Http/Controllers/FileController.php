<?php

namespace App\Http\Controllers;

use App\Models\UserFile;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Str;

class FileController extends Controller
{
    public function index()
    {
        $files = UserFile::whereNull('deleted_at')
            ->where('user_id', Auth::id())
            ->get();

        $formattedFiles = $files->map(function ($file) {
            $fileExtension = pathinfo($file->file_name, PATHINFO_EXTENSION);
            list($fileType, $icon) = $this->getFileTypeAndIcon($fileExtension);
            return [
                'id' => $file->id,
                'name' => $file->file_name,
                'type' => $fileType,
                'preview' => null,
                'icon' => $icon,
                'downloadPath' => route('file.download', ['id' => $file->id]),
                'created_at' => $file->created_at->format('Y-m-d'),
                'deletePath' => route('file.delete', ['id' => $file->id]),
                'sharePath' => route('file.share', ['id' => $file->id]),
            ];
        });

        return view('main', ['files' => $formattedFiles]);
    }

    private function getFileTypeAndIcon($extension)
    {
        $fileType = 'file';
        $icon = 'insert_drive_file';

        switch (strtolower($extension)) {
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
        }

        return [$fileType, $icon];
    }

    public function download($id)
    {
        $file = UserFile::findOrFail($id);

        if ($file->user_id !== Auth::id()) {
            abort(403, 'You do not have permission to download this file.');
        }

        $filePath = public_path("uploads/{$file->file_name}");

        if (file_exists($filePath)) {
            return response()->download($filePath);
        }

        abort(404, 'File not found.');
    }

    public function delete($id)
    {
        $file = UserFile::findOrFail($id);

        if ($file->user_id !== Auth::id()) {
            abort(403, 'You do not have permission to delete this file.');
        }

        $file->update(['deleted_at' => now()]);

        return redirect()->route('main')->with('success', 'File deleted successfully!');
    }

    public function share($id)
    {
        $file = UserFile::findOrFail($id);
        $shareToken = Str::random(32);
        $file->share_token = $shareToken;
        $file->save();
        $shareLink = route('file.shared.download', ['token' => $shareToken]);

        return response()->json([
            'message' => 'File shareable link generated successfully.',
            'share_link' => $shareLink,
        ]);
    }

    public function downloadSharedFile($token)
    {
        $file = UserFile::where('share_token', $token)->first();
        if ($file) {
            if ($file->share_expiration && now()->greaterThan($file->share_expiration)) {
                abort(404, 'This share link has expired.');
            }

            $filePath = public_path("uploads/{$file->file_name}");

            if (file_exists($filePath)) {
                return response()->download($filePath);
            }
            abort(404, 'File not found.');
        }

        abort(404, 'Invalid or expired link.');
    }
}
