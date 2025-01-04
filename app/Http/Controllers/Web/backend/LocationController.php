<?php

namespace App\Http\Controllers\Web\backend;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateLocationRequest;
use App\Models\Location;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class LocationController extends Controller
{
    private $location;

    public function __construct(Location $location = null) 
    {
         $this->location = $location;
    }


    public function index(Request $request)
    {

        try {
            if ($request->ajax()) {
                $data = $this->location::orderBy('created_at', 'DESC')->get();

                return DataTables::of($data)
                    ->addIndexColumn()
                    ->addColumn('status', function ($data) {
                        $status = ' <div class="form-check form-switch" style="margin-left:40px;">';
                        $status .= ' <input onclick="changeStatus(' . $data->id . ')" type="checkbox" class="form-check-input" id="customSwitch' . $data->id . '" getAreaid="' . $data->id . '" name="status"';
                        if ($data->status == 'active') {
                            $status .= "checked";
                        }
                        $status .= '><label for="customSwitch' . $data->id . '" class="form-check-label" for="customSwitch"></label></div>';

                        return $status;
                    })
                    ->addColumn('action', function ($data) {
                        $editUrl = route('admin.location.edit', $data->id);
                        return '<div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                        <a href="' . $editUrl . '" class="btn btn-primary text-white" title="Edit">
                            <i class="fa fa-pencil"></i>
                        </a>
                        <a href="#" onclick="deleteRole(' . $data->id . ')" class="btn btn-danger text-white" title="Delete">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>';
                    })

                    ->rawColumns(['short_description', 'status', 'action'])
                    ->make(true);
            }
            return view('backend.layout.map.index');
        } catch (Exception $e) {
            return redirect()->back()->with('t-error', 'Something went wrong! Please try again.');
        }
    }

    public function create()
    {
        return view('backend.layout.map.create');
    }

    public function store(CreateLocationRequest $request)
    {
        try {

            $data = new $this->location;
            $data->name = $request->name;
            $data->latitude = $request->latitude;
            $data->longitude = $request->longitude;
            $data->creator_id = auth()->user()->id;
            $data->save();
            flash()->success('Location created successfully.');
            return redirect()->route('admin.location.list');

        }
        catch (Exception $e) {
            return redirect()->back()->with('t-error', 'Something went wrong! Please try again.');
        }

    }

    public function edit($id)
    {
        $data = $this->location->findOrFail($id);
        return view('backend.layout.map.edit', compact('data'));
    }
    public function update(CreateLocationRequest $request, $id)
    {
        try {

            $data = $this->location->findOrFail($id);
            $data->name = $request->name;
            $data->latitude = $request->latitude;
            $data->longitude = $request->longitude;
            $data->creator_id = auth()->user()->id;
            $data->save();
            flash()->success('Location Updated successfully.');
            return redirect()->route('admin.location.list');

        }
        catch (Exception $e) {
            return redirect()->back()->with('t-error', 'Something went wrong! Please try again.');
        }
    }
    
    public function destroy(string $id)
    {
        try {
            $page = $this->location::findOrFail($id);
            $page->delete();
            flash()->success('FAQ deleted successfully');
            return response()->json([

                'success' => true,
                "message" => "FAQ deleted successfully."

            ]);
        } catch (\Exception $e) {
            return response()->json([

                'error' => true,
                "message" => "Failed to delete FAQ."

            ]);
        }
    }

    public function status($id)
    {
        $page = $this->location::find($id);

        if (empty($page)) {
            return response()->json([
                "success" => false,
                "message" => "Item not found."
            ], 404);
        }

        if ($page->status == "active") {
            $page->status = "inactive";
        } else {
            $page->status = "active";
        }
        $page->save();
        return response()->json([
            'success' => true,
            'message' => 'Item status changed successfully.'
        ]);

    }
}
