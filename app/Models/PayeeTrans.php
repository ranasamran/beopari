<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayeeTrans extends Model
{
    use HasFactory;

    protected $table = 'payee_trans';
    protected $primaryKey = 'trans_id';

    protected $fillable = [
        'name', 'cus_id', 'amount', 'remain_amount', 'status', 'datetime', 'description',
    ];

    public function payee() { return $this->belongsTo(Payee::class, 'cus_id'); }
} 