<?php

namespace App\Jobs;

use App\Models\Deal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PublishWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $deal;

    public function __construct(Deal $deal)
    {
        $this->deal = $deal;
    }

    public function handle(): void
    {
        $deal = $this->deal->load(['contact', 'product']);
        
        // Coba cari Nomor SIA dari tabel Customers berdasarkan nama contact
        $customerData = \App\Models\Customer::where('customer_name', $deal->contact->name)->first();
        $nomorSia = $customerData ? $customerData->nomor_sia : 'SIA-NOT-FOUND';

        $payload = [
            'event' => 'crm.deal_won',
            'deal_info' => [
                'opp_id' => 'OPP-' . $deal->id,
                'gross_amt' => (float) $deal->amount,
                'customer' => $deal->contact->name,
                'email' => $deal->contact->email ?? '',
                'product_id' => $deal->product ? 'PROD-' . $deal->product->id : null,
            ],
            'CRRM' => [
                'nem_field' => 'metadata_crm_ok',
                'Nomor SIA' => $nomorSia
            ]
        ];

        $webhookUrl = env('WEBHOOK_URL', 'http://localhost:8000/api/mock-webhook');
        $token = env('WEBHOOK_JWT_SECRET', 'mock-jwt-token');

        Log::info('Publishing webhook payload: ', $payload);

        try {
            $response = Http::withToken($token)->post($webhookUrl, $payload);
            
            if ($response->failed()) {
                Log::error('Webhook failed', ['status' => $response->status(), 'response' => $response->body()]);
            } else {
                Log::info('Webhook published successfully');
            }
        } catch (\Exception $e) {
            Log::error('Webhook connection error: ' . $e->getMessage());
        }
    }
}
