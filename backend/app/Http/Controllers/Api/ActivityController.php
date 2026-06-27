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
        $leads = DB::table('leads')->orderBy('created_at', 'desc')->limit(10)->get();
        foreach ($leads as $lead) {
            $activities[] = [
                'id' => 'lead_' . $lead->id,
                'type' => 'Lead',
                'title' => 'New Lead: ' . $lead->first_name . ' ' . $lead->last_name,
                'description' => 'Company: ' . $lead->company . ' | Status: ' . $lead->status,
                'date' => $lead->created_at,
                'color' => 'blue',
            ];
        }

        // Optys
        $optys = DB::table('optys')->orderBy('created_at', 'desc')->limit(10)->get();
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
        $customers = DB::table('customers')->orderBy('created_at', 'desc')->limit(10)->get();
        foreach ($customers as $customer) {
            $activities[] = [
                'id' => 'customer_' . $customer->id,
                'type' => 'Customer',
                'title' => 'New Customer: ' . $customer->customer_name,
                'description' => 'Segment: ' . $customer->segment,
                'date' => $customer->created_at,
                'color' => 'green',
            ];
        }

        // Contracts
        $contracts = DB::table('contracts')->orderBy('created_at', 'desc')->limit(10)->get();
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

        // Users
        $users = DB::table('users')->orderBy('created_at', 'desc')->limit(10)->get();
        foreach ($users as $user) {
            $activities[] = [
                'id' => 'user_' . $user->id,
                'type' => 'User',
                'title' => 'New User: ' . $user->name,
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
