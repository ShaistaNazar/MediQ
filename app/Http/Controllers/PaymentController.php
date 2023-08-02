<?php

namespace App\Http\Controllers;

use Session;
use App\User;
use App\Services;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Validator;
use URL;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Expr\Cast\Double;

/*
 * PaymentController controller exteds Controller and provides apis for Payments.
*/
class PaymentController extends Controller
{
	function __construct(Request $request)
	{
		foreach ($request->headers->all() as $name => $value) {
			if ($name == 'user-id') {
				$this->_headerUserId = $value[0];
			}
			if ($name == 'device-type') {
				$this->_deviceType = $value[0];
			}
		}
	}

	// TODO: Configurations to be externalized.
	public function easyPaisaPayment(Request $request)
	{
		$client = new Client();
		if ($request['transactionType'] == 'OTC') {
			$res = $client->request(
				'POST',
				'https://easypaystg.easypaisa.com.pk/easypay-service/rest/v4/initiate-otc-transaction',
				[
					'headers' => [
						'Credentials'    	 => 'SG9tZU1lZGljc1B2dC5MdGQ6ZWU2YmMwOGU1N2EwYTY0NTJmOGVjYTZiYjg2NzM3YTE=',
						'Accept'    	 	 => 'application/json',
						'Content-Type'   	 => 'application/json',
					],
					'verify' => false,
					'json' => $request->all()

				]
			);
		}
		if ($request['transactionType'] == 'MA') {
			$res = $client->request(
				'POST',
				'https://easypaystg.easypaisa.com.pk/easypay-service/rest/v4/initiate-ma-transaction',
				[
					'headers' => [
						'Credentials'    	 => 'SG9tZU1lZGljc1B2dC5MdGQ6ZWU2YmMwOGU1N2EwYTY0NTJmOGVjYTZiYjg2NzM3YTE=',
						'Accept'    	 	 => 'application/json',
						'Content-Type'   	 => 'application/json',
					],
					'verify' => false,
					'json' => $request->all()

				]
			);
		}

		$result = $res->getBody()->getContents();
		$result = json_decode($result);
		$data 	= new \stdClass();

		if ($result) {
			return responseMsg(\Config::get('constants.response.ResponseCode_success'), __($result->responseDesc), 'Payment', $result);
		}
	}

	// TODO: Configurations to be externalized.
	public function easyPaisaPaymentStatus(Request $request)
	{
		$client = new Client();
		$res = $client->request(
			'POST',
			'https://easypaystg.easypaisa.com.pk/easypay-service/rest/v4/inquire-transaction',
			[
				'headers' => [
					'Credentials'    	 => 'SG9tZU1lZGljc1B2dC5MdGQ6ZWU2YmMwOGU1N2EwYTY0NTJmOGVjYTZiYjg2NzM3YTE=',
					'Accept'    	 	 => 'application/json',
					'Content-Type'   	 => 'application/json',
				],
				'verify' => false,
				'json' => $request->all()

			]
		);

		return $result = $res->getBody()->getContents();
		$result = json_decode($result);
		$data 	= new \stdClass();
	}

	public function jazzResponse(Request $request)
	{
		$message = Session::get('message');
		return view('jazzSuccess')->with('msg', $message);
	}
	public function jazzResponseFail(Request $request)
	{
		$message = Session::get('message');
		return view('jazzFail')->with('msg', $message);
	}
	public function otc(Request $request)
	{
		$message = Session::get('message');
		return view('otc')->with('msg', $message);
	}

