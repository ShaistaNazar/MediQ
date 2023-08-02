<?php

namespace App\Http\Controllers;

use App\CityRental;
use App\Equipments;
use Illuminate\Http\Request;
use App\Repositories\HomeServiceRepository;
use App\Http\Requests\HomeServiceRequest;
use App\RentalEquipment;
use Illuminate\Support\Facades\Auth;

/*
 * RentalController controller exteds Controller and provides apis for Rentals.
*/
class RentalController extends Controller
{
    protected $homeServiceRepo;

    function __construct(HomeServiceRepository $homeServiceRepository)
    {

        $this->homeServiceRepo = $homeServiceRepository;
    }

    public function orderRent(HomeServiceRequest $request)
    {

        $input = $request->all();
        $validated = $request->validated();
        $auth = Auth::id();
        $order_id = orderNumber();
        $getList = $this->homeServiceRepo->orderRent($input, $auth, $order_id);

        if ($getList) {

            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('homeServices.request_submitted');
            $param = 'Labs';
            $values = $getList;
        } else {

            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('homeServices.services_not_found');
            $param = 'Labs';
            $values = new \stdClass();
        }

        return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
    }

    public function rentalHistory(HomeServiceRequest $request)
    {
        $offset = ($request['page'] * $request['limit']) - $request['limit'];
        $serviceOrder = RentalEquipment::where('user_id', Auth::id())->where('price', '!=', null)
        ->where('status', '!=', 1)->where('status', '!=', 1)->where('payment_method_id', '!=', null)->with('Equipments')->orderByDesc('created_at')->offset($offset)->limit($request['limit'])->get();
        foreach ($serviceOrder as $key => $val) {
            $val['equipment_name'] = $val['equipments']['equipment_name'];
        }
        if ($serviceOrder)
            return responseMsg(\Config::get('constants.response.ResponseCode_success'),  __('orders.order_list'), 'orders', $serviceOrder);
        return responseMsg(\Config::get('constants.response.ResponseCode_fail'), __('orders.order_list'), 'orders', []);
    }
    
    public function getAllRental(HomeServiceRequest $request)
    {
        $serviceOrder = CityRental::where('city_id', $request['city_id'])->with('equipment')->whereHas('equipment')->orderByDesc('created_at')->get();
        if ($serviceOrder) {
            foreach ($serviceOrder as $key => $val) {
                $val['equipment_name'] = $val['equipment']['equipment_name'];
                $val['brand'] = $val['equipment']['brand'];
                $val['image'] = $val['equipment']['image'];
                $val['price'] = $val['equipment']['price'];
                $val['in_demand'] = $val['equipment']['in_demand'];
                $val['is_rental'] = $val['equipment']['is_rental'];
                $val['description'] = $val['equipment']['description'];
                unset($val['equipment']);
            }
            return responseMsg(\Config::get('constants.response.ResponseCode_success'),  __('orders.order_list'), 'orders', $serviceOrder);
        }
        return responseMsg(\Config::get('constants.response.ResponseCode_fail'), __('orders.order_list'), 'orders', []);
    }
}