<?php

namespace App\Http\Controllers;

use App\Http\Requests\Save3dModelFromTemp;
use App\Models\Product3dModel;
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

        $sessionId = $request->session()->getId();

        $tempFilePath = $request->file('file')->storeAs(
            'public/temp/'.$sessionId,
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
        $displayImgFilePath = $request->file('displayImgFile')->storeAs(
            'public/'.$companyUUID.'/3dModelDisplayImages/',
            (string)Str::orderedUuid().".".$request->file('displayImgFile')->getClientOriginalExtension());

        $modelFileUUID = basename($validatedData['tempFilePath']);
        $newFilePath = 'public/'.$companyUUID.'/3dModels/'.$modelFileUUID;

        $sessionId = $request->session()->getId();
        $tempFilePath = 'public/temp/'.$sessionId.'/'.$modelFileUUID;
        $isFileSaved = Storage::move($tempFilePath, $newFilePath);

        // File move successful
        if ($isFileSaved && $displayImgFilePath){
            $publicDisplayImgFilePath = str_replace('public/', 'storage/', $displayImgFilePath);

            $publicModelFilePath = str_replace('public/', 'storage/', $newFilePath);
            $product3dModel = Product3dModel::create([
                'company_id' => $request->user()->company_id,
                'name' => $validatedData['name'],
                'file_path' =>  $publicModelFilePath,
                'display_img_path' => $publicDisplayImgFilePath
            ]);
            Storage::deleteDirectory('public/temp/'.$sessionId);

            $meshMaterialNames = json_decode($validatedData['meshMaterialNames']);
            foreach ($meshMaterialNames as $meshMaterialName){
                $product3dModel->meshMaterials()->create([
                    'material_name' => $meshMaterialName,
                    'display_name' => $meshMaterialName
                ]);
            }

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
