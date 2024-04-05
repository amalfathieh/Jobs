<?php

namespace App\services;


use Illuminate\Support\Facades\Storage;
use function Symfony\Component\HttpKernel\Log\format;

class FileService
{

    public function store($file , $folder_name){
        $file_Path = null;
        if ($file && $file->isValid()) {
            $file_Path = $folder_name . '/' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path( $folder_name), $file_Path);
        }
        return $file_Path;
    }

    public function update($new_file , $old_file ,$folder_name){
        if($old_file){
            if(file_exists(public_path($old_file))) {
                unlink(public_path($old_file));
                return $this->store($new_file, $folder_name);

            }    }
            return   $this->store($new_file ,$folder_name);
    }

}
