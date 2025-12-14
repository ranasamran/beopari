<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\ApiResponse;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|confirmed',
            'company_name' => 'required|string',
            'company_contact' => 'nullable|string',
            'company_address' => 'nullable|string',
            'company_logo' => 'nullable|string',
            'company_name' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated) {
            $company = Company::create([
                'name' => $validated['company_name'],
                'contact' => $validated['company_contact'] ?? null,
                'address' => $validated['company_address'] ?? null,
                'logo' => $validated['company_logo'] ?? null,
                'shopname' => $validated['company_shopname'] ?? null,
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'company_id' => $company->id,
            ]);

            // Assign Admin role to the creator
            $user->assignRole('admin');

            $token = $user->createToken('auth_token')->plainTextToken;

            return ApiResponse::success([
                'user' => $user,
                'company' => $company,
                'token' => $token,
            ], 'Registration successful', 201);
        });
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $credentials['email'])->first();
        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return ApiResponse::error('Invalid credentials', 401);
        }
        $token = $user->createToken('api')->plainTextToken;
        return ApiResponse::success([
            'user' => $user,
            'company' => $user->company,
            'token' => $token,
        ], 'Login successful');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return ApiResponse::success(null, 'Logged out');
    }
} 