<?php

namespace App\Http\Controllers\API;
  
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Device;
use App\Models\UserOtp;
use Twilio\Rest\Client;

/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="API Endpoints for user Authentication"
 * )
 */
class AuthOtpController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/otp/generate",
     *     summary="Generate OTP for user",
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
     *             @OA\Property(property="userOtp", type="object"),
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
            'phone_number' => 'required|exists:users,phone_number'
        ]);
  
        /* Generate An OTP */
        $userOtp = $this->generateOtp($request->phone_number);
        $userOtp->sendSMS($request->phone_number);

        return response()->json([
            'status' => true,
            'message' => 'OTP has been sent on Your Mobile Number.',
        ]);
    }
  
    /**
     * Write code on Method
     *
     * @return response()
     */
    protected function generateOtp($phone_number)
    {
        $user = User::where('phone_number', $phone_number)->first();
  
        /* user Does not Have Any Existing OTP */
        $userOtp = UserOtp::where('user_id', $user->id)->latest()->first();
  
        $now = now();
  
        if($userOtp && $now->isBefore($userOtp->expire_at)){
            return $userOtp;
        }
  
        /* Create a New OTP */
        return UserOtp::create([
            'user_id' => $user->id,
            'otp' => '0000',
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
     *             required={"user_id", "otp"},
     *             @OA\Property(property="otp", type="integer", example="0000"),
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
            'otp' => 'required'
        ]);  
  
        /* Validation Logic */
        $userOtp = UserOtp::where('otp', $request->otp)->first();
  
        $now = now();
        if (!$userOtp) {
            return response()->json([
                'error' => 'Your OTP is not correct!',
            ]);
        } else if($userOtp && $now->isAfter($userOtp->expire_at)){
            return response()->json([
                'error' => 'Your OTP has been expired!',
            ]);
        }
    
        $user = User::whereId($request->user_id)->first();
  
        if($user){
              
            $userOtp->update([
                'expire_at' => now()
            ]);
  
            if($request->header('Device') == 'Mobile') {
                $device = Device::where('user_id', $user->id)->first();
                if($device == null) {
                    $add = new Device();
                    $add->user_id = $user->id;
                    $add->token = $request->device_token;
                    $add->save();
                } else {
                    $device->token = $request->device_token;
                    $device->update();
                }
            }
    
            $token = $user->createToken('api-token')->plainTextToken;
    
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
