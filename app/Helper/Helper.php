<?php

namespace App\Helper;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class Helper
{
    
    // Upload Image
    public static function fileUpload($file, $folder, $name)
    {
        $imageName = Str::slug($name) . '.' . $file->extension();
        $file->move(public_path('uploads/' . $folder), $imageName);
        $path = 'uploads/' . $folder . '/' . $imageName;
        return $path;
    }
    //file delete
    public static function fileDelete($filePath)
    {
        $fullPath = public_path($filePath); 

        if (file_exists($fullPath)) {
           
            unlink($fullPath);
            return true; 
        }
        return false;
    }
//tableCheckbox
    public static function tableCheckbox($row_id)
    {
        return '<div class="form-checkbox">
                <input type="checkbox" class="form-check-input select_data" id="checkbox-' . $row_id . '" value="' . $row_id . '" onClick="select_single_item(' . $row_id . ')">
                <label class="form-check-label" for="checkbox-' . $row_id . '"></label>
            </div>';
    }


    //video upload
    public static function videoUpload($file, $folder, $name)
    {
        $videoName = Str::slug($name) . '.' . $file->extension();
        $file->move(public_path('uploads/' . $folder), $videoName);
        $path = 'uploads/' . $folder . '/' . $videoName;
        return $path;
    }


    // audio upload
    public static function audioUpload($file, $folder, $name)
    {
        $audioName = Str::slug($name) . '.' . $file->extension();
        $file->move(public_path('uploads/' . $folder), $audioName);
        $path = 'uploads/' . $folder . '/' . $audioName;
        return $path;
    }

    //video delete

    public static function videoDelete($filePath)
    {
        $fullPath = public_path($filePath); 
    
        if (file_exists($fullPath)) {
           
            unlink($fullPath);
            return true; 
        }
        return false;
    }
    

}
