<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTexture;
use App\Models\Texture;
use App\Models\TextureCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TextureController extends Controller
{
    public function createTexture(CreateTexture $request, $modelId, $materialId, $textureCategoryId){
        $validatedData = $request->validated();
        $companyUUID = $request->user()->company()->first()->uuid;

        $textureFile = $validatedData['textureFile']->storeAs(
            'public/'.$companyUUID.'/textures/',
            (string)Str::orderedUuid().'.'.$validatedData['textureFile']->getClientOriginalExtension());

        $iconFile = $validatedData['iconFile']->storeAs(
            'public/'.$companyUUID.'/textureIcons/',
            (string)Str::orderedUuid().'.'.$validatedData['iconFile']->getClientOriginalExtension());

        if($textureFile && $iconFile){

            $result = TextureCategory::find($textureCategoryId)->textures()->create([
                'name' =>   $validatedData['name'],
                'description' => $validatedData['description'],
                'file_path' => str_replace('public/', 'storage/', $textureFile),
                'icon_path' => str_replace('public/', 'storage/', $iconFile)
            ]);

            $result = Texture::find($result->id)->toJson();

            return response(['message' => 'success', 'data' => $result], 200);
        }

        return response(['message' => 'SERVER ERROR possibly file not found'], 500);
    }
}
