<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\MatchMessage;
use App\Models\MatchProposal;
use App\Models\PetMatch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // GET /notifications/summary — contadores para el badge
    public function summary(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $proposals = MatchProposal::where('to_user_id', $userId)
            ->where('status', 'pending')
            ->count();

        // Mensajes no leídos en mis matches activos
        $matchIds = PetMatch::where(function ($q) use ($userId) {
                $q->where('user_a_id', $userId)->orWhere('user_b_id', $userId);
            })
            ->where('status', 'active')
            ->pluck('id');

        $unreadMessages = MatchMessage::whereIn('match_id', $matchIds)
            ->where('user_id', '!=', $userId)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'proposals'       => $proposals,
            'unread_messages' => $unreadMessages,
            'total'           => $proposals + $unreadMessages,
        ]);
    }

    // GET /notifications
    public function index(Request $request): JsonResponse
    {
        $notifications = AppNotification::where('user_id', $request->user()->id)
            ->latest()
            ->limit(50)
            ->get();

        $unreadCount = AppNotification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $unreadCount,
        ]);
    }

    // POST /notifications/read-all
    public function readAll(Request $request): JsonResponse
    {
        AppNotification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'Notificaciones marcadas como leídas.']);
    }

    // PATCH /notifications/{notification}
    public function markRead(Request $request, AppNotification $notification): JsonResponse
    {
        if ($notification->user_id !== $request->user()->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $notification->update(['read_at' => now()]);

        return response()->json($notification);
    }
}
