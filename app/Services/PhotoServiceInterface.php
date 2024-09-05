<?php
namespace App\Services;
use Illuminate\Http\UploadedFile;
interface PhotoServiceInterface
{
    public function convertAndStorePhoto(UploadedFile $photo): ?string;
}