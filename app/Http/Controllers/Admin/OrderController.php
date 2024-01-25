<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerCharge;
use App\Models\GuestDetail;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function getOrder(Request $request)
    {
        $reference = $request->reference;
        $customerId = $request->id;
        $orders = Order::where(['reference' => $reference,'customer_id' => $customerId])->get();
        $userDetail = CustomerCharge::where(['reference' => $reference,'customer_id' => $customerId])->first();
        return view('admin.orders.customer-orders')->with(['orders' => $orders, 'userDetail' => $userDetail]);
    }
    public function getGuestOrder(Request $request)
    {
        $reference = $request->reference;
        $orders = Order::where(['reference' => $reference])->get();
        $userDetail = GuestDetail::where(['reference' => $reference])->first();
        return view('admin.orders.orders')->with(['orders' => $orders, 'userDetail' => $userDetail]);
    }

    public function getOrderDetails()
    {
        $userDetails = CustomerCharge::all();

        return view('admin.orders.order-details')->with('userDetails',$userDetails);
    }

    public function getGuestOrderDetails()
    {
        $userDetails = GuestDetail::all();
        return view('admin.orders.guest-order-details')->with('userDetails',$userDetails);
    }
}
