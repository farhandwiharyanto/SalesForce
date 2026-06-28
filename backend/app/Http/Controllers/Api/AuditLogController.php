<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index($type, $id)
    {
        // map type string to model class
        $map = [
            'customer' => \App\Models\Customer::class,
            'contract' => \App\Models\Contract::class,
            'sia' => \App\Models\ServiceInstanceAccount::class,
        ];

        if (!isset($map[$type])) {
            return response()->json(['message' => 'Invalid auditable type'], 400);
        }

        $logs = AuditLog::with('user:id,first_name,last_name,email')
            ->where('auditable_type', $map[$type])
            ->where('auditable_id', $id)
            ->latest()
            ->get();

        return response()->json($logs);
    }
}
