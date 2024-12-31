<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    // Get all users
    public function index()
    {
        // Return all users
        $users = User::all();
        return response()->json($users);
    }

    // Create a new user
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048', // Validate image
            'role' => 'required|string',
        ]);

        // Handle the profile picture upload if exists
        if ($request->hasFile('profile_picture')) {
            $profilePicture = $request->file('profile_picture');
            // Generate a unique file name for the image
            $fileName = Str::random(40) . '.' . $profilePicture->getClientOriginalExtension();
            // Store the image in the 'public/images' folder
            $profilePicture->move(public_path('images'), $fileName);
            $path = 'images/' . $fileName; // Store relative file path
        } else {
            $path = null;
        }

        // Create the new user
        $user = User::create([
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'profile_picture' => $path,  // Store relative file path
            'role' => $request->input('role'),
        ]);

        // Return the user data with the image URL
        return response()->json([
            'user' => $user,
            'profile_picture_url' => url($path) // Return the full URL for the image
        ], 201);
    }

    // Get a single user by ID
    public function show($id)
    {
        // Fetch user by ID
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    // Update an existing user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validate the incoming request
        $request->validate([
            'username' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $id,
            'password' => 'sometimes|required|string|min:8',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'role' => 'sometimes|required|string',
        ]);

        // Handle the profile picture upload if exists
        if ($request->hasFile('profile_picture')) {
            // Delete the old profile picture if it exists
            if ($user->profile_picture && Storage::exists(public_path($user->profile_picture))) {
                unlink(public_path($user->profile_picture)); // Delete old image
            }

            // Store the new image
            $profilePicture = $request->file('profile_picture');
            $fileName = Str::random(40) . '.' . $profilePicture->getClientOriginalExtension();
            $profilePicture->move(public_path('images'), $fileName);
            $path = 'images/' . $fileName; // Store relative file path
        } else {
            $path = $user->profile_picture; // Keep the existing profile picture if no new file uploaded
        }

        // Update user data
        $user->update([
            'username' => $request->input('username', $user->username),
            'email' => $request->input('email', $user->email),
            'password' => $request->has('password') ? bcrypt($request->input('password')) : $user->password,
            'profile_picture' => $path,
            'role' => $request->input('role', $user->role),
        ]);

        return response()->json($user);
    }

    // Delete a user
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Delete the profile picture if it exists
        if ($user->profile_picture && Storage::exists(public_path($user->profile_picture))) {
            unlink(public_path($user->profile_picture)); // Delete the image
        }

        // Delete the user from the database
        $user->delete();

        return response()->json(null, 204);
    }
}
