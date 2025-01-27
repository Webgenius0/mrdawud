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
use App\Models\AudioUpload;
use Carbon\Carbon;

class RemainderController extends Controller
{
    use apiresponse;

    public function uploadReminder(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'audio_id' => 'required|array',
            'audio_id.*' => 'required|exists:audio_uploads,id',
            'type' => 'required|array',
            'type.*' => 'required', // Allowed types
            'date' => 'required|array',
            'date.*' => 'required|date', // Validate date format
            'time' => 'required|array',
            'time.*' => 'required|date_format:g:i a', // Validate time format (e.g., 5:20 pm)
        ]);

        // If validation fails, return errors
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error.',
                'data' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Get all inputs as arrays
            $audioIds = $request->audio_id; // Array of audio IDs
            $types = $request->type;
            $dates = $request->date;
            $times = $request->time;

            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found.',
                ], 404);
            }

            $responseMessages = []; // Store multiple messages

            // Loop through the arrays and insert each reminder
            for ($i = 0; $i < count($types); $i++) {
               
                $formattedTime = Carbon::createFromFormat('g:i a', $times[$i])->format('H:i:s');
                $formattedDate = Carbon::createFromFormat('Y-m-d', $dates[$i])->format('Y-m-d');

                // Fetch the audio file for the current reminder
                $audio = AudioUpload::find($audioIds[$i]);

                if (!$audio) {
                    $responseMessages[] = "Audio with ID {$audioIds[$i]} not found.";
                    continue; 
                }

                // Check if a reminder already exists for this combination
                $existingReminder = Remainder::where('user_id', $user->id)
                    ->where('type', $types[$i])
                    ->where('date', $formattedDate)
                    ->where('time', $formattedTime)
                    ->first();

                if ($existingReminder) {
                    $responseMessages[] =  ucfirst($types[$i]) . " reminder already exists.";
                    continue; // Skip this iteration
                }

                // Create a new reminder entry
                $reminder = new Remainder();
                $reminder->user_id = $user->id;
                $reminder->audio = $audio->id;
                $reminder->type = $types[$i];
                $reminder->date = $formattedDate;
                $reminder->time = $formattedTime;
                $reminder->save();

                $responseMessages[] = ucfirst($types[$i]) . " reminder has been added.";;
            }

            DB::commit();
            $concatenatedMessages = implode(' ', $responseMessages);
            // Return success with all messages
            return response()->json([
                'status' => 200,
                'message' =>  $concatenatedMessages,
               // 'data' => $concatenatedMessages,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'An error occurred while adding or updating the reminder.',
                'data' => $e->getMessage(),
            ], 500);
        }
    }
    
    
    
    

    //show remainder-List  by user

    public function remainderList()
{
    try {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Fetch reminders and sort them by date and time
        $remainders = Remainder::where('user_id', $user->id)
            ->with('audio:id,audio')
            ->select('id', 'type', 'date', 'time', 'audio')
            ->orderBy('date', 'asc')
            ->orderBy('time', 'asc')
            ->get();

        if ($remainders->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No reminders found.',
            ], 404);
        }

        $now = Carbon::now(); // Current date and time
        $remainingTimes = []; // Store remaining times

        foreach ($remainders as $remainder) {
            $reminderDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $remainder->date . ' ' . $remainder->time);
            
            if ($reminderDateTime->greaterThan($now)) {
                $remainingTime = $reminderDateTime->diffForHumans($now, [
                    'syntax' => Carbon::DIFF_ABSOLUTE,
                ]);
                $remainingTimes[] = [
                    'type' => ucfirst($remainder->type),
                    'remaining_time' => $remainingTime,
                    'time' => $reminderDateTime->format('g:i A'),
                ];
            }
        }

        // Check for the next reminder
        $nextReminder = collect($remainingTimes)->first();

        if (!$nextReminder) {
            $nextReminder = [
                'type' => 'No upcoming reminders today',
                'remaining_time' => 'N/A',
                'time' => 'N/A',
            ];
        }

        return response()->json([
            'status' => true,
            'message' => 'Reminders fetched successfully.',
            'next_reminder' => $nextReminder,
            'data' => [
                'remainders' => $remainders,
                
            ],
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Something went wrong.',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    //Edit remainder-list by user

    public function remainderEdit(Request $request, $id)
{
    try {
        // Validate the request
        $validation = Validator::make($request->all(), [
            'audio' => 'nullable|exists:audio_uploads,id', // Audio should be selected by audio ID (exists in AudioUpload model)
            'type' => 'nullable|in:fajar,zuhur,asor,magrib,esha,jumma',
            'time' => 'nullable|string',
            'date' => 'nullable|date',
        ]);

        if ($validation->fails()) {
            return response()->json([
                "message" => "Validation Error.",
                "errors" => $validation->errors(),
            ], 422);
        }

        DB::beginTransaction();

        // Find the remainder by ID
        $remainder = Remainder::find($id);

        if (!$remainder) {
            return response()->json([
                "message" => "Remainder not found.",
            ], 404);
        }

        $user = auth()->user();

        // Ensure the user owns the remainder
        if ($remainder->user_id !== $user->id) {
            return response()->json([
                "message" => "Unauthorized access.",
            ], 403);
        }

        // Check for duplicate remainder for the same type, date, and time
        if ($request->has('type') && $request->has('date') && $request->has('time')) {
            $existingRemainder = Remainder::where('user_id', $user->id)
                ->where('type', $request->type)
                ->where('date', $request->date)
                ->where('time', $request->time)
                ->where('id', '!=', $id)  // Exclude the current remainder
                ->first();

            if ($existingRemainder) {
                return response()->json([
                    "message" => "Remainder for type '{$request->type}' and date '{$request->date}' already exists.",
                ], 409);
            }
        }

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

        // Handle audio update (selecting an audio by its ID)
        if ($request->has('audio')) {
            // Validate if the audio exists in the AudioUpload table
            $audio = AudioUpload::find($request->audio); // Find the audio by its ID

            if (!$audio) {
                return response()->json([
                    "message" => "Audio not found.",
                ], 404);
            }

            // Assign the audio ID to the remainder
            $remainder->audio = $audio->id; // Store the audio ID in the remainder's audio field
        }

        // Save the updated remainder
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

        // Find the remainder by user_id and ID
        $remainder = Remainder::where('user_id', $user->id)->find($id);

        if (!$remainder) {
            return response()->json([
                "message" => "Remainder not found.",
            ], 404);
        }

        // If the remainder has an associated audio (audio_id), it is now a relationship, not a file path
        if ($remainder->audio) {
            // Optional: You could perform cleanup tasks like detaching or updating the related `AudioUpload`
            $audio = AudioUpload::find($remainder->audio); // Get the audio object using the audio_id
            if ($audio) {
                // You may choose to unlink the audio or perform some other task, such as logging or updating the audio record
                // For now, we're not doing anything to the audio record itself.
            }
        }

        // Delete the remainder
        $remainder->delete();

        return response()->json([
            "message" => "Remainder with ID '{$id}' has been deleted successfully.",
            'remainder' => $remainder->only('id', 'type', 'date', 'time', 'audio')
        ], 200);
    } catch (Exception $e) {
        return response()->json([
            "message" => "An error occurred while deleting the remainder.",
            "error" => $e->getMessage(),
        ], 500);
    }
}


}
