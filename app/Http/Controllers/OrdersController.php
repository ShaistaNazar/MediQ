<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\OrdersRepository;
use App\Repositories\OrdersTempRepository;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\OrdersRequest;
use App\Http\Services\NotificationService;
use App\Orders;
use Carbon\Carbon;
use App\OrderTemp;
use App\Promotion;
use App\RentalEquipment;
use App\ServiceOrder;
use App\UserNotification;
use App\User;
use Auth;

/*
 * OrdersController controller exteds Controller and provides apis for Orders.
*/

class OrdersController extends Controller
{
	protected $ordersRepo;
	protected $ordersTempRepo;

	function __construct(OrdersRepository $OrdersRepository, OrdersTempRepository $ordersTempRepository, Request $request)
	{

		$this->ordersRepo = $OrdersRepository;
		$this->ordersTempRepo = $ordersTempRepository;
	}

	public function serviceOrder(OrdersRequest $request)
	{
		$offset = ($request['page'] * $request['limit']) - $request['limit'];
		$serviceOrder = ServiceOrder::where('order_from_user', Auth::id())->where('provider_id', '!=', 0)
		->where('payment_method_id', '!=', 0)->where('status', '!=', 1)->where('status', '!=', 1)->with('service', 'serviceProviders')
			->orderByDesc('created_at')->offset($offset)->limit($request['limit'])->get();
		if ($serviceOrder)
			return responseMsg(\Config::get('constants.response.ResponseCode_success'),  __('orders.order_list'), 'orders', $serviceOrder);
		return responseMsg(\Config::get('constants.response.ResponseCode_fail'), __('orders.order_list'), 'orders', []);
	}

	/*
     * Change order status & send noty
	*/
	public function confirmOrder(OrdersRequest $request)
	{
		if ($request['status']) {
			$auth = User::where('id', Auth::id())->first();
			if ($request['rental'] == 1) {
				$confirmStatusGet = RentalEquipment::where('order_number', $request['order_number'])->first();
				$confirmStatus = RentalEquipment::where('order_number', $request['order_number'])->Update(['status' => $request['status'], 'payment_method_id' => $request['payment_method_id']]);
				$confirmStatusGet1 = RentalEquipment::where('order_number', $request['order_number'])->first();
				$confirmPaidRent = UserNotification::where('note_heading', $request['order_number'])->Update(['is_paid' => 1, 'total_amount' => $confirmStatusGet1['price']]);
				$confirm = RentalEquipment::where('order_number', $request['order_number'])->first();
			} else if ($request['service'] == 1) {
				$confirm = ServiceOrder::where('order_number', $request['order_number'])->Update(['status' => $request['status'], 'payment_method_id' => $request['payment_method_id']]);
				$confirmPaid = UserNotification::where('note_heading', $request['order_number'])->Update(['is_paid' => 1]);
				$confirm = ServiceOrder::where('order_number', $request['order_number'])->first();
			} else {
				$confirm = OrderTemp::where('order_number', $request['order_number'])->Update(['status' => $request['status']]);
				$confirm = OrderTemp::where('order_number', $request['order_number'])->first();
				$confirmPaid = UserNotification::where('note_heading', $request['order_number'])->Update(['is_paid' => 1]);
			}
			if($request['status'] == 2 || $request['status'] == 4 || $request['payment_method_id'] == 4)
			{
				$checkNotyResponce = NotificationService::notify($auth, $confirm, $type = 'Your order has been placed successfully.');
			}
			if ($request['rental'] == 1) {
				$confirmPaidRent = UserNotification::where('note_heading', $request['order_number'])->first();
				$confirmPaidRent = UserNotification::where('note_heading', $request['order_number'])->Update([
					'is_paid' => 1,
					'total_amount' => $confirmPaidRent->total_amount
				]);
				return responseMsg(\Config::get('constants.response.ResponseCode_success'), 'successfully Notified', 'Confirmation', $confirm);
			}
			if ($request['service'] == 1) {
				$confirmPaidRent = UserNotification::where('note_heading', $request['order_number'])->Update(['is_paid' => 1, 'note_heading' => $confirm['order_number'], 'total_amount' => $confirm['total_amount']]);
			} else {
				$confirmPaid = UserNotification::where('note_heading', $request['order_number'])->Update(['is_paid' => 1, 'note_heading' => $confirm['order_number'], 'total_amount' => $confirm['total_amount']]);
			}
			return responseMsg(\Config::get('constants.response.ResponseCode_success'), 'successfully Notified', 'Confirmation', $confirm);
		}
		return responseMsg(\Config::get('constants.response.ResponseCode_fail'), 'Failed to notify', 'Failed to notify', []);
	}

