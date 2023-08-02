<?php

namespace App\Http\Controllers;

use App\DeviceId;
use Validator;
use App\User;
use Hash;
use Session;
use App\Http\Services\MessageService;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Auth;
use App\Http\Requests\UserRequest;
use App\OrderTemp;
use App\UserPrescriptions;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;


/*
 * UserController controller exteds Controller and provides apis for User Management.
*/
class UserController extends Controller
{
	protected $UserRepo;
	protected $msgService;

	function __construct(UserRepository $UserRepository, Request $request, MessageService $msgService)
	{

		$this->UserRepo = $UserRepository;
		$this->msgService = $msgService;
	}
    /**
 * insert Download
 */
public function createDownloads(UserRequest $request)
{
	 
	 $response = DeviceId::firstOrCreate([
		 'device_id'=>$request->device_id
	 ]);
	 
	 if($response){
		return responseMsg(\Config::get('constants.response.ResponseCode_success'), __('user.post_device_id'), 'User', new \stdClass());
	 }
	 return responseMsg(\Config::get('constants.response.ResponseCode_fail'), __('user.post_device_id_error'), 'User', new \stdClass());
}

	/*
     * User Register
	*/
	public function Signup(UserRequest $request)
	{


		$input = $request->all();
		$validated = $request->validated();
		$activation_code = mt_rand(100000, 999999);
		$password = Hash::make($input['password']);

		$input['password'] = $password;
		$input['activation_code'] = $activation_code;
		$userExist = User::where('phone', $input['phone'])->first();
		if ($userExist) {
			if ($userExist->is_active == 0) {
				$maskName = "Home Medics";
				$textMessage = "Welcome To mediQ.\rThe Activation Code is " . $activation_code;
				$sendMsg = $this->msgService->sendSmsMessage($textMessage, $input['phone'], $maskName);
				return responseMsg(\Config::get('constants.response.ResponseCode_success'), __('users.account_not_active'), 'User', $userExist);
			}
		} else {

			$recipients = $input['phone'];
			$maskName = "Home Medics";
			$textMessage = "Welcome To mediQ.\rThe Activation Code is " . $activation_code;
			$sendMsg = $this->msgService->sendSmsMessage($textMessage, $input['phone'], $maskName);
			$newUser = $this->UserRepo->create($input);
			$UserToken = $newUser->createToken($newUser->full_name)->accessToken;
			$newUser->token = $UserToken;
			if ($newUser) {
				$ResponseCode = \Config::get('constants.response.ResponseCode_created');
				$ResponseMessage = __('users.register_success');
				$param = 'User';
				$values = $newUser;
				return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
			}
		}
		$ResponseCode = \Config::get('constants.response.ResponseCode_fail');
		$ResponseMessage =  __('users.exist');
		$param = 'User';
		$values = new \stdClass();

		return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
	}

	/*
     * User login
    */
	public function userLogin(UserRequest $request)
	{
		$input = $request->all();
		$validated = $request->validated();
		$new_sessid = Session::getId();
		$credentials = $request->only('phone', 'password');
		$userDetails = $this->UserRepo->getUserByPhone($input['phone']);

		if ($userDetails && $userDetails->role_id == \Config::get('constants.roles.user')) {
			if (!Hash::check($input['password'], $userDetails->password)) {
				$ResponseCode = \Config::get('constants.response.ResponseCode_non_authoritative_information');
				$ResponseMessage = __('users.login_none');
				$param = 'User';
				$values = new \stdClass();
				return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
			}
			if ($userDetails->is_active == \Config::get('constants.user.user_not_active')) {
				$user_phone = $userDetails['phone'];
				$ResponseCode = \Config::get('constants.response.ResponseCode_fail');
				$ResponseMessage = __('users.account_not_found_phone');
				$param = 'User';
				$values = $userDetails;
				return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
			}
			if (Auth::attempt($credentials)) {
				$authUser = Auth()->user();
				$UserToken = $authUser->createToken($authUser->full_name)->accessToken;
				$authUser->token = $UserToken;
				$authUser->session_id = $new_sessid;
				$userDetails->session_id = $new_sessid;
				$userDetails->save();
				$ResponseCode = \Config::get('constants.response.ResponseCode_success');
				$ResponseMessage = __('users.login_success');
				$param = 'User';
				$values = $authUser;
				return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
			}
		} else {
			$ResponseCode = \Config::get('constants.response.ResponseCode_no_content');
			$ResponseMessage = __('users.nouserfound');
			$param = 'User';
			$values = new \stdClass();
			return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
		}
	}

