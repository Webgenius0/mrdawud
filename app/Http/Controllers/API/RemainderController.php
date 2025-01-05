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

class RemainderController extends Controller
{
    use apiresponse;

    public function remainder(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'audio' => 'required|mimes:png|max:20000', 
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

            $existingRemainder = Remainder::where('user_id', $user->id)
                ->where('type', $type)
                ->where('date', $date)
                ->first();

            $responseMessage = [];

            if ($existingRemainder) {
                
                $existingRemainder->time = $time;
                $existingRemainder->audio = $audioPath;
                $existingRemainder->save();

                $responseMessage[] = "Remainder for type '{$type}' and date '{$date}' has been updated successfully.";
            } else {
            
                $remainder = new Remainder();
                $remainder->user_id = $user->id;
                $remainder->type = $type;
                $remainder->date = $date;
                $remainder->time = $time;
                $remainder->audio = $audioPath;
                $remainder->save();

                $responseMessage[] = "Remainder for type '{$type}' and date '{$date}' has been added successfully.";
            }

            DB::commit();

            return $this->success([
                'messages' => $responseMessage,
                'remainder' => $existingRemainder ? $existingRemainder : $remainder
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error('An error occurred while adding or updating the remainder.', $e->getMessage());
        }
    }
}
