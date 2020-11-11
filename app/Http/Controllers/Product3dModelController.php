<?php

namespace App\Http\Controllers;

use App\Models\Product3DModel;
use Illuminate\Http\Request;

class Product3dModelController extends Controller
{
    public function get3dModelList(Request $request){
        $models = $request->user()->product3dModels()->get()->toArray();
        return response($models, 200);
    }
}
