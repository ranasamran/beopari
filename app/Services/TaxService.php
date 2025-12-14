<?php

namespace App\Services;

use App\Models\TaxRate;

class TaxService
{
    /**
     * Calculate tax for order items
     */
    public function calculateOrderTax(array $items, ?TaxRate $taxRate = null): array
    {
        // Calculate subtotal
        $subtotal = collect($items)->sum(function ($item) {
            return ($item['quantity'] ?? 0) * ($item['price'] ?? 0);
        });

        // Get default tax rate if not provided
        if (!$taxRate && auth()->check()) {
            $taxRate = TaxRate::where('company_id', auth()->user()->company_id)
                ->where('is_active', true)
                ->first();
        }

        // Calculate tax
        $taxAmount = $taxRate ? $taxRate->calculateTax($subtotal) : 0;

        return [
            'subtotal' => round($subtotal, 2),
            'tax_amount' => round($taxAmount, 2),
            'total' => round($subtotal + $taxAmount, 2),
            'tax_rate_id' => $taxRate?->id,
        ];
    }

    /**
     * Validate that calculated totals match provided totals
     */
    public function validateTotals(array $calculated, array $provided, float $tolerance = 0.01): bool
    {
        $subtotalMatch = abs($calculated['subtotal'] - ($provided['subtotal'] ?? 0)) <= $tolerance;
        $taxMatch = abs($calculated['tax_amount'] - ($provided['tax_amount'] ?? 0)) <= $tolerance;
        $totalMatch = abs($calculated['total'] - ($provided['total'] ?? 0)) <= $tolerance;

        return $subtotalMatch && $taxMatch && $totalMatch;
    }
}
