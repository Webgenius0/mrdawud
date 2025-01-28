<?php

namespace App\Http\Controllers\API\instructor;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\apiresponse;
use Illuminate\Support\Facades\Validator;
use App\Models\FavouriteTeacher;
use App\Models\Support;


class InstructorListController extends Controller
{
    use apiresponse;
    public function index(Request $request)
    {
        try {
           
            $query = User::where('role', 'instructor');
                       
            if ($request->has('sort_by') && in_array($request->sort_by, ['name', 'email'])) {
                $query = $query->orderBy($request->sort_by, $request->get('sort_order', 'asc')); // Default to ascending order
            }
               
            if ($request->has('search')) {
                $query = $query->where('name', 'like', '%' . $request->search . '%');
            }
             
            $instructors = $query->get();
         
            return response()->json([
                'status' => 200,
                'message' => 'Instructor list fetched successfully.',
                'data' => $instructors,
            ]);
        } catch (\Exception $e) {
            
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong while fetching the instructor list.',
                'error' => $e->getMessage(), 
            ], 500);
        }
    }

    //favourite Teacher create
   
    public function favouriteTeacher(Request $request, $teacherId)
    {
        $user = auth()->user();
    
        
        $teacher = User::find($teacherId);
    
        if (!$teacher || $teacher->role !== 'instructor') {
            return response()->json(['error' => 'Teacher not found.'], 404);
        }
    
       
        $existingFavorite = FavouriteTeacher::where('user_id', $user->id)
                                            ->where('teacher_id', $teacher->id) 
                                            ->first();
    
        if ($existingFavorite) {
            return response()->json(['message' => 'Teacher already in favorites']);
        }
    
        
        $favorite = FavouriteTeacher::create([
            'user_id' => $user->id,
            'teacher_id' => $teacher->id,  
        ]);
    
        return response()->json([
            'status' => 200,
            'message' => 'Teacher added to favorites'
        ]);
    }
    //show favourite teacher
    public function showFavouriteTeacher()
    {
        
        $user = auth()->user();
        // Get the logged-in user
        
        // Get the favourite teachers along with their images, selecting only 'username' and 'image_path'
        $favourites = FavouriteTeacher::where('user_id', $user->id)
            ->with(['instructor' => function ($query) {
                $query->select('id', 'username');
            }, 'instructor.image' => function ($query) {
                $query->select('user_id', 'image')
                    ->limit(1); 
            }])
            ->select('id', 'user_id', 'teacher_id') 
            ->get();
        
        // Format response to include image directly inside the favourites array
        $favourites->transform(function ($favourite) {
            $favourite->username = $favourite->instructor->username; 
            $favourite->image = $favourite->instructor->image->isNotEmpty() 
                                ? $favourite->instructor->image->first()->image 
                                : null;
                               
                                                    
            unset($favourite->instructor); // Remove the instructor relationship
            return $favourite;
        });
        
        return response()->json(['favourites' => $favourites]);
        
    }

    //delete favourite teacher
    public function deleteFavouriteTeacher($teacherId)
    {
        $user = auth()->user();
    
        $favorite = FavouriteTeacher::where('user_id', $user->id)
                                    ->where('id', $teacherId)
                                    ->first();
    
        if (!$favorite) {
            return response()->json(['message' => 'Teacher not found in favorites'], 404);
        }
    
        $favorite->delete();
    
        return response()->json(['message' => 'Teacher remove from favorites']);
    }

    //support message 
    public function supportMessage(Request $request)
    {
        // Get the authenticated user
        $user = auth()->user();
        
        // Check if the user exists
        if (!$user) {
            return response()->json([
                'status' => 400,
                'message' => 'User not found',
            ]);
        }
    
        // Set dynamic validation rules based on the user's role
        $phoneRule = $user->role == 'instructor' ? 'required|string' : 'nullable|string';
    
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => $phoneRule, // Apply the dynamic rule for phone
            'message' => 'required|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first(),
                'data' => $validator->errors(),
            ]);
        }
    
        DB::beginTransaction();
        try {
           
            // Create and save the support message
            $infromation = new Support();
            $infromation->user_id = $user->id;
            $infromation->name = $request->name;
            $infromation->email = $request->email;
            $infromation->phone = $request->phone;
            $infromation->message = $request->message;
            $infromation->save();
    
            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Message sent successfully.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => 'Something went wrong while sending the message.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    
}
