<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'rate',
        'type',
        'is_active',
        'description',
    ];

    protected $casts = [
        'rate' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Business logic
    public function calculateTax(float $amount): float
    {
        if ($this->type === 'percentage') {
            return round($amount * ($this->rate / 100), 2);
        }
        
        return (float) $this->rate;
    }
}
