<?php

namespace App\Http\Controllers;

use App\Models\Texture;
use App\Models\TextureGroup;
use App\Models\TextureOption;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TesterController extends Controller
{
    public function test(Request $request)
    {
        $texture = Texture::find(1);
        $textureGroup = TextureGroup::find(1);
        $textureOption = TextureOption::find(1);
        dd($texture->textureGroups(), $texture->textureOptions(), $textureGroup->textures(), $textureOption->textures());
    }
}
