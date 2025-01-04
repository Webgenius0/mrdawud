<?php

namespace App\Http\Controllers\Web\backend\settings;

use Exception;
use App\Services\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileSettingController extends Controller
{

    public function index(Request $request)
    {

        return view('backend.layout.setting.profileSettings');
    }

    public function updateProfile(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
        ]);

        try {

            $user = Auth::user();
            $user->name = $request->name;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->save();

            flash()->success( 'Profile updated successfully.');
            return redirect()->back();
        } catch (Exception $e) {
            flash()->error( $e->getMessage());
            return redirect()->back();
        }
    }

    public function updatePassword(Request $request)
    {

        $request->validate([
            'old_password' => 'required|string|min:8',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // dd($validatedData);

        try {

            if (! Hash::check($request->old_password, Auth::user()->password)) {
                throw new Exception('Old password does not match.');
            }

            $user = Auth::user();
            $user->password = Hash::make($request->password);
            $user->save();

            flash()->success( 'Password updated successfully.');
            return redirect()->route('profile');
        } catch (Exception $e) {
            flash()->error( $e->getMessage());
            return redirect()->back();
        }
    }

    public function updateProfilePicture(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            $user = Auth::user();

            if ($request->hasFile('profile_picture')) {
                // Delete old avatar if exists
                if ($user->avatar && Storage::exists($user->avatar)) {
                    Storage::delete($user->avatar);
                }

                // Upload new file
                $path = Service::fileUpload($request->file('profile_picture'), 'profile_pictures');
                $user->avatar = $path;
                $user->save();

                $imageUrl = asset($user->avatar); // Generate the URL of the uploaded image

                return response()->json([
                    'success' => true,
                    'message' => 'Profile picture updated successfully.',
                    'image_url' => $imageUrl, // Send the image URL to the frontend
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'No file uploaded.',
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
}
