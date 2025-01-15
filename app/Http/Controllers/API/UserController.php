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
     
        $validation = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . Auth::id()],
            'language' => ['required', 'string', 'in:en,ar'],
            'city' => ['nullable', 'string', 'max:50'],
            'state' => ['nullable', 'string', 'max:50'],
            'image' => ['nullable', 'array'],
            'image.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'age' => ['nullable', 'integer', 'min:14'],
            'phone' => ['nullable', 'string'],
            'bio' => ['nullable', 'string'],
        ]);

        if ($validation->fails()) {
            return $this->error([], $validation->errors(), 500);
        }

        DB::beginTransaction();
        $user = Auth::user();
        try {
            $user = Auth::user();

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
            ]));



            if ($request->hasFile('image')) {
                foreach ($user->image as $oldImage) {   
                   // dd($oldImage);              
                    $oldFilePath = 'uploads/users/' . $oldImage->image; 

                    $deleteResult = Storage::disk('public')->delete($oldFilePath);
                    if (!$deleteResult) {
                        return $this->error([], 'Failed to delete old image', 500);
                    }
                    if()
                }
            
                $userImages = [];
                foreach ($request->file('image') as $image) {
                    $url = Helper::fileUpload($image, 'users',  $user->username . "-" . uniqid(). "-" . time());
                    array_push($userImages, [
                        'image' => $url
                    ]);
                }

               

                $user->image()->delete();
                $user->image()->createMany($userImages);
            }
            if ($user->role === 'instructor') {
                $validation->after(function ($validator) use ($request) {
                    $validator->addRules([
                        'video' => 'nullable|array',
                        'video.*' => 'nullable|mimes:mp4,avi,mkv|max:20000',
                        'title' => 'nullable|array',
                        'title.*' => 'nullable|string',
                        'description' => 'nullable|array',
                        'description.*' => 'nullable|string',
                        'document' => 'nullable|array',
                        'document.*' => 'nullable|mimes:pdf,doc,docx|max:20000',
                    ]);
                });

                if ($validation->fails()) {
                    return $this->error([], $validation->errors(), 500);
                }

                if ($request->hasFile('video')) {                   
                    $titles = $request->title ?? [];
                    $descriptions = $request->description ?? [];
                   
                    if (count($titles) !== count($descriptions) ||  count($titles) !== count($request->file('video'))) {
                        return $this->error('The number of videos, titles, descriptions');
                    }
                    // Prepare the videos array for insertion
                    $userVideos = [];
                    foreach ($request->file('video') as $key => $video) {                       
                        $videoUrl = Helper::fileUpload($video, 'videos', $user->username ."-" .uniqid(). "-" . time());
                        array_push($userVideos, [
                            'video' => $videoUrl,
                            'title' => $titles[$key],
                            'description' => $descriptions[$key],
                        ]);
                    }  
                    foreach ($user->videos as $oldVideo) {
                        // Delete the old video file from the folder (ensure path is correct)
                        $oldFilePath = storage_path('app/public/' . $oldVideo->video);  // Assuming videos are stored in the 'public' disk
                        
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);  // Delete the old video file
                        }
                    }                
                    $user->videos()->delete();
                    $user->videos()->createMany($userVideos);
                }

                // Handle document uploads if they exist in the request
                if ($request->hasFile('document')) {
                    $userDocuments = [];
                    foreach ($request->file('document') as $key => $document) {
                        $documentUrl = Helper::fileUpload($document, 'documents', $user->username. "-" .uniqid(). "-" . time());
                        array_push($userDocuments, [
                            'document' => $documentUrl,                       
                        ]);
                    }                  
                    $user->documents()->delete(); // Assuming you have a `documents()` relationship in the User model
                    $user->documents()->createMany($userDocuments);
                }
            }
            
            DB::commit();

            return $this->success([
                'user' => $user,
            ], 'User updated successfully', 200);
        } catch (\Exception $e) {
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
