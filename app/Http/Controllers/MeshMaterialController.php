<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateMaterial;
use App\Models\MeshMaterial;
use App\Models\Product3dModel;
use Illuminate\Http\Request;

class MeshMaterialController extends Controller
{
    public function getMeshMaterials(Request $request, $modelId){
        $meshMaterials = Product3dModel::find($modelId)->meshMaterials()->with(['textureCategories', 'textureCategories.textures'])->get()->toArray();
        return response($meshMaterials, 200);
    }

    public function updateMaterial(UpdateMaterial $request, $modelId, $materialId){
        $validatedData = $request->validated();
        $material = MeshMaterial::find($materialId);

        if (array_key_exists('scaling', $validatedData)){
            $material->texture_setting_repeat_u = $validatedData['scaling'];
            $material->texture_setting_repeat_v = $validatedData['scaling'];
        }
        if (array_key_exists('display_name', $validatedData)){
            $material->display_name = $validatedData['display_name'];
        }
        $saved = $material->save();
        if ($saved){
            return response(['message' => 'success'], 200);
        }
        return response(['message' => 'SERVER ERROR'], 500);
    }
}
