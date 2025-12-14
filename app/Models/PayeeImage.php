<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayeeImage extends Model
{
	use HasFactory;

	protected $fillable = [
		'payee_id', 'path',
	];

	protected $appends = ['url'];
	protected $hidden = ['path', 'payee_id', 'created_at', 'updated_at'];

	public function payee()
	{
		return $this->belongsTo(Payee::class);
	}

	public function getUrlAttribute()
	{
		return $this->path ? asset('storage/' . $this->path) : null;
	}
} 