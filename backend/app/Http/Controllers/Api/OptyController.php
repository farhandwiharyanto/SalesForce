<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Opty;
use Illuminate\Http\Request;

class OptyController extends Controller
{
    public function index()
    {
        return response()->json(Opty::with(['customer', 'product', 'assignee', 'owner'])->latest()->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'assigned_to' => 'nullable|exists:users,id',
            'owner_id' => 'required|exists:users,id',
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'nullable|exists:products,id',
            'target_close_date' => 'nullable|date',
            'customer_expected_rfs' => 'nullable|date',
            'request_type' => 'nullable|string',
            'stage' => 'nullable|string',
            'confidence_level' => 'nullable|string',
            'estimated_value_otc' => 'nullable|numeric|min:0',
            'estimated_value_mrc' => 'nullable|numeric|min:0',
            'is_converted_from_lead' => 'boolean'
        ]);

        $validated['opportunity_number'] = 'POT' . rand(100000000, 999999999);
        
        if (!isset($validated['stage'])) {
            $validated['stage'] = 'Prospect and Analysis';
        }

        $opty = Opty::create($validated);
        return response()->json($opty->load(['customer', 'product', 'assignee', 'owner']), 201);
    }

    public function show(Opty $opty)
    {
        return response()->json($opty->load(['customer', 'product', 'assignee', 'owner']));
    }

    public function update(Request $request, Opty $opty)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'assigned_to' => 'nullable|exists:users,id',
            'owner_id' => 'sometimes|required|exists:users,id',
            'customer_id' => 'sometimes|required|exists:customers,id',
            'product_id' => 'nullable|exists:products,id',
            'target_close_date' => 'nullable|date',
            'customer_expected_rfs' => 'nullable|date',
            'request_type' => 'nullable|string',
            'stage' => 'nullable|string',
            'confidence_level' => 'nullable|string',
            'estimated_value_otc' => 'nullable|numeric|min:0',
            'estimated_value_mrc' => 'nullable|numeric|min:0',
            'is_converted_from_lead' => 'boolean'
        ]);

        if (isset($validated['stage']) && $validated['stage'] === 'Closed Won') {
            if ($opty->discount_status === 'pending') {
                abort(403, 'Cannot close won while discount is still pending approval.');
            }
        }

        $oldStage = $opty->stage;
        $opty->update($validated);

        if ($oldStage !== 'Closed Won' && $opty->stage === 'Closed Won') {
            event(new \App\Events\OptyClosedWon($opty));

            \App\Models\WebhookLog::create([
                'event_type' => 'opty.closed_won',
                'target_url' => 'https://ordersales.farhandwih.my.id/api/webhooks/optys',
                'status_code' => 200,
                'payload' => [
                    'opty_id' => $opty->id,
                    'amount' => $opty->estimated_value_otc ?? 0,
                ],
                'response' => '{"message": "Opty synced successfully"}'
            ]);
        }

        return response()->json($opty->load(['customer', 'product', 'assignee', 'owner']));
    }

    public function destroy(Request $request, Opty $opty)
    {
        abort_if(!in_array($request->user()->role, ['admin', 'administrator']), 403, 'Only Admin and Administrator can delete optys.');
        
        $opty->delete();
        return response()->json(null, 204);
    }

    public function requestDiscount(Request $request, Opty $opty)
    {
        abort_if(!in_array($request->user()->role, ['sales', 'admin']), 403, 'Only Sales and Admin can request discounts.');

        $validated = $request->validate([
            'discount_amount' => 'required|numeric|min:0|max:'.($opty->estimated_value_otc ?: 0)
        ]);

        $opty->update([
            'discount_amount' => $validated['discount_amount'],
            'discount_status' => 'pending'
        ]);

        return response()->json($opty->load(['customer', 'product', 'assignee', 'owner']));
    }

    public function approveDiscount(Request $request, Opty $opty)
    {
        abort_if(!in_array($request->user()->role, ['pimpinan_sales', 'admin']), 403, 'Only Pimpinan Sales and Admin can approve discounts.');

        $opty->update([
            'discount_status' => 'approved'
        ]);

        return response()->json($opty->load(['customer', 'product', 'assignee', 'owner']));
    }

    public function rejectDiscount(Request $request, Opty $opty)
    {
        abort_if(!in_array($request->user()->role, ['pimpinan_sales', 'admin']), 403, 'Only Pimpinan Sales and Admin can reject discounts.');

        $opty->update([
            'discount_status' => 'rejected'
        ]);

        return response()->json($opty->load(['customer', 'product', 'assignee', 'owner']));
    }
}
