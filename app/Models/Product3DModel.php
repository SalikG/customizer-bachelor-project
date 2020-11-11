<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product3DModel extends Model
{
    use HasFactory;


    protected $fillable = ['company_id', 'name', 'file_path'];

    public function company(){
        return $this->belongsTo(Company::class);
    }

    public function meshMaterials(){
        return $this->hasMany(MeshMaterial::class);
    }
}
