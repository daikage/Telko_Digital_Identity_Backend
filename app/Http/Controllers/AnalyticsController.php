<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\ProfileView;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $profile = Profile::where('user_id', $user->id)->first();

        if (!$profile) {
            return response()->json(['views' => 0, 'shares' => 0, 'connections' => 0]);
        }

        $views = ProfileView::where('profile_id', $profile->id)->count();

        // This is a stub for the MVP, shares and connections can be extended later.
        return response()->json([
            'views' => $views,
            'shares' => 0,
            'connections' => 0
        ]);
    }
}
