<?php

namespace App\Http\Controllers\API\v1\companyUser;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\{CompanyUser, Companies, Move, CompanyAgent};
use App\Device_token;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends BaseController
{
	public function login(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'username' => 'required|min:2',
			'password' => 'required|min:8',
		]);

		if ($validator->fails()) {
			return $this->apiError($validator->errors()->first());
		}

		$user = User::with(['companyUser', 'company'])->whereUsername($request->username)->first();
		// $user = User::with(['companyUser' => function ($query1) {
		// 			$query1->with(['company' => function ($query2)
		// 					{
		// 							$query2->select('*');
		// 					}
		// 					])->select('*', 'company_id');
		// 	}])->whereUsername($request->username)->first();

		if ($user && !$user->hasRole('company-user')) {
			return $this->apiError("You dont have permission to login to this module.");
		}

		if ($user && $user->companyUser->is_login == 1) {
			return $this->apiError("This user is logged into another device.");
		} else {

			if (Auth::attempt(['username' => request('username'), 'password' => request('password')])) {
				$user = Auth::user();
				$token = $user->createToken('MyApp')->accessToken;
				$user['company_user'] = $user->companyUser;
				$user['company_user']['company'] = $user->companyUser->company;
				$user['token'] = $token;

				if (!Device_token::where('token_key', $request->device_token)->where('user_id', $user->id)->exists() && $request->device_token) {
					$userToken = new Device_token();
					$userToken->user_id = $user->id;
					$userToken->token_key = $request->device_token;
					$userToken->status = 1;
					$userToken->save();
				}
				$update_is_login = CompanyUser::where('user_id', $user->id)->update(['is_login' => 1]);
				// $companyId = $user->company_user->company_id;
				// $user['assign_move'] = Move::select('id as move_id','move_number','company_id as create_company_id','assign_destination_company_id','is_assign')->where('assign_destination_company_id','=',$companyId)->where('is_assign',1)->get();
				$this->user = $user;
				return $this->sendResponse($this->user, 'User successfully logged in..');
			} else {
				if ($user) {
					$crypted_password = bcrypt($request->password);
					if ($user->password != $crypted_password) {
						return $this->apiError("Your password is incorrect.");
					}
				}
				return $this->apiError("No user found for given credentials.");
			}
		}
	}

	public function test(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'username' => 'required|min:2',
			'password' => 'required|min:8',
		]);

		if ($validator->fails()) {
			return $this->apiError($validator->errors()->first());
		}

		return $this->apiError("Success!!!");

	}

	public function logout()
	{
		if (Auth::check()) {
			$user = Auth::user();
			CompanyUser::where('user_id', $user->id)
				->update(['is_login' => 0]);
			Auth::user()->AauthAcessToken()->delete();
			return $this->apiSuccess("Logout Successfully.");
		} else {
			return $this->apiError("Something went Wrong");
		}
	}

	// public function loginUser(Request $request)
	// {
	// 	$validator = Validator::make($request->all(), [
	// 		'user_id' => 'required',
	// 	]);

	// 	if($validator->fails()){
	//         return $this->apiError($validator->errors()->first());
	//     }

	// 	if(Auth::user()->AauthAcessToken()->exists())
	// 	{
	// 		$user = User::with('companyUser')->where('id',$request->user_id)->first();
	// 	}
	// }
}
