<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function index()
    {
        return response()->json(Lead::with(['customer', 'owner', 'product', 'updates.user'])->latest()->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'customer_id' => 'required|exists:customers,id',
            'owner_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'status' => 'required|string|in:Open,Contacted,Converted,Qualified,Unqualified'
        ]);

        $lead = Lead::create($validated);

        \App\Models\LeadUpdate::create([
            'lead_id' => $lead->id,
            'user_id' => $request->user() ? $request->user()->id : null,
            'action' => 'created',
            'description' => 'created the lead'
        ]);

        return response()->json($lead->load(['customer', 'owner', 'product', 'updates.user']), 201);
    }

    public function show(Lead $lead)
    {
        return response()->json($lead->load(['customer', 'owner', 'product', 'updates.user']));
    }

    public function update(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'sometimes|required|email|max:255',
            'customer_id' => 'sometimes|required|exists:customers,id',
            'owner_id' => 'sometimes|required|exists:users,id',
            'product_id' => 'sometimes|required|exists:products,id',
            'status' => 'sometimes|required|string|in:Open,Contacted,Converted,Qualified,Unqualified'
        ]);

        $oldStatus = $lead->status;
        $lead->update($validated);

        if (isset($validated['status']) && $validated['status'] !== $oldStatus) {
            if ($validated['status'] === 'Converted') {
                $lead->load(['customer', 'product']);
                
                // 1. Create Contact (Keep this if you still use Contacts elsewhere, otherwise optional)
                $contact = \App\Models\Contact::create([
                    'name' => trim(($lead->first_name ?? '') . ' ' . ($lead->last_name ?? '')),
                    'email' => $lead->email,
                    'company' => $lead->customer ? $lead->customer->customer_name : 'Unknown',
                    'phone' => '-'
                ]);

                // 2. Create Opty
                $amount = $lead->product ? $lead->product->price : 0;
                \App\Models\Opty::create([
                    'opportunity_number' => 'POT' . rand(100000000, 999999999),
                    'name' => trim(($lead->first_name ?? '') . ' ' . ($lead->last_name ?? '')) . ' - ' . ($lead->product ? $lead->product->name : 'Opty'),
                    'assigned_to' => $lead->owner_id,
                    'owner_id' => $lead->owner_id,
                    'customer_id' => $lead->customer_id,
                    'product_id' => $lead->product_id,
                    'stage' => 'Prospect and Analysis',
                    'request_type' => 'New Installation',
                    'confidence_level' => 'PipeLine',
                    'estimated_value_otc' => $amount,
                    'estimated_value_mrc' => 0,
                    'target_close_date' => now()->addWeeks(3)->toDateString(),
                    'customer_expected_rfs' => now()->addMonth()->toDateString(),
                    'is_converted_from_lead' => true
                ]);

                \App\Models\LeadUpdate::create([
                    'lead_id' => $lead->id,
                    'user_id' => $request->user() ? $request->user()->id : null,
                    'action' => 'converted_to_opty',
                    'description' => 'converted the lead to Opty'
                ]);
            } else {
                \App\Models\LeadUpdate::create([
                    'lead_id' => $lead->id,
                    'user_id' => $request->user() ? $request->user()->id : null,
                    'action' => 'status_updated',
                    'description' => 'changed status from ' . $oldStatus . ' to ' . $validated['status']
                ]);
            }
        }

        return response()->json($lead->load(['customer', 'owner', 'product', 'updates.user']));
    }

    public function destroy(Request $request, Lead $lead)
    {
        abort_if($request->user() && !in_array($request->user()->role, ['admin', 'administrator']), 403, 'Only Admin and Administrator can delete leads.');
        $lead->delete();
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
            // Map CSV to fields
            $first_name = $item['First Name'] ?? null;
            $last_name = $item['Last Name'] ?? null;
            $email = $item['Email'] ?? null;
            $status = $item['Status'] ?? 'New';
            $customer_id = $item['Customer ID'] ?? null;
            $owner_id = $item['Owner ID'] ?? null;
            $product_id = $item['Product ID'] ?? null;

            if ($first_name && $last_name && $email) {
                // Generate lead number
                $year = date('Y');
                $count = \App\Models\Lead::where('lead_number', 'like', "LD-{$year}%")->count() + 1;
                $leadNumber = "LD-{$year}" . str_pad($count, 3, '0', STR_PAD_LEFT);

                Lead::create([
                    'lead_number' => $leadNumber,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'status' => $status,
                    'customer_id' => $customer_id ?: null,
                    'owner_id' => $owner_id ?: null,
                    'product_id' => $product_id ?: null,
                ]);
                $inserted++;
            }
        }

        return response()->json(['message' => "Successfully imported {$inserted} leads."]);
    }
}
