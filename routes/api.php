<?php

use Dingo\Api\Routing\Router;

/** @var Router $api */
$api = app(Router::class);

$api->version('v1', function (Router $api) {


    $api->group(['prefix' => 'auth'], function(Router $api) {
        
       
        $api->group(['middleware' => 'cors'], function(Router $api) {
            $api->post('signup', 'App\\Api\\V1\\Controllers\\SignUpController@signUp');       
            $api->post('login', 'App\\Api\\V1\\Controllers\\LoginController@login');
            $api->post('recovery', 'App\\Api\\V1\\Controllers\\ForgotPasswordController@sendResetEmail');
            $api->post('reset', 'App\\Api\\V1\\Controllers\\ResetPasswordController@resetPassword');
            $api->post('emailExists', 'App\\Api\\V1\\Controllers\\LoginController@emailExists');
            
        });

       
    });

    

    $api->group(['middleware' => 'jwt.auth'], function(Router $api) {

        $api->group(['middleware' => 'cors'], function(Router $api) {
            $api->get('users', 'App\\Api\\V1\\Controllers\\UserController@getUsers');        
            $api->get('userDetails/{id}', 'App\\Api\\V1\\Controllers\\UserController@userDetails');        
            $api->get('report', 'App\\Api\\V1\\Controllers\\UserController@getReport');        
            $api->get('graph', 'App\\Api\\V1\\Controllers\\UserController@getGraph'); 
			$api->get('leatestUsers', 'App\\Api\\V1\\Controllers\\UserController@getLatestUsers');			
			$api->post('signup', 'App\\Api\\V1\\Controllers\\UserController@signUp');			
			$api->delete('delete/{id}', 'App\\Api\\V1\\Controllers\\UserController@deleteUser');			
			$api->post('update/{id}', 'App\\Api\\V1\\Controllers\\UserController@updateUser');			
			$api->post('emailExists', 'App\\Api\\V1\\Controllers\\UserController@emailExists');			
			$api->post('emailExistsEdit/{id}', 'App\\Api\\V1\\Controllers\\UserController@emailExistsEdit');			
        });
        $api->get('protected', function() {
            return response()->json([
                'message' => 'Access to protected resources granted! You are seeing this text as you provided the token correctly.'
            ]);
        });

        $api->get('refresh', [
            'middleware' => 'jwt.refresh',
            function() {
                return response()->json([
                    'message' => 'By accessing this endpoint, you can refresh your access token at each request. Check out this response headers!'
                ]);
            }
        ]);

        $api->get('hello2', function() {
            return response()->json([
                'message' => 'This is a simple example of item returned by your APIs. Everyone can see it.'
            ]);
        });

    });

    $api->get('hello', function() {
        return response()->json([
            'message' => 'This is a simple example of item returned by your APIs. Everyone can see it.'
        ]);
    });
});
