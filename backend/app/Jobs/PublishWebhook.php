<?php

namespace App\Jobs;

use App\Models\Opty;
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

    protected $opty;

    public function __construct(Opty $opty)
    {
        $this->opty = $opty;
    }

    public function handle(): void
    {
        $opty = $this->opty->load(['contact', 'product']);
        
        // Coba cari Nomor SIA dari tabel Customers berdasarkan nama contact
        $customerData = \App\Models\Customer::where('customer_name', $opty->contact->name)->first();
        $nomorSia = $customerData ? $customerData->nomor_sia : 'SIA-NOT-FOUND';

        $payload = [
            'event' => 'crm.opty_won',
            'opty_info' => [
                'opp_id' => 'OPP-' . $opty->id,
                'gross_amt' => (float) $opty->amount,
                'customer' => $opty->contact->name,
                'email' => $opty->contact->email ?? '',
                'product_id' => $opty->product ? 'PROD-' . $opty->product->id : null,
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
