<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;

class BankController extends Controller
{
    public function index()
    {
        $banks = Bank::where('company_id', Auth::user()->company_id)->get();
        return ApiResponse::success($banks);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'number' => 'required|string',
            'name' => 'required|string',
            'balance' => 'required|numeric',
            'status' => 'required|integer',
        ]);
        $validated['company_id'] = Auth::user()->company_id;
        $bank = Bank::create($validated);
        return ApiResponse::success($bank, 'Bank created', 201);
    }

    public function show(Bank $bank)
    {
        $this->authorizeCompany($bank);
        return ApiResponse::success($bank);
    }

    public function update(Request $request, Bank $bank)
    {
        $this->authorizeCompany($bank);
        $validated = $request->validate([
            'title' => 'sometimes|string',
            'number' => 'sometimes|string',
            'name' => 'sometimes|string',
            'balance' => 'sometimes|numeric',
            'status' => 'sometimes|integer',
        ]);
        $bank->update($validated);
        return ApiResponse::success($bank);
    }

    public function destroy(Bank $bank)
    {
        $this->authorizeCompany($bank);
        $bank->delete();
        return ApiResponse::success(null, 'Deleted');
    }

    protected function authorizeCompany(Bank $bank)
    {
        if ($bank->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }
    }
} 