<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PayeeTrans;
use App\Models\Payee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;

class PayeeTransController extends Controller
{
    public function index()
    {
        $payeeIds = Payee::where('company_id', Auth::user()->company_id)->pluck('id');
        $trans = PayeeTrans::whereIn('cus_id', $payeeIds)->get();
        return ApiResponse::success($trans);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'cus_id' => 'required|exists:payees,id',
            'amount' => 'required|numeric',
            'remain_amount' => 'required|numeric',
            'status' => 'required|integer',
            'datetime' => 'required|date',
            'description' => 'nullable|string',
        ]);
        $payee = Payee::findOrFail($validated['cus_id']);
        if ($payee->company_id !== Auth::user()->company_id) abort(403, 'Unauthorized');
        $trans = PayeeTrans::create($validated);
        return ApiResponse::success($trans, 'Payee transaction created', 201);
    }

    public function show(PayeeTrans $payeeTrans)
    {
        $this->authorizeCompany($payeeTrans);
        return ApiResponse::success($payeeTrans);
    }

    public function update(Request $request, PayeeTrans $payeeTrans)
    {
        $this->authorizeCompany($payeeTrans);
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'amount' => 'sometimes|numeric',
            'remain_amount' => 'sometimes|numeric',
            'status' => 'sometimes|integer',
            'datetime' => 'sometimes|date',
            'description' => 'nullable|string',
        ]);
        $payeeTrans->update($validated);
        return ApiResponse::success($payeeTrans);
    }

    public function destroy(PayeeTrans $payeeTrans)
    {
        $this->authorizeCompany($payeeTrans);
        $payeeTrans->delete();
        return ApiResponse::success(null, 'Deleted');
    }

    protected function authorizeCompany(PayeeTrans $payeeTrans)
    {
        $payee = $payeeTrans->payee;
        if ($payee->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }
    }
} 