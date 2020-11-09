<?php

namespace App\Http\Controllers;

use App\Http\Requests\Login;
use App\Http\Requests\Register;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function register(Register $request)
    {
        $validatedData = $request->validated();
        try {
            DB::transaction(function () use ($validatedData) {
                $company = Company::create(['name' => $validatedData['companyName']]);
                $user = $company->users()->create([
                    'firstname' => $validatedData['firstname'],
                    'lastname' => $validatedData['lastname'],
                    'email' => $validatedData['email'],
                    'password' => Hash::make($validatedData['password']),
                ]);
            });
        } catch (\Throwable $e) {
            return response(['success' => false, 'message' => 'SERVER ERROR'], 500);
        }

        return response(['success' => true, 'message' => 'Company and user created'], 200);
    }

    public function login(Login $request)
    {
        $validatedData = $request->validated();
        if (Auth::attempt(['email' => $validatedData['email'], 'password' => $validatedData['password']])) {
            $user = Auth::user();
            return response([
                'message' => 'Success'
            ], 200);
        } else {
            return response([
                'message' => 'Unauthorized'], 401);
        }
    }

    public function logout(Request $request){
        Auth::logout();
    }
}
