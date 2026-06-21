<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WebhookLog;

class WebhookLogController extends Controller
{
    public function index()
    {
        return response()->json(WebhookLog::latest()->get());
    }

    public function retry(WebhookLog $webhookLog)
    {
        // For simulation, we'll just update it to success and update the response and updated_at
        $webhookLog->update([
            'status_code' => 200,
            'response' => '{"message": "Retry successful, data synced"}',
        ]);
        
        return response()->json($webhookLog);
    }
}
