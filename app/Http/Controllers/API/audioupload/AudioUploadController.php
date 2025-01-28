<?php

namespace App\Http\Controllers\API\audioupload;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helper\Helper;
use Exception;
use App\Models\AudioUpload;
use App\Models\User;
use App\Traits\apiresponse;
class AudioUploadController extends Controller
{
    use apiresponse;
    public function audioUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'audio' => ['nullable', 'file', 'mimes:mp3,wav,aac', 'max:10240'],
        ]);

        if($validator->fails())
        {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        $audio = $request->file('audio');

        if ($audio) {
            try {
                $path = Helper::audioUpload($audio, 'audio', $audio->getClientOriginalName());
                $oldAudio = AudioUpload::where('audio', $path)->first();
                if ($oldAudio) {
                    return response()->json([
                        
                        'message' => 'Audio already exists',
                    ], 200);
                }
               
                $audio= new AudioUpload();
                $audio->user_id = $user->id;
                $audio->audio = $path;
                $existingAudio = AudioUpload::where('user_id', $user->id)->first();
                if (!$existingAudio) {
                    $audio->status = 1; 
                }else{
                    $audio->status = 0;
                }
                $audio->save();
                return response()->json([
                    'path' => $path,
                    'message' => 'Audio uploaded successfully',
                    'status' => $audio->status == 1 ? 'Active' : 'Inactive',
                ], 200);
            } catch (Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }
    }

    //show audio
    public function showAudio()
    {
        $audio = AudioUpload::all();
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                "message" => "User not found or user not authorized.",
            ], 404);
        }
        return response()->json([
            'message' => 'Audio fetched successfully.',
            'audio' => $audio,
        ], 200);
    }

    //audio edit
    public function updateAudio(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'audio' => 'nullable|file|mimes:mp3,wav,aac',
            'status' => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        $audio = AudioUpload::find($id);
        if (!$audio) {
            return response()->json([
                "message" => "Audio not found.",
            ], 404);
        }
        if($audio->audio &&file_exists(public_path($audio->audio))){
            unlink(public_path($audio->audio));
        }

        $activeAudio=AudioUpload::where('status',1)->first();

        if($activeAudio && $activeAudio->id !=$id){
            $activeAudio->status=0;
            $activeAudio->save();
        }

        $audio->status = $request->status ?? $audio->status;
        $audio->audio = $request->audio;
        try {
            if ($request->hasFile('audio')) {
                
                $newAudioPath = Helper::audioUpload($request->file('audio'), 'audio', $request->file('audio')->getClientOriginalName());
               
                $audio->audio = $newAudioPath;
               
                
            }  
            if ($request->has('status')) {
                $audio->status = $request->status;
            }

            $audio->save();
            return response()->json([
                'message' => 'Audio Updated successfully.',
                'audio' => $audio,
            ], 200);
    
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
       
    }

    //audio delete
    public function deleteAudio($id)
    {
        $audio = AudioUpload::find($id);
        if (!$audio) {
            return response()->json([
                "message" => "Audio not found.",
            ], 404);
        }
        if($audio->audio &&file_exists(public_path($audio->audio))){
            unlink(public_path($audio->audio));
        }
        $audio->delete();
        return response()->json([
            "message" => "Audio deleted successfully.",
        ], 200);
    }
}
