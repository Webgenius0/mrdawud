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
    public function index(Request $request)
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
            $document = $request->file('document');

            // Loop through each document if there are multiple files
            $documentPaths = [];
            foreach ($document as $file) {
                $documentPath = Helper::fileUpload($file, 'documents', $user->id);
                $documentPaths[] = $documentPath;
            }
            DB::commit();

            return response()->json([
                'message' => 'Documents uploaded successfully',
                'data' => $documentPaths,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Something went wrong. Please try again later.'], 500);
        }
    }
}
