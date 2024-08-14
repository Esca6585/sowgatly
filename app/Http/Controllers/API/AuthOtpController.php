<?php

namespace App\Http\Controllers\API;
  
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Device;
use App\Models\UserOtp;
use Illuminate\Support\Facades\DB; // Add this import
use Illuminate\Support\Facades\Log; // Add this for logging
use Illuminate\Support\Facades\Validator;
use App\Rules\TurkmenistanPhoneNumber;

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
            'phone_number' => ['required', new TurkmenistanPhoneNumber],
        ]);

        try {
            /* Generate An OTP */
            $userOtp = $this->generateOtp($request->phone_number);

            return response()->json([
                'status' => true,
                'message' => 'OTP has been sent on Your Mobile Number.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 404); // Using 404 status code for "Not Found"
        }
    }
  
    /**
     * Generate OTP for the given phone number
     *
     * @param string $phone_number
     * @return UserOtp|null
     * @throws \Exception
     */
    protected function generateOtp($phone_number)
    {
        DB::beginTransaction();
        try {
            // Find the user by phone number
            $user = User::where('phone_number', $phone_number)->first();

            // If user not found, throw an exception
            if (!$user) {
                throw new \Exception('User not found with the provided phone number.');
            }

            // Check for existing OTP
            $userOtp = UserOtp::where('user_id', $user->id)->latest()->first();
            $now = now();

            if ($userOtp && $now->isBefore($userOtp->expire_at)) {
                DB::commit();
                return $userOtp;
            }

            // Create a new OTP
            $newOtp = UserOtp::create([
                'user_id' => $user->id,
                'otp' => "0000", // Set default OTP to "0000" // str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT), // Generate a random 4-digit OTP
                'expire_at' => $now->addMinutes(10)
            ]);

            DB::commit();
            return $newOtp;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error
            \Log::error('Error in generateOtp: ' . $e->getMessage());
            throw $e;
        }
    }
  
    /**
     * @OA\Post(
     *     path="/api/otp/login",
     *     summary="Login with OTP",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"user_id", "otp"},
     *             @OA\Property(property="phone_number", type="integer", example="65656585"),
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
            'phone_number' => ['required', new TurkmenistanPhoneNumber],
            'otp' => 'required|min:4'
        ]);  
  
        /* Validation Logic */
        $user = User::where('phone_number', $request->phone_number)->first();

        $userOtp = UserOtp::where('otp', $request->otp)->where('user_id', $user->id)->latest()->first();
  
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
    
        $user = User::whereId($userOtp->user_id)->first();
  
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
                'user' => new UserResource($user),
                'shop' => $shop ? new ShopResource($shop) : null,
            ]);
        }
  
        return response()->json([
            'error' => 'Your Otp is not correct!',
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/otp/register",
     *     summary="Register user with OTP",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"phone_number", "otp"},
     *             @OA\Property(property="phone_number", type="string", example="65656585"),
     *             @OA\Property(property="otp", type="string", example="0000"),
     *             @OA\Property(property="name", type="string", example="Esen Meredow"),
     *             @OA\Property(property="email", type="string", example="esca656585@gmail.com"),
     *             @OA\Property(property="device_token", type="string", example="device_token_here")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Registration successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid OTP or expired OTP"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function registerWithOtp(Request $request)
    {
        /* Validation */
        $request->validate([
            'phone_number' => ['required', new TurkmenistanPhoneNumber],
            'otp' => 'required|min:4',
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users',
            'device_token' => 'nullable|string'
        ]);

        /* OTP Validation Logic */
        $userOtp = UserOtp::where('otp', $request->otp)
                          ->whereHas('user', function ($query) use ($request) {
                              $query->where('phone_number', $request->phone_number);
                          })->latest()->first();

        if (!$userOtp) {
            return response()->json([
                'error' => 'Invalid OTP or expired OTP!',
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Register new user
            $user = User::create([
                'phone_number' => $request->phone_number,
                'name' => $request->name,
                'email' => $request->email, // Nullable email field
            ]);

            // Update OTP as used
            $userOtp->update([
                'expire_at' => now(),
            ]);

            // Save device token if provided
            if ($request->device_token) {
                Device::updateOrCreate(
                    ['user_id' => $user->id],
                    ['token' => $request->device_token]
                );
            }

            // Generate token for the user
            $token = $user->createToken('api-token')->plainTextToken;

            DB::commit();

            return response()->json([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => new UserResource($user),
                'shop' => $shop ? new ShopResource($shop) : null,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in registerWithOtp: ' . $e->getMessage());
            return response()->json([
                'error' => 'Registration failed!',
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/otp/logout",
     *     summary="Logout the user",
     *     tags={"Authentication"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logged out successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function logout(Request $request)
    {
        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ], 200);
    }
}
