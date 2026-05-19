<?php

namespace App\Events;

use App\Models\MatchMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * NewMessageNotification — evento "global del usuario".
 *
 * Se emite en el canal privado personal del DESTINATARIO (`user.{id}`),
 * NO en el canal del match. Sirve para que cualquier pantalla de la app
 * (Feed, perfil, listado de propuestas…) reciba la señal de que llegó
 * un mensaje nuevo y pueda actualizar badges/contadores en tiempo real
 * sin estar suscrita al canal del match.
 *
 * El backend es la fuente autoritativa del total de no leídos: ya lo
 * calculamos aquí y lo enviamos en el payload, así el frontend solo
 * asigna `setUnreadMessages(payload.unread_total)` y nunca incrementa
 * manualmente (cero desincronización).
 */
class NewMessageNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int          $recipientId,
        public MatchMessage $message,
        public int          $unreadTotal,
        public int          $unreadInMatch,
    ) {}

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("user.{$this->recipientId}");
    }

    public function broadcastAs(): string
    {
        return 'NewMessageNotification';
    }

    public function broadcastWith(): array
    {
        return [
            'match_id'        => $this->message->match_id,
            'sender_id'       => $this->message->user_id,
            'sender_name'     => $this->message->user->name ?? '',
            'preview'         => mb_substr($this->message->body, 0, 80),
            'created_at'      => optional($this->message->created_at)->toISOString(),
            'unread_total'    => $this->unreadTotal,
            'unread_in_match' => $this->unreadInMatch,
        ];
    }
}
