<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Config, DB, Log};

class LoginController extends Controller
{
    use ApiResponse;

    /**
     * Handle the incoming request.
     */
    public function __invoke(LoginRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if($user) {
                if(Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                    $auth_user = Auth::user();
                    $auth_user['token'] = $user->createToken('SPA')->plainTextToken;
                    return $this->success(Config::get('constants.AUTH.LOGIN_SUCCESS'), 200, $auth_user);
                }
            }
            return $this->error(Config::get('constants.AUTH.LOGIN_FAIL'), 401);
        } catch(\Exception $e) {
            DB::rollback();
            Log::error('LoginController/login() => '.$e->getMessage());
            return $this->error(Config::get('constants.SOMETHING_WENT_WRONG'), 200);
        }
    }
}
