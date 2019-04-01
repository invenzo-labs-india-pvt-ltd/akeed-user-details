<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Support\Facades\Hash;

use Tymon\JWTAuth\Facades\JWTAuth;

use App\Models\User;
use App\Helpers\ResponseBuilder;
//use Cache;

class LoginController extends Controller
{
    public $successStatus = 200;
    public $failureStatus = 400;
    public $validationErrStatus = 402;

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
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        try {
            $input = $request->all();
            $input['user_id'] = isset($input['user_id']) ? trim($input['user_id']) : '';
            $input['mobile_number'] = $input['email'] = '';
            /* To check the given input is email or mobile number */
            if (is_numeric($input['user_id'])) {
                $input['mobile_number'] = $input['user_id'];
            } else {
                $input['email'] = $input['user_id'];
            }
            $rules = [
                'user_type' => ['required', 'max:5', 'numeric'],
                'mobile_number' => ['required_if:login_type,1', 'numeric', 'digits_between:7,10', 'exists:users,mobile_number'],
                'email' => ['required_if:login_type,2', 'email', 'max:150', 'exists:users,email'],
                'password' => ['required', 'min:6', 'max:16'],
                'device_type' => ['required'],
                'android_device_token' => ['required_if:device_type,android'],
                'ios_device_token' => ['required_if:device_type,ios'],
            ];
            $validator = app('validator')->make($input, $rules);

            $error = $result = array();
            if ($validator->fails()) {
                foreach ($validator->errors()->messages() as $key => $value) {
                    $error[] = is_array($value) ? implode(',', $value) : $value;
                }
                $errors = implode(', \n ', $error);
                return ResponseBuilder::responseResult($this->failureStatus, $errors);
            }
            /* To check the mobile is valid or not */
            $userDetail = User::getLoginDetails($input['user_type'], $input['email'], $input['mobile_number']);
            if (!empty($userDetail)) {
                if (Hash::check($input['password'], $userDetail->password)) {
                    $data['token'] = JWTAuth::fromUser($userDetail);

                    return ResponseBuilder::responseResult($this->successStatus, 'Logged-in Successfully', $data);
                } else {
                    return ResponseBuilder::responseResult($this->failureStatus, 'Invalid Password');
                }
            } else {
                return ResponseBuilder::responseResult($this->failureStatus, 'Invalid Credential');
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return ResponseBuilder::responseResult($this->failureStatus, $e->getMessage());
        } catch (\Exception $e) {
            return ResponseBuilder::responseResult($this->failureStatus, $e->getMessage());
        }
    }
    /* To check the token is valid or not */
    public function checkAuthToken ()
    {
        echo 11;die;
    }
}
