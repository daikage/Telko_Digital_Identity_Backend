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
}
