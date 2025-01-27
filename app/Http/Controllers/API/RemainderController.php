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
use Illuminate\Support\Carbon;


class RemainderController extends Controller
{
    use apiresponse;

    public function uploadReminder(Request $request)
{
    // Validate the incoming request
    $validator = Validator::make($request->all(), [
        'audio_id' => 'required',
        //'audio_id.*' => 'required|exists:audio_uploads,id',
        'type' => 'required|array',
        'type.*' => 'required',
        'date' => 'required|array',
        'date.*' => 'date',
        'time' => 'required|array',
        'time.*' => 'string',
    ]);

    // If validation fails, return errors
    if ($validator->fails()) {
        return $this->error('Validation Error.', $validator->errors());
    }

    DB::beginTransaction();
    try {
        // Get all inputs as arrays
        $audioId = $request->audio_id; // Audio ID selected by the user
        $types = $request->type;
        $dates = $request->date;
        $times = $request->time;

        $user = auth()->user();

        if (!$user) {
            return $this->error('User not found.');
        }

        // Fetch the audio file from the AudioUpload model using the selected audio ID
        $audio = AudioUpload::find($audioId);

        if (!$audio) {
            return $this->error('Audio not found.');
        }

        // Store reminders
        $responseMessages = [];  // Store multiple messages
        $reminders = [];

        // Loop through the arrays and insert each reminder
        for ($i = 0; $i < count($types); $i++) {
            // Check if reminder already exists for this combination
            $remainder = Remainder::where('user_id', $user->id)
                ->where('type', $types[$i])
                ->where('date', $dates[$i])
                ->where('time', $times[$i])
                ->first();

            if ($remainder) {
                $responseMessages[] = "Reminder for type '{$types[$i]}' and date '{$dates[$i]}' already exists.";
                continue;  // Skip this iteration if reminder already exists
            }

            // Convert time to 24-hour format using Carbon
            $formattedTime = Carbon::createFromFormat('h:i A', $times[$i])->format('H:i:s');

            // Create a new remainder entry
            $remainder = new Remainder();
            $remainder->user_id = $user->id;
            $remainder->audio = $audio->id;
            $remainder->type = $types[$i];
            $remainder->date = $dates[$i];
            $remainder->time = $formattedTime; // Store time in 24-hour format
            $remainder->save();

            $reminders[] = $remainder;
            $responseMessages[] = "Reminder for type '{$types[$i]}' and date '{$dates[$i]}' has been added successfully.";
        }

        DB::commit();

        // Return success with all messages
        return $this->success([
            'status' => 200,
            'messages' => $responseMessages,  // Return an array of messages

        ], 'Reminders added successfully.', 200);

    } catch (Exception $e) {
        DB::rollBack();
        return $this->error('An error occurred while adding or updating the reminder.', $e->getMessage());
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

            $remainders = Remainder::where('user_id', $user->id)->with('audio:id,audio')->select('id', 'type', 'date', 'time', 'audio')->get();

            if ($remainders->isEmpty()) {
                return $this->error('Remainder not found', 404);
            }

            return response()->json([
                'status' => 200,
                'message' => 'Remainder List has been fetched successfully',
                'data' => [
                    'remainders' => $remainders,
                ]
            ], 200);
        } catch (Exception $e) {
            return $this->error('Something went wrong', 500);
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
