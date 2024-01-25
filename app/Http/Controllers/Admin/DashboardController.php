<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\GuestDetail;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $productOrders = Order::all();
        return view('admin.dashboard')->with([
                'productOrders' => $productOrders,
            ]);
    }
    
    public function getCustomers(){
        $userDetails = Customer::all();
        return view('admin.users.users')->with('users',$userDetails);
    }
    
    public function getGuests(){
        $userDetails = GuestDetail::all();
        return view('admin.users.guest-users')->with('users',$userDetails);
    }
}
