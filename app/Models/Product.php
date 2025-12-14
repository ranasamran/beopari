<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'quantity', 'cost_price', 'retail_price', 'margin', 'description', 'company_id',
        'discounted_price', 'net_weight', 'category', 'note', 'type',
        'sku', 'barcode', 'upc', 'reorder_point', 'is_active',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'cost_price' => 'decimal:2',
        'retail_price' => 'decimal:2',
        'margin' => 'decimal:2',
        'discounted_price' => 'decimal:2',
        'reorder_point' => 'integer',
        'is_active' => 'boolean',
    ];

    public function company() { return $this->belongsTo(Company::class); }

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function inventoryTransactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->reorder_point;
    }
}
 