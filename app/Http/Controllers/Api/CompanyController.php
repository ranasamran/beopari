<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\ApiResponse;

class CompanyController extends Controller
{
    public function show()
    {
        $company = Auth::user()->company;
        return ApiResponse::success($company);
    }

    public function update(Request $request)
    {
        $company = Auth::user()->company;
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'contact' => 'nullable|string',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|max:2048', // Accept image file
            'shopname' => 'nullable|string',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('companies', 'public');
            $validated['logo'] = \Storage::url($path);
        }

        \DB::transaction(function () use ($company, $validated) {
            $company->update($validated);
        });

        return \App\Helpers\ApiResponse::success($company->fresh(), 'Company updated');
    }
} 