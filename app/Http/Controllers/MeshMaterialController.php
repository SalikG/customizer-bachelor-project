<?php

namespace App\Http\Controllers;

use App\Models\Product3dModel;
use Illuminate\Http\Request;

class MeshMaterialController extends Controller
{
    public function getMeshMaterials(Request $request, $id){
        $meshMaterials = Product3dModel::find($id)->meshMaterials()->get()->toArray();
        return response($meshMaterials, 200);
    }
}
