<?php

namespace App\Http\V1\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
        $this->middleware('jwt', ['except' => []]);
    }

    public function allUsers()
    {
        try {
            $data = User::all();
            if ($data->count() > 0) {
                return $this->responseJson(200, trans('messages.success'), $data);
            } else {
                return $this->responseJson(200, trans('messages.data_not_found'), []);
            }
        } catch (\Exception $e) {
            return $this->responseJson(200, trans('messages.data_not_found'), []);
        }
    }

    public function show($id)
    {
        try {
            $data = User::findOrFail($id);
            return $this->responseJson(200, trans('messages.success'), $data);
        } catch (\Exception $e) {
            return $this->responseJson(200, trans('messages.data_not_found'), new \stdClass());
        }
    }

    public function update($id, Request $request)
    {
        //validate incoming request
        $this->validate($request, [
            'name' => 'required|string',
        ]);

        try {
            // find user
            $user = User::findOrFail($id);
            $user->name = $request->input('name');
            $user->update(); // update user details

            return $this->responseJson(200, trans('messages.user_updated'), new \stdClass());
        } catch (\Exception $e) {
            return $this->responseJson(200, trans('messages.data_not_found'), new \stdClass());
        }

    }

    public function delete($id)
    {
        try {
            User::findOrFail($id)->delete();
            return $this->responseJson(200, trans('messages.user_deleted'), new \stdClass());
        } catch (\Exception $e) {
            return $this->responseJson(200, trans('messages.data_not_found'), new \stdClass());
        }

    }

}
