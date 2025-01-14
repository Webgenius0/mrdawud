<?php

namespace App\Http\Controllers\API\instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document'=>'required|file|mimes:pdf,doc,docx',
        ]);

        if($validator->fails())
        {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user= auth()->user();
        if(!$user)
        {
            return response()->json(['message'=>'User not found.'],404);
        }

        $document = new Document();
        $document->user_id= $user->id;
        if$request->hasFile('document')
        {
            
        }
    }
}
