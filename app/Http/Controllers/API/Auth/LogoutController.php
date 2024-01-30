<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Config, Log, Session};

class LogoutController extends Controller
{
    use ApiResponse;
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        try {
            Session::flush();
            Auth::guard('web')->logout();
            return $this->success(Config::get('constants.AUTH.LOGOUT_SUCCESS'), 200);
        } catch(\Exception $e) {
            Log::error('AuthController/logout() => '.$e->getMessage());
            return $this->error(Config::get('constants.SOMETHING_WENT_WRONG'), 200);
        }
    }
}
