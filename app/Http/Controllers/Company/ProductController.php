<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $company = auth()->user()->company;
        $products = $company->products;
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
        ]);

        $company = auth()->user()->company;
        
        $imagePath = $request->file('image')->store('images', 'public');

        $product = $company->products()->create([
            'image' => $imagePath,
            'description' => $request->description,
            'price' => $request->price,
        ]);

        return response()->json($product, 201);
    }

    public function show($id)
    {
        $company = auth()->user()->company;
        $product = $company->products()->findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $company = auth()->user()->company;
        $product = $company->products()->findOrFail($id);

        $request->validate([
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
        ]);

        $data = $request->except('image');
        
        if ($request->hasFile('image')) {
            // Delete old image
            Storage::disk('public')->delete($product->image);
            
            // Store new image
            $data['image'] = $request->file('image')->store('images', 'public');
        }

        $product->update($data);

        return response()->json($product);
    }

    public function destroy($id)
    {
        $company = auth()->user()->company;
        $product = $company->products()->findOrFail($id);
        
        // Delete associated image
        Storage::disk('public')->delete($product->image);
        
        $product->delete();

        return response()->json(null, 204);
    }
}