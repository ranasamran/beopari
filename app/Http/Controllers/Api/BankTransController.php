<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BankTrans;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;

class BankTransController extends Controller
{
    public function index()
    {
        $bankIds = Bank::where('company_id', Auth::user()->company_id)->pluck('id');
        $trans = BankTrans::whereIn('bank_id', $bankIds)->get();
        return ApiResponse::success($trans);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_id' => 'required|exists:banks,id',
            'name' => 'required|string',
            'cus_id' => 'required|integer',
            'amount' => 'required|numeric',
            'status' => 'required|integer',
            'datetime' => 'required|date',
            'description' => 'nullable|string',
        ]);
        $bank = Bank::findOrFail($validated['bank_id']);
        if ($bank->company_id !== Auth::user()->company_id) abort(403, 'Unauthorized');
        $trans = BankTrans::create($validated);
        return ApiResponse::success($trans, 'Bank transaction created', 201);
    }

    public function show(BankTrans $bankTrans)
    {
        $this->authorizeCompany($bankTrans);
        return ApiResponse::success($bankTrans);
    }

    public function update(Request $request, BankTrans $bankTrans)
    {
        $this->authorizeCompany($bankTrans);
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'cus_id' => 'sometimes|integer',
            'amount' => 'sometimes|numeric',
            'status' => 'sometimes|integer',
            'datetime' => 'sometimes|date',
            'description' => 'nullable|string',
        ]);
        $bankTrans->update($validated);
        return ApiResponse::success($bankTrans);
    }

    public function destroy(BankTrans $bankTrans)
    {
        $this->authorizeCompany($bankTrans);
        $bankTrans->delete();
        return ApiResponse::success(null, 'Deleted');
    }

    protected function authorizeCompany(BankTrans $bankTrans)
    {
        $bank = $bankTrans->bank;
        if ($bank->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }
    }
} 