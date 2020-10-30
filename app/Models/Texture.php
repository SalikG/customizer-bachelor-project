<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Texture extends Model
{
    use HasFactory;

    public function textureCategories()
    {
        return $this->belongsToMany(TextureCategory::class, 'texture_category_items')->get();
    }
}
