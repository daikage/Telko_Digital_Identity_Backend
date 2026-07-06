<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use App\Models\ProfileView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    public function show($username, Request $request)
    {
        $user = User::where('username', $username)->firstOrFail();
        $profile = Profile::where('user_id', $user->id)
            ->with(['experiences', 'socialLinks'])
            ->firstOrFail();

        // Track profile view asynchronously if possible, or synchronously
        if ($request->ip()) {
            ProfileView::create([
                'profile_id' => $profile->id,
                'viewer_ip' => $request->ip(),
            ]);
        }

        return response()->json([
            'user' => [
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
            ],
            'profile' => $profile,
        ]);
    }

    public function myProfile(Request $request)
    {
        $user = $request->user();
        $profile = Profile::where('user_id', $user->id)
            ->with(['experiences', 'socialLinks'])
            ->first();

        return response()->json([
            'user' => [
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
            ],
            'profile' => $profile,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'headline' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'linkedin' => 'nullable|string|max:255',
            'avatar_url' => 'nullable|string',
            'avatar_base64' => 'nullable|string',
            'theme_color' => 'nullable|string',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string',
            'skills' => 'nullable|array',
            'projects' => 'nullable|array',
            'experiences' => 'nullable|array',
            'social_links' => 'nullable|array',
        ]);

        $user = $request->user();

        DB::beginTransaction();
        try {
            $data = $request->only([
                'headline', 'bio', 'linkedin', 
                'avatar_url', 'theme_color', 'contact_email', 
                'contact_phone', 'skills', 'projects'
            ]);

            if ($request->filled('avatar_base64')) {
                $base64 = $request->input('avatar_base64');
                @list($type, $file_data) = explode(';', $base64);
                @list(, $file_data)      = explode(',', $file_data);
                if ($file_data) {
                    $image = base64_decode($file_data);
                    $imageName = 'avatars/' . Str::random(40) . '.png';
                    Storage::disk('public')->put($imageName, $image);
                    $data['avatar_url'] = url('storage/' . $imageName);
                }
            }

            $profile = Profile::updateOrCreate(
                ['user_id' => $user->id],
                $data
            );

            if ($request->has('experiences')) {
                $profile->experiences()->delete();
                foreach ($request->experiences as $exp) {
                    $profile->experiences()->create($exp);
                }
            }

            if ($request->has('social_links')) {
                $profile->socialLinks()->delete();
                foreach ($request->social_links as $link) {
                    $profile->socialLinks()->create($link);
                }
            }

            DB::commit();

            return response()->json(
                $profile->load(['experiences', 'socialLinks'])
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to update profile'], 500);
        }
    }
}
