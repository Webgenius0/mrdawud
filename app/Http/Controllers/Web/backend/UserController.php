<?php

namespace App\Http\Controllers\Web\backend;

use App\Models\User;
use App\Helper\Helper;
use Illuminate\Http\Request;
use App\Services\UserService;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public $userServiceObj;

    public function __construct()
    {
        $this->userServiceObj = new UserService();
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::where('role', 'user')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function ($data) {
                    $status = $data->status;
                    return '<div class="form-check form-switch mb-2">
                                <input class="form-check-input" onclick="showStatusChangeAlert(' . $data->id . ')" type="checkbox" ' . ($status == 'active' ? 'checked' : '') . '>
                            </div>';

                })
                ->addColumn('bulk_check', function ($data) {
                    return Helper::tableCheckbox($data->id);

                })
                ->addColumn('action', function ($data) {
                    $viewRoute = route('show.user', ['id' => $data->id]);
                    return '<div>
                         <a class="btn btn-sm btn-primary" href="' . $viewRoute . '">
                             <i class="fa-solid fa-eye"></i>
                         </a>
                         <button type="button" onclick="statusUpdate(' . $data->id . ')" class="btn btn-sm btn-danger">
                             <i class="fa-regular fa-circle-xmark"></i>
                         </button>
                     </div>';
                })
                ->rawColumns(['bulk_check', 'status', 'action'])
                ->make(true);
        }

        return view('backend.layout.user.index');
    }

    public function show($id)
    {
        return $this->userServiceObj->show($id);
    }

    /**
     * Change the status of the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function status(int $id): JsonResponse
    {
        $data = User::findOrFail($id);
        if ($data->status == 'active') {
            $data->status = 'inactive';
            $data->save();

            return response()->json([
                'success' => false,
                'message' => 'Unpublished Successfully.',
                'data' => $data,
            ]);
        } else {
            $data->status = 'active';
            $data->save();

            return response()->json([
                'success' => true,
                'message' => 'Published Successfully.',
                'data' => $data,
            ]);
        }
    }
}
