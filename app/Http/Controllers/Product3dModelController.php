<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateModel;
use App\Models\Product3dModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Product3dModelController extends Controller
{
    public function get3dModels(Request $request){
        $models = $request->user()->product3dModels()->get()->toArray();
        return response($models, 200);
    }

    public function updateModel(UpdateModel $request, $modelId){
        $validatedData = $request->validated();
        $model = Product3dModel::find($modelId);

        if (array_key_exists('name', $validatedData)){
            $model->name = $validatedData['name'];
        }

        $saved = $model->save();
        if ($saved){
           return response(['message' => 'success'], 200);
        }

        return response(['message' => 'SERVER ERROR'], 500);
    }

    public function deleteModel(Request $request, $modelId){
        $product3DModel = Product3dModel::find($modelId);
        $companyUUID = $request->user()->company()->first()->uuid;
        $success = Product3dModel::destroy($modelId);
        if ($success){
            Storage::delete(str_replace('storage/', 'public/', $product3DModel->file_path));
            Storage::delete(str_replace('storage/', 'public/', $product3DModel->display_img_path));
            return response(['message' => 'success'], 200);
        }
        return response(['message' => 'SERVER ERROR'], 500);
    }
}