	/*
     * Place user new order
    */
	public function newOrder(OrdersRequest $request)
	{
		$input = $request->all();
		$validated = $request->validated();
		$orderNumber = orderNumber();
		$AuthUser = Auth::user();

		$status = \Config::get('constants.orders.pending_status');
		$data = [
			'user_id' => Auth::id(), 'order_number' => $orderNumber, 'order' => $input['order'], 'total_amount' => $input['total_amount'],
			'payment_method_id' => $input['payment_method_id'], 'billing' => $input['billing'], 'shipping' => $input['shipping']
		];
		$newOrder = $this->ordersTempRepo->createNewOrder($data);
		if ($newOrder) {
			$order = $this->ordersTempRepo->getOrderByNumber($orderNumber);
			$ResponseCode = \Config::get('constants.response.ResponseCode_created');
			$ResponseMessage = __('orders.new_order_success');
			$param = 'User';
			$values = $newOrder;
		} else {

			$ResponseCode = \Config::get('constants.response.ResponseCode_fail');
			$ResponseMessage =  __('orders.new_order_fail');
			$param = 'User';
			$values = new \stdClass();
		}
		return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
	}

	/*
     * get user's Equipment order history
    */
	public function getEquOrdersByUserId(OrdersRequest $request)
	{
		$input = $request->all();
		$validated = $request->validated();
		$AuthUserId = Auth::id();
		$page = $input['page'];
		$limit = $input['limit'];
		$ordersHistory = $this->ordersTempRepo->getEquOrdersByUserId($AuthUserId, $page, $limit);

		if ($ordersHistory) {

			$ResponseCode = \Config::get('constants.response.ResponseCode_success');
			$ResponseMessage = __('orders.order_list');
			$param = 'User';
			$values = $ordersHistory;
		} else {
			$ResponseCode = \Config::get('constants.response.ResponseCode_fail');
			$ResponseMessage =  __('orders.order_list');
			$param = 'User';
			$values = new \stdClass();
		}
		return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
	}

	/*
     * get user's Test order history
    */
	public function getTestOrdersByUserId(OrdersRequest $request)
	{

		$input = $request->all();
		$validated = $request->validated();
		$AuthUserId = Auth::id();
		$page = $input['page'];
		$limit = $input['limit'];
		$ordersHistory = $this->ordersTempRepo->getTestOrdersByUserId($AuthUserId, $page, $limit);

		if ($ordersHistory) {

			$ResponseCode = \Config::get('constants.response.ResponseCode_success');
			$ResponseMessage = __('orders.order_list');
			$param = 'User';
			$values = $ordersHistory;
		} else {
			$ResponseCode = \Config::get('constants.response.ResponseCode_fail');
			$ResponseMessage =  __('orders.order_list');
			$param = 'User';
			$values = new \stdClass();
		}
		return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
	}

	/*
     * get user's Medicine order history
    */
	public function getMedOrdersByUserId(OrdersRequest $request)
	{

		$input = $request->all();
		$validated = $request->validated();
		$AuthUserId = Auth::id();
		$page = $input['page'];
		$limit = $input['limit'];
		$ordersHistory = $this->ordersTempRepo->getMedOrdersByUserId($AuthUserId, $page, $limit);

		if ($ordersHistory) {

			$ResponseCode = \Config::get('constants.response.ResponseCode_success');
			$ResponseMessage = __('orders.order_list');
			$param = 'User';
			$values = $ordersHistory;
		} else {
			$ResponseCode = \Config::get('constants.response.ResponseCode_fail');
			$ResponseMessage =  __('orders.order_list');
			$param = 'User';
			$values = new \stdClass();
		}
		return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
	}

	/*
     * make user reorder
    */
	public function reOrder(OrdersRequest $request)
	{

		$input = $request->all();
		$validated = $request->validated();
		$newOrderNumber = orderNumber();

		$newOrder = $this->ordersTempRepo->UserReorder($input, $newOrderNumber);

		if ($newOrder) {

			$order = $this->ordersTempRepo->getOrderByNumber($newOrderNumber);

			$ResponseCode = \Config::get('constants.response.ResponseCode_success');
			$ResponseMessage = __('orders.new_order_success');
			$param = 'User';
			$values = $order;
		} else {

			$ResponseCode = \Config::get('constants.response.ResponseCode_fail');
			$ResponseMessage =  __('orders.new_order_fail');
			$param = 'User';
			$values = new \stdClass();
		}
		return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
	}

