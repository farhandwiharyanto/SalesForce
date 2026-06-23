<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractController extends Controller
{
    public function index()
    {
        return response()->json(Contract::with(['customer', 'assignee', 'lastModifiedBy'])->latest()->get());
    }

    public function show($id)
    {
        $contract = Contract::with(['customer', 'assignee', 'lastModifiedBy'])->findOrFail($id);
        return response()->json($contract);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'status' => 'required|in:Created,In Use,Completed,Terminate',
            'customer_id' => 'required|exists:customers,id',
            'assigned_to' => 'nullable|exists:users,id',
            'start_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:start_date',
            'contract_division' => 'required|in:Corporate,Retail',
        ]);

        // Generate Contract Number
        // Format: CON{000X}/{CORP|RETAIL}/{YYYY}
        $year = date('Y', strtotime($validated['start_date'])); // Use start date year, or current year
        $divisionCode = $validated['contract_division'] === 'Corporate' ? 'CORP' : 'RETAIL';
        
        // Find the highest sequence number across all contracts (no year reset)
        $latestContract = Contract::orderBy('id', 'desc')->first();
        
        $newIncrement = 1;
        if ($latestContract && preg_match('/^CON(\d+)\//', $latestContract->contract_number, $matches)) {
            $newIncrement = (int) $matches[1] + 1;
        }

        $incrementFormatted = str_pad($newIncrement, 4, '0', STR_PAD_LEFT);
        $contractNumber = "CON{$incrementFormatted}/{$divisionCode}/{$year}";

        $contract = Contract::create(array_merge($validated, [
            'contract_number' => $contractNumber,
            'last_modified_by' => Auth::id() ?? null, // Optional if you track creation as well
        ]));

        return response()->json($contract->load(['customer', 'assignee', 'lastModifiedBy']), 201);
    }

    public function update(Request $request, $id)
    {
        $contract = Contract::findOrFail($id);

        $validated = $request->validate([
            'subject' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|in:Created,In Use,Completed,Terminate',
            'customer_id' => 'sometimes|required|exists:customers,id',
            'assigned_to' => 'nullable|exists:users,id',
            'start_date' => 'sometimes|required|date',
            'due_date' => 'sometimes|required|date|after_or_equal:start_date',
            'contract_division' => 'sometimes|required|in:Corporate,Retail',
        ]);

        $contract->fill($validated);
        $contract->last_modified_by = Auth::id() ?? null;
        
        // Update contract number format if start_date or division changes?
        // Usually contract numbers are immutable. We will keep it immutable.

        $contract->save();

        return response()->json($contract->load(['customer', 'assignee', 'lastModifiedBy']));
    }

    public function destroy($id)
    {
        $contract = Contract::findOrFail($id);
        $contract->delete();

        return response()->json(['message' => 'Contract deleted successfully']);
    }

    public function bulkStore(Request $request)
    {
        $data = $request->input('data');
        if (!is_array($data) || empty($data)) {
            return response()->json(['message' => 'No data provided'], 400);
        }

        $inserted = 0;
        foreach ($data as $item) {
            $subject = $item['Subject'] ?? null;
            $customer_id = $item['Customer ID'] ?? null;
            
            if ($subject && $customer_id) {
                // Generate contract number
                $division = $item['Division'] ?? 'Corporate';
                $divCode = $division === 'Retail' ? 'RETAIL' : 'CORP';
                $year = date('Y');

                $latestContract = Contract::orderBy('id', 'desc')->first();
                if ($latestContract) {
                    $parts = explode('/', $latestContract->contract_number);
                    if (count($parts) === 3 && str_starts_with($parts[0], 'CON')) {
                        $lastIncrement = (int) substr($parts[0], 3);
                        $newIncrement = $lastIncrement + 1;
                    } else {
                        $newIncrement = 1;
                    }
                } else {
                    $newIncrement = 1;
                }

                $incrementFormatted = str_pad($newIncrement, 4, '0', STR_PAD_LEFT);
                $contractNumber = "CON{$incrementFormatted}/{$divCode}/{$year}";

                $startDateStr = $item['Start Date'] ?? null;
                $startDate = date('Y-m-d');
                if ($startDateStr) {
                    try {
                        $startDate = \Carbon\Carbon::parse(str_replace('/', '-', $startDateStr))->format('Y-m-d');
                    } catch (\Exception $e) {
                        $startDate = date('Y-m-d');
                    }
                }

                $dueDateStr = $item['Due Date'] ?? null;
                $dueDate = date('Y-m-d', strtotime('+1 year'));
                if ($dueDateStr) {
                    try {
                        $dueDate = \Carbon\Carbon::parse(str_replace('/', '-', $dueDateStr))->format('Y-m-d');
                    } catch (\Exception $e) {
                        $dueDate = date('Y-m-d', strtotime('+1 year'));
                    }
                }

                Contract::create([
                    'contract_number' => $contractNumber,
                    'subject' => $subject,
                    'status' => $item['Status'] ?? 'Created',
                    'customer_id' => $customer_id,
                    'assigned_to' => $item['Assigned To (User ID)'] ?? null,
                    'start_date' => $startDate,
                    'due_date' => $dueDate,
                    'contract_division' => $division,
                ]);
                $inserted++;
            }
        }

        return response()->json(['message' => "Successfully imported {$inserted} contracts."]);
    }
}
