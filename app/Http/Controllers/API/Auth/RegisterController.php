<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Config, DB, Log, Hash};

class RegisterController extends Controller
{
    use ApiResponse;

    /**
     * Handle the incoming request.
     */
    public function __invoke(RegisterRequest $request)
    {
        try {
            DB::beginTransaction();
            $input_fields = $request->all();
            $input_fields['password'] = Hash::make($request['password']);
            $user = User::create($input_fields);
            $auth_user['token'] = $user->createToken('SPA')->plainTextToken;
            DB::commit();
            return $this->success(Config::get('constants.AUTH.REGISTRATION_SUCCESS'), 200, $auth_user);
        } catch(\Exception $e) {
            DB::rollback();
            Log::error('RegisterController/register() => '.$e->getMessage());
            return $this->error(Config::get('constants.SOMETHING_WENT_WRONG'), 200);
        }
    }
}
