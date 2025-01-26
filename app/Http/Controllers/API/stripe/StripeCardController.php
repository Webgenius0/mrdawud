<?php

namespace App\Http\Controllers\API\stripe;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\apiresponse;
use App\Models\StripeCard;
class StripeCardController extends Controller
{
    use apiresponse;

    public function index()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'status' => 400,
                'message' => 'User not found',
            ]);
        }
        $card = StripeCard::where('user_id', $user->id)->select('id', 'card_name', 'card_number', 'card_expiry', 'card_cvc')->get();
        return response()->json([
            'status' => 200,
            'message' => 'Card fetched successfully',
            'data' => [
                'card' => $card,
            ]
        ], 200);
    }


    // store card
    public function storeCard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'card_name' => 'required',
            'card_number' => 'required',
            'card_expiry' => 'required',
            'card_cvc' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first(),
                'data' => $validator->errors()
            ]);
        }

        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'status' => 400,
                'message' => 'User not found',
            ]);
        }
        DB::beginTransaction();
        try {
        
            $infromation = new StripeCard();
            $infromation->user_id = $user->id;
            $infromation->card_name = $request->card_name;
            $infromation->card_number = $request->card_number;
            $infromation->card_expiry = $request->card_expiry;
            $infromation->card_cvc = $request->card_cvc;
            $infromation->save();
            DB::commit();
            return response()->json([   
                'status' => 200,
                'message' => 'Card added successfully',
                'data' => $infromation
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 400,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
