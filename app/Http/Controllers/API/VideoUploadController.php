<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Helper\Helper;
use Illuminate\Support\Facades\DB;
use App\Traits\apiresponse;
use App\Http\Controllers\Controller;
use App\Models\VideoUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class VideoUploadController extends Controller
{
    use apiresponse;
    
    public function uploadVideo(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'video' => 'required|array|max:5', // Multiple videos allowed
            'video.*' => 'mimes:mp4,avi,mov,mkv|max:20000', 
            'title' => 'required|string|max:255', // Single title for all videos
            'description' => 'required|string', // Single description for all videos
        ]);
    
        if ($validator->fails()) {
            return $this->error('Validation Error.', $validator->errors());
        }
    
        $user = auth()->user();
    
        // Check if the user is an instructor
        if (!$user || $user->role !== 'instructor') {
            return response()->json(['message' => 'User not found or user not authorized.'], 404);
        }
    
        DB::beginTransaction();
    
        try {
            $videos = $request->file('video'); 
            $title = $request->title;  
            $description = $request->description; 
            $responseMessages = [];
    
           
            if (count($videos) == 0) {
                return $this->error('At least one video is required.');
            }
    
            // Iterate through each video and upload it
            foreach ($videos as $video) {
    
                
                $originalFilename = $video->getClientOriginalName();
    
                // Generate a unique filename using the user's username, uniqid, and original filename
                $uniqueFilename = $user->username . '_' . uniqid() . '_' . $originalFilename;
    
                // Upload the video with the unique filename
                $videoPath = Helper::videoUpload($video, 'videos', $uniqueFilename);
    
                // Create a new video record in the database
                $videoRecord = new VideoUpload();
                $videoRecord->title = $title; 
                $videoRecord->description = $description; 
                $videoRecord->video = $videoPath; 
                $videoRecord->user_id = $user->id;
                $videoRecord->save();
    
                // Add success message for this video
                $responseMessages = "Video has been added successfully.";
            }
    
            DB::commit();
    
            // Return success response with messages
            return $this->success([
                'messages' => $responseMessages,
            ], 'Videos added successfully.', 200);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error('An error occurred while adding the video(s).', $e->getMessage());
        }
    }


    

// show all videos
public function showVideo()
{
    $user = auth()->user();
    if(!$user || $user->role !== 'instructor')
    {
        return response()->json(['message' => 'User not found or user not authorized.'], 404);
    }
   try {
    $videos = VideoUpload::where('user_id', $user->id)->select('id','user_id', 'title', 'description', 'video')->get();

    return response()->json([
        'message' => 'Videos fetched successfully.',
        'videos' => $videos
    ]);
   } catch (Exception $e) {
    DB::rollBack();
    return response()->json(['message' => 'An error occurred while showing the video(s).'], 400);
   }
}
    

// video edit

public function editVideo(Request $request, $id)
{
   
    $validator = Validator::make($request->all(), [
        'video' => 'nullable|mimes:mp4,avi,mov,mkv|max:20000',
        'title' => 'required|string|max:255',
        'description' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }

    $user = auth()->user();
    if (!$user || $user->role !== 'instructor') {
        return response()->json(['message' => 'User not found or user not authorized.'], 404);
    }

    DB::beginTransaction();  

    try {
        // Find the video by user id and video id
        $video = VideoUpload::where('user_id', $user->id)->find($id);

        if (!$video) {
            return response()->json(['message' => 'Video not found.'], 404);
        }

        
        $video->title = $request->title;
        $video->description = $request->description;

        // If new video file is uploaded, process it
        if ($request->hasFile('video')) {
            // Delete old video file if it exists
            if ($video->video) {
                Helper::videoDelete($video->video);
            }

            $originalFilename = $request->file('video')->getClientOriginalName();

            $username = $user->username; 
            $uniqueFilename = $username . '_' . uniqid() . '_' . $originalFilename;

            // Upload the video with the new unique filename
            $videoPath = Helper::videoUpload($request->file('video'), 'videos', $uniqueFilename);
            $video->video = $videoPath; // Update video path
        }

        $video->save();  
        DB::commit();  

        
        return response()->json([
            'message' => 'Video updated successfully.',
            'video' => $video
        ]);
    } catch (Exception $e) {
        DB::rollBack();  
        return response()->json(['message' => 'An error occurred while updating the video.', 'error' => $e->getMessage()], 400);
    }
}


// delete video
public function deleteVideo($id)
{
    $user = auth()->user();
    if(!$user)
    {
        return response()->json(['message' => 'User not found or user not authorized.'], 404);
    }
    try {
        $video = VideoUpload::where('user_id', $user->id)->find($id);
        if (!$video) {
            return response()->json(['message' => 'Video not found.'], 404);
        }
        if ($video->video) {
            Helper::videoDelete($video->video);
        }
        $video->delete();
        return response()->json([
            'message' => 'Video deleted successfully.', 
        ]);
    } catch (Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'An error occurred while deleting the video.'], 400);
    }       
}


}