<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payee extends Model
{
	use HasFactory;

	protected $fillable = [
		'name', 'contact', 'payable', 'type', 'company_id', 'image_path', 'date', 'order_date', 'delivery_date',
	];

	protected $casts = [
		'date' => 'date',
		'order_date' => 'date',
		'delivery_date' => 'date',
	];

	protected $appends = ['image_url'];

	public function company() { return $this->belongsTo(Company::class); }

	public function images()
	{
		return $this->hasMany(PayeeImage::class);
	}

	public function getImageUrlAttribute()
	{
		if ($this->image_path) {
			return asset('storage/' . $this->image_path);
		}
		$imagesRelation = $this->relationLoaded('images') ? $this->getRelation('images') : null;
		$first = $imagesRelation ? $imagesRelation->first() : $this->images()->first();
		return $first ? $first->url : null;
	}
} 