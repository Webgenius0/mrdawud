<?php

namespace App\Http\Controllers\API\stripe;
use App\Models\BillingAddress;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\apiresponse;
class BillingAddressController extends Controller
{
    use apiresponse;
    public function index()
    {
        $user = auth()->user();
        if(!$user){ 
            return response()->json([
                'status' => 400,
                'message' => 'User not found',
            ]);
        }
        $address = BillingAddress::where('user_id', $user->id)->select('id', 'name', 'address', 'city', 'state', 'zip_code', 'phone_number')->get();
        return response()->json([
            'status' => 200,
            'message' => 'Billing address fetched successfully',
            'data' => [
                'address' => $address,
            ]
        ], 200);
    }
    public function store(Request $request)
    {
        //dd($request->all());
        $validator = Validator::make($request->all(), [
            'name'=>'required',
            'address'=>'required',
            'city'=>'required',
            'state'=>'required',
            'zip_code'=>'required',
            'phone_number'=>'required', 
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first(),
                'data' => $validator->errors()
            ]);
        }
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $infromation = new BillingAddress();
            $infromation->user_id = $user->id;
            $infromation->name = $request->name;
            $infromation->address = $request->address;
            $infromation->city = $request->city;
            $infromation->state = $request->state;
            $infromation->zip_code = $request->zip_code;
            $infromation->phone_number = $request->phone_number;
            $infromation->save();
            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Billing address Create successfully',
                'data' => $infromation
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ]);
        } 
              
    }

    //Billing address update
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'=>'nullable',
            'address'=>'nullable',
            'city'=>'nullable',
            'state'=>'nullable',
            'zip_code'=>'nullable',
            'phone_number'=>'nullable', 
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'message' => $validator->errors()->first(),
                'data' => $validator->errors()
            ]);
        }
        DB::beginTransaction();
        try {       
            $user = auth()->user();
            $infromation = BillingAddress::find($id)->select('id', 'user_id', 'name', 'address', 'city', 'state', 'zip_code', 'phone_number')->first();
            if (!$infromation) {
                return response()->json([
                    'status' => 400,
                    'message' => 'Billing address not found',
                ]);
            }
            $infromation->user_id = $user->id;
            $infromation->name = $request->name;
            $infromation->address = $request->address;
            $infromation->city = $request->city;
            $infromation->state = $request->state;
            $infromation->zip_code = $request->zip_code;
            $infromation->phone_number = $request->phone_number;
            $infromation->save();
            DB::commit();
            return response()->json([
                'status' => 200,
                'message' => 'Billing address update successfully',
                'data' =>[
                    'address' => $infromation

                    ]
            ]); 
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ]);
        } 
    }

    //Billing address delete
    public function destroy($id)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'status' => 400,
                'message' => 'User not found',
            ]);
        }
        $infromation = BillingAddress::find($id);
        if (!$infromation) {
            return response()->json([
                'status' => 400,
                'message' => 'Billing address not found',
            ]);
        }
        $infromation->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Billing address delete successfully',
        ]);
    }
}
