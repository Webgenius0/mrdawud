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
                'remainder' =>$remainder
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
    $user= auth()->user();

    if(!$user)
    {
        return $this->error(('User not found'),404);
    }

    $remainders = Remainder::where('user_id', $user->id)->select('id','type','date','time','audio')->get();

    if($remainders->isEmpty())
    {
        return $this-> error('Remainder not found', 404);
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
   //dd($request->all());
    $validation = Validator::make($request->all(), [
        'type' => 'required|in:fajar,zuhur,asor,magrib,esha,jumma',
        'time' => 'required|string',  
        'date' => 'nullable|date',
        'audio' => 'nullable|mimes:mp3|max:20000',  
    ]);

    
    if ($validation->fails()) {
        return response()->json([
            "message" => "Validation Error.",
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

    
    $remainder->user_id = $user->id;
    $remainder->type = $request->type;
    $remainder->date = $request->date;
    $remainder->time = $request->time;

  
    if ($request->hasFile('audio')) {
        if ($remainder->audio && Storage::exists(public_path($remainder->audio))) {
            
            Storage::delete(public_path($remainder->audio));
        }

      
        $audio = $request->file('audio');
        $audioPath = Helper::audioUpload($audio, 'audios', $remainder->type);
        $remainder->audio = $audioPath;
    }

   
    $remainder->save();

   
    DB::commit();

   
    return $this->success([
        'messages' => ["Remainder with ID '{$id}' has been updated successfully."],
        'remainder' => $remainder
    ], 200);
}

}
