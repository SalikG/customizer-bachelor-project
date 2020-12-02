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
        $result = TextureCategory::find($result->id)->load(['textures'])->toJson();
        return response(['message' => 'success', 'data' => $result], 200);
    }

    public function updateTextureCategory(CreateTextureCategory $request, $modelId, $materialId, $textureCategoryId){
        $validatedData = $request->validated();
        $textureCategory = TextureCategory::find($textureCategoryId);
        $textureCategory->name = $validatedData['name'];
        $saved = $textureCategory->save();

        if ($saved){
            return response(['message' => 'success'], 200);
        }
        return response(['message' => 'SERVER ERROR'], 500);
    }
}
