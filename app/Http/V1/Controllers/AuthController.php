<?php

namespace App\Http\V1\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Store a new user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function register(Request $request)
    {
        //validate incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        if ($validator->fails()) {
            $message = trans('messages.invalid_params');

            foreach ($validator->errors()->all() as $key => $error) {
                $message = $error;
                break;
            }
            return $this->responseJson(422, $message, new \stdClass());
            // return $this->responseJson(422, trans('messages.error'), $validator->messages());
        }

        try {

            $user = new User;
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $plainPassword = $request->input('password');
            $user->password = app('hash')->make($plainPassword);

            $user->save();

            $credentials = $request->only(['email', 'password']);

            if (!$token = Auth::attempt($credentials)) {
                return $this->responseJson(401, trans('messages.unauthorized'), new \stdClass());
            }

            $data['user'] = Auth::user();
            $data['token'] = $token;
            $data['token_type'] = 'bearer';
            $data['expires_in'] = Auth::factory()->getTTL() * 60;

            return $this->responseJson(200, trans('messages.user_created'), $data);

        } catch (\Exception $e) {
            //return error message
            return $this->responseJson(401, trans('messages.unauthorized'), new \stdClass());
        }

    }

    /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */
    public function login(Request $request)
    {
        //validate incoming request
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            $message = trans('messages.invalid_params');

            foreach ($validator->errors()->all() as $key => $error) {
                $message = $error;
                break;
            }
            return $this->responseJson(422, $message, new \stdClass());
            // return $this->responseJson(422, trans('messages.error'), $validator->messages());
        }

        try {

            $credentials = $request->only(['email', 'password']);
            if (!$token = Auth::attempt($credentials)) {
                return $this->responseJson(401, trans('messages.unauthorized'), new \stdClass());
            }

            $data['user'] = Auth::user();
            $data['token'] = $token;
            $data['token_type'] = 'bearer';
            $data['expires_in'] = Auth::factory()->getTTL() * 60;
            return $this->responseJson(200, trans('messages.success'), $data);

        } catch (\Exception $e) {
            //return error message
            return $this->responseJson(401, trans('messages.unauthorized'), new \stdClass());
        }

    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            Auth::logout();
            return $this->responseJson(200, trans('messages.successfully_logged_out'), new \stdClass());
        } catch (\Exception $e) {
            //return error message
            return $this->responseJson(401, trans('messages.unauthorized'), new \stdClass());
        }

    }

    public function refreshToken($token = null)
    {
        try {
            $data['user'] = Auth::user();
            $data['token'] = Auth::refresh();
            $data['token_type'] = 'bearer';
            $data['expires_in'] = Auth::factory()->getTTL() * 60;
            return $this->responseJson(200, trans('messages.success'), $data);

        } catch (\Exception $e) {
            //return error message
            return $this->responseJson(401, trans('messages.unauthorized'), new \stdClass());
        }
    }

}
