<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Opty;
use App\Models\OptyHistory;
use Illuminate\Http\Request;

class OptyController extends Controller
{
    public function index()
    {
        return response()->json(Opty::with(['customer', 'product', 'assignee', 'owner', 'serviceInstanceAccount'])->latest()->get());
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
        
        OptyHistory::create([
            'opty_id' => $opty->id,
            'user_id' => $request->user()->id ?? null,
            'action' => 'created',
            'description' => 'Opportunity created'
        ]);

        return response()->json($opty->load(['customer', 'product', 'assignee', 'owner', 'serviceInstanceAccount']), 201);
    }

    public function show(Opty $opty)
    {
        return response()->json($opty->load(['customer', 'product', 'assignee', 'owner', 'serviceInstanceAccount']));
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
        
        $opty->fill($validated);
        $dirty = $opty->getDirty();
        if (count($dirty) > 0) {
            $changes = [];
            foreach ($dirty as $key => $newValue) {
                $oldValue = $opty->getOriginal($key);
                $keyLabel = ucwords(str_replace('_', ' ', $key));
                $changes[] = "$keyLabel changed to " . ($newValue ?: 'Empty');
            }
            OptyHistory::create([
                'opty_id' => $opty->id,
                'user_id' => $request->user()->id ?? null,
                'action' => 'updated',
                'description' => implode(', ', $changes)
            ]);
        }
        $opty->save();

        if ($oldStage !== 'Closed Won' && $opty->stage === 'Closed Won') {
            if (!\App\Models\ServiceInstanceAccount::where('deal_id', $opty->id)->exists()) {
                $year = date('Y');
                $custIdFormatted = str_pad($opty->customer_id, 3, '0', STR_PAD_LEFT);
                $latestSia = \App\Models\ServiceInstanceAccount::where('sia_number', 'like', "{$year}{$custIdFormatted}%")
                                        ->orderBy('sia_number', 'desc')
                                        ->first();

                $newIncrement = $latestSia ? ((int) substr($latestSia->sia_number, -3)) + 1 : 1;
                $incrementFormatted = str_pad($newIncrement, 3, '0', STR_PAD_LEFT);
                $siaNumber = "{$year}{$custIdFormatted}{$incrementFormatted}";

                \App\Models\ServiceInstanceAccount::create([
                    'sia_number' => $siaNumber,
                    'deal_id' => $opty->id,
                    'customer_id' => $opty->customer_id,
                    'company_name' => $opty->customer->customer_name ?? 'Unknown',
                    'status' => 'Registered',
                ]);
            }

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

        return response()->json($opty->load(['customer', 'product', 'assignee', 'owner', 'serviceInstanceAccount']));
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

        OptyHistory::create([
            'opty_id' => $opty->id,
            'user_id' => $request->user()->id ?? null,
            'action' => 'discount_requested',
            'description' => "Requested discount of Rp " . number_format($validated['discount_amount'], 0, ',', '.')
        ]);

        return response()->json($opty->load(['customer', 'product', 'assignee', 'owner']));
    }

    public function approveDiscount(Request $request, Opty $opty)
    {
        abort_if(!in_array($request->user()->role, ['pimpinan_sales', 'admin']), 403, 'Only Pimpinan Sales and Admin can approve discounts.');

        $opty->update([
            'discount_status' => 'approved'
        ]);

        OptyHistory::create([
            'opty_id' => $opty->id,
            'user_id' => $request->user()->id ?? null,
            'action' => 'discount_approved',
            'description' => 'Discount request approved'
        ]);

        return response()->json($opty->load(['customer', 'product', 'assignee', 'owner']));
    }

    public function rejectDiscount(Request $request, Opty $opty)
    {
        abort_if(!in_array($request->user()->role, ['pimpinan_sales', 'admin']), 403, 'Only Pimpinan Sales and Admin can reject discounts.');

        $opty->update([
            'discount_status' => 'rejected'
        ]);

        OptyHistory::create([
            'opty_id' => $opty->id,
            'user_id' => $request->user()->id ?? null,
            'action' => 'discount_rejected',
            'description' => 'Discount request rejected'
        ]);

        return response()->json($opty->load(['customer', 'product', 'assignee', 'owner']));
    }

    public function histories(Opty $opty)
    {
        return response()->json($opty->histories()->with('user:id,first_name,last_name,role')->get());
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
            $target_close_date = $item['Target Close Date'] ?? null;
            
            if ($name && $target_close_date) {
                // Generate opty number
                $year = date('Y');
                $count = \App\Models\Opty::where('opportunity_number', 'like', "OPTY-{$year}%")->count() + 1;
                $opportunityNumber = "OPTY-{$year}" . str_pad($count, 3, '0', STR_PAD_LEFT);

                $targetCloseDateParsed = date('Y-m-d');
                if ($target_close_date) {
                    try {
                        $targetCloseDateParsed = \Carbon\Carbon::parse(str_replace('/', '-', $target_close_date))->format('Y-m-d');
                    } catch (\Exception $e) {
                        $targetCloseDateParsed = date('Y-m-d');
                    }
                }

                $rfsDateStr = $item['Customer RFS Date'] ?? $target_close_date;
                $rfsDateParsed = $targetCloseDateParsed;
                if ($rfsDateStr) {
                    try {
                        $rfsDateParsed = \Carbon\Carbon::parse(str_replace('/', '-', $rfsDateStr))->format('Y-m-d');
                    } catch (\Exception $e) {
                        $rfsDateParsed = $targetCloseDateParsed;
                    }
                }

                Opty::create([
                    'opportunity_number' => $item['Opportunity Number'] ?? $opportunityNumber,
                    'name' => $name,
                    'assigned_to' => $item['Assigned To (User ID)'] ?? null,
                    'owner_id' => $item['Owner ID'] ?? null,
                    'customer_id' => $item['Customer ID'] ?? null,
                    'product_id' => $item['Product ID'] ?? null,
                    'target_close_date' => $targetCloseDateParsed,
                    'customer_expected_rfs' => $rfsDateParsed,
                    'request_type' => $item['Request Type'] ?? 'New Installation',
                    'stage' => $item['Stage'] ?? 'Discovery',
                    'confidence_level' => $item['Confidence Level'] ?? 'Low',
                    'estimated_value_otc' => floatval(str_replace(',', '', $item['Estimated Value OTC'] ?? 0)),
                    'estimated_value_mrc' => floatval(str_replace(',', '', $item['Estimated Value MRC'] ?? 0)),
                ]);
                $inserted++;
            }
        }

        return response()->json(['message' => "Successfully imported {$inserted} opportunities."]);
    }
}
