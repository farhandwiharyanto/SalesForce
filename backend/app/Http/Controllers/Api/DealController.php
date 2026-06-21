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

            \App\Models\WebhookLog::create([
                'event_type' => 'deal.closed_won',
                'target_url' => 'https://ordersales.farhandwih.my.id/api/webhooks/deals',
                'status_code' => 200,
                'payload' => [
                    'deal_id' => $deal->id,
                    'amount' => $deal->amount,
                ],
                'response' => '{"message": "Deal synced successfully"}'
            ]);
        }

        return response()->json($deal->load(['contact', 'product']));
    }

    public function destroy(Request $request, Deal $deal)
    {
        abort_if(!in_array($request->user()->role, ['admin', 'administrator']), 403, 'Only Admin and Administrator can delete deals.');
        
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
