<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\TaxRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Helpers\ApiResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\InventoryService;
use App\Services\TaxService;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    protected $inventoryService;
    protected $taxService;

    public function __construct(InventoryService $inventoryService, TaxService $taxService)
    {
        $this->inventoryService = $inventoryService;
        $this->taxService = $taxService;
    }

    public function index()
    {
        $orders = Order::where('company_id', Auth::user()->company_id)
            ->with(['orderDetails', 'customer', 'payments'])
            ->orderBy('created_at', 'desc')
            ->get();
        return ApiResponse::success($orders);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'discount' => 'nullable|numeric|min:0',
            'tax_rate_id' => 'nullable|exists:tax_rates,id',
            'details' => 'required|array|min:1',
            'details.*.product_id' => 'required|integer|exists:products,id',
            'details.*.quantity' => 'required|integer|min:1',
            'details.*.price' => 'required|numeric|min:0',
            'payments' => 'required|array|min:1',
            'payments.*.method' => 'required|in:cash,card,mobile_money,check,store_credit',
            'payments.*.amount' => 'required|numeric|min:0',
            'payments.*.reference_number' => 'nullable|string',
            'payments.*.notes' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            // Calculate tax
            $taxCalc = $this->taxService->calculateOrderTax(
               $validated['details'],
                isset($validated['tax_rate_id']) ? TaxRate::find($validated['tax_rate_id']) : null
            );

            $discount = $validated['discount'] ?? 0;
            $grossTotal = $taxCalc['subtotal']; 
            $total = $taxCalc['total'] - $discount;
            $totalPaid = collect($validated['payments'])->sum('amount');

            // Validate payment covers total
            if ($totalPaid < $total) {
                throw ValidationException::withMessages([
                    'payments' => "Payment amount ($totalPaid) must equal or exceed order total ($total)"
                ]);
            }

            // Fetch customer for legacy support
            $customerInfo = \App\Models\Customer::findOrFail($validated['customer_id']);

            // Create order
            $order = Order::create([
                'company_id' => Auth::user()->company_id,
                'customer_id' => $validated['customer_id'],
                'customer' => $customerInfo->name, // Legacy field requirement
                'number' => $this->generateOrderNumber(),
                'status' => 'completed',
                'subtotal' => $taxCalc['subtotal'],
                'tax_amount' => $taxCalc['tax_amount'],
                'tax_rate_id' => $taxCalc['tax_rate_id'],
                'discount' => $discount,
                'gross_total' => $grossTotal,
                'payable' => $total,
                'total_paid' => $totalPaid,
                'balance' => $totalPaid - $total,
                'created_by' => Auth::id(),
                'tyre' => 'Standard', // Legacy field requirement
            ]);

            // Create order details & deduct inventory
            foreach ($validated['details'] as $detail) {
                $product = Product::findOrFail($detail['product_id']);

                // Check product belongs to same company
                if ($product->company_id !== Auth::user()->company_id) {
                    throw ValidationException::withMessages([
                        'details' => 'Product does not belong to your company'
                    ]);
                }

                // Deduct stock (will throw exception if insufficient)
                $this->inventoryService->deductStock(
                    $product,
                    $detail['quantity'],
                    $order,
                    "Sale - Order #{$order->number}"
                );

                $order->orderDetails()->create([
                    'product_id' => $detail['product_id'],
                    'name' => $product->name,
                    'quantity' => $detail['quantity'],
                    'price' => $detail['price'],
                ]);
            }

            // Record payments
            foreach ($validated['payments'] as $payment) {
                // Map validation key 'method' to database column 'payment_method'
                $payment['payment_method'] = $payment['method'];
                unset($payment['method']);
                
                $order->payments()->create($payment);
            }

            return ApiResponse::success(
                $order->load(['orderDetails.product', 'payments', 'customer']),
                'Order created successfully',
                201
            );
        });
    }

    public function show($id)
    {
        $order = Order::where('company_id', Auth::user()->company_id)
            ->with(['orderDetails.product', 'customer', 'payments', 'taxRate'])
            ->findOrFail($id);
        return ApiResponse::success($order);
    }

    public function update(Request $request, $id)
    {
        $order = Order::where('company_id', Auth::user()->company_id)->findOrFail($id);

        // Only allow updating pending orders
        if ($order->status !== 'pending') {
            return ApiResponse::error('Only pending orders can be updated', 400);
        }

        $validated = $request->validate([
            'customer_id' => 'sometimes|exists:customers,id',
            'discount' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:pending,completed,void',
        ]);

        $validated['updated_by'] = Auth::id();
        $order->update($validated);

        return ApiResponse::success($order->load(['orderDetails', 'payments', 'customer']));
    }

    public function destroy($id)
    {
        $order = Order::where('company_id', Auth::user()->company_id)->findOrFail($id);
        
        // Soft delete only
        $order->delete();
        
        return ApiResponse::success(null, 'Order deleted');
    }

    public function void($id)
    {
        $order = Order::where('company_id', Auth::user()->company_id)->findOrFail($id);

        if ($order->status === 'void') {
            return ApiResponse::error('Order is already voided', 400);
        }

        return DB::transaction(function () use ($order) {
            // Return stock to inventory
            foreach ($order->orderDetails as $detail) {
                $product = Product::find($detail->product_id);
                if ($product) {
                    $this->inventoryService->addStock(
                        $product,
                        $detail->quantity,
                        $order,
                        "Void - Order #{$order->number}"
                    );
                }
            }

            // Update order status
            $order->update([
                'status' => 'void',
                'updated_by' => Auth::id(),
            ]);

            return ApiResponse::success($order, 'Order voided successfully');
        });
    }

    public function downloadPdf($id)
    {
        $order = Order::where('company_id', Auth::user()->company_id)
            ->with(['orderDetails', 'customer', 'company'])
            ->findOrFail($id);
        
        $pdf = Pdf::loadView('orders.pdf', compact('order'));
        return $pdf->download('order_' . $order->number . '.pdf');
    }

    protected function generateOrderNumber(): string
    {
        $lastOrder = Order::where('company_id', Auth::user()->company_id)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastOrder ? ((int) substr($lastOrder->number, -6)) + 1 : 1;
        
        return 'ORD-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}