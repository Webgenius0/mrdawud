<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Helper\Helper;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Exception;
use Illuminate\Support\Facades\Storage;



class UserController extends Controller
{
    use apiresponse;

    /**
     * Update user primary info
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function updateUserInfo(Request $request)
    {
        $user = Auth::user();
    
        // Validation for both instructors and regular users
        if ($user->role === 'instructor') { 
            $validation = Validator::make($request->all(), [
                'video' => 'nullable|array',
                'video.*' => 'nullable|mimes:mp4,avi,mkv|max:20000',
                'title' => 'nullable|array',
                'title.*' => 'nullable|string',
                'description' => 'nullable|array',
                'description.*' => 'nullable|string',
                'document' => 'nullable|array',
                'document.*' => 'nullable|mimes:pdf,doc,docx|max:20000',
                'image' => 'nullable|array',
                'image.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // Image validation for all users
            ]);
        } else {
            $validation = Validator::make($request->all(), [
                'username' => ['nullable', 'string', 'max:255'],
                'language' => ['nullable', 'string', 'in:en,ar'],
                'city' => ['nullable', 'string', 'max:50'],
                'state' => ['nullable', 'string', 'max:50'],
                'image' => 'nullable|array',
                'image.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // Image validation for all users
                'age' => ['nullable', 'integer', 'min:14'],
                'phone' => ['nullable', 'string'],
                'bio' => ['nullable', 'string'],
                'country'=>['nullable','string'],
                'lat' => ['nullable', 'string'],
                'lng' => ['nullable', 'string'],
            ]);
        }
    
        // If validation fails
        if ($validation->fails()) {
            return $this->error([], $validation->errors(), 500);
        }
    
        DB::beginTransaction();
        try {
            // Update the basic user information
            $user->update($request->only([
                'username',
                'email',
                'password',
                'language',
                'city',
                'state',
                'age',
                'phone',
                'bio',
                'country',
                'lat',
                'lng',
            ]));
    
            // Handle image upload for all users (both regular users and instructors)
            if ($request->hasFile('image')) {
                // Delete old images if they exist
                foreach ($user->image as $oldImage) {
                    $oldFilePath = public_path($oldImage->image);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
    
                // Remove any old image records from the database
                $user->image()->delete(); 
    
                // Upload new images
                $userImages = [];
                foreach ($request->file('image') as $image) {
                    $url = Helper::fileUpload($image, 'users', $user->username . "-" . uniqid() . "-" . time());
                    if (!$url) {
                        return $this->error([], "Failed to upload image", 500); // Error if URL is empty
                    }
                    $userImages[] = ['image' => $url];
                }
    
                // Save new images to the database
                $user->image()->createMany($userImages);
            }
    
            // Handle instructor-specific files (video, document)
            if ($user->role === 'instructor') {
                $validated['username'] = $request->input('first_name') . ' ' . $request->input('last_name');
                $user->update($validated);
                // Video upload
                if ($request->hasFile('video')) {
                    $titles = $request->title ?? [];
                    $descriptions = $request->description ?? [];
    
                    // Ensure that the number of videos, titles, and descriptions match
                    if (count($titles) !== count($descriptions) || count($titles) !== count($request->file('video'))) {
                        return $this->error([], 'The number of videos, titles, and descriptions must match', 400);
                    }
    
                    // Delete old videos
                    foreach ($user->videos as $oldVideo) {
                        $oldFilePath = public_path($oldVideo->video);
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                        }
                    }
    
                    $userVideos = [];
                    foreach ($request->file('video') as $key => $video) {
                        $videoUrl = Helper::fileUpload($video, 'videos', $user->username . "-" . uniqid() . "-" . time());
                        $userVideos[] = [
                            'video' => $videoUrl,
                            'title' => $titles[$key],
                            'description' => $descriptions[$key],
                        ];
                    }
    
                    // Delete old videos and create new ones
                    $user->videos()->delete();
                    $user->videos()->createMany($userVideos);
                }
    
                // Document upload
                if ($request->hasFile('document')) {
                    // Delete old documents
                    foreach ($user->documents as $oldDocument) {
                        $oldFilePath = public_path($oldDocument->document);
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                        }
                    }
    
                    $userDocuments = [];
                    foreach ($request->file('document') as $document) {
                        $documentUrl = Helper::fileUpload($document, 'documents', $user->username . "-" . uniqid() . "-" . time());
                        $userDocuments[] = ['document' => $documentUrl];
                    }
    
                    // Delete old documents and create new ones
                    $user->documents()->delete();
                    $user->documents()->createMany($userDocuments);
                }
                if($request->role==='instructor'){
                    
                }
                $user->update($validated);
            }
    
            // Commit the transaction
            DB::commit();
    
            return $this->success([
                'user' => $user,
            ], 'User updated successfully', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error([], $e->getMessage(), 400);
        }
    }
    

    /**
     * Change Password
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'old_password' => 'required|string|max:255',
            'new_password' => 'required|string|max:255',
        ]);

        if ($validation->fails()) {
            return $this->error([], $validation->errors(), 500);
        }

        try {
            $user = User::where('id', Auth::id())->first();
            if (password_verify($request->old_password, $user->password)) {
                $user->password = Hash::make($request->new_password);
                $user->save();
                return $this->success([], "Password changed successfully", 200);
            } else {
                return $this->error([], "Old password is incorrect", 500);
            }
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 500);
        }
    }



    /**
     * Get My Notifications
     * @return \Illuminate\Http\Response
     */
    public function getMyNotifications()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->get();
        return $this->success([
            'notifications' => $notifications,
        ], "Notifications fetched successfully", 200);
    }


    /**
     * Delete User
     * @return \Illuminate\Http\Response
     */
    public function deleteUser()
    {
        $user = User::where('id', Auth::id())->first();
        if ($user) {
            $user->delete();
            return $this->success([], "User deleted successfully", 200);
        } else {
            return $this->error("User not found", 404);
        }
    }
}
