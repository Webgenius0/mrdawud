<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\BlockUser;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
}
