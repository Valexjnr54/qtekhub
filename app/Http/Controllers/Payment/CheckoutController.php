<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerCharge;
use App\Models\CustomerReferral;
use App\Models\GuestDetail;
use App\Models\Order;
use App\Models\ReceiptUpload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderMail;
use App\Mail\AdminOrderMail;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    public function getPaystackLink(Request $request)
    {
        $this->validate($request,[
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'phoneNumber' => 'required',
            'deliveryAddress' => 'required',
            'amount' => 'required',
            'currency' => 'required',
            'reference' => 'required',
            'payment_method' => 'required',
            'products' =>"array",
            'deliveryFee' => 'required'
        ]);
        $products = $request->input('products');
        if (auth('api')->user()) {
            $userDetail = new CustomerCharge;
            $userDetail->customer_id = auth('api')->user()->id;
            $userDetail->reference = $request->input('reference');
            $userDetail->delivery_fee = $request->input('deliveryFee');
            $userDetail->location = $request->input('deliveryAddress');
            $userDetail->service_charge = $request->input('serviceCharge');
            $userDetail->payment_method = $request->payment_method;
            if($request->input('discount') !=0){
                $userDetail->discount = $request->input('discount');
            }
            $userDetail->amount = $request->input('amount');
            $save = $userDetail->save();
            if ($save) {
                $myDate = date("Y-m-d");
                $day = Carbon::createFromFormat('Y-m-d', $myDate)->format('d');
                $month = Carbon::createFromFormat('Y-m-d', $myDate)->format('m');
                $year = Carbon::createFromFormat('Y-m-d', $myDate)->format('Y');
                if (!empty($products)) {
                    foreach($products as $order)
                    {
                        if (is_array($order)) {
                            $orders = new Order;
                            $orders->product_name = $order['product_name'];
                            $orders->qty = $order['qty'];
                            $orders->price = $order['price'];
                            $orders->reference = $request->input('reference');
                            $orders->status = 'Pending';
                            $orders->customer_id = auth('api')->user()->id;
                            $orders->product_image = $order['image'];
                            $orders->day = $day;
                            $orders->month = $month;
                            $orders->year = $year;
                            $orders->save();
                        } else {
                            echo "Not an array.\n";
                        }
                    }
                }
                
                if ($request->input('referralCode') !== null) {
                    $referral = Customer::where('refferal_id',$request->input('referralCode'))->first();
                    $referral_id = $referral->id;
                    $referral_point = $referral->points;
                    $new_referral_point = $referral_point + 50;
                    $updateReferralPoint = Customer::where('refferal_id',$request->input('referralCode'))->update(['points'=>$new_referral_point]);
                    $refer = new CustomerReferral;
                    $refer->customer_id = $referral_id;
                    $refer->customer_referral_id = $request->input('referralCode');
                    $refer->name = $request->input('name');
                    $refer->email = $request->input('email');
                    $refer->phone = $request->input('phoneNumber');
                    $refer->save();
                }
                if ($request->payment_method  == "Paystack")
                {
                    $curl = curl_init();
                    $email = $request->input('email');
                    $tot = $request->input('amount');
                    $total = $tot * 100;
                    $amount = $total;
                    $reference = $request->input('reference');
                    $fullname = $request->name;

                    // url to go to after payment
                    $callback_url = url('api/v1/checkout/paystack-callback');

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => json_encode([
                        'amount'=>$amount,
                        'email'=>$email,
                        'callback_url' => $callback_url,
                        'reference' => $reference,
                        'name' => $fullname,
                        'phoneNumber'=>$request->phoneNumber,
                        ]),
                        CURLOPT_HTTPHEADER => [
                        "authorization: Bearer sk_test_4e8fd1e07801aa989c6599d9dbcf911fe06ba691", //replace this with your own test key
                        "content-type: application/json",
                        "cache-control: no-cache"
                        ],
                    ));

                    $response = curl_exec($curl);
                    $err = curl_error($curl);

                    if($err){
                        // there was an error contacting the Paystack API
                        die('Curl returned error: ' . $err);
                    }

                    $tranx = json_decode($response, true);

                    if(!$tranx['status']){
                        // there was an error from the API
                        print_r('API returned error: ' . $tranx['message']);
                    }

                    return response()->json(['response' => 'success', 'link' => $tranx['data']['authorization_url']]);
                }
            } else {
                return response()->json(['status' => 'failed','response'=>'Internal Server Error',500]);
            }
        } else {
            $userDetail = new GuestDetail;
            $userDetail->first_name = $request->input('first_name');
            $userDetail->last_name = $request->input('last_name');
            $userDetail->location = $request->input('deliveryAddress');
            $userDetail->phone_number = $request->input('phoneNumber');
            $userDetail->email = $request->input('email');
            $userDetail->reference = $request->input('reference');
            $userDetail->status = 0;
            $userDetail->delivery_fee = $request->input('deliveryFee');
            $userDetail->service_charge = $request->input('serviceCharge');
            $userDetail->payment_method = $request->payment_method;
            if($request->input('discount') !=0){
                $userDetail->discount = $request->input('discount');
            }
            $userDetail->amount = $request->input('amount');
            $save = $userDetail->save();
            if ($save) {
                $myDate = date("Y-m-d");
                $day = Carbon::createFromFormat('Y-m-d', $myDate)->format('d');
                $month = Carbon::createFromFormat('Y-m-d', $myDate)->format('m');
                $year = Carbon::createFromFormat('Y-m-d', $myDate)->format('Y');
                if (!empty($products)) {
                    foreach($products as $order)
                    {
                        if (is_array($order)) {
                            $orders = new Order;
                            $orders->product_name = $order['product_name'];
                            $orders->qty = $order['qty'];
                            $orders->price = $order['price'];
                            $orders->reference = $request->input('reference');
                            $orders->status = 'Pending';
                            $orders->product_image = $order['image'];
                            $orders->day = $day;
                            $orders->month = $month;
                            $orders->year = $year;
                            $orders->save();
                        } else {
                            echo "Not an array.\n";
                        }
                    }
                }
                
                if ($request->input('referralCode') !== null) {
                    $referral = Customer::where('refferal_id',$request->input('referralCode'))->first();
                    $referral_id = $referral->id;
                    $referral_point = $referral->points;
                    $new_referral_point = $referral_point + 50;
                    $updateReferralPoint = Customer::where('refferal_id',$request->input('referralCode'))->update(['points'=>$new_referral_point]);
                    $refer = new CustomerReferral;
                    $refer->customer_id = $referral_id;
                    $refer->customer_referral_id = $request->input('referralCode');
                    $refer->name = $request->input('name');
                    $refer->email = $request->input('email');
                    $refer->phone = $request->input('phoneNumber');
                    $refer->save();
                }
                if ($request->payment_method  == "Paystack")
                {
                    $curl = curl_init();
                    $email = $request->input('email');
                    $tot = $request->input('amount');
                    $total = $tot * 100;
                    $amount = $total;
                    $reference = $request->input('reference');
                    $fullname = $request->name;

                    // url to go to after payment
                    $callback_url = url('api/v2/checkout/paystack-callback');

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => json_encode([
                        'amount'=>$amount,
                        'email'=>$email,
                        'callback_url' => $callback_url,
                        'reference' => $reference,
                        'name' => $fullname,
                        'phoneNumber'=>$request->phoneNumber,
                        ]),
                        CURLOPT_HTTPHEADER => [
                        "authorization: Bearer sk_test_4e8fd1e07801aa989c6599d9dbcf911fe06ba691", //replace this with your own test key
                        "content-type: application/json",
                        "cache-control: no-cache"
                        ],
                    ));

                    $response = curl_exec($curl);
                    $err = curl_error($curl);

                    if($err){
                        // there was an error contacting the Paystack API
                        die('Curl returned error: ' . $err);
                    }

                    $tranx = json_decode($response, true);

                    if(!$tranx['status']){
                        // there was an error from the API
                        print_r('API returned error: ' . $tranx['message']);
                    }

                    return response()->json(['response' => 'success', 'link' => $tranx['data']['authorization_url']]);
                }
            } else {
                return response()->json(['status' => 'failed','response'=>'Internal Server Error',500]);
            }

        }

    }

    public function paystackCallback()
    {
        $curl = curl_init();
        $reference = isset($_GET['reference']) ? $_GET['reference'] : '';
        if(!$reference){
            return response()->json([
                'status' => 'Request Failed',
                'message' => 'No Transaction Reference Supplied',
            ],422);
        }else{
            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "authorization: Bearer sk_test_4e8fd1e07801aa989c6599d9dbcf911fe06ba691",
                "cache-control: no-cache"
              ],
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            if($err){
                // there was an error contacting the Paystack API
              die('Curl returned error: ' . $err);
            }

            $callback = json_decode($response);

            if(!$callback->status){
              // there was an error from the API
              die('API returned error: ' . $callback->message);
            }
            $status = $callback->data->status;
            
            if(auth('api')->user()){
                $userId = auth('api')->user()->id;
                $customerDetail = CustomerCharge::where(['reference' => $reference, 'customer_id' => auth('api')->user()->id])->first();
                $DBreference = $customerDetail->reference;
                if ($reference == $DBreference && $status == true) {
                    $customerDetail = CustomerCharge::where(['reference' => $reference, 'customer_id' => auth('api')->user()->id])->update(['status'=>1]);
                    $charge = CustomerCharge::where(['reference' => $reference, 'customer_id' => auth('api')->user()->id])->first();
                    $customer = Customer::where(['id' => auth('api')->user()->id])->first();
                    $orders = Order::where(['reference' => $reference, 'customer_id' => auth('api')->user()->id])->get();
                    $fro = 'info@qmarthub.com';
                    $subject = 'Order Details';
                    $view = 'mail-template.order';
                    $view2 = 'mail-template.admin-order';
                    $data = [
                        'first_name' => $customer->first_name,
                        'last_name' => $customer->last_name,
                        'email' => $customer->email,
                        'location' => $charge->location,
                        'phone' => $customer->phone_number,
                        'reference' => $charge->reference,
                        'amount' => $charge->amount,
                        'orders' => $orders,
                        'delivery' => $charge->delivery_fee,
                        'service' => $charge->service_charge,
                        'discount' => $charge->discount
                    ];
                    $send1 = Mail::to($customer->email)->send(new OrderMail($fro, $subject, $view, $data));

                    if ($send1) {
                        $send2 = Mail::to('info@qmarthub.com')->send(new AdminOrderMail($customer->email, $subject, $view2, $data));
                    }
                    // return response()->json(['message'=>'Payment Have been Confirmed','url'=>'https://qmarthub.com/order-summary?trxref='.$callback->data->reference.'&reference='.$callback->data->reference],200);
                    return response()->json("https://qmarthub.com/order-summary?trxref=".$callback->data->reference."&reference=".$callback->data->reference."");
                }else{
                    return response()->json(['message','Failed to confirm Payment',400]);
                }
            }else{
                $userDetail = GuestDetail::where('reference',$reference)->first();
                $DBreference = $userDetail->reference;
                $firstName = $userDetail->first_name;
                $lastName = $userDetail->last_name;
                $fullname = $lastName." ".$firstName;
                $email = $userDetail->email;
                $DBreference = $userDetail->reference;
                if ($reference == $DBreference && $status == true) {
                    $userDetail = GuestDetail::where('reference',$reference)->update(['status'=>1]);
                    $detail = GuestDetail::where('reference',$reference)->first();
                    $orders = Order::where('reference',$reference)->get();
                    $fro = 'info@qmarthub.com';
                    $subject = 'Order Details';
                    $view = 'mail-template.order';
                    $view2 = 'mail-template.admin-order';
                    $data = [
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'email' => $detail->email,
                        'location' => $detail->location,
                        'phone' => $detail->phone,
                        'reference' => $detail->reference,
                        'amount' => $detail->amount,
                        'orders' => $orders,
                        'delivery' => $detail->delivery_fee,
                        'service' => $detail->service_charge,
                        'discount' => $detail->discount
                    ];

                    $send3 = Mail::to($detail->email)->send(new OrderMail($fro, $subject, $view, $data));

                    if ($send3) {
                        $send4 = Mail::to('info@qmarthub.com')->send(new AdminOrderMail($detail->email, $subject, $view2, $data));
                    }
                    // return response()->json(['message'=>'Payment Have been Confirmed','url'=>'https://qmarthub.com/order-summary?trxref='.$callback->data->reference.'&reference='.$callback->data->reference],200);
                     return response()->json("https://qmarthub.com/order-summary?trxref=".$callback->data->reference."&reference=".$callback->data->reference."");
                }else{
                    return response()->json(['message'=>'Failed to confirm Payment',400]);
                }
            }



        }

    }

    public function uploadReceipt(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
        'first_name' => 'required',
        'last_name' => 'required',
        'email' => 'required',
        'phoneNumber' => 'required',
        'deliveryAddress' => 'required',
        'amount' => 'required',
        'currency' => 'required',
        'reference' => 'required',
        'payment_method' => 'required',
        // 'products' =>"array",
        'receipt' => 'required|mimes:jpeg,jpg,pdf,png|max:5120',
        'deliveryFee' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }
        
        if(is_array($request->input('products'))){
            $products = $request->input('products');
        }else{
            $products = json_decode($request->input('products'), true);
        }
        
            if (auth('api')->user()) {
                try{
                    $userDetail = new CustomerCharge;
                    $userDetail->customer_id = auth('api')->user()->id;
                    $userDetail->reference = $request->input('reference');
                    $userDetail->delivery_fee = $request->input('deliveryFee');
                    $userDetail->location = $request->input('deliveryAddress');
                    $userDetail->service_charge = $request->input('serviceCharge');
                    $userDetail->payment_method = $request->input('payment_method');
                    if($request->input('discount') !=0){
                        $userDetail->discount = $request->input('discount');
                    }
                    $userDetail->amount = $request->input('amount');
                    $save = $userDetail->save();
                    if ($save) {
                        $myDate = date("Y-m-d");
                        $day = Carbon::createFromFormat('Y-m-d', $myDate)->format('d');
                        $month = Carbon::createFromFormat('Y-m-d', $myDate)->format('m');
                        $year = Carbon::createFromFormat('Y-m-d', $myDate)->format('Y');
                        if (!empty($products)) {
                            foreach($products as $order)
                            {
                                if (is_array($order)) {
                                    $orders = new Order;
                                    $orders->product_name = $order['product_name'];
                                    $orders->qty = $order['qty'];
                                    $orders->price = $order['price'];
                                    $orders->reference = $request->input('reference');
                                    $orders->status = 'Pending';
                                    $orders->customer_id = auth('api')->user()->id;
                                    $orders->product_image = $order['image'];
                                    $orders->day = $day;
                                    $orders->month = $month;
                                    $orders->year = $year;
                                    $orders->save();
                                } else {
                                    echo "Not an array.\n";
                                }
                            }
                        }
                        
                        if ($request->input('referralCode') !== null) {
                            $referral = Customer::where('refferal_id',$request->input('referralCode'))->first();
                            $referral_id = $referral->id;
                            $referral_point = $referral->points;
                            $new_referral_point = $referral_point + 50;
                            $updateReferralPoint = Customer::where('refferal_id',$request->input('referralCode'))->update(['points'=>$new_referral_point]);
                            $refer = new CustomerReferral;
                            $refer->customer_id = $referral_id;
                            $refer->customer_referral_id = $request->input('referralCode');
                            $refer->name = $request->input('name');
                            $refer->email = $request->input('email');
                            $refer->phone = $request->input('phoneNumber');
                            $refer->save();
                        }
                        if($request->hasFile('receipt')){
                            $file = $request->file('receipt');
                            $folder = 'qmarthub/receipt/receipt_files';
                            $uploadedFile = cloudinary()->upload($file->getRealPath(), [
                                'folder' => $folder
                            ]);
                
                            $fileNameToStore = $uploadedFile->getSecurePath();
                        }
                        $receipt = new ReceiptUpload;
                        $receipt->first_name = auth('api')->user()->first_name;
                        $receipt->last_name = auth('api')->user()->last_name;
                        $receipt->email = auth('api')->user()->email;
                        $receipt->reference = $request->input('reference');
                        $receipt->phone_number = auth('api')->user()->phone_number;
                        $receipt->user = 'Customer';
                        $receipt->receipt = $fileNameToStore;
                        $save3 = $receipt->save();
                        if ($save3) {
                            // $charge = CustomerCharge::where(['reference' => $request->input('reference'), 'customer_id' => auth('api')->user()->id])->first();
                            // $customer = Customer::where(['id' => auth('api')->user()->id])->first();
                            // $orders = Order::where(['reference' => $request->input('reference'), 'customer_id' => auth('api')->user()->id])->get();
                            // $fro = 'info@qmarthub.com';
                            // $subject = 'Order Details';
                            // $view = 'mail-template.order';
                            // $view2 = 'mail-template.admin-order';
                            // $data = [
                            //     'fullname' => $customer->name,
                            //     'email' => $customer->email,
                            //     'location' => $charge->location,
                            //     'phone' => $customer->phone_number,
                            //     'reference' => $charge->reference,
                            //     'amount' => $charge->amount,
                            //     'orders' => $orders,
                            // ];
                            // $send = Mail::to($customer->email)->send(new OrderMail($fro, $subject, $view, $data));
        
                            // if ($send) {
                            //     $send = Mail::to('info@qmarthub.com')->send(new AdminOrderMail($customer->email, $subject, $view2, $data));
                            // }
                             return response()->json(['message'=>'Thank you our esteemed customer, Your receipt has been uploaded Successfully and your order has been successfully placed. We are currently processing your order and would reach out to you in no time.','url'=>'https://qmarthub.com/order-summary?trxref='.$request->input('reference').'&reference='.$request->input('reference')], 200);
                        } else {
                            return response()->json(['message'=>'Unable to Save Details',500]);
                        }
                    } else {
                        return response()->json(['message'=>'Internal Server Error',500]);
                    }
                }catch(\Exception $e){
                    return $e;
                }
            } else {
                try{
                    $userDetail = new GuestDetail;
                    $userDetail->first_name = $request->input('first_name');
                    $userDetail->last_name = $request->input('last_name');
                    $userDetail->location = $request->input('deliveryAddress');
                    $userDetail->phone_number = $request->input('phoneNumber');
                    $userDetail->email = $request->input('email');
                    $userDetail->reference = $request->input('reference');
                    $userDetail->status = 0;
                    $userDetail->delivery_fee = $request->input('deliveryFee');
                    $userDetail->service_charge = $request->input('serviceCharge');
                    $userDetail->payment_method = $request->input('payment_method');
                    if($request->input('discount') !=0){
                        $userDetail->discount = $request->input('discount');
                    }
                    $userDetail->amount = $request->input('amount');
                    $save = $userDetail->save();
                    if ($save) {
                        $myDate = date("Y-m-d");
                        $day = Carbon::createFromFormat('Y-m-d', $myDate)->format('d');
                        $month = Carbon::createFromFormat('Y-m-d', $myDate)->format('m');
                        $year = Carbon::createFromFormat('Y-m-d', $myDate)->format('Y');
                        if (!empty($products)) {
                            foreach($products as $order)
                            {
                                if (is_array($order)) {
                                    $orders = new Order;
                                    $orders->product_name = $order['product_name'];
                                    $orders->qty = $order['qty'];
                                    $orders->price = $order['price'];
                                    $orders->reference = $request->input('reference');
                                    $orders->status = 'Pending';
                                    $orders->product_image = $order['image'];
                                    $orders->day = $day;
                                    $orders->month = $month;
                                    $orders->year = $year;
                                    $orders->save();
                                } else {
                                    echo "Not an array.\n";
                                }
                            }
                        }
                        
                        if ($request->input('referralCode') !== null) {
                            $referral = Customer::where('refferal_id',$request->input('referralCode'))->first();
                            $referral_id = $referral->id;
                            $referral_point = $referral->points;
                            $new_referral_point = $referral_point + 50;
                            $updateReferralPoint = Customer::where('refferal_id',$request->input('referralCode'))->update(['points'=>$new_referral_point]);
                            $refer = new CustomerReferral;
                            $refer->customer_id = $referral_id;
                            $refer->customer_referral_id = $request->input('referralCode');
                            $refer->name = $request->input('name');
                            $refer->email = $request->input('email');
                            $refer->phone = $request->input('phoneNumber');
                            $refer->save();
                        }
                        if($request->hasFile('receipt')){
                            $file = $request->file('receipt');
                            $folder = 'qmarthub/receipt/receipt_files';
                            $uploadedFile = cloudinary()->upload($file->getRealPath(), [
                                'folder' => $folder
                            ]);
                
                            $fileNameToStore = $uploadedFile->getSecurePath();
                        }
                        $receipt = new ReceiptUpload;
                        $receipt->first_name = $request->input('first_name');
                        $receipt->last_name = $request->input('last_name');
                        $receipt->email = $request->input('email');
                        $receipt->reference = $request->input('reference');
                        $receipt->phone_number = $request->input('phoneNumber');
                        $receipt->user = 'Guest';
                        $receipt->receipt = $fileNameToStore;
                        $save3 = $receipt->save();
                        if ($save3) {
                            // $detail = GuestDetail::where('reference',$request->input('reference'))->first();
                            // $orders = Order::where('reference',$request->input('reference'))->get();
                            // $fro = 'info@qmarthub.com';
                            // $subject = 'Order Details';
                            // $view = 'mail-template.order';
                            // $view2 = 'mail-template.admin-order';
                            // $data = [
                            //     'fullname' => $detail->name,
                            //     'email' => $detail->email,
                            //     'location' => $detail->location,
                            //     'phone' => $detail->phone,
                            //     'reference' => $detail->reference,
                            //     'amount' => $detail->amount,
                            //     'orders' => $orders,
                            // ];
        
        
        
                            // $send = Mail::to($detail->email)->send(new OrderMail($fro, $subject, $view, $data));
        
                            // if ($send) {
                            //     $send = Mail::to('info@qmarthub.com')->send(new AdminOrderMail($detail->email, $subject, $view2, $data));
                            // }
                            // return $this->success('','Receipt Has been uploaded Successfully',200);
                            return response()->json(['message'=>'Thank you our esteemed customer, Your receipt has been uploaded Successfully and your order has been successfully placed. We are currently processing your order and would reach out to you in no time.','url'=>'https://qmarthub.com/order-summary?trxref='.$request->input('reference').'&reference='.$request->input('reference')], 200);
                        } else {
                            return response()->json(['message'=>'Unable to Save Details',500]);
                        }
                    } else {
                        return response()->json(['message'=>'Internal Server Error',500]);
                    }
                }catch(\Exception $e){
                    return $e;
                }
            }
        

        
    }
}
