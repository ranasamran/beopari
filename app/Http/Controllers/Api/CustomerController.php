<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::where('company_id', Auth::user()->company_id)->get();
        return ApiResponse::success($customers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'tax_number' => 'nullable|string|max:100',
        ]);

        $validated['company_id'] = Auth::user()->company_id;
        $customer = Customer::create($validated);

        return ApiResponse::success($customer, 'Customer created', 201);
    }

    public function show($id)
    {
        $customer = Customer::where('company_id', Auth::user()->company_id)
            ->with('orders')
            ->findOrFail($id);
        return ApiResponse::success($customer);
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::where('company_id', Auth::user()->company_id)->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|nullable|email|max:255',
            'phone' => 'sometimes|nullable|string|max:50',
            'address' => 'sometimes|nullable|string',
            'tax_number' => 'sometimes|nullable|string|max:100',
            'loyalty_points' => 'sometimes|integer|min:0',
        ]);

        $customer->update($validated);
        return ApiResponse::success($customer);
    }

    public function destroy($id)
    {
        $customer = Customer::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $customer->delete();
        return ApiResponse::success(null, 'Customer deleted');
    }
}
