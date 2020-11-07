<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function register(Request $request){
        $validatedData = $request->validate([
            'companyName' => ['required', 'max:50'],
            'firstname' => ['required', 'max:50'],
            'lastname' => ['required', 'max:50'],
            'email' => ['required', 'max:100'],
            'password' => ['required', 'max:30'],
        ]);

        $company = Company::create(['name' => $validatedData['companyName']]);
        dd($company);
    }
}
