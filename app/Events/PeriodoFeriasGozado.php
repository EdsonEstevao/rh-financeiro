<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

use App\Models\Domain\RH\PeriodoFerias;

class PeriodoFeriasGozado
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public readonly PeriodoFerias $periodo)
    {
        //
    }

    /**
     * Get the channels the event should broadcast on.
     *
     *  return array<int, Channel>
     */
    // public function broadcastOn(): array
    // {
    //     return [
    //         new PrivateChannell('channel-name'),
    //     ];
    // }
}