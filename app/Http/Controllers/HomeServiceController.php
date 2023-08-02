<?php

namespace App\Http\Controllers;

use App\Equipments;
use Illuminate\Http\Request;
use App\Http\Requests\HomeServiceRequest;
use App\Repositories\HomeServiceRepository;
use App\ServicesType;
use Illuminate\Support\Facades\Auth;

/*
 *  HomeServiceController controller exteds Controller and provides apis for Home Services.
*/
class HomeServiceController extends Controller
{
    protected $homeServiceRepo;

    function __construct(HomeServiceRepository $homeServiceRepository)
    {
        $this->homeServiceRepo = $homeServiceRepository;
    }

    /*
     * get home medical services
    */
    public function getHomeMedicalServices(HomeServiceRequest $request)
    {

        $input = $request->all();
        $validated = $request->validated();

        $getList = $this->homeServiceRepo->getHomeServicesTypesByCity($input);

        if ($getList) {

            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('homeServices.services_found');
            $param = 'Labs';
            $values = $getList;
        } else {

            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('homeServices.services_found');
            $param = 'Labs';
            $values = new \stdClass();
        }

        return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
    }
    
    /*
     * order services
    */
    public function orderService(HomeServiceRequest $request)
    {

        $input = $request->all();
        $validated = $request->validated();
        $auth = Auth::id();
        $order_id = orderNumber();
        $getList = $this->homeServiceRepo->orderService($input, $auth, $order_id);

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

    public function searchMedicalService(HomeServiceRequest $request)
    {

        $validated = $request->validated();
        $input = $request->all();
        $service_name = $input['service_name'];
        $city_id = $input['city_id'];
        // print_r($service_name);print_r($city_id);
        $result = $this->homeServiceRepo->searchServiceMethod($city_id, $service_name);

        if (count($result) > 0) {

            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('homeServices.search_result_found');
            $param = 'Service';
            $values = $result;
        } else {

            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('homeServices.search_result_found');
            $param = 'Service';
            $values = new \stdClass();
        }
        return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
    }

    /*
     * get home medical equipments
    */
    public function searchMedicalEquipment(HomeServiceRequest $request)
    {

        $validated = $request->validated();
        $input = $request->all();

        $result = $this->homeServiceRepo->searchEquipment($input);

        if (count($result) > 0) {

            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('homeServices.search_result_found');
            $param = 'Service';
            $values = $result;
        } else {

            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('homeServices.search_result_found');
            $param = 'Service';
            $values = new \stdClass();
        }
        return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
    }

    /*
     * get home medical types
    */
    public function getAllServiceTypes(HomeServiceRequest $request)
    {


        $validated = $request->validated();
        $servicesTypes = ServicesType::all();
        if ($servicesTypes) {

            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('homeServices.service_types_found');
            $param = 'ServiceType';
            $values = $servicesTypes;
        } else {

            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('homeServices.service_types_found');
            $param = 'ServiceType';
            $values = new \stdClass();
        }
        return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
    }
    
    /*
     * get all equipment
    */
    public function MedicalEquipment(HomeServiceRequest $request)
    {
        $offset = ($request['page'] * $request['limit']) - $request['limit'];
        $allEquipment = Equipments::where('is_rental', 0)->offset($offset)->limit($request['limit'])->orderByDesc('created_at')->get();
        if ($allEquipment) {
            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('homeServices.All_Equipment');
            $param = 'All_Equipment';
            $values = $allEquipment;
        } else {
            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('homeServices.All_Equipment');
            $param = 'All_Equipment';
            $values = new \stdClass();
        }
        return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
    }
}
