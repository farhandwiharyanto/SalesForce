<?php

namespace Tests\Feature;

use App\Events\OptyClosedWon;
use App\Jobs\PublishWebhook;
use App\Models\Contact;
use App\Models\Opty;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class WebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_opty_status_update_triggers_event()
    {
        Event::fake();

        $contact = Contact::create(['name' => 'Test', 'email' => 'test@test.com']);
        $opty = Opty::create(['name' => 'Opty 1', 'amount' => 100, 'stage' => 'prospecting', 'contact_id' => $contact->id]);

        $response = $this->putJson("/api/optys/{$opty->id}", [
            'stage' => 'closed_won'
        ]);

        $response->assertStatus(200);

        Event::assertDispatched(OptyClosedWon::class);
    }
}
