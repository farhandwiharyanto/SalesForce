<?php

namespace App\Listeners;

use App\Events\DealClosedWon;
use App\Jobs\PublishWebhook;

class TriggerWebhookOnDealWon
{
    public function __construct()
    {
        //
    }

    public function handle(DealClosedWon $event): void
    {
        PublishWebhook::dispatch($event->deal);
    }
}
