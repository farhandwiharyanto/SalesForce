<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiaContract;
use App\Models\Deal;
use App\Models\WebhookLog;
use Illuminate\Http\Request;

class SiaContractController extends Controller
{
    public function index()
    {
        return response()->json(SiaContract::with('deal.contact')->latest()->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'deal_id' => 'required|exists:deals,id',
            'customer_id' => 'required|string',
            'company_name' => 'required|string',
        ]);

        $deal = Deal::findOrFail($validated['deal_id']);
        if ($deal->stage !== 'closed_won') {
            abort(400, 'Only Closed Won deals can be bound to an SIA Contract.');
        }

        if (SiaContract::where('deal_id', $deal->id)->exists()) {
            abort(400, 'An SIA Contract already exists for this deal.');
        }

        // Generate SIA Number: YYYY + CustID (3 digits) + AutoIncrement (3 digits)
        $year = date('Y');
        
        // Ensure customer_id is 3 digits
        $custIdFormatted = str_pad($validated['customer_id'], 3, '0', STR_PAD_LEFT);
        
        // Get the latest SIA contract for this year to increment
        $latestSia = SiaContract::where('sia_number', 'like', "{$year}{$custIdFormatted}%")
                                ->orderBy('sia_number', 'desc')
                                ->first();

        if ($latestSia) {
            $lastIncrement = (int) substr($latestSia->sia_number, -3);
            $newIncrement = $lastIncrement + 1;
        } else {
            $newIncrement = 1;
        }

        $incrementFormatted = str_pad($newIncrement, 3, '0', STR_PAD_LEFT);
        $siaNumber = "{$year}{$custIdFormatted}{$incrementFormatted}";

        $contract = SiaContract::create([
            'sia_number' => $siaNumber,
            'deal_id' => $deal->id,
            'customer_id' => $validated['customer_id'],
            'company_name' => $validated['company_name'],
            'status' => 'generated',
        ]);

        // Trigger Webhook Event
        WebhookLog::create([
            'event_type' => 'sia_contract.generated',
            'target_url' => 'https://ordersales.farhandwih.my.id/api/webhooks/sia',
            'status_code' => 200,
            'payload' => [
                'sia_number' => $siaNumber,
                'deal_id' => $deal->id,
                'company_name' => $validated['company_name'],
                'amount' => $deal->amount
            ],
            'response' => '{"message": "SIA Contract received successfully"}'
        ]);

        return response()->json($contract->load('deal.contact'), 201);
    }
}
