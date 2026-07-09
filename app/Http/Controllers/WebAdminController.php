<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use App\Models\ProfileView;
use App\Models\DoorLock;
use App\Models\AccessCard;
use App\Models\AccessLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WebAdminController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->is_admin) {
            return redirect('/admin/dashboard');
        }
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            if (Auth::user()->is_admin) {
                return redirect()->intended('/admin/dashboard');
            }

            Auth::logout();
            return back()->withErrors([
                'email' => 'You do not have administrative access.',
            ]);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/admin/login');
    }

    public function dashboard()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect('/admin/login');
        }

        $totalUsers = User::count();
        $totalProfiles = Profile::count();
        $activeCards = Profile::whereNotNull('headline')->count();

        $recentSignups = User::orderBy('created_at', 'desc')->take(5)->get();
        $recentViews = ProfileView::with('profile.user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $allUsers = User::with('profile')->orderBy('created_at', 'desc')->paginate(50);

        return view('admin.dashboard', compact('totalUsers', 'totalProfiles', 'activeCards', 'recentSignups', 'recentViews', 'allUsers'));
    }

    // ──────────────────────────────────────────────
    // Door Locks & Access Cards (Server-Side Blade)
    // ──────────────────────────────────────────────

    /**
     * Show the door locks management page with all data.
     */
    public function doorLocks(Request $request)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect('/admin/login');
        }

        $locks = DoorLock::withCount('accessCards')->orderBy('created_at', 'desc')->get();
        $cards = AccessCard::with(['user', 'doorLock'])->orderBy('created_at', 'desc')->get();
        $logs = AccessLog::with(['accessCard.user', 'doorLock'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);
        $allUsers = User::orderBy('name')->get();

        // Metrics
        $totalLocks = DoorLock::count();
        $totalCards = AccessCard::where('is_active', true)->count();
        $totalGranted = AccessLog::where('status', 'granted')->count();
        $totalDenied = AccessLog::where('status', 'denied')->count();

        // Check if editing a lock
        $editLock = null;
        if ($request->has('edit')) {
            $editLock = DoorLock::find($request->edit);
        }

        return view('admin.doorlocks', compact(
            'locks', 'cards', 'logs', 'allUsers',
            'totalLocks', 'totalCards', 'totalGranted', 'totalDenied',
            'editLock'
        ));
    }

    /**
     * Create a new door lock.
     */
    public function storeDoorLock(Request $request)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect('/admin/login');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'lock_type' => 'required|in:nfc,ble,both,tuya',
            'tuya_device_id' => 'nullable|string|max:255',
            'nfc_aid' => 'nullable|string|max:255',
            'ble_service_uuid' => 'nullable|string|max:255',
            'ble_characteristic_uuid' => 'nullable|string|max:255',
        ]);

        $secretKey = Str::random(64);

        DoorLock::create([
            'name' => $request->name,
            'location' => $request->location,
            'lock_type' => $request->lock_type,
            'tuya_device_id' => $request->tuya_device_id,
            'nfc_aid' => $request->nfc_aid,
            'ble_service_uuid' => $request->ble_service_uuid,
            'ble_characteristic_uuid' => $request->ble_characteristic_uuid,
            'secret_key' => $secretKey,
        ]);

        return redirect()->route('admin.doorlocks')
            ->with('success', 'Door lock created successfully.')
            ->with('secret_key', $secretKey);
    }

    /**
     * Update an existing door lock.
     */
    public function updateDoorLock(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect('/admin/login');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'lock_type' => 'required|in:nfc,ble,both,tuya',
            'tuya_device_id' => 'nullable|string|max:255',
            'nfc_aid' => 'nullable|string|max:255',
            'ble_service_uuid' => 'nullable|string|max:255',
            'ble_characteristic_uuid' => 'nullable|string|max:255',
        ]);

        $lock = DoorLock::findOrFail($id);
        $lock->update([
            'name' => $request->name,
            'location' => $request->location,
            'lock_type' => $request->lock_type,
            'tuya_device_id' => $request->tuya_device_id,
            'nfc_aid' => $request->nfc_aid,
            'ble_service_uuid' => $request->ble_service_uuid,
            'ble_characteristic_uuid' => $request->ble_characteristic_uuid,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.doorlocks')
            ->with('success', 'Door lock updated successfully.');
    }

    /**
     * Assign an access card to a user for a door lock.
     */
    public function assignAccessCard(Request $request)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect('/admin/login');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'door_lock_id' => 'required|exists:door_locks,id',
            'card_name' => 'nullable|string|max:255',
            'expires_at' => 'nullable|date|after:now',
        ]);

        // Check for existing assignment
        $existing = AccessCard::where('user_id', $request->user_id)
            ->where('door_lock_id', $request->door_lock_id)
            ->first();

        if ($existing) {
            return redirect()->route('admin.doorlocks')
                ->with('error', 'This user already has an access card for this door lock.');
        }

        $lock = DoorLock::findOrFail($request->door_lock_id);

        AccessCard::create([
            'user_id' => $request->user_id,
            'door_lock_id' => $request->door_lock_id,
            'card_token' => Str::uuid()->toString(),
            'card_name' => $request->card_name ?: $lock->name,
            'expires_at' => $request->expires_at,
        ]);

        return redirect()->route('admin.doorlocks')
            ->with('success', 'Access card assigned successfully.');
    }

    /**
     * Revoke (delete) an access card.
     */
    public function revokeAccessCard($id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect('/admin/login');
        }

        $card = AccessCard::findOrFail($id);
        $userName = $card->user->name ?? $card->user->username ?? 'Unknown';
        $card->delete();

        return redirect()->route('admin.doorlocks')
            ->with('success', "Access card revoked for {$userName}.");
    }
}

