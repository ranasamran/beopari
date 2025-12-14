<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'contact', 'address', 'logo', 'shopname',
    ];

    public function users() { return $this->hasMany(User::class); }
    public function products() { return $this->hasMany(Product::class); }
    public function orders() { return $this->hasMany(Order::class); }
    public function payees() { return $this->hasMany(Payee::class); }
    public function banks() { return $this->hasMany(Bank::class); }
    public function customers() { return $this->hasMany(Customer::class); }
    public function taxRates() { return $this->hasMany(TaxRate::class); }
    public function inventoryTransactions() { return $this->hasMany(InventoryTransaction::class); }
}
 