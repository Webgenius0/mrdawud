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

class VideoUploadController extends Controller
{
    use apiresponse;
    public function uploadVideo(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'video' => 'required|mimes:mp4,avi,mov,mkv|max:20000',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation Error.', $validator->errors());
        }

        DB::beginTransaction();

        try {
            $video = $request->file('video');
            $title = $request->title;
            $description = $request->description;

            $user = auth()->user();
            $videoPath = Helper::videoUpload($video, 'videos', $title);


            $existingVideo = VideoUpload::where('title', $title)->first();
            $responseMessage = [];

            if ($existingVideo) {

                $existingVideo->update([
                    'description' => $description,
                    'video' => $videoPath,
                    'user_id' => $user->id,
                ]);
                $responseMessage[] = "Video with title '{$title}' has been updated successfully.";
            } else {

                $videoRecord = new VideoUpload();
                $videoRecord->title = $title;
                $videoRecord->description = $description;
                $videoRecord->video = $videoPath;
                $videoRecord->user_id = $user->id;
                $videoRecord->save();
                $responseMessage[] = "Video with title '{$title}' has been added successfully.";
            }

            DB::commit();

            return $this->success([
                'messages' => $responseMessage,
                'video' => $existingVideo ? $existingVideo : $videoRecord
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error('An error occurred while adding or updating the video.', $e->getMessage());
        }
    }
}
