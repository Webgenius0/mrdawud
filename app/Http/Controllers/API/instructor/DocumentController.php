<?php

namespace App\Http\Controllers\API\instructor;

use App\Http\Controllers\Controller;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Helper\Helper;
use App\Models\Document;
use Illuminate\Support\Facades\DB;
use Exception;


class DocumentController extends Controller
{
    use apiresponse;
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'document' => 'required|array',
            'document.*' => 'required|mimes:pdf,doc,docx|max:20000',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        
        DB::beginTransaction();
        
        try {
            // Check if the user is authenticated
            $user = auth()->user();
            if (!$user || $user->role !=='instructor') {
                return response()->json(['error' => 'User not authenticated or not authorized'], 403);
            }
        
            // Proceed with file upload
            $documents = $request->file('document'); 
            $documentPaths = [];
        
            foreach ($documents as $document) {
                $documentPath = Helper::fileUpload($document, 'documents', $user->id); 
                $documentPaths[] = $documentPath; 
            }
        
            $documentPathsJson = json_encode($documentPaths);

            
            Document::create([
                'user_id' => $user->id,
                'document' => $documentPathsJson, 
            ]);
        
            DB::commit();
        
            return response()->json([
                'message' => 'Documents uploaded successfully',
                'data' => $documentPaths,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            dd($e->getMessage());
            return response()->json(['error' => 'Something went wrong. Please try again later.'], 500);
        }
        
    }   
    
    // show documents

    public function show()
    {
        $user = auth()->user();
        if(!$user || $user->role !=='instructor')
        {
            return response()->json(['message' => 'User not found or user not authorized.'], 404);
        }
        try {
            $documents = Document::where('user_id', $user->id)->get();
            return response()->json([
                'message' => 'Documents fetched successfully.',
                'documents' => $documents
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'An error occurred while showing the document(s).'], 400);
        }
    }

    
}
