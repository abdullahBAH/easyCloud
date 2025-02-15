<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class UserFile extends Model
{
    use HasFactory;
    use SoftDeletes; // Enables soft delete functionality

    protected $dates = ['deleted_at']; // Adds a timestamp for soft deletes
    protected $fillable = ['user_id', 'file_name'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shareFile($fileId)
    {
        // Find the file by ID
        $file = UserFile::findOrFail($fileId);

        // Generate a random token
        $shareToken = Str::random(32);

        // Save the token to the database
        $file->share_token = $shareToken;
        $file->save();

        // Generate the shareable link
        $shareLink = route('file.shared.download', ['token' => $shareToken]);

        return response()->json([
            'message' => 'File is ready to be shared',
            'share_link' => $shareLink
        ]);
    }
}
