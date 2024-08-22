<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Device;
use App\Models\UserOtp;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Rules\TurkmenistanPhoneNumber;
use App\Http\Resources\UserResource;
use App\Http\Resources\ShopResource;
use Str;

class AuthOtpController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/otp/generate",
     *     summary="Generate OTP",
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
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function generate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => ['required', new TurkmenistanPhoneNumber],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 200);
        }

        try {
            $userOtp = $this->generateOtp($request->phone_number);

            if ($userOtp) {
                return response()->json([
                    'success' => true,
                    'message' => 'OTP has been sent on Your Mobile Number.',
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.',
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 200);
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
     *     path="/api/login",
     *     summary="Login with OTP",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"phone_number", "otp"},
     *             @OA\Property(property="phone_number", type="string", example="65656585"),
     *             @OA\Property(property="otp", type="string", example="0000")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string"),
     *             @OA\Property(property="user", type="object"),
     *             @OA\Property(property="shops", type="array", @OA\Items())
     *         )
     *     )
     * )
     */
    public function loginWithOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => ['required', new TurkmenistanPhoneNumber],
            'otp' => 'required|digits:4'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 200);
        }

        $user = User::where('phone_number', $request->phone_number)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 200);
        }

        $userOtp = UserOtp::where('otp', $request->otp)->where('user_id', $user->id)->latest()->first();

        $now = now();
        if (!$userOtp) {
            return response()->json([
                'success' => false,
                'message' => 'Your OTP is not correct!',
            ], 200);
        } else if ($userOtp && $now->isAfter($userOtp->expire_at)) {
            return response()->json([
                'success' => false,
                'message' => 'Your OTP has been expired!',
            ], 200);
        }

        $userOtp->update([
            'expire_at' => now()
        ]);

        if ($request->header('Device') == 'Mobile') {
            Device::updateOrCreate(
                ['user_id' => $user->id],
                ['token' => $request->device_token]
            );
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => new UserResource($user),
            'shops' => ShopResource::collection($user->shops),
        ], 200);
    }
    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register with OTP",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"phone_number", "name"},
     *             @OA\Property(property="phone_number", type="string", example="65656565"),
     *             @OA\Property(property="name", type="string", example="Esen Meredow"),
     *             @OA\Property(property="email", type="string", example="esca6585@modahouse.top")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Registration successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="token_type", type="string"),
     *             @OA\Property(property="otp", type="string"),
     *             @OA\Property(property="user", type="object"),
     *             @OA\Property(property="shops", type="array", @OA\Items()),
     *             @OA\Property(property="device_token", type="string")
     *         )
     *     )
     * )
     */
    public function registerWithOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => ['required', new TurkmenistanPhoneNumber],
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 200);
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'phone_number' => $request->phone_number,
                'name' => $request->name,
                'email' => $request->email,
            ]);

            $otpCode = "0000"; // For testing purposes, use a fixed OTP

            UserOtp::create([
                'user_id' => $user->id,
                'otp' => $otpCode,
                'expire_at' => now()->addMinutes(10),
            ]);

            // Generate a unique device token
            $deviceToken = Str::random(64);

            Device::create([
                'user_id' => $user->id,
                'token' => $deviceToken
            ]);

            $token = $user->createToken('api-token')->plainTextToken;

            DB::commit();

            return response()->json([
                'success' => true,
                'access_token' => $token,
                'token_type' => 'Bearer',
                'otp' => $otpCode,
                'user' => new UserResource($user),
                'shops' => ShopResource::collection($user->shops),
                'device_token' => $deviceToken,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in registerWithOtp: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Registration failed: ' . $e->getMessage(),
            ], 200);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout",
     *     tags={"Authentication"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout failed: ' . $e->getMessage(),
            ], 200);
        }
    }
}