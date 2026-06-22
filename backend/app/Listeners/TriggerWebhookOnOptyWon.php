<?php

namespace App\Listeners;

use App\Events\OptyClosedWon;
use App\Jobs\PublishWebhook;

class TriggerWebhookOnOptyWon
{
    public function __construct()
    {
        //
    }

    public function handle(OptyClosedWon $event): void
    {
        PublishWebhook::dispatch($event->opty);
    }
}
