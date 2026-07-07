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

        // Build avatar_url from avatar_data if no external URL is set
        $profileData = $profile->toArray();
        $profileData['avatar_url'] = $this->resolveAvatarUrl($profile);
        // Don't send raw base64 blob to public consumers
        unset($profileData['avatar_data']);

        return response()->json([
            'user' => [
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
            ],
            'profile' => $profileData,
        ]);
    }

    public function myProfile(Request $request)
    {
        $user = $request->user();
        $profile = Profile::where('user_id', $user->id)
            ->with(['experiences', 'socialLinks'])
            ->first();

        $profileData = $profile ? $profile->toArray() : null;
        if ($profile) {
            $profileData['avatar_url'] = $this->resolveAvatarUrl($profile);
            // Don't send raw base64 blob
            unset($profileData['avatar_data']);
        }

        return response()->json([
            'user' => [
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
            ],
            'profile' => $profileData,
        ]);
    }

    /**
     * Dedicated avatar upload endpoint.
     * Accepts multipart/form-data with an 'avatar' file field.
     * Compresses the image and stores it as base64 in the database.
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
        ]);

        $user = $request->user();
        $file = $request->file('avatar');

        // Read and compress the image
        $imageData = file_get_contents($file->getRealPath());
        $image = @imagecreatefromstring($imageData);

        if (!$image) {
            return response()->json(['error' => 'Invalid image file'], 422);
        }

        // Resize to max 400x400 to keep DB size reasonable
        $width = imagesx($image);
        $height = imagesy($image);
        $maxDim = 400;

        if ($width > $maxDim || $height > $maxDim) {
            $ratio = min($maxDim / $width, $maxDim / $height);
            $newWidth = (int) round($width * $ratio);
            $newHeight = (int) round($height * $ratio);
            $resized = imagecreatetruecolor($newWidth, $newHeight);

            // Preserve transparency for PNG
            imagealphablending($resized, false);
            imagesavealpha($resized, true);

            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($image);
            $image = $resized;
        }

        // Encode as JPEG for compression (quality 85)
        ob_start();
        imagejpeg($image, null, 85);
        $compressedData = ob_get_clean();
        imagedestroy($image);

        $base64 = 'data:image/jpeg;base64,' . base64_encode($compressedData);

        // Store in the database
        $profile = Profile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'avatar_data' => $base64,
                'avatar_url' => null, // Clear any old external URL
            ]
        );

        return response()->json([
            'message' => 'Avatar uploaded successfully',
            'avatar_url' => $base64,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'headline' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'linkedin' => 'nullable|string|max:255',
            'avatar_url' => 'nullable|string',
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

            // Don't overwrite avatar_data via the general update endpoint
            // Avatar uploads are handled by the dedicated uploadAvatar endpoint
            // Remove avatar_base64 if accidentally sent
            unset($data['avatar_base64']);

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

            $result = $profile->load(['experiences', 'socialLinks'])->toArray();
            $result['avatar_url'] = $this->resolveAvatarUrl($profile);
            unset($result['avatar_data']);

            return response()->json($result);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update profile',
                'error' => app()->environment('production') ? null : $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Resolve the effective avatar URL for a profile.
     * Prioritizes avatar_data (base64 stored in DB) over avatar_url (external link).
     */
    private function resolveAvatarUrl(Profile $profile): ?string
    {
        if (!empty($profile->avatar_data)) {
            return $profile->avatar_data;
        }

        if (!empty($profile->avatar_url)) {
            return $profile->avatar_url;
        }

        return null;
    }
}
