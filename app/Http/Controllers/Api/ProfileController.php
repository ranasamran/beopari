<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Helpers\ApiResponse;

class ProfileController extends Controller
{
    public function show()
    {
        return ApiResponse::success(Auth::user());
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
        ]);
        DB::transaction(function () use ($user, $validated) {
            $user->update($validated);
        });
        return ApiResponse::success($user->fresh(), 'Profile updated');
    }

    public function changePassword(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|confirmed',
        ]);
        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 422);
        }
        DB::transaction(function () use ($user, $validated) {
            $user->update(['password' => Hash::make($validated['password'])]);
        });
        return ApiResponse::success(null, 'Password updated successfully');
    }
} 