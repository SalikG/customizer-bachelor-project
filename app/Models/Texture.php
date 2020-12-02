<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Texture extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'file_path', 'icon_path'];

    public function textureCategories()
    {
        return $this->belongsToMany(TextureCategory::class, 'texture_category_items')->get();
    }
}
