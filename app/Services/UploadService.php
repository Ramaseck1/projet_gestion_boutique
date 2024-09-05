<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class UploadService
{
    /**
     * Upload an image to the specified disk and directory.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @param string $disk
     * @return string|null
     */
    public function uploadImage($file, $directory = 'photo', $disk = 'public')
    {
        if ($file->isValid()) {
            // Store the file and return its path
            return $file->store($directory, $disk);
        }
        
        return null;
    }
}
