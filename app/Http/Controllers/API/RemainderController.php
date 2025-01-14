<?php

namespace App\Http\Controllers\API;
use Exception;
use App\Helper\Helper;
use Illuminate\Support\Facades\DB;
use App\Traits\apiresponse;
use App\Http\Controllers\Controller;
use App\Models\Remainder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class RemainderController extends Controller
{
    use apiresponse;

    public function remainder(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'audio' => 'required|mimes:mp3|max:20000',
            'type' => 'required|in:fajar,zuhur,asor,magrib,esha,jumma',
            'date' => 'required|date',
            'time' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->error('Validation Error.', $validator->errors());
        }

        DB::beginTransaction();
        try {
            $audio = $request->file('audio');
            $type = $request->type;
            $date = $request->date;
            $time = $request->time;

            $user = auth()->user();
            $audioPath = Helper::audioUpload($audio, 'audios', $type);

            $responseMessage = [];

            $remainder = new Remainder();
            $remainder->user_id = $user->id;
            $remainder->type = $type;
            $remainder->date = $date;
            $remainder->time = $time;
            $remainder->audio = $audioPath;
            $remainder->save();

            $responseMessage[] = "Remainder for type '{$type}' and date '{$date}' has been added successfully.";


            DB::commit();

            return $this->success([
                'messages' => $responseMessage,

                'remainder' => $remainder

            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error('An error occurred while adding or updating the remainder.', $e->getMessage());
        }
    }



    //show remainder-List  by user

    public function remainderList()
    {

        try {
            $user = auth()->user();

            if (!$user) {
                return $this->error(('User not found'), 404);
            }

            $remainders = Remainder::where('user_id', $user->id)->select('id', 'type', 'date', 'time', 'audio')->get();

            if ($remainders->isEmpty()) {
                return $this->error('Remainder not found', 404);
            }

            return $this->success([
                'remainders' => $remainders,

            ], 'Remainder List has been fetched successfully', 200);
        } catch (Exception $e) {
            return $this->error('Something went wrong', 500);
        }
    }


    //Edit remainder-list by user

    public function remainderEdit(Request $request, $id)
    {
        try {
            $validation = Validator::make($request->all(), [
                'type' => 'nullable|in:fajar,zuhar,asor,magrib,esha,jumma',
                'time' => 'nullable|string',
                'date' => 'nullable|date',
                'audio' => 'nullable|mimes:mp3|max:20000',
            ]);
    
            if ($validation->fails()) {
                return response()->json([
                    "message" => "Validation Error.",
                    "errors" => $validation->errors(),
                ], 422);
            }
    
            DB::beginTransaction();
    
            $remainder = Remainder::find($id);
    
            if (!$remainder) {
                return response()->json([
                    "message" => "Remainder not found.",
                ], 404);
            }
    
            $user = auth()->user();
    
            
            // Only update fields if they are present in the request
    
            if ($request->has('type')) {
                $remainder->type = $request->type;
            }
            
            if ($request->has('date')) {
                $remainder->date = $request->date;
            }
    
            if ($request->has('time')) {
                $remainder->time = $request->time;
            }
    
            if ($request->hasFile('audio')) {
                // Delete the old audio file if it exists
                if ($remainder->audio && Storage::exists(public_path($remainder->audio))) {
                    Storage::delete(public_path($remainder->audio));
                }
    
                // Upload the new audio file
                $audio = $request->file('audio');
                $audioPath = Helper::audioUpload($audio, 'audios', $remainder->type);
                $remainder->audio = $audioPath;
            }
    
            $remainder->save();  
    
            DB::commit();
    
            return $this->success([
                'messages' => ["Remainder with ID '{$id}' has been updated successfully."],
                'remainder' => $remainder->only('id', 'type', 'date', 'time', 'audio', 'updated_at')
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();  
            return response()->json([
                "message" => "An error occurred while updating the remainder.",
                "error" => $e->getMessage(),
            ], 500);  
        }
    }
    

    //remainder delete
    public function remainderDelete(Request $request, $id)
    {

        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    "message" => "User not found.",
                ], 404);
            }

            $remainder = Remainder::where('user_id', $user->id)->find($id);

            if (!$remainder) {
                return response()->json([
                    "message" => "Remainder not found.",
                ], 404);
            }

            if ($remainder->audio && Storage::exists(public_path($remainder->audio))) {
                Storage::delete(public_path($remainder->audio));
            }
            $remainder->delete();
            return response()->json([
                "message" => "Remainder with ID '{$id}' has been deleted successfully.",
                'remainder' => $remainder->only('id', 'type', 'date', 'time', 'audio')
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "message" => "An error occurred while deleting the remainder.",
                "error" => $e->getMessage(),
            ]);
        }
    }

}
