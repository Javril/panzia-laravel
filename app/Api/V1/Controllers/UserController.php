<?php

namespace App\Api\V1\Controllers;
use Validator;
use Config;
use App\User;
use Tymon\JWTAuth\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Api\V1\Requests\UserRequest;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Input;
use Auth;


class UserController extends Controller
{
	/* get all users method
	* @param no
	* 
	*/
    public function getUsers()
    {
		$pagination = Input::get();
		 $search = $pagination['search'];
		 if(!empty($search)){
			
         $users = User::where('first_name', 'like', "%".$search."%")->paginate($pagination['per_page']);
		 }else{
          $users = User::orderBy('id','desc')->paginate($pagination['per_page']);
		}
        return response()->json([
                'status' => 'ok',
                'users'=>$users->toArray()
            ]);

        if(!Config::get('boilerplate.sign_up.release_token')) {
            return response()->json([
                'status' => 'ok'
            ], 201);
        }

        $token = $JWTAuth->fromUser($user);
        return response()->json([
            'status' => 'ok',
            'token' => $token
        ], 201);
    }
	
	/* user get for update method
	* @param user id
	* 
	*/
	public function userDetails($id){
		$userdata = User::findOrFail($id);
		return response()->json([
            'status' => 'ok',
			'getuser'=>$userdata->toArray()
        ], 201);
	}
	
	/* user update method
	* @param user id
	* 
	*/
	public function updateUser(Request $request, $id){
		 $input = $request->all();
		 $data = User::findOrFail($id);	
        if($data->update($input)) {
            return response()->json([
                'status' => 'ok'
            ], 201);
        }		
		 
	}
	
	/* user delete method
	* @param user id
	* 
	*/
	public function deleteUser($id){
		if($id){
			DB::table('users')->delete($id);
		}		
	}
	

	/* for dashboard Top report
	*
	*
	*/	
	public function getReport(){
		$totalUser = User::count();
		$toaluserMonth = User::where('created_at', '>=', Carbon::now()->startOfMonth())->count();
		$bannedUser = User::where('status','banned')->count();
		$unconfirmedUser = User::where('status','unconfirmed')->count();
		 return response()
			->json([
				'status' =>'ok',
				'totalUsers' =>$totalUser,
				'toalUsersByMonth' =>$toaluserMonth,
				'bannedUsers' =>$bannedUser,
				'unconfirmedUsers' =>$unconfirmedUser
			]);
	}
	
	/* For signup Method
	*
	*
	*/
	public function signUp(UserRequest $request){
		//print_r($request->all());
		$user = new User($request->all());
		
        if(!$user->save()) {
            throw new HttpException(500);
        }

        if(!Config::get('boilerplate.sign_up.release_token')) {
            return response()->json([
                'status' => 'ok'
            ], 201);
        }
	}
	
	private function getUserByMonth($month,$users){
		foreach($users as $key =>$user){
			if(isset($user['month']) && $user['month'] == $month){
				return $user['count'];	
			}
		}
	}
	
	/* for dashboard graph
	*
	*
	*/
	public function getGraph(){	
		$graphregister = User::select(
			[DB::raw("DATE_FORMAT(created_at, '%Y-%m') AS `date`,DATE_FORMAT(created_at, '%m') AS `month`"),
			DB::raw('COUNT(id) AS count'),])
		->groupBy('date')
		->orderBy('date', 'ASC')
		->get();
		 
		$months = array('Jan','Feb','Mar','Apr','May','Jun','July','Aug','Sep','Oct','Nov','Dec');	
		
		$graphregister = $graphregister->toArray();
		
		$graphString = array(array('Month','Registration'));
		foreach($months as $key=>$month){			
			$userByMonth = $this->getUserByMonth($key+1,$graphregister);
			if($userByMonth < 1) $userByMonth = 0;
			$graphString[] = array($month,$userByMonth);
		} 

		return response()->json([
				 'data'=>$graphString
			 ]);
	}
	
	/* leatest user functin
	*
	*
	*/
	public function getLatestUsers(){
	 $users = User::orderBy('id')->limit(5)->get();
	 return response()
		->json([
			'status' => 'ok',
			'users'=>$users->toArray()
		]);	
	}
	
	/* emailExists functin
	*
	*
	*/
	public function emailExists(Request $request){
		$userExists = User::where('email', $request->email)->count();	
		return response()
			->json([
				'status' => 'ok',
				'data'=>['emailExists'=>$userExists]
			]);
	
	}
	
	/* emailExistsEdit functin
	*
	*
	*/
	public function emailExistsEdit(Request $request, $id){
		$userExists = User::where('email', $request->email)->count();	
		 $validator = Validator::make(['email' => $request->email],
			['email' => "required|unique:users,email,{$id}"] );
		if ($validator->fails()){
			return response()
			->json([
				'status' => 'ok',
				'data'=>['emailExists'=>$userExists]
			]);
		}else{
			return response()
			->json([
				'status' => 'Failed'
			]);
		}
	}
	
	
}
