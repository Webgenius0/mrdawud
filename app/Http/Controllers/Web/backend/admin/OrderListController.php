<?php

namespace App\Http\Controllers\Web\backend\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Yajra\DataTables\Facades\DataTables;
use App\Helper\Helper;
use App\Notifications\OrderNotification;

class OrderListController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Order::with(['user', 'order_items.product']) 
            ->latest()                           // Order by the latest order
            ->get();
    
            return DataTables::of($data)
                ->addIndexColumn()

                ->addColumn('username', function ($data) {
                    return $data->user ? $data->user->username : 'Unknown';  // Ensure 'username' is accessed correctly
                })
                ->addColumn('stripe_customer_id', function ($data) {
                    return $data->user ? $data->user->stripe_customer_id : 'Unknown';  // Ensure 'username' is accessed correctly
                })
                ->addColumn('product_name', function ($data) {
                    $productNames = '';
                    foreach ($data->order_items as $item) {
                        $productNames .= $item->product ? $item->product->title : 'Unknown Product';
                        $productNames .= ',';  // Separate products with line breaks
                    }
                    return $productNames;  // Return formatted product names
                })
                ->addColumn('quantity', function ($data) {
                    $quantities = '';
                    foreach ($data->order_items as $item) {
                        $quantities .= $item->quantity ;
                        //$quantities .= $item->product ? $item->product->title : 'Unknown Product';
                        $quantities .= ',';  // Separate quantities with line breaks
                    }
                    return $quantities;  // Return formatted quantities
                })
                ->addColumn('status', function ($data) {
                    // Create a dropdown for status with the current value pre-selected
                    $statusOptions = [
                        'ongoing' => 'Ongoing',
                        'completed' => 'Completed',
                        'canceled' => 'Canceled',
                    ];
                
                    $dropdown = '<select class="form-control status-dropdown" id="statusDropdown' . $data->id . '" onchange="showStatusChangeAlert(' . $data->id . ', this.value)">';
                
                    foreach ($statusOptions as $value => $label) {
                        $selected = $data->status == $value ? 'selected' : '';
                        $dropdown .= '<option value="' . $value . '" ' . $selected . '>' . $label . '</option>';
                    }
                
                    $dropdown .= '</select>';
                    return $dropdown;
                })
                
                ->addColumn('bulk_check', function ($data) {
                    return Helper::tableCheckbox($data->id);
                })
                ->addColumn('action', function ($data) {
                    $viewRoute = route('show.block.user', ['id' => $data->id]);
                    return '<div>
                             
                            <button type="button" onclick="showDeleteAlert(' . $data->id . ')" class="btn btn-sm btn-danger">
                             <i class="fa-regular fa-trash-can"></i>
                         </button>
                         </div>';
                })
                ->rawColumns(['bulk_check', 'status', 'action', 'uuid','order_items'])
                ->make(true);
        }
        return view('backend.layout.orderlist.index');
    }
    

    //update status
    public function updateStatus(Request $request)
{
    $order = Order::find($request->order_id);

    if (!$order) {
        return response()->json(['status' => false, 'message' => 'Order not found'], 404);
    }

    // Validate the new status
    $validStatuses = ['ongoing', 'completed', 'canceled'];
    if (!in_array($request->status, $validStatuses)) {
        return response()->json(['status' => false, 'message' => 'Invalid status'], 400);
    }

    // Update the order's status
    $order->status = $request->status;
    $order->save();
    $user = $order->user; // Assuming there's a relationship between Order and User
    $user->notify(new OrderNotification($order));
    return response()->json(['status' => true, 'message' => 'Status updated successfully']);
}

//delete orderlist
public function destroy(string $id)
{
    try {
        $page = Order::findOrFail($id);
        $page->delete();
        flash()->success('Order deleted successfully');
        return response()->json([

            'success' => true,
            "message" => "Order deleted successfully."

        ]);
    } catch (\Exception $e) {
        return response()->json([

            'error' => true,
            "message" => "Order to delete Order."

        ]);
    }
}

//get notifiction



}
