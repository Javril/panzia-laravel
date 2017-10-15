<?php

namespace App\Api\V1\Controllers;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\LoginRequest;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use App\User;

class LoginController extends Controller
{
    public function login(LoginRequest $request, JWTAuth $JWTAuth)
    {
        $credentials = $request->only(['email', 'password']);



        try {
            $token = $JWTAuth->attempt($credentials);

            if(!$token) {
                throw new AccessDeniedHttpException();
            }

        } catch (JWTException $e) {
            throw new HttpException(500);
        }

        $user = User::where('email', '=', $request->get('email'))->first();

    
        return response()
            ->json([
                'status' => 'ok',
                'token' => $token,
                'user'=>$user->toArray()
            ]);
    }
	
	public function signUp(Request $request){
		 $input = $request->all();
		  if($input->save()) {		 
			return response()
				->json([
					'status' => 'ok'
				]);
		  }
	}
	
	public function emailExists(Request $request){
		return response()
			->json([
				'status' => 'ok'
			]);
	}
	
}
