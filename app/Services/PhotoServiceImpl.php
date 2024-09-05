<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Services\PhotoServiceInterface;



class PhotoServiceImpl implements PhotoServiceInterface
{
    public function convertAndStorePhoto(UploadedFile $photo): ?string
    {
        if ($photo) {
            // Convert the photo to base64
            $base64Photo = base64_encode(file_get_contents($photo->getRealPath()));

            // Optionally save the photo file in the storage
            $photoPath = $photo->store('photo', 'public');

            // Return the base64 encoded string
            return "data:$photoPath;base64,$base64Photo";
        }

        return null;
    }
}
