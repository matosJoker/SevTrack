<?php
// app/Services/ImageCompressionService.php

namespace App\Services;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ImageCompressionService
{
    /**
     * Kompres dan simpan gambar
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $folder
     * @param int $quality (1-100)
     * @param int|null $maxWidth
     * @return string
     */
    public function compressAndStore($file, $folder = 'photos', $quality = 60, $maxWidth = 800)
    {
        $extension = $file->getClientOriginalExtension();
        $filename = uniqid() . '_' . time() . '.' . $extension;
        $path = $folder . '/' . $filename;
        
        $image = Image::make($file);
        
        // Resize jika melebihi lebar maksimum
        if ($maxWidth && $image->width() > $maxWidth) {
            $image->resize($maxWidth, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        }
        
        // Simpan dengan kualitas terkompresi
        $image->encode($extension, $quality);
        
        Storage::disk('public')->put($path, $image);
        
        return Storage::url($path);
    }

    /**
     * Kompres multiple gambar
     *
     * @param array $files
     * @param string $folder
     * @param int $quality
     * @param int|null $maxWidth
     * @return array
     */
    public function compressMultiple($files, $folder = 'photos', $quality = 60, $maxWidth = 800)
    {
        $paths = [];
        
        foreach ($files as $file) {
            if ($file && $file->isValid()) {
                $paths[] = $this->compressAndStore($file, $folder, $quality, $maxWidth);
            }
        }
        
        return $paths;
    }
}