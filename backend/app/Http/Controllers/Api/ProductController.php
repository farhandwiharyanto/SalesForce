<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json(Product::latest()->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0'
        ]);

        $product = Product::create($validated);
        return response()->json($product, 201);
    }

    public function show(Product $product)
    {
        return response()->json($product);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0'
        ]);

        $product->update($validated);
        return response()->json($product);
    }

    public function destroy(Request $request, Product $product)
    {
        abort_if($request->user() && !in_array($request->user()->role, ['admin', 'administrator']), 403, 'Only Admin and Administrator can delete products.');
        $product->delete();
        return response()->json(null, 204);
    }

    public function bulkStore(Request $request)
    {
        $data = $request->input('data');
        if (!is_array($data) || empty($data)) {
            return response()->json(['message' => 'No data provided'], 400);
        }

        $inserted = 0;
        foreach ($data as $item) {
            $name = $item['Name'] ?? null;
            if ($name) {
                // Generate product number
                $count = \App\Models\Product::count() + 1;
                $productNumber = $item['Product Number'] ?? 'PRD-' . str_pad($count, 4, '0', STR_PAD_LEFT);

                Product::create([
                    'product_number' => $productNumber,
                    'name' => $name,
                    'description' => $item['Description'] ?? '',
                    'price' => floatval(str_replace(',', '', $item['Price'] ?? 0)),
                ]);
                $inserted++;
            }
        }

        return response()->json(['message' => "Successfully imported {$inserted} products."]);
    }
}
