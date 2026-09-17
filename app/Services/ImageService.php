<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageService
{
    protected ImageManager $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Store an uploaded image with optional resizing
     *
     * @param UploadedFile $file
     * @param string $path
     * @param int|null $width
     * @param int|null $height
     * @return string
     */
    public function storeImage(UploadedFile $file, string $path = 'posters', ?int $width = 500, ?int $height = 750): string
    {
        // Generate a unique filename
        $filename = uniqid() . '_' . time() . '.' . $file->getClientOriginalExtension();

        // Create the directory if it doesn't exist
        $directory = storage_path('app/public/' . $path);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Process and resize the image if dimensions are provided
        if ($width && $height) {
            // Create an instance of the image (v3 API)
            $img = $this->manager->read($file->getRealPath());

            // Resize the image while maintaining aspect ratio (don't upscale)
            $img->scale(width: $width, height: $height);

            // Save the image to storage
            $img->save($directory . '/' . $filename);

            return $path . '/' . $filename;
        }

        // If no resizing needed, store the original file
        $file->storeAs('public/' . $path, $filename);

        return $path . '/' . $filename;
    }

    /**
     * Delete an image from storage
     *
     * @param string $path
     * @return bool
     */
    public function deleteImage(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }

        return false;
    }
}
