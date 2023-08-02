<?php

namespace App\Http\Controllers;

use App\Repositories\ShippingDetailsRepository;
use App\Http\Requests\ShippingDetailsRequest;
use App\ShippingDetails;

/*
 * ShippingController controller exteds Controller and provides apis for Shipping.
*/
class ShippingController extends Controller
{
    protected $shippingDetailsRepo;

    public function __construct(ShippingDetailsRepository $shipRepo)
    {

        $this->shippingDetailsRepo =  $shipRepo;
    }

    /*
     * Add user shipping details
    */
    public function addOrUpdateShippingDetails(ShippingDetailsRequest $request)
    {
        $validated = $request->validated();
        $input = $request->all();
        $data = ['first_name' => $input['first_name'], 'last_name' => $input['last_name'], 'phone' => $input['phone'], 'email' => $input['email'], 'home_address' => $input['home_address'], 'province' => $input['province'], 'city' => $input['city']];
        $shippingDetails = $this->shippingDetailsRepo->addOrUpdateShippingDetailsMethod($data);
        if ($shippingDetails) {
            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('shipping_det.details_added');
            $param = 'ShippingDetails';
            $values = $shippingDetails;
        } else {
            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('shipping_det.details_not_added');
            $param = 'ShippingDetails';
            $values = new \stdClass();
        }
        return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
    }
    
    /*
     * get user shpiping details
    */
    public function getShippingDetails()
    {
        $allShippingDetails = $this->shippingDetailsRepo->getShippingDetailsMethod();
        if ($allShippingDetails) {
            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('shipping_det.details_found');
            $param = 'ShippingDetails';
            $values = $allShippingDetails;
        } else {
            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('shipping_det.details_not_found');
            $param = 'ShippingDetails';
            $values = new \stdClass();
        }
        return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
    }
}