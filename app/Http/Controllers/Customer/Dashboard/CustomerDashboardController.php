<?php

namespace App\Http\Controllers\Customer\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerCharge;
use App\Models\CustomerReferral;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class CustomerDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function orders()
    {
        $userId = auth('api')->user()->id;
        $orders = Order::where('customer_id',$userId)->orderBy('created_at', 'desc')->get();
        $pending = Order::where(['customer_id'=>$userId,'status' => 'Pending'])->count();
        $delivered = Order::where(['customer_id'=>$userId,'status'=> 'Delivered'])->count();
        if(!$orders->isEmpty()){
            return response()->json(['orders'=>$orders, 'pending' => $pending, 'delivered' => $delivered]);
        }else{
            return response()->json(['message' => 'No Order has been placed by You']);
        }
        
    }
    
    public function singleOrders()
    {
        $reference = isset($_GET['reference']) ? $_GET['reference'] : '';
        if (!$reference) {
            return response()->json([
                'status' => 'Request Failed',
                'message' => 'No Transaction Reference Supplied',
            ],422);
        } else {
            $userId = auth('api')->user()->id;
            if(CustomerCharge::where(['customer_id' => $userId,'reference' => $reference])->exists())
            {
                $orderDetail = CustomerCharge::where(['customer_id' => $userId,'reference' => $reference])->first();
                $orders = Order::where(['customer_id' => $userId,'reference' => $reference])->get();
                if ($orders != null) {
                    return response()->json(['orders' => $orders]);
                } else {
                    return response()->json([
                        'status' => 'Not Found',
                        'message' => 'Orders Associated with this reference can not be found.',
                    ],404);
                }
            }else {
                return response()->json([
                    'status' => 'Request Failed',
                    'message' => 'Invalid Transaction Reference Supplied',
                ],422);
            }
            
            
        }
    }
    
    public function refers()
    {
        $userId = auth('api')->user()->id;
        $userReferralId = auth('api')->user()->refferal_id;
        $refers = CustomerReferral::where(['customer_id' => $userId,'customer_referral_id' => $userReferralId])->get();
        if (count($refers) != null) {
            return response()->json(['refers' => $refers]);
        } else {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'Referral Not Found.',
            ],404);
        }
        
        
    }

    public function details()
    {
        $userId = auth('api')->user()->id;
        $details = Customer::where('id',$userId)->first();
        if ($details != null) {
            return response()->json(['customer'=>$details]);
        } else {
            return response()->json([
                'status' => 'Not Found',
                'message' => 'Customer Not Found.',
            ],404);
        }
        
        
    }

    public function changePassword(Request $request)
    {
        $user = auth('api')->user();


        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = JWTAuth::parseToken()->authenticate();

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return response()->json(['error' => 'Invalid current password'], 400);
        }

        // Update the password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password changed successfully',
            'user' => auth('api')->user(),
        ]);
    }

    public function changeLocation(Request $request)
    {
        $user = auth('api')->user();

        $validator = Validator::make($request->all(), [
            'address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = JWTAuth::parseToken()->authenticate();

        // Update the address
        $user->address = $request->address;
        $user->save();

        return response()->json([
            'message' => 'Delivery Address changed successfully',
            'user' => auth('api')->user(),
        ]);
    }
    public function deleteAccount()
    {
        $userId = auth('api')->user()->id;
        $customer = Customer::find($userId);

        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $customer->delete();

        return response()->json(['message' => 'Customer deleted successfully']);

    }

    public function customerLocation()
    {
        $userId = auth('api')->user()->id;
        $orders = CustomerCharge::where(['customer_id' => $userId])->get();
        if (count($orders) != null) {
            return response()->json(['details' => $orders]);
        } else {
            $orders = Customer::where(['id' => $userId])->first();
            $location = $orders->address;
            return response()->json(['address' => $location]);
        }
    }
}
