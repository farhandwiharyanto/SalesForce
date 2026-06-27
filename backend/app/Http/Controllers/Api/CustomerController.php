<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        return response()->json(Customer::with('owner')->latest()->get());
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'nomor_sia' => 'nullable|string|unique:customers',
            'customer_name' => 'required|string|max:255',
            'status' => 'required|string|in:Registered,Active,Deactivated',
            'email' => 'required|email|unique:customers',
            'initial' => 'required|string|max:4',
            'owner_id' => 'nullable|exists:users,id',
        ]);

        $customer = Customer::create($validated);
        return response()->json($customer, 201);
    }

    public function show(Customer $customer)
    {
        return response()->json($customer);
    }

    public function update(Request $request, Customer $customer)
    {
        if ($request->user() && $request->user()->role === 'sales' && $customer->owner_id !== $request->user()->id) {
            abort(403, 'Permission Denied: You cannot edit a customer that does not belong to you.');
        }

        $validated = $request->validate([
            'nomor_sia' => 'nullable|string|unique:customers,nomor_sia,'.$customer->id,
            'nomor_customer' => 'sometimes|required|string|unique:customers,nomor_customer,'.$customer->id,
            'customer_name' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|string|in:Registered,Active,Deactivated',
            'email' => 'sometimes|required|email|unique:customers,email,'.$customer->id,
            'initial' => 'sometimes|required|string|max:4',
            'owner_id' => 'sometimes|nullable|exists:users,id',
        ]);

        $customer->update($validated);
        return response()->json($customer);
    }

    public function destroy(Request $request, Customer $customer)
    {
        abort_if($request->user() && !in_array($request->user()->role, ['admin', 'administrator']), 403, 'Only Admin and Administrator can delete customers.');

        $customer->delete();
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
            $customerName = $item['Customer Name'] ?? null;
            $email = $item['Email'] ?? null;
            
            if ($customerName && $email) {
                // Generate numbers
                $initial = $item['Initial'] ?? strtoupper(substr($customerName, 0, 4));
                $customerId = $item['Customer ID'] ?? str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
                $siaNumber = $item['SIA Number'] ?? 'SIA-' . $customerId;

                Customer::create([
                    'nomor_sia' => $siaNumber,
                    'nomor_customer' => $customerId,
                    'customer_name' => $customerName,
                    'status' => $item['Status'] ?? 'Active',
                    'email' => $email,
                    'initial' => $initial,
                ]);
                $inserted++;
            }
        }

        return response()->json(['message' => "Successfully imported {$inserted} customers."]);
    }
}
