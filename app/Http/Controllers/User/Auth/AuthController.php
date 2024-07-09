<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Str;

class AuthController extends Controller
{
    public function sendOTP($phone_number)
    {
        $seller = Seller::where('phone_number', $phone_number)->first();

        if($seller){
            $otp = rand(1000,9999);
            
            $seller->otp = $otp;

            $seller->update();

            return response()->json($otp);
                    
            } else {
            return response()->json(['message'=>'No sellers exists with this phone number'], 404);
        }
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'phone_number' => 'required|string|max:255',
        ]);

        $user = Seller::create([
            'name' => 'Adyňyz',
            'phone_number' => $validatedData['phone_number'],
            'status' => true,
        ]);

        $this->sendOTP($phone_number);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

	public function login(Request $request)
    {
        if (!Auth::attempt($request->only('username', 'password'))) {
            return response()->json(['message' => 'Invalid login details'], 401);
        }

        $user = User::where('username', $request['username'])->where('status', 1)->firstOrFail();

        $user->online = true;

        $user->update();

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


    public function logout()
    {
        $user = User::findOrFail(auth()->user()->id);

        $user->online = false;

        $user->update();

        $tokenId = Str::before(request()->bearerToken(), '|');
        
        auth()->user()->tokens()->where('id', $tokenId )->delete();

        return response()->json([
            'success' => true,
        ]);
    }

    public function me(Request $request)
    {
        return $request->user();
    }
}
