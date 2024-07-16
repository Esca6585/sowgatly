<?php

namespace App\Http\Controllers\API;
  
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Seller;
use App\Models\Device;
use App\Models\SellerOtp;
use Twilio\Rest\Client;

/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="API Endpoints for Seller Authentication"
 * )
 */
class AuthOtpController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/otp/generate",
     *     summary="Generate OTP for seller",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"phone_number"},
     *             @OA\Property(property="phone_number", type="string", example="65656585")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP generated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="sellerOtp", type="object"),
     *             @OA\Property(property="success", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
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
  
        /* Seller Does not Have Any Existing OTP */
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
     * @OA\Post(
     *     path="/api/otp/login",
     *     summary="Login with OTP",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"seller_id", "otp"},
     *             @OA\Property(property="seller_id", type="integer", example=1),
     *             @OA\Property(property="otp", type="string", example="123456"),
     *             @OA\Property(property="device_token", type="string", example="device_token_here")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid OTP or expired OTP"
     *     )
     * )
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
                $device = Device::where('seller_id', $seller->id)->first();
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
    
            $token = $seller->createToken('auth_token')->plainTextToken;
    
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
