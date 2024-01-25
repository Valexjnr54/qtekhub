<?php

namespace App\Http\Controllers\Customer\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;

class CustomerAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api',['except' => ['login','register','email_verify']]);
    }

    
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone_number' => 'required|string|max:11',
            'email' => 'required|email|max:255|unique:customers',
            'password' => 'required|string|min:6',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $refferalCode = 'QM'.strtoupper($request->name.'-'.Str::random(6));
        $refferalId  = str_replace(' ', '-', $refferalCode);
        $token = md5(time().$request->first_name.$request->last_name.$request->email);
        
        $user = Customer::create(array_merge(
            $validator->validated(),
            [
                'password' => bcrypt($request->password),
                'refferal_id' => $refferalId,
                'verification_token' => $token,
                'points' => 0
            ]
        ));
        $sender = 'info@qmarthub.com';
        $data = [
            'name' => $request->name,
            'content' => 'This is a Welcome email content from Qmarthub',
        ];

        Mail::to($request->email)->send(new WelcomeMail($user, $token, $sender));
        
        // Generate JWT token for the user
        // $token = JWTAuth::fromUser($user);
        return response()->json([
            'message' => 'Registration successful',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        if (!$token = auth('api')->attempt($validator->validated())) {
            return response()->json(['errors' =>'Invalid Email / Password'], 422);
        }

        $customer_detail = Customer::where('id',auth('api')->user()->id)->first();
        $email_verified = $customer_detail->email_verified;

        if($email_verified == false){
            return response()->json(['errors' =>'E-Mail Verification Required'], 422);
        }

        return $this->createToken($token);
    }

    public function createToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth('api')->user()
        ]);
    }

    public function email_verify()
    {
        $token = isset($_GET['token']) ? $_GET['token'] : '';

        if(!$token){
            return response()->json([
                'status' => 'Request Failed',
                'message' => 'Invaild URL provided',
            ],404);
        }

        try {
            $customer = Customer::where('verification_token', '=', $token)->first();
            if (isset($customer)) {
                $customer->email_verified = true;
                $customer->update();
                return response()->json([
                    'status' => 'Successful',
                    'message' => 'E-mail Address has been verified',
                ],200);
            }else{
                return response()->json([
                    'status' => 'Request Failed',
                    'message' => 'Invaild Token Provided',
                ],404);
            }
        } catch (\Throwable $error) {
            return $error;
        }

        // return response()->json([
        //     'status' => 'Successful',
        //     'message' => 'E-mail Address has been verified',
        // ],200);
    }

    public function profile()
    {
        return response()->json(auth('api')->user());
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json([
            'message' => 'User Logged out successful'
        ]);
    }
}
