<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends BaseController
{

    //
    public function login(Request $request)
    {
        # code...

        $user = User::where('username', $request->username)->first();

        if ($user) {
            if (Hash::check($request->password, $user->password)) {

                $dept = Department::where('id', $user->dept_id)->first();

                $result["token"] = $user->createToken('token')->accessToken;

                $result_to_return = (object)["name" => $user->name, "token" => $user->createToken('token')->accessToken, "departiment" => $dept->name, "dept_id" => $dept->id];


                Log::channel('userlogingactivities')->info('Time ' . Carbon::now()->toDateTimeString() . ' user of id ' . $user->id . ' login successfully');


                return $this->returnResponse("Login pass", $result_to_return);
            } else {
                return $this->returnError("Wrong username or password", ["Wrong username or password"]);
            }
        } else {
            return $this->returnError('Wrong username or password', []);
        }
    }


    public function logout(Request $request)
    {

        $user_id = Auth::user()->id;
        $request->user()->token()->revoke();
        // Revoke all of the token's refresh tokens

        $refreshTokenRepository = app('Laravel\Passport\RefreshTokenRepository');
        $refreshTokenRepository->revokeRefreshTokensByAccessTokenId($request->user()->token()->id);

        Log::channel('userlogingactivities')->info('Time ' . Carbon::now()->toDateTimeString() . ' user of id ' . $user_id . ' logout successfully');

        return $this->returnResponse('Logged out successfully', 200);
    }
}
