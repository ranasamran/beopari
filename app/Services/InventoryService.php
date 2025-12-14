<?php

namespace App\Services;

use App\Models\Product;
use App\Models\InventoryTransaction;
use Exception;

class InventoryService
{
    /**
     * Deduct stock for a sale
     */
    public function deductStock(Product $product, int $quantity, $reference, ?string $notes = null): InventoryTransaction
    {
        if ($product->quantity < $quantity) {
            throw new Exception("Insufficient stock for product '{$product->name}'. Available: {$product->quantity}, Requested: {$quantity}");
        }

        // Deduct quantity
        $product->decrement('quantity', $quantity);
        $product->refresh();

        // Record transaction
        return $this->recordTransaction(
            product: $product,
            type: 'sale',
            quantity: -$quantity,
            reference: $reference,
            notes: $notes
        );
    }

    /**
     * Add stock for a purchase/return
     */
    public function addStock(Product $product, int $quantity, $reference, ?string $notes = null): InventoryTransaction
    {
        $product->increment('quantity', $quantity);
        $product->refresh();

        return $this->recordTransaction(
            product: $product,
            type: $reference instanceof \App\Models\Order ? 'return' : 'purchase',
            quantity: $quantity,
            reference: $reference,
            notes: $notes
        );
    }

    /**
     * Adjust stock (manual adjustment)
     */
    public function adjustStock(Product $product, int $newQuantity, ?string $notes = null): InventoryTransaction
    {
        $difference = $newQuantity - $product->quantity;
        
        $product->update(['quantity' => $newQuantity]);

        return $this->recordTransaction(
            product: $product,
            type: 'adjustment',
            quantity: $difference,
            reference: null,
            notes: $notes ?? 'Manual stock adjustment'
        );
    }

    /**
     * Record an inventory transaction
     */
    protected function recordTransaction(
        Product $product,
        string $type,
        int $quantity,
        $reference = null,
        ?string $notes = null
    ): InventoryTransaction {
        return InventoryTransaction::create([
            'company_id' => $product->company_id,
            'product_id' => $product->id,
            'type' => $type,
            'quantity' => $quantity,
            'balance_after' => $product->quantity,
            'reference_type' => $reference ? get_class($reference) : null,
            'reference_id' => $reference?->id,
            'notes' => $notes,
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Get low stock products
     */
    public function getLowStockProducts(int $companyId)
    {
        return Product::where('company_id', $companyId)
            ->where('is_active', true)
            ->whereColumn('quantity', '<=', 'reorder_point')
            ->get();
    }
}
