<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FileUploadController extends Controller
{
    private $validFileExtensions = [
        'obj',
    ];

    public function temporary3dModelSingeFile(Request $request){
        $result = [
            'status'=> 200,
            'message'=> 'OK',
        ];
        if(!$this->validate3dFile($request)){
            $result['status'] = 415;
            $result['message'] = 'Unsupported Media Type';
            return $result;
        }
        $file = $request->file('file');
        $originalExtension = $file->getClientOriginalExtension();

        //Move Uploaded File
        $destinationPath = 'uploads\temp';
        $uuid = Str::uuid();
        $savedFile = $file->move($destinationPath, (string)$uuid.".".$file->getClientOriginalExtension());
        $result['uploadedPath'] = $savedFile->getPathname();
        return $result;
    }

    private function validate3dFile(Request $request){
        $file = $request->file('file');
        $originalExtension = $file->getClientOriginalExtension();
        if (in_array($originalExtension, $this->validFileExtensions)){
            return true;
        }
        return false;
    }
}
