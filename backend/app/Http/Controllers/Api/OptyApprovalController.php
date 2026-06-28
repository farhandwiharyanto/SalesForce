<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Opty;
use App\Models\OptyHistory;
use Illuminate\Http\Request;

class OptyApprovalController extends Controller
{
    /**
     * Get optys that need approval by the current user's role.
     */
    public function inbox(Request $request)
    {
        $role = $request->user()->role;
        $query = Opty::with(['customer', 'product', 'assignee', 'owner', 'contract'])
            ->where('stage', 'Negotiation Review');

        if ($role === 'pimpinan_sales') {
            $query->where('pimpinan_approval_status', 'pending')
                  ->whereHas('owner', function($q) use ($request) {
                      $q->where('manager_id', $request->user()->id);
                  });
        } elseif ($role === 'director_sales') {
            $query->where('director_approval_status', 'pending');
        } elseif ($role === 'verificator') {
            // Verificator only sees it if BOTH pimpinan and director have approved
            $query->where('pimpinan_approval_status', 'approved')
                  ->where('director_approval_status', 'approved')
                  ->where('verificator_approval_status', 'pending');
        } elseif ($role === 'admin' || $role === 'administrator') {
            // Admin can see everything that is pending any approval
            $query->where(function($q) {
                $q->where('pimpinan_approval_status', 'pending')
                  ->orWhere('director_approval_status', 'pending')
                  ->orWhere('verificator_approval_status', 'pending');
            });
        } else {
            // Other roles don't have inbox
            return response()->json([]);
        }

        return response()->json($query->latest()->get());
    }

    /**
     * Approve or reject an opty
     */
    public function submitApproval(Request $request, Opty $opty)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'note' => 'nullable|string'
        ]);

        $user = $request->user();
        $role = $user->role;
        $action = $request->action;
        $note = $request->note;

        // Ensure opty is in correct stage
        abort_if($opty->stage !== 'Negotiation Review', 400, 'Opportunity is not in Negotiation Review stage.');

        $statusField = '';
        if ($role === 'pimpinan_sales') {
            $statusField = 'pimpinan_approval_status';
        } elseif ($role === 'director_sales') {
            $statusField = 'director_approval_status';
        } elseif ($role === 'verificator') {
            $statusField = 'verificator_approval_status';
            // ensure pimpinan and director have approved
            abort_if($opty->pimpinan_approval_status !== 'approved' || $opty->director_approval_status !== 'approved', 400, 'Waiting for other approvals first.');
        } elseif ($role === 'admin' || $role === 'administrator') {
            // Admin can force approve/reject based on current pending state (for testing/bypass)
            if ($opty->pimpinan_approval_status === 'pending') $statusField = 'pimpinan_approval_status';
            elseif ($opty->director_approval_status === 'pending') $statusField = 'director_approval_status';
            elseif ($opty->verificator_approval_status === 'pending') $statusField = 'verificator_approval_status';
            else abort(400, 'No pending approvals.');
        } else {
            abort(403, 'You are not authorized to approve this opportunity.');
        }

        abort_if($opty->$statusField !== 'pending', 400, 'This approval is no longer pending.');

        if ($action === 'approve') {
            $opty->update([
                $statusField => 'approved'
            ]);

            OptyHistory::create([
                'opty_id' => $opty->id,
                'user_id' => $user->id,
                'action' => 'approved',
                'description' => "Approved by " . ucfirst(str_replace('_', ' ', $role))
            ]);

            // If verificator approved (the final step), move to Closed Won and Auto-Generate SIA
            if ($statusField === 'verificator_approval_status') {
                $opty->update([
                    'stage' => 'Closed Won'
                ]);
                OptyHistory::create([
                    'opty_id' => $opty->id,
                    'user_id' => $user->id,
                    'action' => 'stage_updated',
                    'description' => "Stage changed to Closed Won automatically after final approval."
                ]);

                // Auto Generate SIA
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
                        'company_name' => $opty->customer ? $opty->customer->customer_name : 'Unknown Company',
                        'service_status' => 'active',
                        'activation_date' => now(),
                        'billing_cycle' => 'monthly',
                        'contract_term_months' => 12,
                        'mrc_total' => $opty->estimated_value_mrc ?: 0,
                        'otc_total' => $opty->estimated_value_otc ?: 0,
                        'payment_terms' => 'Net 30'
                    ]);

                    OptyHistory::create([
                        'opty_id' => $opty->id,
                        'user_id' => $user->id,
                        'action' => 'sia_generated',
                        'description' => "Auto-generated SIA Contract: {$siaNumber}"
                    ]);
                }
            }
        } else { // reject
            // If rejected, bounce back to Prospect and Analysis
            $opty->update([
                'stage' => 'Prospect and Analysis',
                'pimpinan_approval_status' => 'none',
                'director_approval_status' => 'none',
                'verificator_approval_status' => 'none',
                'rejection_note' => $note
            ]);

            OptyHistory::create([
                'opty_id' => $opty->id,
                'user_id' => $user->id,
                'action' => 'rejected',
                'description' => "Rejected by " . ucfirst(str_replace('_', ' ', $role)) . ". Note: " . $note
            ]);
        }

        return response()->json($opty->load(['customer', 'product', 'assignee', 'owner', 'contract']));
    }
}