	/*
     * resend activations code for activate the account 
    */
	public function resendActivationCode(UserRequest $request)
	{

		$input = $request->all();
		$validated = $request->validated();
		$new_sessid = Session::getId();
		$userDetails = User::find($input['user_id']);
		if ($userDetails) {
			$Code = mt_rand(100000, 999999);
			$userDetails->activation_code = $Code;
			$userDetails->session_id = $new_sessid;
			$userDetails->save();
			$userPhone = $userDetails->phone;
			$recipients = [$userPhone];
			$maskName = "Home Medics";
			$textMessage = "The Activation Code is " . $Code;
			$sendMsg = $this->msgService->sendSmsMessage($textMessage, $userPhone, $maskName);
			$message = __('users.account_verify_code', ['code' => $userDetails['activation_code']]);

			$ResponseCode = \Config::get('constants.response.ResponseCode_success');
			$ResponseMessage = __('users.account_activation_code', ['userphone' => $userPhone]);
			$param = 'User';
			$values = $userDetails;
		} else {
			$ResponseCode = \Config::get('constants.response.ResponseCode_no_content');
			$ResponseMessage = __('users.nouserfound');
			$param = 'User';
			$values = new \stdClass();
		}
		
		return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
	}

	public function socialPhoneVerification(UserRequest $request)
	{
		$input = $request->all();
		$validated = $request->validated();
		$new_sessid = Session::getId();
		$Code = mt_rand(100000, 999999);
		$userDetails = $this->UserRepo->getUserByPhone($input['phone']);
		$userNew = $this->UserRepo->getUser($input['id']);
		if ($userDetails && $userDetails->is_active == 1) {
			return responseMsg(\Config::get('constants.response.ResponseCode_fail'), __('users.phone_exist'), 'User', new \stdClass());
		} elseif ($userDetails && $userDetails->is_active == 0) {
			$userNew->activation_code = $Code;
			$userNew->session_id = $new_sessid;
			$userNew->phone = $input['phone'];
			$userNew->gender = $input['gender'];
			$userNew->save();
			$maskName = "Home Medics";
			$textMessage = "Welcome To mediQ.\rThe Activation Code is " . $Code;
			$sendMsg = $this->msgService->sendSmsMessage($textMessage, $input['phone'], $maskName);
			$message = __('users.account_verify_code', ['code' => $userNew['activation_code']]);
			return responseMsg(\Config::get('constants.response.ResponseCode_success'),  __('users.account_activation_code', ['userphone' => $input['phone']]), 'User', $userNew);
		}
		$activationCode = new \stdClass();
		$activationCode->activation_code = $Code;
		$activationCode->id = $input['id'];
		$maskName = "Home Medics";
		$textMessage = "Welcome To mediQ.\rThe Activation Code is " . $Code;
		$sendMsg = $this->msgService->sendSmsMessage($textMessage, $input['phone'], $maskName);
		User::where('id', $input['id'])->update(['phone' => $input['phone'], 'gender' => $input['gender'], 'activation_code' => $Code]);
		return responseMsg(\Config::get('constants.response.ResponseCode_success'),  __('users.account_activation_code', ['userphone' => $input['phone']]), 'User', $activationCode);
	}

	// user activate api
	public function activateUserAccount(UserRequest $request)
	{
		$input = $request->all();
		$validated = $request->validated();
		$new_sessid = Session::getId();
		$user_device  = DeviceId::where('device_id',$input['device_id'])->first();
		$userDetails = $this->UserRepo->getUser($input['user_id']);
		if ($userDetails) {
			if ($userDetails->is_active == \Config::get('constants.user.user_not_active')) {
				if ($input['code'] == $userDetails->activation_code) {
					$userDetails->is_active = \Config::get('constants.user.user_Active');
					$userDetails->session_id = $new_sessid;
					$userDetails->save();
					$user_device->user_id  = $userDetails->id;
			        $user_device->save();
					Auth::attempt(['phone' => $userDetails->phone, 'password' => $userDetails->password]);
					$userDetails->token = $userDetails->createToken($userDetails->full_name)->accessToken;
					$ResponseCode = \Config::get('constants.response.ResponseCode_success');
					$ResponseMessage = __('users.account_activated');
					$param = 'User';
					$values = $userDetails;
				} else {
					$ResponseCode = \Config::get('constants.response.ResponseCode_fail');
					$ResponseMessage = __('users.enter_valid_code');
					$param = 'User';
					$values = new \stdClass();
				}
			} else {
				$ResponseCode = \Config::get('constants.response.ResponseCode_fail');
				$ResponseMessage = __('users.already_active');
				$param = 'User';
				$values = new \stdClass();
			}
		} else {
			$ResponseCode = \Config::get('constants.response.ResponseCode_no_content');
			$ResponseMessage = __('users.nouserfound');
			$param = 'User';
			$values = new \stdClass();
		}
		return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
	}

