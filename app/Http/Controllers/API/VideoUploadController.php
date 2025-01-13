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
        'video' => 'required|array|max:5',
        'video.*' => 'mimes:mp4,avi,mov,mkv|max:20000', 
        'title' => 'required|array|max:5',
        'title.*' => 'required|string|max:255', 
        'description' => 'required|array|max:5', 
        'description.*' => 'required|string', 
    ]);

    if ($validator->fails()) {
        return $this->error('Validation Error.', $validator->errors());
    }

    $user = auth()->user();
    if(!$user)
    {
        return response()->json(['message' => 'User not found.'], 404);
    }
    
    DB::beginTransaction();

    try {
        $videos = $request->file('video'); 
        $titles = $request->title; 
        $descriptions = $request->description; 

        $user = auth()->user();
        $responseMessages = [];

        // Ensure that the arrays for title, description, and video all have the same number of elements
        if (count($titles) !== count($descriptions) || count($titles) !== count($videos)) {
            return $this->error('The number of videos, titles, and descriptions must be the same.');
        }

        // Iterate through each video, title, and description and upload them
        foreach ($titles as $index => $title) {
            $description = $descriptions[$index];
            $video = $videos[$index];

            
            $videoPath = Helper::videoUpload($video, 'videos', $title);

            // Check if video already exists
            $existingVideo = VideoUpload::where('title', $title)->first();

           
            if ($existingVideo) {
                continue;
            }
 
            $videoRecord = new VideoUpload();
            $videoRecord->title = $title;
            $videoRecord->description = $description;
            $videoRecord->video = $videoPath;
            $videoRecord->user_id = $user->id;
            $videoRecord->save();

            // Add success message for this video
            $responseMessages[] = "Video with title '{$title}' has been added successfully.";
        }

        DB::commit();

        // Return success response
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
    if(!$user)
    {
        return response()->json(['message' => 'User not found.'], 404);
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

    if($validator->fails())
    {
        return response()->json(['errors' => $validator->errors()], 400);
    }

    $user = auth()->user();
    if(!$user)
    {
        return response()->json(['message' => 'User not found.'], 404);
    }

   try {
    $video = VideoUpload::where('user_id', $user->id)->find($id);

    if (!$video) {
        return response()->json(['message' => 'Video not found.'], 404);
    }

    $video->title = $request->title;
    $video->description = $request->description;

    if ($request->hasFile('video')) {
            //delete old video file 
            if ($video->video) {
                Helper::videoDelete($video->video);
            }

        $videoPath = Helper::videoUpload($request->file('video'), 'videos', $request->title);
        $video->video = $videoPath;
    }
   
    $video->save();
    DB::commit();
    return response()->json([
        'message' => 'Video updated successfully.',
        'video' => $video
    ]); 
   } catch (Exception $e) {
    DB::rollBack();
    return response()->json(['message' => 'An error occurred while updating the video.'], 400);
   }
}
// delete video
public function deleteVideo($id)
{
    $user = auth()->user();
    if(!$user)
    {
        return response()->json(['message' => 'User not found.'], 404);
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
