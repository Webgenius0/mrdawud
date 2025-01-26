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
            
            $user = auth()->user();
            if (!$user || $user->role !== 'instructor') {
                return response()->json(['error' => 'User not authenticated or not authorized'], 403);
            }
    
            
            $documents = $request->file('document');
            $documentPaths = [];
    
            foreach ($documents as $document) {
                
                $documentPath = Helper::fileUpload($document, 'documents', $document->getClientOriginalName());

                $existingDocument = Document::where('user_id', $user->id)->where('document', $documentPath)->first();
                if ($existingDocument) {
                    return response()->json(['error' => 'Document already exists.'], 400);
                }

                $documentPaths[] = $documentPath;
    
               
                Document::create([
                    'user_id' => $user->id,
                    'document' => $documentPath,  
                ]);
            }
    
            DB::commit();
    
            return response()->json([
                'status' =>200 ,
                'message' => 'Documents uploaded successfully',
                
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Something went wrong. Please try again later.'], 500);
        }
    }
    
    // show documents

    public function show()
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'instructor') {
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

    //edit Document



   public function edit(Request $request, $id)
{
    // Validate the incoming request for file uploads
    $validator = Validator::make($request->all(), [
        'document' => 'required|array',
        'document.*' => 'required|mimes:pdf,doc,docx|max:20000',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }

    DB::beginTransaction();

    try {
        // Check if the user is authenticated and authorized
        $user = auth()->user();
        if (!$user || $user->role !== 'instructor') {
            return response()->json(['error' => 'User not authenticated or not authorized'], 403);
        }

        // Find the document by ID for the user
        $document = Document::where('user_id', $user->id)->find($id);

        if (!$document) {
            return response()->json(['error' => 'Document not found.'], 404);
        }
     
        if ($request->hasFile('document')) {
            if ($document->document) {
                // Delete the previous file(s) if they exist
                Helper::fileDelete($document->document);
            }

            $documents = $request->file('document');
            $documentPaths = [];

            // Upload each document and save the path
            foreach ($documents as $documentFile) {
                $documentPath = Helper::fileUpload($documentFile, 'documents', $documentFile->getClientOriginalName());
                $documentPaths[] = $documentPath;

                // Insert each uploaded document as a new row
                Document::create([
                    'user_id' => $user->id,
                    'document' => $documentPath,    
                ]);
            }

            // Optionally update the document record if you need to store the last uploaded document
            // In case the updated document is replacing the old one, you can update that record
            $document->document = $documentPaths[0]; // Or whatever logic you need to apply
            $document->save();
        }

        DB::commit();

        return response()->json([
            'message' => 'Document updated successfully.',
            'data' => $documentPaths,  
        ], 200);
    } catch (Exception $e) {
        DB::rollBack();

        return response()->json(['error' => 'Something went wrong. Please try again later.'], 500);
    }
}


    //delete document
    public function delete($id)
    {
        $user = auth()->user();
        if (!$user || $user->role !== 'instructor') {
            return response()->json(['error' => 'User not authenticated or not authorized'], 403);
        }
        try {
            $document = Document::where('user_id', $user->id)->find($id);
            if (!$document) {
                return response()->json(['error' => 'Document not found.'], 404);
            }
            if($document->document){
                Helper::fileDelete($document->document);
            }
            $document->delete();
            return response()->json([
                'message' => 'Document deleted successfully.',
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => 'Something went wrong. Please try again later.'], 500);
        }
    }
}