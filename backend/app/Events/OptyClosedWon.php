<?php

namespace App\Events;

use App\Models\Opty;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OptyClosedWon
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $opty;

    public function __construct(Opty $opty)
    {
        $this->opty = $opty;
    }
}
