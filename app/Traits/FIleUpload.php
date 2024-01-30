<?php
namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait FileUpload {

    /**
     * Image upload.
     * 
     * @param $image
     * @return string
     * @throws \Exception
     */
    public function uploadFile($file, $image_path) 
    {
        try {
            if($file) {
                $file_name = uniqid() .'-'. $file->getClientOriginalName();
                $path = $file->storeAs($image_path, $file_name);
            }
        } catch(\Exception $e) {
            throw $e;
        }
        return $file_name;
    }

    /**
     * Delete old image.
     * 
     * @param $image
     * @throws \Exception
     */
    public function deleteImage($image)
    {
        try {
            if(Storage::disk('public')->exists('images/'.$image)) {
                Storage::disk('public')->delete('images/'.$image);
            }
        } catch(\Exception $e) {
            throw $e;
        }
    }
}