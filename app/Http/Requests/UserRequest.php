<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\User;

/*
 *  UserRequest exteds FormRequest and provides request for User.
*/
class UserRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules(Request $request)
	{

		$rules = [];
		if ($request->path() == 'api/create_downloads') 
		{
			$rules = [
				'device_id' => 'required|string|min:16|max:16',
			];
		}
		if ($request->path() == 'api/signup') 
		{
			$userExist = User::where('phone', $request['phone'])->where('email', $request['email'])->where('is_active', 1)->first();
			if (!$userExist)
			{
				$rules = [
					'full_name' => 'required',
					'gender' => 'required|alpha',
					// 'dob'=>'required|date_format:Y/m/d',
					'email' => 'required|email',
					'phone' => 'required|numeric|min:12',
					'password' => [
						'required',
						'string',
						'min:8',
						'max:12',             // must be at least 8 characters in length
					],
				];
			} 
			else 
			{
				$rules = [
					'full_name' => 'required',
					'gender' => 'required|alpha',
					// 'dob'=>'required|date_format:Y/m/d',
					'email' => 'required|email|unique:users',
					'phone' => 'required|numeric|unique:users|min:12',
					'password' => [
						'required',
						'string',
						'min:8',
						'max:12',             // must be at least 8 characters in length
					],
				];
			}
		}
		if ($request->path() == 'api/signin') 
		{
			$rules = [
				'phone' => 'required|min:10',
				'password' => 'required'
			];
		}
		if ($request->path() == 'api/resend_code') 
		{
			$rules = [
				'user_id' => 'required',
			];
		}
		if ($request->path() == 'api/user_activate') 
		{
			$rules = [
				'code' => 'required|min:6',
				'user_id' => 'required|numeric',
				'device_id' => 'required|string|max:16',
			];
		}
		if ($request->path() == 'api/change_password') 
		{
			$rules = [
				// 'phone' => 'required|min:12',
				'old_password' => 'required',
				'new_password' => 'required|min:8|different:old_password',
				'confirm_password' => 'required|same:new_password|min:8'
			];
		}
		if ($request->path() == 'api/social_signup') 
		{
			$rules = [
				'full_name' => 'required',
				// 'gender'=>'required|alpha',
				// 'phone'=>'required',
				'email' => 'required|email',
				'social_id' => 'required',
				'social_access_token' => 'required'
			];
		}

		if ($request->path() == 'api/forgot_password') 
		{
			$rules = [
				'phone' => 'required|min:12|numeric',
			];
		}
		if ($request->path() == 'api/reset_password') 
		{
			$rules = [
				'reset_code' => 'required',
				'user_id' => 'required',
				'new_password' => [
					'required',
					'string',
					'min:8'
				],
				// 'confirm_password' => 'required|same:new_password|min:8'    
			];
		}
		// TODO: to be removed
		// if($request->path()=='api/edit_profile'){
		// 	$rules = [
		// 		'email' => 'email|unique:users',
		// 		'phone' => 'unique:users|min:12',
		// 	];
		// }
		if ($request->path() == 'api/upload_profile_picture') 
		{
			$rules = [
				'image' => 'required',
			];
		}
		if ($request->path() == 'api/set_player_id') 
		{
			$rules = [
				'player_id' => 'required',
			];
		}
		if ($request->path() == 'api/socialPhoneVerification') 
		{
			$rules = [
				'phone' => 'required|min:12|numeric',
				'gender' => 'required',
				'id' => 'required'
			];
		}
		return $rules;
	}

	protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
	{

		$message = $validator->errors()->first();
		$rescode = \Config::get('constants.response.ResponseCode_precondition_required');
		$param = 'Data';
		$values = new \stdClass();
		$response = new JsonResponse([
			'ResponseHeader' => [
				'ResponseCode' => $rescode,
				'ResponseMessage' =>  $message
			],
		], \Config::get('constants.response.ResponseCode_precondition_required'));

		throw new \Illuminate\Validation\ValidationException($validator, $response);
	}
}
