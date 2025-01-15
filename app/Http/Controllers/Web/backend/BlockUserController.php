<?php

namespace App\Http\Controllers\Web\backend;

use App\Http\Controllers\Controller;
use App\Models\BlockUser;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Helper\Helper;
use App\Models\User;
use App\Models\ReportUser;
class BlockUserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = BlockUser::where('role', 'user')->latest();
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
                    $viewRoute = route('block.user', ['id' => $data->id]);
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

        return view('backend.layout.blockuser.index');
    }


    public function show($id)
    {
        $user=BlockUser::find($id);
        $report=ReportUser::where('reported_user_id',$user->id)->get();
        return view('backend.layout.blockuser.show',compact('user','report'));
    }
}
