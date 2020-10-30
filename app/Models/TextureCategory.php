<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TextureCategory extends Model
{
    use HasFactory;

    public function textures()
    {
        return $this->belongsToMany(Texture::class, 'texture_category_items');
    }

    public function meshMaterials()
    {
        return $this->belongsToMany(MeshMaterial::class, 'material_texture_categories');
    }
}
