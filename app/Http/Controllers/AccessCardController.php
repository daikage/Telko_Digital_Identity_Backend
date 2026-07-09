<?php

namespace App\Http\Controllers;

use App\Models\AccessCard;
use App\Models\AccessLog;
use Illuminate\Http\Request;

class AccessCardController extends Controller
{
    /**
     * List all access cards for the authenticated user.
     */
    public function index(Request $request)
    {
        $cards = AccessCard::where('user_id', $request->user()->id)
            ->with(['doorLock' => function ($query) {
                $query->select('id', 'name', 'location', 'lock_type', 'ble_service_uuid', 'ble_characteristic_uuid', 'nfc_aid', 'is_active');
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'cards' => $cards,
        ]);
    }

    /**
     * Get a single access card with full details for NFC/BLE usage.
     */
    public function show(Request $request, $id)
    {
        $card = AccessCard::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->with(['doorLock' => function ($query) {
                $query->select('id', 'name', 'location', 'lock_type', 'ble_service_uuid', 'ble_characteristic_uuid', 'nfc_aid', 'is_active');
            }])
            ->firstOrFail();

        return response()->json([
            'card' => $card,
            'is_valid' => $card->isValid(),
        ]);
    }

    /**
     * Toggle activation of an access card.
     */
    public function toggleActivation(Request $request, $id)
    {
        $card = AccessCard::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->firstOrFail();

        $card->is_active = !$card->is_active;
        $card->save();

        return response()->json([
            'card' => $card,
            'message' => $card->is_active ? 'Card activated' : 'Card deactivated',
        ]);
    }

    /**
     * Get access logs for the authenticated user.
     */
    public function logs(Request $request)
    {
        $cardIds = AccessCard::where('user_id', $request->user()->id)->pluck('id');

        $logs = AccessLog::whereIn('access_card_id', $cardIds)
            ->with([
                'accessCard:id,card_name,door_lock_id',
                'doorLock:id,name,location',
            ])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'logs' => $logs,
        ]);
    }
}
