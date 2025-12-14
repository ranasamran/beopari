<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'number', 'name', 'balance', 'status', 'company_id',
    ];

    public function company() { return $this->belongsTo(Company::class); }
} 