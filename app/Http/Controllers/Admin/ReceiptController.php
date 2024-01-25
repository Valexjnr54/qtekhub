<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AdminOrderMail;
use App\Mail\OrderMail;
use App\Models\Customer;
use App\Models\CustomerCharge;
use App\Models\GuestDetail;
use App\Models\Order;
use App\Models\ReceiptUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ReceiptController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $receipts = ReceiptUpload::all();
        return view('admin.receipts.receipts')->with(['receipts'=>$receipts]);
    }

    public function confirmReceipt($id)
    {
        $receipt = ReceiptUpload::find($id);
        $reference = $receipt->reference;
        $receiptUpdate = ReceiptUpload::where('id',$id)->update(['status'=>1]);
        $user = $receipt->user;
        if ($user == 'Guest') {
            $userDetail = GuestDetail::where('reference',$reference)->first();
            $userReference = $userDetail->reference;
            $firstName = $userDetail->first_name;
            $lastName = $userDetail->last_name;
            $userUpdate = GuestDetail::where('reference',$userReference)->update(['status'=>1]);
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
            return back()->with(['success' => 'Payment Confirmed!','title'=>'Confirm Payment']);
        } else {
            $userDetail = CustomerCharge::where('reference',$reference)->first();
            $userReference = $userDetail->reference;
            $userUpdate = CustomerCharge::where('reference',$userReference)->update(['status'=>1]);
            $charge = CustomerCharge::where(['reference' => $reference])->first();
            $customer_id = $charge->customer_id;
            $customer = Customer::where(['id' => $customer_id])->first();
            $orders = Order::where(['reference' => $reference, 'customer_id' =>$customer_id])->get();
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
            return back()->with(['success' => 'Payment Confirmed!','title'=>'Confirm Payment']);
        }
        
        
    }

    public function deleteReceipt($id)
    {
        $receipt = ReceiptUpload::find($id);
        $receipt->delete();
        return back()->with(['success'=>'Receipt has been deleted Successfully','title'=>'Delete Receipt']);
    }

    public function loadModal($id)
    {
        $receipt = ReceiptUpload::find($id);

      $html = "";
      if(!empty($receipt)){
        $receiptFile = $receipt->receipt;
        $split = explode('.',$receiptFile);
        $ext = $split[1];
        if ($ext == 'jpg' || $ext== 'jpeg' || $ext == 'png' || $ext=='gif' || $ext == 'JPG' || $ext == 'JPEG' || $ext == 'PNG' || $ext == 'GIF') {
            $show = '<img src="'.$receiptFile.'" width="auto" height="auto" alt="Image Receipt">';
        } else {
            $show = '<iframe src="'.$receiptFile.'" width="600px" height="600px">
            </iframe>';
        }

         $html = "
            <tr>
              <td width='30%'><b>Last Name:</b></td>
              <td width='70%'> ".$receipt->last_name."</td>
           </tr>
           <tr>
              <td width='30%'><b>First Name:</b></td>
              <td width='70%'> ".$receipt->first_name."</td>
           </tr>
           <tr>
              <td width='30%'><b>Phone Number:</b></td>
              <td width='70%'> ".$receipt->phone_number."</td>
           </tr>
           <tr>
              <td width='30%'><b>Email:</b></td>
              <td width='70%'> ".$receipt->email."</td>
           </tr>
           <tr>
              <td width='30%'><b>Type:</b></td>
              <td width='70%'> Receipt for ".$receipt->type."</td>
           </tr>
           <tr>
              <td width='30%'><b>Reference:</b></td>
              <td width='70%'> ".$receipt->reference."</td>
           </tr>
           <tr>
                <td width='10%'><b>Receipt:</b></td>
                <td width='90%'>".$show."</td>
           </tr>
           ";
      }
      $response['html'] = $html;

      return response()->json($response);
    }
}
