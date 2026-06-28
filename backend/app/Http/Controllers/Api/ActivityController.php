<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityController extends Controller
{
    public function index()
    {
        // Fetch recent items from different tables and map them to activities
        $activities = [];

        // Leads
        $leads = \App\Models\Lead::orderBy('created_at', 'desc')->limit(10)->get();
        foreach ($leads as $lead) {
            $activities[] = [
                'id' => 'lead_' . $lead->id,
                'type' => 'Lead',
                'title' => 'New Lead: ' . $lead->first_name . ' ' . $lead->last_name,
                'description' => 'Lead No: ' . $lead->lead_number . ' | Status: ' . $lead->status,
                'date' => $lead->created_at,
                'color' => 'blue',
            ];
        }

        // Optys
        $optys = \App\Models\Opty::orderBy('created_at', 'desc')->limit(10)->get();
        foreach ($optys as $opty) {
            $activities[] = [
                'id' => 'opty_' . $opty->id,
                'type' => 'Opportunity',
                'title' => 'New Opty: ' . $opty->name,
                'description' => 'Stage: ' . $opty->stage . ' | Value: Rp ' . number_format($opty->estimated_value_otc, 0, ',', '.'),
                'date' => $opty->created_at,
                'color' => 'indigo',
            ];
        }

        // Customers
        $customers = \App\Models\Customer::orderBy('created_at', 'desc')->limit(10)->get();
        foreach ($customers as $customer) {
            $activities[] = [
                'id' => 'customer_' . $customer->id,
                'type' => 'Customer',
                'title' => 'New Customer: ' . $customer->customer_name,
                'description' => 'Status: ' . $customer->status,
                'date' => $customer->created_at,
                'color' => 'green',
            ];
        }

        // Contracts
        $contracts = \App\Models\Contract::orderBy('created_at', 'desc')->limit(10)->get();
        foreach ($contracts as $contract) {
            $activities[] = [
                'id' => 'contract_' . $contract->id,
                'type' => 'Contract',
                'title' => 'New Contract: ' . $contract->contract_name,
                'description' => 'Status: ' . $contract->status,
                'date' => $contract->created_at,
                'color' => 'purple',
            ];
        }

        // Opty Histories (for steps and approvals)
        $optyHistories = \App\Models\OptyHistory::with('opty')->orderBy('created_at', 'desc')->limit(15)->get();
        foreach ($optyHistories as $history) {
            $activities[] = [
                'id' => 'history_' . $history->id,
                'type' => 'Opty Update',
                'title' => 'Opty Activity: ' . ($history->opty->opportunity_number ?? 'Unknown Opty'),
                'description' => $history->description,
                'date' => $history->created_at,
                'color' => 'pink',
            ];
        }

        // Users
        $userQuery = \App\Models\User::orderBy('created_at', 'desc')->limit(10);
        $currentUser = request()->user();
        
        if ($currentUser && $currentUser->role === 'pimpinan_sales') {
            $userQuery->where('manager_id', $currentUser->id);
        }
        
        $users = $userQuery->get();
        foreach ($users as $user) {
            $activities[] = [
                'id' => 'user_' . $user->id,
                'type' => 'User',
                'title' => 'New User: ' . trim($user->first_name . ' ' . $user->last_name),
                'description' => 'Role: ' . $user->role,
                'date' => $user->created_at,
                'color' => 'orange',
            ];
        }

        // Sort by date desc
        usort($activities, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        // Return top 20
        return response()->json(array_slice($activities, 0, 20));
    }
}
