<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeshMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_3d_model_id',
        'material_name',
        'display_name',
        'texture_setting_wrap_s',
        'texture_setting_wrap_t',
        'texture_setting_repeat_u',
        'texture_setting_repeat_v'
    ];

    public function product3DModel(){
        return $this->belongsTo(Product3dModel::class);
    }

    public function textureCategories(){
        return $this->belongsToMany(TextureCategory::class, 'material_texture_categories');
    }
}
