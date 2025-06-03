<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:jpeg,png,jpg,gif,webp,pdf,doc,docx|max:10240', // max 10MB
        ]);
        $file = $request->file('file');
        $mime = $file->getMimeType();
        if (str_starts_with($mime, 'image/')) {
            $path = $file->store('images', 'public');
        } else {
            $path = $file->store('files', 'public');
        }
        $url = asset('storage/' . $path);
        return response()->json(['location' => $url]);
    }
}