	// user logOut
	public function userLogout(Request $request)
	{
		if (Auth::check()) {
			$authUser = Auth::user();
			$authUser->player_id = '';
			$authUser->save();
			$user = Auth::user()->token();
			$user->revoke();
			$ResponseCode = \Config::get('constants.response.ResponseCode_success');
			$ResponseMessage = __('users.logout_success');
			$param = 'User';
			$values = $user;
		} else {
			$ResponseCode = \Config::get('constants.response.ResponseCode_not_authenticated');
			$ResponseMessage = __('users.logout_none');
			$param = 'User';
			$values = new \stdClass();
		}

		return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
	}

	/*
	* Change password API
	*/
	public function changePassword(UserRequest $request)
	{
		$input = $request->all();
		$validated = $request->validated();
		$userDetails = Auth::user();
		if ($userDetails) {
			if (Hash::check($input['old_password'], $userDetails->password)) {
				$password = Hash::make($input['new_password']);
				$userDetails->password = $password;
				$userDetails->save();
				$ResponseCode = \Config::get('constants.response.ResponseCode_success');
				$ResponseMessage = __('users.password_changed');
				$param = 'User';
				$values = $userDetails;
			} else {
				$ResponseCode = \Config::get('constants.response.ResponseCode_fail');
				$ResponseMessage = __('users.Not_valid_old_password');
				$param = 'User';
				$values = $userDetails;
			}
		} else {
			$ResponseCode = \Config::get('constants.response.ResponseCode_no_content');
			$ResponseMessage = __('users.nouserfound');
			$param = 'User';
			$values = new \stdClass();
		}
		return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
	}

	/*
     * Register user via social
    */
	public function signupUserBySocial(UserRequest $request)
	{
		$input = $request->all();
		$validated = $request->validated();
		$userExist = User::where('email', $input['email'])->first();
		if ($userExist) {
			$userExist->token = $userExist->createToken($userExist->full_name)->accessToken;
			$ResponseCode = \Config::get('constants.response.ResponseCode_success');
			$ResponseMessage = __('users.login_success');
			$param = 'User';
			$values = $userExist;
		} else {
			$userData = $this->UserRepo->getUserByEmail($input['email']);
			$activation_code = mt_rand(100000, 999999);
			$input['activation_code'] = $activation_code;
			$input['login_type'] = 'social';
			$newUser = $this->UserRepo->create($input);
			$userExist = User::where('email', $input['email'])->first();
			$UserToken = $userExist->createToken($newUser->full_name)->accessToken;
			$userExist->token = $UserToken;

			$ResponseCode = \Config::get('constants.response.ResponseCode_created');
			$ResponseMessage = __('users.register_success');
			$param = 'User';
			$values = $userExist;
		}

		return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
	}

	/*
	 * forgot password API
	*/
	public function forgotPassword(UserRequest $request)
	{

		$input = $request->all();
		$validated = $request->validated();
		$userDetails = $this->UserRepo->getUserByPhone($input['phone']);
		if ($userDetails) {
			$resetCode = mt_rand(100000, 999999);
			$input['activation_code'] = $resetCode;
			$recipients = [$input['phone']];
			$maskName = "Home Medics";
			$textMessage = "Your recovery code is " . $resetCode;
			$sendMsg = $this->msgService->sendSmsMessage($textMessage, $input['phone'], $maskName);
			$userDetails->activation_code = $resetCode;
			$userDetails->save();

			$ResponseCode = \Config::get('constants.response.ResponseCode_success');
			$ResponseMessage = __('users.account_recovery_code', ['phone' => $input['phone']]);
			$param = 'Data';
			$values = $userDetails;
		} else {
			$ResponseCode = \Config::get('constants.response.ResponseCode_no_content');
			$ResponseMessage = __('users.account_not_found_phone');
			$param = 'User';
			$values = new \stdClass();
		}
		return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
	}

	//reset password
	public function resetPassword(UserRequest $request)
	{
		$input = $request->all();
		$validated = $request->validated();
		$userDetails = $this->UserRepo->getUser($input['user_id']);
		if ($userDetails) {
			if ($userDetails->activation_code == $input['reset_code']) {
				$userDetails->password = Hash::make($input['new_password']);
				$userDetails->save();
				$userDetails->token = $userDetails->createToken($userDetails->full_name)->accessToken;
				$ResponseCode = \Config::get('constants.response.ResponseCode_success');
				$ResponseMessage = __('passwords.reset');
				$param = 'Data';
				$values = $userDetails;
			} else {
				$ResponseCode = \Config::get('constants.response.ResponseCode_fail');
				$ResponseMessage = __('users.tokenExpired');
				$param = 'User';
				$values = new \stdClass();
			}
		} else {
			$ResponseCode = \Config::get('constants.response.ResponseCode_no_content');
			$ResponseMessage = __('users.account_not_found');
			$param = 'User';
			$values = new \stdClass();
		}

		return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
	}

