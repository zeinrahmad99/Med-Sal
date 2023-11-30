<?php

namespace App\Traits\Api\V1;

use Illuminate\Support\Facades\Storage;

trait Images
{
    // This function changes the name of uploaded image files
    public static function giveImageRandomName($image)
    {
        return uniqid() . '.' . $image->getClientOriginalExtension();
    }

    // This function deletes an image file
    public static function deleteImage($image, string $path)
    {
        Storage::delete($path . $image);
    }

    // This function stores image files
    public static function storeImage($image, string $imageName, string $path)
    {
        // $image->move($path, $imageName);
        Storage::putFileAs($path, $image, $imageName);
    }
}