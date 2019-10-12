<?php

namespace App\Http\Controllers\API;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Auth;

class PassportController extends Controller
{
    public $successStatus = 200;

    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(){
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            return response()->json(['code' => '200','message' => 'Login Success', 'token' => 'Bearer '.$user->createToken('MyApp')->accessToken], $this->successStatus);
        }
        else{
            throw  new AuthorizationException('Unauthorised');
        }
    }

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        // validating request
        $this->validating($request);

        $input = $request->all();
//        dd($input);
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        $success['first_name'] =  $user->first_name;
        $success['last_name'] =  $user->last_name;
        $success['gender'] =  $user->gender;
        $success['birth_date'] =  $user->birth_date;
        $success['mobile_number'] =  $user->mobile_number;
        $success['email'] =  $user->email;
        $success['token'] =  $user->createToken('MyApp')->accessToken;

        return response()->json(['code' => '201', 'success'=>$success], $this->successStatus);
    }

    /**
     * details api
     *
     * @return \Illuminate\Http\Response
     */
    public function getDetails()
    {
        $user = Auth::user();
        return response()->json([
            'code' => '200',
            'message' => 'Berhasil get user',
            'data' => $user], $this->successStatus);
    }

    /**
     * Function for validation
     * @param Request $request
     * @throws \Illuminate\Validation\ValidationException
     */
    private function validating($request){
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'birth_date' => 'required|date',
            'mobile_number' => 'required|unique:users',
            'gender' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
    }
}
