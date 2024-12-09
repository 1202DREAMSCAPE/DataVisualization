<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // Ensure the User model is imported

class ProfilePageController extends Controller
{
    /**
     * Display the authenticated user's profile.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        // Retrieve the currently authenticated user
        $user = Auth::user();

        // Pass the user data to the profile page view
        return view('profilepage', compact('user'));
    }

    /**
     * Update the authenticated user's profile.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // Validation rules
        $rules = [
            'name' => 'sometimes|required|string|max:255',
            'username' => 'sometimes|required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'current_password' => 'sometimes|required_with:new_password|current_password',
            'new_password' => 'sometimes|required_with:current_password|confirmed|min:8',
        ];

        $validatedData = $request->validate($rules);

        $updatedFields = [];

        // Update Name
        if ($request->has('name') && isset($validatedData['name'])) {
            $user->name = $validatedData['name'];
            $updatedFields[] = 'Name';
        }

        // Update Username
        if ($request->has('username') && isset($validatedData['username'])) {
            $user->username = $validatedData['username'];
            $updatedFields[] = 'Username';
        }

        // Update Email
        if ($request->has('email') && isset($validatedData['email'])) {
            $user->email = $validatedData['email'];
            $updatedFields[] = 'Email';
        }

        // Update Profile Picture
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if it exists
            if ($user->profile_picture) {
                Storage::delete('public/' . $user->profile_picture);
            }

            // Store the new profile picture
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
            $updatedFields[] = 'Profile Picture';
        }

        // Update Password
        if ($request->filled('new_password')) {
            $user->password = Hash::make($validatedData['new_password']);
            $updatedFields[] = 'Password';
        }

        // Save the updated user model if any changes were made
        if (!empty($updatedFields)) {
            $user->save();
            $fields = implode(', ', $updatedFields);
            return redirect()->route('profile')->with('success', "{$fields} updated successfully.");
        }

        return redirect()->route('profile')->with('success', 'No changes were made.');
    }
}
