<?php

namespace App\Http\Controllers;

use App\Models\Product3dModel;
use Illuminate\Http\Request;

class MeshMaterialController extends Controller
{
    public function getMeshMaterials(Request $request, $modelId){
        $meshMaterials = Product3dModel::find($modelId)->meshMaterials()->with(['textureCategories', 'textureCategories.textures'])->get()->toArray();
        return response($meshMaterials, 200);
    }
}
