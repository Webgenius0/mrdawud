<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\ReportUser;
use App\Traits\apiresponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportUserController extends Controller
{
    use apiresponse;

    /**
     * My Blocked Users List
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(){
        $reports = ReportUser::where('user_id', auth()->id())->with('reported_user')->get();
        return $this->success([
            'reports' => $reports
        ], "Reports Users Fetched Successfully", 200);

    }

    /**
     * Block User
     * @return \Illuminate\Http\JsonResponse
     * @param User $user
     */
    public function reportUser(User $user, Request $request){
        ReportUser::create([
            'user_id' => auth()->id(),
            'reported_user_id' => $user->id,
            'report' => $request->report,
        ]);
        return $this->success([], "Report Sended Successfully", 200);
    }
}
