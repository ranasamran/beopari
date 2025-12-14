<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TaxRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;

class TaxRateController extends Controller
{
    public function index()
    {
        $taxRates = TaxRate::where('company_id', Auth::user()->company_id)->get();
        return ApiResponse::success($taxRates);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'type' => 'required|in:percentage,fixed',
            'is_active' => 'sometimes|boolean',
            'description' => 'nullable|string',
        ]);

        $validated['company_id'] = Auth::user()->company_id;
        $taxRate = TaxRate::create($validated);

        return ApiResponse::success($taxRate, 'Tax rate created', 201);
    }

    public function show($id)
    {
        $taxRate = TaxRate::where('company_id', Auth::user()->company_id)->findOrFail($id);
        return ApiResponse::success($taxRate);
    }

    public function update(Request $request, $id)
    {
        $taxRate = TaxRate::where('company_id', Auth::user()->company_id)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'rate' => 'sometimes|numeric|min:0|max:100',
            'type' => 'sometimes|in:percentage,fixed',
            'is_active' => 'sometimes|boolean',
            'description' => 'sometimes|nullable|string',
        ]);

        $taxRate->update($validated);
        return ApiResponse::success($taxRate);
    }

    public function destroy($id)
    {
        $taxRate = TaxRate::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $taxRate->delete();
        return ApiResponse::success(null, 'Tax rate deleted');
    }
}