	// get user profile
	public function getUserProfile(Request $request)
	{
		$user_id = Auth::id();
		$userData = $this->UserRepo->userProfile($user_id);
		$ResponseCode = \Config::get('constants.response.ResponseCode_success');
		$ResponseMessage = __('users.get_user_profile');
		$param = 'User';
		$values = $userData;

		return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
	}

	// Update profile
	public function updateUserProfile(UserRequest $request)
	{
		$current_user = Auth::user();
		$input = $request->all();
		$user = User::find($current_user->id);

		if ($user) {
			$user->fill($request->all())->save();
			$ResponseCode = \Config::get('constants.response.ResponseCode_success');
			$ResponseMessage = __('users.profile_updated');
			$param = 'User';
			$values = $user;
		} else {
			$ResponseCode = \Config::get('constants.response.ResponseCode_no_content');
			$ResponseMessage = __('users.profile_updated');
			$param = 'User';
			$values = new \stdClass();
		}

		return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
	}

	/*
	 * Upload user profile picture
	*/
	public function uploadProfilePicture(UserRequest $request)
	{

		$input = $request->all();
		$user = Auth::user();
		$validated = $request->validated();
		$file = $request->file('image');
		$extension = $request->file('image')->extension();
		$fileName = time() . "-" . '10' . "." . $extension;
		$imageSaved = Storage::disk('users')->put($fileName, File::get($file));

		if ($imageSaved) {
			$imagePathAndName = $fileName;
			$userDetails = $this->UserRepo->getUser($user->id);
			$deleteImage = $userDetails['avatar'];
			$userDetails->avatar = 'users/' . $imagePathAndName;
			$userDetails->save();

			if ($deleteImage != "") {
				Storage::disk('users')->delete($deleteImage); //Delete prvious image from storage
			}
			$userDetails = $this->UserRepo->getUser($user->id);
			$ResponseCode = \Config::get('constants.response.ResponseCode_success');
			$ResponseMessage = __('users.profile_img_uploaded');
			$param = 'User';
			$values = $userDetails;
		} else {
			$ResponseCode = \Config::get('constants.response.ResponseCode_fail');
			$ResponseMessage = __('users.profile_img_error');
			$param = 'User';
			$values = new \stdClass();
		}
		return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
	}

	/*
	 * get user profile picture
	*/
	public function viewProfilePicture(UserRequest $request)
	{

		$user = Auth::user();
		$path = storage_path('app/public/users/');
		$file = $path . $user->avatar;
		$ResponseCode = \Config::get('constants.response.ResponseCode_success');
		$ResponseMessage = __('users.profile_img');
		$param = 'User';
		$values = (object) $file;

		return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
	}

	// add player id
	public function setUserPlyerID(UserRequest $request)
	{
		$validated = $request->validated();
		$current_user = Auth::id();
		$input = $request->all();
		$updatedPlayerId = $this->UserRepo->updatePlayerId($current_user, $input['player_id']);

		if ($updatedPlayerId) {
			$ResponseCode = \Config::get('constants.response.ResponseCode_success');
			$ResponseMessage = __('users.plyer_id_update');
			$param = 'User';
			$values = $updatedPlayerId;
		} else {

			$ResponseCode = \Config::get('constants.response.ResponseCode_fail');
			$ResponseMessage = __('users.update_fail');
			$param = 'User';
			$values = new \stdClass();
		}

		return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
	}

	public function bannerImages(UserRequest $request)
	{
		$input = $request->all();
		$updatedPlayerId = $this->UserRepo->bannerMedia();
		if ($updatedPlayerId) {
			$ResponseCode = \Config::get('constants.response.ResponseCode_success');
			$ResponseMessage = __('users.banner_images');
			$param = 'User';
			$values = $updatedPlayerId;
		} else {
			$ResponseCode = \Config::get('constants.response.ResponseCode_fail');
			$ResponseMessage = __('users.banner_not_found');
			$param = 'User';
			$values = new \stdClass();
		}

		return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
	}
	
	public function updatePlayerId(UserRequest $request)
	{
		$validated = $request->validated();
		$input = $request->all();
		$player_id = $input['player_id'];
		$user = User::find(Auth::id());
		$updated_user = User::where('id', Auth::id())->update(['player_id' => $player_id]);
		if (!empty($updated_user)) {
			return responseMsg(\Config::get('constants.response.ResponseCode_success'), __('user.player_id_updated'), 'User', new \stdClass());
		} else {
			return responseMsg(\Config::get('constants.response.ResponseCode_fail'), __('user.player_id_updated_fail'), 'User', new \stdClass());
		}
	}
}