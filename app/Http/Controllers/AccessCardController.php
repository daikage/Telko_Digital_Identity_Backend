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

    /**
     * Unlock a Tuya cloud-connected door lock.
     */
    public function tuyaUnlock(Request $request, $id, \App\Services\TuyaService $tuyaService)
    {
        $card = AccessCard::where('user_id', $request->user()->id)
            ->where('id', $id)
            ->with('doorLock')
            ->firstOrFail();

        if (!$card->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Access card is expired, inactive, or outside allowed schedule.',
            ], 403);
        }

        $lock = $card->doorLock;

        if (!$lock || $lock->lock_type !== 'tuya' || empty($lock->tuya_device_id)) {
            return response()->json([
                'success' => false,
                'message' => 'This door lock does not support Cloud Unlock.',
            ], 400);
        }

        try {
            // Send command to Tuya Cloud
            $tuyaService->remoteUnlock($lock->tuya_device_id);

            // Log successful access
            AccessLog::create([
                'access_card_id' => $card->id,
                'door_lock_id' => $lock->id,
                'method' => 'cloud',
                'status' => 'granted',
                'verified_at' => \Carbon\Carbon::now(),
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Door unlocked successfully via Cloud.',
            ]);

        } catch (\Exception $e) {
            // Log failed access due to cloud error
            AccessLog::create([
                'access_card_id' => $card->id,
                'door_lock_id' => $lock->id,
                'method' => 'cloud',
                'status' => 'denied',
                'verified_at' => \Carbon\Carbon::now(),
                'ip_address' => $request->ip(),
            ]);

            \Illuminate\Support\Facades\Log::error('Tuya Unlock Failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Cloud Unlock failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
