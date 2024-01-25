<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\CustomerCharge;
use App\Models\GuestDetail;
use App\Models\Order;
use Illuminate\Http\Request;

class VerifyOrderController extends Controller
{
    public function verify()
    {
        $reference = isset($_GET['reference']) ? $_GET['reference'] : '';
        if(!$reference){
            return response()->json([
                'status' => 'Request Failed',
                'message' => 'No Transaction Reference Supplied',
            ],422);
        }else{
            if(auth('api')->user()){
                $orders = Order::where('reference',$reference)->get();
                if($orders->isEmpty()){
                  return response()->json(['message' => 'No order was found using the reference', 'reference' => $reference]);
                }else{
                    $details = auth('api')->user();
                    $fees = CustomerCharge::where(['reference' => $reference, 'customer_id' => auth('api')->user()->id])->first();
                    if ($fees) {
                        $delivery = $fees->delivery_fee;
                        $service = $fees->service_charge;
                        $discount = $fees->discount;
                    return response()->json(['user'=>$details, 'delivery' =>$delivery,'serviceCharge' =>$service,'discount' => $discount,'orders'=>$orders],200);
                    } else {
                        return response()->json(['message' => 'This order does not exist for this user', 'user' => $details],500);
                    }
                }
            }else{
                $orders = Order::where('reference',$reference)->get();
                if($orders->isEmpty()){
                    return response()->json(['message' => 'No order was found using the reference', 'reference' => $reference]);
                }else{
                    $details = GuestDetail::where('reference',$reference)->first();
                    if ($details) {
                        $delivery = $details->delivery_fee;
                        $service = $details->service_charge;
                        $discount = $details->discount;
                        return response()->json(['user'=>$details, 'delivery' =>$delivery,'serviceCharge' =>$service,'discount' => $discount,'orders'=>$orders],200);
                    } else {
                        return response()->json(['message' => 'No order was found using the reference', 'reference' => $reference]);
                    }
                }
            }
            
        }
    }
}
