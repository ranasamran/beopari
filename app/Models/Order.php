<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'gross_total', 'discount', 'total_paid', 'balance', 'tyre', 'customer', 'number', 'payable', 'company_id',
        'customer_id', 'status', 'subtotal', 'tax_amount', 'tax_rate_id', 'created_by', 'updated_by',
    ];

    protected $casts = [
        'gross_total' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_paid' => 'decimal:2',
        'balance' => 'decimal:2',
        'payable' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
    ];

    public function company() { return $this->belongsTo(Company::class); }

    public function customer() { return $this->belongsTo(Customer::class); }

    public function taxRate() { return $this->belongsTo(TaxRate::class); }

    public function orderDetails() {
        return $this->hasMany(OrderDetail::class);
    }

    public function payments() {
        return $this->hasMany(Payment::class);
    }

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater() {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
 