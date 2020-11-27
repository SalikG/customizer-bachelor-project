<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTextureCategory;
use App\Models\MeshMaterial;
use App\Models\TextureCategory;
use Illuminate\Http\Request;

class TextureCategoryController extends Controller
{
    public function createTextureCategory(CreateTextureCategory $request, $modelId, $materialId){
        $validatedData = $request->validated();
        $result =  MeshMaterial::find($materialId)->textureCategories()->create(['name' => $validatedData['name']]);
//        TextureCategory::create([
//            'name'=> $validatedData['name']
//        ]);
        $result = TextureCategory::find($result->id)->load(['textures'])->toJson();
//            MeshMaterial::find($result->id)->with(['textures'])->get()->toArray());
        return response(['message' => 'success', 'data' => $result], 200);
    }
}
