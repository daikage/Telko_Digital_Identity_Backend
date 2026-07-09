<?php

namespace App\Http\Controllers;

use App\Models\DoorLock;
use App\Models\AccessCard;
use App\Models\AccessLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DoorLockController extends Controller
{
    // ──────────────────────────────────────────────
    // Lock Hardware Verification (public, key-auth)
    // ──────────────────────────────────────────────

    /**
     * Verify an access token presented to a lock.
     * Called by the lock hardware (ESP32, etc.) via HTTP.
     *
     * Expected payload: { "token": "...", "lock_id": 1, "method": "nfc", "secret_key": "..." }
     */
    public function verify(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'lock_id' => 'required|integer',
            'method' => 'required|in:nfc,ble,qr',
            'secret_key' => 'required|string',
        ]);

        // Authenticate the lock via its secret key
        $lock = DoorLock::where('id', $request->lock_id)
            ->where('is_active', true)
            ->first();

        if (!$lock || !hash_equals($lock->secret_key, $request->secret_key)) {
            return response()->json([
                'granted' => false,
                'reason' => 'Invalid lock credentials',
            ], 403);
        }

        // Look up the access card by token
        $card = AccessCard::where('card_token', $request->token)
            ->where('door_lock_id', $lock->id)
            ->first();

        if (!$card) {
            // Log denied attempt
            AccessLog::create([
                'access_card_id' => 0, // unknown card
                'door_lock_id' => $lock->id,
                'method' => $request->input('method'),
                'status' => 'denied',
                'verified_at' => Carbon::now(),
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'granted' => false,
                'reason' => 'Unknown token',
            ]);
        }

        // Validate the card (active, not expired, within schedule)
        $isValid = $card->isValid();
        $status = $isValid ? 'granted' : ($card->expires_at && Carbon::now()->greaterThan($card->expires_at) ? 'expired' : 'denied');

        // Log the access attempt
        AccessLog::create([
            'access_card_id' => $card->id,
            'door_lock_id' => $lock->id,
            'method' => $request->input('method'),
            'status' => $status,
            'verified_at' => Carbon::now(),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'granted' => $isValid,
            'reason' => $isValid ? 'Access granted' : 'Access denied — card ' . $status,
            'user' => $isValid ? [
                'name' => $card->user->name ?? $card->user->username,
            ] : null,
        ]);
    }

    // ──────────────────────────────────────────────
    // Admin Endpoints (authenticated + is_admin)
    // ──────────────────────────────────────────────

    /**
     * List all door locks.
     */
    public function index(Request $request)
    {
        $locks = DoorLock::withCount('accessCards')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['locks' => $locks]);
    }

    /**
     * Create a new door lock.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'lock_type' => 'required|in:nfc,ble,both',
            'ble_service_uuid' => 'nullable|string|max:255',
            'ble_characteristic_uuid' => 'nullable|string|max:255',
            'nfc_aid' => 'nullable|string|max:255',
        ]);

        $lock = DoorLock::create([
            'name' => $request->name,
            'location' => $request->location,
            'lock_type' => $request->lock_type,
            'ble_service_uuid' => $request->ble_service_uuid,
            'ble_characteristic_uuid' => $request->ble_characteristic_uuid,
            'nfc_aid' => $request->nfc_aid,
            'secret_key' => Str::random(64), // Auto-generate secret key
        ]);

        return response()->json([
            'lock' => $lock,
            'secret_key' => $lock->secret_key, // Show once on creation
            'message' => 'Door lock created. Save the secret_key — it will not be shown again.',
        ], 201);
    }

    /**
     * Update a door lock.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'location' => 'nullable|string|max:255',
            'lock_type' => 'sometimes|in:nfc,ble,both',
            'ble_service_uuid' => 'nullable|string|max:255',
            'ble_characteristic_uuid' => 'nullable|string|max:255',
            'nfc_aid' => 'nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        $lock = DoorLock::findOrFail($id);
        $lock->update($request->only([
            'name', 'location', 'lock_type',
            'ble_service_uuid', 'ble_characteristic_uuid',
            'nfc_aid', 'is_active',
        ]));

        return response()->json(['lock' => $lock]);
    }

    /**
     * Assign an access card to a user for a specific door.
     */
    public function assignCard(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'door_lock_id' => 'required|exists:door_locks,id',
            'card_name' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date|after:now',
            'access_schedule' => 'nullable|array',
        ]);

        // Check if the user already has a card for this door
        $existing = AccessCard::where('user_id', $request->user_id)
            ->where('door_lock_id', $request->door_lock_id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'User already has an access card for this door.',
                'card' => $existing,
            ], 409);
        }

        $lock = DoorLock::findOrFail($request->door_lock_id);

        $card = AccessCard::create([
            'user_id' => $request->user_id,
            'door_lock_id' => $request->door_lock_id,
            'card_token' => Str::uuid()->toString(),
            'card_name' => $request->card_name ?? $lock->name,
            'expires_at' => $request->expires_at,
            'access_schedule' => $request->access_schedule,
        ]);

        return response()->json([
            'card' => $card->load('doorLock', 'user'),
            'message' => 'Access card assigned successfully.',
        ], 201);
    }

    /**
     * Revoke (delete) an access card.
     */
    public function revokeCard($id)
    {
        $card = AccessCard::findOrFail($id);
        $card->delete();

        return response()->json([
            'message' => 'Access card revoked.',
        ]);
    }

    /**
     * Get all access logs (admin view).
     */
    public function allLogs(Request $request)
    {
        $logs = AccessLog::with([
            'accessCard:id,card_name,user_id',
            'accessCard.user:id,name,username',
            'doorLock:id,name,location',
        ])
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        return response()->json(['logs' => $logs]);
    }

    /**
     * List all users (for the assign dropdown).
     */
    public function listUsers()
    {
        $users = User::select('id', 'name', 'username', 'email')
            ->orderBy('name')
            ->get();

        return response()->json(['users' => $users]);
    }
}