	// TODO: Configurations to be externalized.
	public function apiResponse(Request $request)
	{
		$HashKey = "xw8815183h";
		$ResponseCode = $_POST['pp_ResponseCode'];
		$ResponseMessage = $_POST['pp_ResponseMessage'];
		$Response = "";
		$comment = "";
		$ReceivedSecureHash = $_POST['pp_SecureHash'];
		$token = $_POST['ppmpf_2'];

		if ($_POST['ppmpf_2'] != '') {
			$client = new Client();
			$res = $client->request(
				'POST',
				'https://citrustelemedicine.com/JazzCash/ReturnJassCash',
				[
					'form_params' => [
						'verify' => false,
						'json' => $_POST

					]
				]
			);
			$valueGet = $res->getBody()->getContents();
			$valueGet = json_decode($valueGet);
			$url = '//citrustelemedicine.com/Jazzcash/PaymentSuccess?Key=' . $valueGet->value;
			return Redirect::to($url);
		}

		$sortedResponseArray = array();
		if (!empty($_POST)) {
			foreach ($_POST as $key => $val) {
				$comment .= $key . "[" . $val . "],<br/>";
				$sortedResponseArray[$key] = $val;
			}
		}
		ksort($sortedResponseArray);
		unset($sortedResponseArray['pp_SecureHash']);
		$Response = $HashKey;
		foreach ($sortedResponseArray as $key => $val) {
			if ($val != null and $val != "") {
				$Response .= '&' . $val;
			}
		}

		$GeneratedSecureHash = hash_hmac('sha256', $Response, $HashKey);
		if ($ResponseCode == '000' || $ResponseCode == '121' || $ResponseCode == '200') {
			return Redirect::route('paymentResponse')->withMessage($ResponseMessage);
		} else if ($ResponseCode == '210') {
			return Redirect::route('paymentResponseFail')->withMessage($ResponseMessage);
		} else if ($ResponseCode == '124') {
			return Redirect::route('otc')->withMessage($ResponseMessage);
		} else if ($ResponseCode == '999' || $ResponseCode == '134') {
			return Redirect::route('paymentResponseFail')->withMessage($ResponseMessage);
		} else {
			return Redirect::route('paymentResponseFail')->withMessage($ResponseMessage);
		}
		$txnrefno = htmlspecialchars($_POST['pp_TxnRefNo']);
		$reqAmount = htmlspecialchars($_POST['pp_Amount']);
		$reqDatetime = htmlspecialchars($_POST['pp_TxnDateTime']);
		$reqBillref = htmlspecialchars($_POST['pp_BillReference']);
		$reqMerchantID = htmlspecialchars($_POST['pp_MerchantID']);
	}

	public function easyPasia(Request $request)
	{
		return view('easyPaisa');
		$token = request()->segment(count(request()->segments()));
		$session = User::where('session_id', $token)->first();

		if ($session) {
			return view('easyPaisa');
		} else {
			abort(404);
		}
	}

	public function jazzCheckout(Request $request)
	{
		$token = request()->segment(count(request()->segments()));
		$session = User::where('session_id', $token)->first();

		if ($session) {
			Session::put('auth_user', $session->id);
			if ($request['method'] == 1)
				$method = 'MWALLET';
			if ($request['method'] == 2)
				$method = 'OTC';
			if ($request['method'] == 3)
				$method = 'MIGS';
			return view('Redirection')->with(['token' => $session['id'], 'order_id' => $request['order_id'], 'total_amount' => $request['total_amount'], 'method' => $method]);
		} else {
			abort(404);
		}
	}

	public function paymentAuth(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'method' => 'required',
			'order_id' => 'required',
			'total_amount' => 'required',
		]);

		if ($validator->fails()) {

			$errorList = $validator->errors();

			if ($errorList->first('method')) {
				$errorMessage = $errorList->first('method');
			}

			if ($errorList->first('order_id')) {
				$errorMessage = $errorList->first('order_id');
			}

			if ($errorList->first('total_amount')) {
				$errorMessage = $errorList->first('total_amount');
			}

			return responseMsg(\Config::get('constants.response.ResponseCode_fail'), __($errorMessage), 'Payment', new \stdClass());
		}

		if (Auth::check()) {

			$token = Auth::user()->session_id;
			if (empty($token)) {

				$ResponseCode = 0;
				$ResponseMessage = "Authentication failed";
				$param = 'paymenMethod';
				$values = new \stdClass();
			} else {
				$baseUrl = URL::to('/');
				$url = $baseUrl . '/api/jazz_checkout/' . $token . '?method=' . $request['method'] . '&order_id=' . $request['order_id'] . '&total_amount=' . $request['total_amount'];
				$ResponseCode = 200;
				$ResponseMessage = 'Authentication success';
				$param = 'paymenMethod';
				$values = $url;
			}
		} else {
			$ResponseCode = 0;
			$ResponseMessage = "Authentication faild";
			$param = 'paymenMethod';
			$values = new \stdClass();
		}
		return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
	}
}