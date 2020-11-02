<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeshMaterial extends Model
{
    use HasFactory;

    public function product3DModel(){
        return $this->belongsTo(Product3DModel::class);
    }

    public function textureCategories(){
        return $this->belongsToMany(TextureCategory::class, 'material_texture_categories');
    }
}
