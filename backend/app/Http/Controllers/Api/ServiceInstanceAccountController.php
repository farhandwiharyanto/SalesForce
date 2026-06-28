<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceInstanceAccount;
use App\Models\Opty;
use App\Models\WebhookLog;
use Illuminate\Http\Request;

class ServiceInstanceAccountController extends Controller
{
    public function index()
    {
        return response()->json(ServiceInstanceAccount::with(['opty.customer', 'opty.product', 'opty.owner', 'contract'])->latest()->get());
    }

    public function show($id)
    {
        $sia = ServiceInstanceAccount::with(['opty.customer', 'opty.product', 'opty.owner', 'contract'])->findOrFail($id);
        return response()->json($sia);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'opty_id' => 'required|exists:opty,id',
            'customer_id' => 'required|string',
            'company_name' => 'required|string',
        ]);

        $opty = Opty::findOrFail($validated['opty_id']);
        if ($opty->stage !== 'Closed Won') {
            abort(400, 'Only Closed Won optys can be bound to an SIA Contract.');
        }

        if (ServiceInstanceAccount::where('deal_id', $opty->id)->exists()) {
            abort(400, 'An SIA Contract already exists for this opty.');
        }

        // Generate SIA Number: YYYY + CustID (3 digits) + AutoIncrement (3 digits)
        $year = date('Y');
        
        // Ensure customer_id is 3 digits
        $custIdFormatted = str_pad($validated['customer_id'], 3, '0', STR_PAD_LEFT);
        
        // Get the latest SIA contract for this year to increment
        $latestSia = ServiceInstanceAccount::where('sia_number', 'like', "{$year}{$custIdFormatted}%")
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

        $contract = ServiceInstanceAccount::create([
            'sia_number' => $siaNumber,
            'deal_id' => $opty->id,
            'customer_id' => $validated['customer_id'],
            'company_name' => $validated['company_name'],
            'status' => 'Registered',
        ]);

        // Trigger Webhook Event
        WebhookLog::create([
            'event_type' => 'sia_contract.generated',
            'target_url' => 'https://ordersales.farhandwih.my.id/api/webhooks/sia',
            'status_code' => 200,
            'payload' => [
                'sia_number' => $siaNumber,
                'opty_id' => $opty->id,
                'company_name' => $validated['company_name'],
                'amount' => $opty->amount
            ],
            'response' => '{"message": "SIA Contract received successfully"}'
        ]);

        return response()->json($contract->load('opty.customer'), 201);
    }

    public function update(Request $request, $id)
    {
        $sia = ServiceInstanceAccount::findOrFail($id);

        $validated = $request->validate([
            'contract_id' => 'sometimes|nullable|exists:contracts,id',
            'billing_account_number' => 'sometimes|nullable|string|max:255',
            'status' => 'sometimes|required|string|max:255',
            'company_name' => 'sometimes|required|string|max:255',
        ]);

        $sia->fill($validated);
        $sia->save();

        return response()->json($sia->load('opty.customer'));
    }

    public function bulkStore(Request $request)
    {
        $data = $request->input('data');
        if (!is_array($data) || empty($data)) {
            return response()->json(['message' => 'No data provided'], 400);
        }

        $inserted = 0;
        foreach ($data as $item) {
            $company_name = $item['Company Name'] ?? null;
            $customer_id = $item['Customer ID'] ?? null;
            
            if ($company_name && $customer_id) {
                // Generate SIA number
                $year = date('Y');
                $custIdFormatted = str_pad($customer_id, 3, '0', STR_PAD_LEFT);
                $latestSia = ServiceInstanceAccount::where('sia_number', 'like', "{$year}{$custIdFormatted}%")
                                        ->orderBy('sia_number', 'desc')
                                        ->first();

                if ($latestSia) {
                    $lastIncrement = (int) substr($latestSia->sia_number, -3);
                    $newIncrement = $lastIncrement + 1;
                } else {
                    $newIncrement = 1;
                }
                $incrementFormatted = str_pad($newIncrement, 3, '0', STR_PAD_LEFT);
                $siaNumber = $item['SIA Number'] ?? "{$year}{$custIdFormatted}{$incrementFormatted}";

                ServiceInstanceAccount::create([
                    'sia_number' => $siaNumber,
                    'deal_id' => $item['Opty ID'] ?? null,
                    'customer_id' => $customer_id,
                    'company_name' => $company_name,
                    'status' => $item['Status'] ?? 'Registered',
                    'billing_account_number' => $item['Billing Account Number'] ?? null,
                ]);
                $inserted++;
            }
        }

        return response()->json(['message' => "Successfully imported {$inserted} SIAs."]);
    }
}
