<?php

namespace App\Services;

class ImageService
{
    /**
     * Encode une image en base64.
     *
     * @param string $path
     * @return string
     */
   
    public function encodeImageToBase64($imagePath)
{
    if (!file_exists($imagePath)) {
        return null;
    }

    $imageData = file_get_contents($imagePath);
    $base64 = base64_encode($imageData);
    $mimeType = mime_content_type($imagePath);

    return 'data:' . $mimeType . ';base64,' . $base64;
}

}
