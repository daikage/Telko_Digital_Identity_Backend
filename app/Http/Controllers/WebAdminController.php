<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Profile;
use App\Models\ProfileView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
