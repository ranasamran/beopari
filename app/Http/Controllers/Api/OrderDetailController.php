<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrderDetail;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;

class OrderDetailController extends Controller
{
    public function index()
    {
        $orderIds = Order::where('company_id', Auth::user()->company_id)->pluck('id');
        $details = OrderDetail::whereIn('order_id', $orderIds)->get();
        return ApiResponse::success($details);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'name' => 'required|string',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
        ]);
        $order = Order::findOrFail($validated['order_id']);
        if ($order->company_id !== Auth::user()->company_id) abort(403, 'Unauthorized');
        $detail = OrderDetail::create($validated);
        return ApiResponse::success($detail, 'Order detail created', 201);
    }

    public function show(OrderDetail $orderDetail)
    {
        $this->authorizeCompany($orderDetail);
        return ApiResponse::success($orderDetail);
    }

    public function update(Request $request, OrderDetail $orderDetail)
    {
        $this->authorizeCompany($orderDetail);
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'quantity' => 'sometimes|integer',
            'price' => 'sometimes|numeric',
        ]);
        $orderDetail->update($validated);
        return ApiResponse::success($orderDetail);
    }

    public function destroy(OrderDetail $orderDetail)
    {
        $this->authorizeCompany($orderDetail);
        $orderDetail->delete();
        return ApiResponse::success(null, 'Deleted');
    }

    protected function authorizeCompany(OrderDetail $orderDetail)
    {
        $order = $orderDetail->order;
        if ($order->company_id !== Auth::user()->company_id) {
            abort(403, 'Unauthorized');
        }
    }
} 