<?php

namespace App\Http\Controllers\Customer\Dashboard;

use App\Http\Controllers\Controller;
use App\Mail\AdminOrderMail;
use App\Mail\AdminWalletMail;
use App\Mail\OrderMail;
use App\Mail\WalletMail;
use App\Models\Customer;
use App\Models\CustomerCharge;
use App\Models\CustomerReferral;
use App\Models\Order;
use App\Models\walletLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class WalletController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function fundWalletLink(Request $request)
    {
        $this->validate($request,[
            'amount' => 'required',
            'currency' => 'required',
            'reference' => 'required',
            'email' => 'required'
        ]);
        if (auth('api')->user()) {
            $userDetail = new walletLog;
            $userDetail->customer_id = auth('api')->user()->id;
            $userDetail->reference = $request->input('reference');
            $userDetail->customer_name = auth('api')->user()->first_name.' '.auth('api')->user()->last_name;
            $userDetail->phone_number = auth('api')->user()->phone_number;
            $userDetail->email = auth('api')->user()->email;
            $userDetail->amount = $request->input('amount');
            $save = $userDetail->save();
            if ($save) {
                $curl = curl_init();
                $email = auth('api')->user()->email;
                $tot = $request->input('amount');
                $total = $tot * 100;
                $amount = $total;
                $reference = $request->input('reference');
                $fullname = auth('api')->user()->first_name.' '.auth('api')->user()->last_name;

                // url to go to after payment
                $callback_url = url('api/v1/customer/dashboard/wallet-callback');

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
                    'phoneNumber'=>auth('api')->user()->phone_number,
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
            } else {
                return response()->json(['status' => 'failed','response'=>'Internal Server Error',500]);
            }
        }
    }

    public function fundWalletCallback()
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
                $customerDetail = walletLog::where(['reference' => $reference, 'customer_id' => auth('api')->user()->id])->first();
                $DBreference = $customerDetail->reference;
                $WalletAmount = $customerDetail->amount;
                $logstatus = $customerDetail->status;
                if ($logstatus == false) {
                    if ($reference == $DBreference && $status == true) {
                        $customerDetail = walletLog::where(['reference' => $reference, 'customer_id' => auth('api')->user()->id])->update(['status'=>1]);
                        $customer = Customer::where(['id' => auth('api')->user()->id])->first();
                        $mainAmount = $customer->wallet;
                        $wallet = $mainAmount + $WalletAmount;
                        $updateWallet = Customer::where(['id' => auth('api')->user()->id])->update(['wallet'=> $wallet]);
                        if ($updateWallet) {
                            $fro = 'info@qmarthub.com';
                            $subject = 'Wallet Funding';
                            $view = 'mail-template.wallet';
                            $view2 = 'mail-template.admin-wallet';
                            $data = [
                                'first_name' => $customer->first_name,
                                'last_name' => $customer->last_name,
                                'email' => $customer->email,
                                'phone' => $customer->phone_number,
                                'reference' => $reference,
                                'amount' => $WalletAmount,
                            ];
                            $send1 = Mail::to($customer->email)->send(new WalletMail($fro, $subject, $view, $data));
    
                            if ($send1) {
                                $send2 = Mail::to('info@qmarthub.com')->send(new AdminWalletMail($customer->email, $subject, $view2, $data));
                            }
                            return response()->json(['message' => 'This transaction was successful and money has been added to your wallet',]);
                        } else {
                            return response()->json(['message' => 'Failed to update wallet'],400);
                        }
                    }else{
                        return response()->json(['message' => 'Failed to confirm Payment'],400);
                    }
                } else {
                    return response()->json(['message' => 'This transaction has already been confirmed',]);
                }
                
                
            }
        }

    }

    public function walletCheckout(Request $request)
    {
        $this->validate($request,[
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
            $customer = Customer::where(['id' => auth('api')->user()->id])->first();
            $WalletAmount = $customer->wallet;
            if ($WalletAmount == 0) {
                return response()->json(['message' => 'Fund Your wallet to make purchase using your wallet'],400);
            } else if($WalletAmount < $request->input('amount')) {
                return response()->json(['message' => 'Insufficient Fund'],400);
            }else{
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
                $userDetail->status = 1;
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
                    $newAmount = $WalletAmount - $request->amount;
                    $cust = Customer::where(['id' => auth('api')->user()->id])->update(['wallet'=> $newAmount]);
                    $charge = CustomerCharge::where(['reference' => $request->reference, 'customer_id' => auth('api')->user()->id])->first();
                    $customer = Customer::where(['id' => auth('api')->user()->id])->first();
                    $orders = Order::where(['reference' => $request->reference, 'customer_id' => auth('api')->user()->id])->get();
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
                    return response()->json("https://qmarthub.com/order-summary?trxref=".$request->reference."&reference=".$request->reference."");
                }
            }
        } else {
            return response()->json(['meassage' => 'Unauthenticated'],403);
        }
        
    }
}
