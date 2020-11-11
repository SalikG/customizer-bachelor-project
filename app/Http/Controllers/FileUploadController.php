<?php

namespace App\Http\Controllers;

use App\Http\Requests\Save3dModelFromTemp;
use App\Models\Product3DModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadController extends Controller
{
    private $validFileExtensions = [
        'obj',
    ];

    public function temporary3dModelSingeFile(Request $request){
        if(!$this->validate3dFile($request)){
            return response(['message' => 'Unsupported Media Type'], 415);
        }

        $tempFilePath = $request->file('file')->storeAs('public/temp',
            (string)Str::orderedUuid().".".$request->file('file')->getClientOriginalExtension());
        if ($tempFilePath){
            $tempFilePath = str_replace('public/', 'storage/', $tempFilePath);
            return response(['message' => 'success', 'uploadedPath' => $tempFilePath], 200);
        }
        return response(['message' => 'SERVER ERROR possibly file not found'], 500);
    }

    public function save3dModelFromTemp(Save3dModelFromTemp $request){
        $validatedData = $request->validated();
        $companyUUID = $request->user()->company()->first()->uuid;
        $fileUUID = basename($validatedData['tempFilePath']);
        $newFilePath = 'public/'.$companyUUID.'/3dModels/'.$fileUUID;

        $tempFilePath = 'public/temp/'.$fileUUID;
        $isFileSaved = Storage::move($tempFilePath, $newFilePath);

        // File move successful
        if ($isFileSaved){
            $publicPath = str_replace('public/', 'storage/', $newFilePath);
            $product3DModel = Product3DModel::create([
                'company_id' => $request->user()->company_id,
                'name' => $validatedData['name'],
                'file_path' =>  $publicPath
            ]);
            return response(['message' => 'success'], 200);
        }
        return response(['message' => 'SERVER ERROR possibly file not found'], 500);
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
