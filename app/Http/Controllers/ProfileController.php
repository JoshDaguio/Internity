<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Link;
use App\Models\SkillTag;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage; 
use Illuminate\View\View;
use Illuminate\Support\Str;


class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function profile(Request $request): View
    {
        $user = Auth::user();
        $profile = $user->profile;
        $links = $profile ? $profile->links : [];
        $skillTags = SkillTag::all();


        return view('profile.profile', compact('user', 'profile', 'links', 'skillTags'));
    }
    
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = Auth::user();
        $profile = $user->profile;
        $links = $profile ? $profile->links : [];

        return view('profile.edit', compact('user', 'profile', 'links'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $profile = $user->profile;
    
        // Update profile information
        $profile->update($request->only([
            'first_name', 'middle_name', 'last_name', 'id_number', 'about', 'address', 'contact_number'
        ]));

        // Allow Super Admin and Admin to update their email
        if ($user->role_id == 1 || $user->role_id == 2) {
            $request->validate([
                'email' => 'required|email|unique:users,email,' . $user->id,
            ]);
            $user->update(['email' => $request->input('email')]);
        }
        
        // Handle file upload for CV
        if ($request->hasFile('cv')) {
            // Delete old CV if it exists
            if ($profile->cv_file_path) {
                Storage::delete($profile->cv_file_path);
            }
    
            // Store new CV
            $file = $request->file('cv');
            $filePath = $file->store('cvs');
            $profile->cv_file_path = $filePath;
            $profile->save();
        }

        // Handle file upload for Profile Picture
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if it exists
            if ($profile->profile_picture) {
                Storage::disk('public')->delete($profile->profile_picture);
            }

            // Store new profile picture
            $file = $request->file('profile_picture');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $relativePath = 'profile_pictures/' . $filename;

            // Move the uploaded file to the public disk
            $file->storeAs('profile_pictures', $filename, 'public');

            // Update the profile picture path in the database
            $profile->profile_picture = $relativePath;
            $profile->save();
        }
    
        // Handle links update (adding new or updating existing)
        $profile->links()->delete(); // Clear old links
        if ($request->has('link_names') && $request->has('link_urls')) {
            foreach ($request->link_names as $index => $name) {
                if (!empty($name) && !empty($request->link_urls[$index])) {
                    $profile->links()->create([
                        'link_name' => $name,
                        'link_url' => $request->link_urls[$index],
                    ]);
                }
            }
        }

        // Sync skill tags
        $skillTags = $request->input('skill_tags', []); // Get selected skill tags or an empty array
        $profile->skillTags()->sync($skillTags); // Sync the skill tags (removes unselected ones)
        
        return redirect()->route('profile.profile')->with('status', 'Profile updated successfully.');
    }

    public function previewCV($id)
    {
        $profile = Profile::findOrFail($id);

        if (!$profile->cv_file_path) {
            abort(404);
        }

        $filePath = storage_path('app/' . $profile->cv_file_path);
        $fileMimeType = mime_content_type($filePath);

        return response()->file($filePath, [
            'Content-Type' => $fileMimeType,
        ]);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
