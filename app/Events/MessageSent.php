<?php

namespace App\Events;

use App\Models\MatchMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public MatchMessage $message) {}

    /**
     * Canal privado del match — solo los dos dueños lo escuchan.
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('match.' . $this->message->match_id),
        ];
    }

    /**
     * Nombre del evento que escucha el frontend (.listen('MessageSent', ...)).
     */
    public function broadcastAs(): string
    {
        return 'MessageSent';
    }

    /**
     * Payload que recibe el cliente.
     */
    public function broadcastWith(): array
    {
        $user = $this->message->user;
        $foto = $user->foto ? Storage::disk('public')->url($user->foto) : null;

        return [
            'message' => [
                'id'         => $this->message->id,
                'user_id'    => $this->message->user_id,
                'body'       => $this->message->body,
                'read_at'    => $this->message->read_at,
                'created_at' => $this->message->created_at->toISOString(),
                'user'       => [
                    'id'   => $user->id,
                    'name' => $user->name,
                    'foto' => $foto,
                ],
            ],
        ];
    }
}
