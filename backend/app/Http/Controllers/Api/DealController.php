<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use Illuminate\Http\Request;

class DealController extends Controller
{
    public function index()
    {
        return response()->json(Deal::with(['contact', 'product'])->latest()->get());
    }

    public function store(Request $request)
    {
        abort_if($request->user()->role === 'admin', 403, 'Admin cannot create deals.');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'stage' => 'nullable|string',
            'contact_id' => 'required|exists:contacts,id',
            'product_id' => 'nullable|exists:products,id'
        ]);

        $deal = Deal::create($validated);
        return response()->json($deal->load(['contact', 'product']), 201);
    }

    public function show(Deal $deal)
    {
        return response()->json($deal->load(['contact', 'product']));
    }

    public function update(Request $request, Deal $deal)
    {
        abort_if($request->user()->role === 'admin', 403, 'Admin cannot update deals.');
        
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'amount' => 'sometimes|required|numeric|min:0',
            'stage' => 'nullable|string',
            'contact_id' => 'sometimes|required|exists:contacts,id',
            'product_id' => 'nullable|exists:products,id'
        ]);

        if (isset($validated['stage']) && $validated['stage'] === 'closed_won') {
            if ($deal->discount_status === 'pending') {
                abort(403, 'Cannot close won while discount is still pending approval.');
            }
        }

        $oldStage = $deal->stage;
        $deal->update($validated);

        if ($oldStage !== 'closed_won' && $deal->stage === 'closed_won') {
            event(new \App\Events\DealClosedWon($deal));
        }

        return response()->json($deal->load(['contact', 'product']));
    }

    public function destroy(Request $request, Deal $deal)
    {
        abort_if($request->user()->role === 'admin', 403, 'Admin cannot delete deals.');
        
        $deal->delete();
        return response()->json(null, 204);
    }

    public function requestDiscount(Request $request, Deal $deal)
    {
        abort_if($request->user()->role !== 'sales', 403, 'Only Sales can request discounts.');

        $validated = $request->validate([
            'discount_amount' => 'required|numeric|min:0|max:'.$deal->amount
        ]);

        $deal->update([
            'discount_amount' => $validated['discount_amount'],
            'discount_status' => 'pending'
        ]);

        return response()->json($deal->load(['contact', 'product']));
    }

    public function approveDiscount(Request $request, Deal $deal)
    {
        abort_if($request->user()->role !== 'pimpinan_sales', 403, 'Only Pimpinan Sales can approve discounts.');

        $deal->update([
            'discount_status' => 'approved'
        ]);

        return response()->json($deal->load(['contact', 'product']));
    }

    public function rejectDiscount(Request $request, Deal $deal)
    {
        abort_if($request->user()->role !== 'pimpinan_sales', 403, 'Only Pimpinan Sales can reject discounts.');

        $deal->update([
            'discount_status' => 'rejected'
        ]);

        return response()->json($deal->load(['contact', 'product']));
    }
}
