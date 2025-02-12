<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\BlockUser;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use App\Models\NewsFeed;

class BlockUserController extends Controller
{
    use apiresponse;

    /**
     * My Blocked Users List
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        $users = BlockUser::where('user_id', auth()->id())->with('blocked_user')->get();
        return $this->success([
            'blocked_users' => $users
        ], "Blocked Users Fetched Successfully", 200);

    }

    /**
     * Block User
     * @return \Illuminate\Http\JsonResponse
     * @param User $user
     */
    public function blockUser(User $user){
        BlockUser::create([
            'user_id' => auth()->id(),
            'blocked_user_id' => $user->id,
        ]);
        return $this->success([
            'blocked_user' => $user
        ], "User Blocked Successfully", 200);
    }


    /**
     * Unblock User
     * @return \Illuminate\Http\JsonResponse
     * @param User $user
     */
    public function unblockUser(User $user){
        $user = BlockUser::where('user_id', auth()->id())->where('blocked_user_id', $user->id);
        if($user->exists()){
            $user->delete();
            return $this->success([], "User Unblocked Successfully", 200);
        }else{
            return $this->error([], "User not found", 404);
        }
        
    }

      /**
     * Newsfeed
     * @return \Illuminate\Http\JsonResponse
     * @param Newsfeed 
     */


     public function newsfeed()
     {
         try {
             $newsfeed = NewsFeed::all('title', 'description', 'location', 'image');
             return response()->json([
                 'status' => 200,
                 'message' => 'Newsfeed fetched successfully',
                 'data' => [
                     'newsfeed' => $newsfeed
                 ]
                ]);
         } catch (\Throwable $th) {
             return response()->json([
                 'status' => 500,
                 'message' => $th->getMessage(),
             ]);
         }

     }
}
