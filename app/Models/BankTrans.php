<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankTrans extends Model
{
    use HasFactory;

    protected $table = 'bank_trans';
    protected $primaryKey = 'trans_id';

    protected $fillable = [
        'bank_id', 'name', 'cus_id', 'amount', 'status', 'datetime', 'description',
    ];

    public function bank() { return $this->belongsTo(Bank::class); }
    public function payee() { return $this->belongsTo(Payee::class, 'cus_id'); }
} 