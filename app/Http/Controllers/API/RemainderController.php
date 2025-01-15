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
            'audio' => 'required|array',
            'audio.*' => 'mimes:mp3|max:20000', // Array of audio files
            'type' => 'required|array',
            'type.*' => 'in:fajar,zuhur,asor,magrib,esha,jumma', // Array of types
            'date' => 'required|array',
            'date.*' => 'date', // Array of dates
            'time' => 'required|array',
            'time.*' => 'string', // Array of times
        ]);
        
        if ($validator->fails()) {
            return $this->error('Validation Error.', $validator->errors());
        }
        
        DB::beginTransaction();
        try {
            // Get all inputs as arrays
            $audioFiles = $request->file('audio');
            $types = $request->type;
            $dates = $request->date;
            $times = $request->time;
        
            $user = auth()->user();
            $responseMessage = [];
        
            // Loop through the arrays and insert each remainder
            $reminders = [];
            for ($i = 0; $i < count($audioFiles); $i++) {
                // Ensure each field is valid for the current index
                $audioPath = Helper::audioUpload($audioFiles[$i], 'audios', $types[$i]);
        
                // Create a new remainder entry
                $remainder = new Remainder();
                $remainder->user_id = $user->id;
                $remainder->type = $types[$i];
                $remainder->date = $dates[$i];
                $remainder->time = $times[$i];
                $remainder->audio = $audioPath;
        
                // Save the remainder
                $remainder->save();
                $reminders[] = $remainder;
        
                $responseMessage[] = "Remainder for type '{$types[$i]}' and date '{$dates[$i]}' has been added successfully.";
            }
        
            DB::commit();
        
            return $this->success([
                'messages' => $responseMessage,
                'reminders' => $reminders
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
