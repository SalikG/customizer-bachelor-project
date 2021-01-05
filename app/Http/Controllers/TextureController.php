<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTexture;
use App\Models\Texture;
use App\Models\TextureCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                'company_id' => $request->user()->company_id,
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

    public function getAllTextures(Request $request){
        return response(['message' => 'success', 'textures' => $request->user()->textures()->get()->toArray()], 200);
    }

    public function addExistingTextureToCategory(Request $request, $modelId, $materialId, $textureCategoryId, $textureId){
        $attached = DB::table('texture_category_items')->select('*')->where([
            ['texture_category_id', '=', $textureCategoryId],
            ['texture_id', '=', $textureId],
        ])->first();
        if ($attached){
            return response(['message' => 'already added'], 202);
        }
        else{
            TextureCategory::find($textureCategoryId)->textures()->attach($textureId);
            $attached = DB::table('texture_category_items')->select('*')->where([
                ['texture_category_id', '=', $textureCategoryId],
                ['texture_id', '=', $textureId],
            ])->first();
            if ($attached){
                return response(['message' => 'success'], 200);
            }
        }
        return response(['message' => 'SERVER ERROR'], 500);
    }
}
