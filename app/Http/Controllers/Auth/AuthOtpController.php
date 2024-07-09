<?php

namespace App\Http\Controllers\Auth;
  
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Seller;
use App\Models\Device;
use App\Models\SellerOtp;
use Twilio\Rest\Client;
  
class AuthOtpController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function login()
    {
        return view('auth.otpLogin');
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function generate(Request $request)
    {
        /* Validate Data */
        $request->validate([
            'phone_number' => 'required|exists:sellers,phone_number'
        ]);
  
        /* Generate An OTP */
        $sellerOtp = $this->generateOtp($request->phone_number);
        $sellerOtp->sendSMS($request->phone_number);

        return response()->json([
            'sellerOtp' => $sellerOtp,
            'success' => 'OTP has been sent on Your Mobile Number.',
        ]);
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function generateOtp($phone_number)
    {
        $seller = Seller::where('phone_number', $phone_number)->first();
  
        /* User Does not Have Any Existing OTP */
        $sellerOtp = SellerOtp::where('seller_id', $seller->id)->latest()->first();
  
        $now = now();
  
        if($sellerOtp && $now->isBefore($sellerOtp->expire_at)){
            return $sellerOtp;
        }
  
        /* Create a New OTP */
        return SellerOtp::create([
            'seller_id' => $seller->id,
            'otp' => rand(123456, 999999),
            'expire_at' => $now->addMinutes(10)
        ]);
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function verification($seller_id)
    {
        return view('auth.otpVerification')->with([
            'seller_id' => $seller_id
        ]);
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function loginWithOtp(Request $request)
    {
        /* Validation */
        $request->validate([
            'seller_id' => 'required|exists:sellers,id',
            'otp' => 'required'
        ]);  
  
        /* Validation Logic */
        $sellerOtp = SellerOtp::where('seller_id', $request->seller_id)->where('otp', $request->otp)->first();
  
        $now = now();
        if (!$sellerOtp) {
            return response()->json([
                'error' => 'Your OTP is not correct!',
            ]);
        } else if($sellerOtp && $now->isAfter($sellerOtp->expire_at)){
            return response()->json([
                'error' => 'Your OTP has been expired!',
            ]);
        }
    
        $seller = Seller::whereId($request->seller_id)->first();
  
        if($seller){
              
            $sellerOtp->update([
                'expire_at' => now()
            ]);
  
            if($request->header('Device') == 'Mobile') {
                $device = Device::where('seller_id',$user->id)->first();
                if($device == null) {
                    $add = new Device();
                    $add->seller_id = $seller->id;
                    $add->token = $request->device_token;
                    $add->save();
                } else {
                    $device->token = $request->device_token;
                    $device->update();
                }
            }
    
            $token = $user->createToken('auth_token')->plainTextToken;
    
            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
            ]);
        }
  
        return response()->json([
            'error' => 'Your Otp is not correct!',
        ]);
    }
}
