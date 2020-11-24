<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product3dModel extends Model
{
    use HasFactory;

    protected $table = 'product_3d_models';
    protected $fillable = ['company_id', 'name', 'file_path', 'display_img_path'];

    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function meshMaterials(){
        return $this->hasMany(MeshMaterial::class, 'product_3d_model_id', 'id');
    }
}
