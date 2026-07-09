<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use App\Models\ProfileView;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        // Simple authorization check
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $totalUsers = User::count();
        $totalProfiles = Profile::count();
        $totalViews = ProfileView::count();

        // Recent users as "activities"
        $recentSignups = User::orderBy('created_at', 'desc')->take(5)->get(['id', 'name', 'username', 'created_at']);
        
        // Recent profile views
        $recentViews = ProfileView::with('profile')->orderBy('created_at', 'desc')->take(5)->get();

        return response()->json([
            'total_users' => $totalUsers,
            'total_profiles' => $totalProfiles,
            'total_views' => $totalViews,
            'activities' => [
                'recent_signups' => $recentSignups,
                'recent_views' => $recentViews,
            ]
        ]);
    }

    public function users(Request $request)
    {
        // Authorization check
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $users = User::with('profile')->orderBy('created_at', 'desc')->get();
        return response()->json($users);
    }

    public function updateRfid(Request $request, $id)
    {
        // Authorization check
        if (!$request->user() || !$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'physical_rfid_uid' => 'nullable|string|max:255',
        ]);

        $user = User::findOrFail($id);
        
        // Ensure profile exists
        if (!$user->profile) {
            $user->profile()->create([
                'physical_rfid_uid' => $request->physical_rfid_uid
            ]);
        } else {
            $user->profile->update([
                'physical_rfid_uid' => $request->physical_rfid_uid
            ]);
        }

        return response()->json(['message' => 'RFID UID updated successfully.']);
    }
}