	public function inDemandProducts(OrdersRequest $request)
	{

		$inDemand = $this->ordersTempRepo->InDemand();

		if ($inDemand) {

			$ResponseCode = \Config::get('constants.response.ResponseCode_success');
			$ResponseMessage = __('orders.in_demand');
			$param = 'User';
			$values = $inDemand;
		} else {

			$ResponseCode = \Config::get('constants.response.ResponseCode_fail');
			$ResponseMessage =  __('orders.no_in_demand');
			$param = 'User';
			$values = new \stdClass();
		}
		return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
	}

	public function inDemandTestCat(OrdersRequest $request)
	{

		$inDemand = $this->ordersTempRepo->inDemandTestCat();

		if ($inDemand) {
			$ResponseCode = \Config::get('constants.response.ResponseCode_success');
			$ResponseMessage = __('orders.in_demand');
			$param = 'User';
			$values = $inDemand;
		} else {

			$ResponseCode = \Config::get('constants.response.ResponseCode_fail');
			$ResponseMessage =  __('orders.no_in_demand');
			$param = 'User';
			$values = new \stdClass();
		}
		return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
	}

	public function inDemandMedCat(OrdersRequest $request)
	{

		$inDemand = $this->ordersTempRepo->inDemandMedCat();

		if ($inDemand) {

			$ResponseCode = \Config::get('constants.response.ResponseCode_success');
			$ResponseMessage = __('orders.in_demand');
			$param = 'User';
			$values = $inDemand;
		} else {

			$ResponseCode = \Config::get('constants.response.ResponseCode_fail');
			$ResponseMessage =  __('orders.no_in_demand');
			$param = 'User';
			$values = new \stdClass();
		}
		return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
	}
	
	public function inDemandEquipment(OrdersRequest $request)
	{
		$input = $request->all();

		$inDemand = $this->ordersTempRepo->inDemandEquipment($input);

		if ($inDemand) {

			$ResponseCode = \Config::get('constants.response.ResponseCode_success');
			$ResponseMessage = __('orders.in_demand');
			$param = 'User';
			$values = $inDemand;
		} else {

			$ResponseCode = \Config::get('constants.response.ResponseCode_fail');
			$ResponseMessage =  __('orders.no_in_demand');
			$param = 'User';
			$values = new \stdClass();
		}
		return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
	}

	public function makePromotion(Request $request)
	{
		$order = $this->ordersRepo->makePromotion($request);
		if ($order) {
			return responseMsg(\Config::get('constants.response.ResponseCode_success'), __('orders.promotion_created'), 'promotion', $order);
		} else {
			return responseMsg(\Config::get('constants.response.ResponseCode_fail'), __('orders.no_promotion_created'), 'promotion', new \stdClass);
		}
	}

	public function checkPromotion(OrdersRequest $request)
	{
		$validated = $request->validated();
		$checkPromotion = Promotion::where('user_id', Auth::id())->where('code', $request['coupon_code'])->first();
		if ($checkPromotion) {
			$checkPromotion->is_applied = 1;
			$checkPromotion->save();
			$discounted_price = new \stdClass;
			$discounted_price->percentage = (int) $checkPromotion['percentage'];
			$message = __('orders.discount_done', ['percent' => $checkPromotion['percentage'] . '%']);
			return responseMsg(\Config::get('constants.response.ResponseCode_success'), $message, 'discount', $discounted_price);
		} else {
			return responseMsg(\Config::get('constants.response.ResponseCode_fail'), __('orders.discount_fail'), 'discount', new \stdClass);
		}
	}

	public function confirmPromotion(OrdersRequest $request)
	{
		$validated = $request->validated();
		$checkPromotion = Promotion::where('user_id', Auth::id())->where('code', $request['coupon_code'])->where('is_applied', 0)->first();
		$discounted_price = new \stdClass;
		if ($checkPromotion) {
			$discounted_price->percentage = (int) $checkPromotion['percentage'];
			$discounted_price->is_promo_valid = 1;
			$message = __('Promotion code is still applicable');
			return responseMsg(\Config::get('constants.response.ResponseCode_success'), $message, 'discount', $discounted_price);
		} else {
			$discounted_price->is_promo_valid = 0;
			return responseMsg(\Config::get('constants.response.ResponseCode_fail'), 'check availability', 'discount', $discounted_price);
		}
	}
}