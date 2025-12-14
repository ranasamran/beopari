<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Storage;
use App\Models\PayeeImage;

class PayeeController extends Controller
{
	public function index()
	{
		$payees = Payee::with('images')->where('company_id', Auth::user()->company_id)->get();
		return ApiResponse::success($payees);
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'name' => 'required|string',
			'contact' => 'required|string',
			'payable' => 'required|numeric',
			'type' => 'required|string',
			'date' => 'sometimes|date',
			'order_date' => 'sometimes|date',
			'delivery_date' => 'sometimes|date',
			'image' => 'sometimes|file|image|max:2048',
			'images' => 'sometimes|array',
			'images.*' => 'file|image|max:2048',
		]);
		$validated['company_id'] = Auth::user()->company_id;

		$payee = Payee::create($validated);

		if ($request->hasFile('image')) {
			$path = $request->file('image')->store('payees', 'public');
			PayeeImage::create(['payee_id' => $payee->id, 'path' => $path]);
		}
		if ($request->hasFile('images')) {
			foreach ($request->file('images') as $file) {
				$path = $file->store('payees', 'public');
				PayeeImage::create(['payee_id' => $payee->id, 'path' => $path]);
			}
		}

		$payee->load('images');
		return ApiResponse::success($payee, 'Payee created', 201);
	}

	public function show($id)
	{
        $payee = Payee::findOrFail($id);
        // dd($id,$payee->company, Auth::user());
		$this->authorizeCompany($payee);
		$payee->load('images');
		return ApiResponse::success($payee);
	}

	public function update(Request $request, $id)
    {
        $payee = Payee::findOrFail($id);
		$this->authorizeCompany($payee);
		$validated = $request->validate([
			'name' => 'sometimes|string',
			'contact' => 'sometimes|string',
			'payable' => 'sometimes|numeric',
			'type' => 'sometimes|string',
			'date' => 'sometimes|date',
			'order_date' => 'sometimes|date',
			'delivery_date' => 'sometimes|date',
			'image' => 'sometimes|file|image|max:2048',
			'images' => 'sometimes|array',
			'images.*' => 'file|image|max:2048',
			'remove_image' => 'sometimes|boolean',
			'remove_image_ids' => 'sometimes|array',
			'remove_image_ids.*' => 'integer',
		]);

		// legacy single image removal
		if ($request->boolean('remove_image') && $payee->image_path) {
			Storage::disk('public')->delete($payee->image_path);
			$validated['image_path'] = null;
		}

		// add new images
		if ($request->hasFile('image')) {
			$path = $request->file('image')->store('payees', 'public');
			PayeeImage::create(['payee_id' => $payee->id, 'path' => $path]);
		}
		if ($request->hasFile('images')) {
			foreach ($request->file('images') as $file) {
				$path = $file->store('payees', 'public');
				PayeeImage::create(['payee_id' => $payee->id, 'path' => $path]);
			}
		}

		// delete selected images
		if ($request->filled('remove_image_ids')) {
			$imageIds = array_map('intval', (array) $request->input('remove_image_ids', []));
			$images = PayeeImage::where('payee_id', $payee->id)
				->whereIn('id', $imageIds)
				->get();
			foreach ($images as $image) {
				Storage::disk('public')->delete($image->path);
				$image->delete();
			}
		}

		$payee->update($validated);
		$payee->load('images');
		return ApiResponse::success($payee);
	}

	public function destroy($id)
	{
        $payee = Payee::findOrFail($id);
		$this->authorizeCompany($payee);
		$payee->load('images');
		if ($payee->image_path) {
			Storage::disk('public')->delete($payee->image_path);
		}
		foreach ($payee->images as $image) {
			Storage::disk('public')->delete($image->path);
			$image->delete();
		}
		$payee->delete();
		return ApiResponse::success(null, 'Deleted');
	}

	protected function authorizeCompany(Payee $payee)
	{
		if ($payee->company_id !== Auth::user()->company_id) {
			abort(403, 'Unauthorized');
		}
	}
}
