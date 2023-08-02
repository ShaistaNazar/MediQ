<?php

namespace App\Http\Controllers;

use App\Repositories\BillingDetailsRepository;
use App\Http\Requests\BillingDetailsRequest;

/*
 * BillingDetailsController controller exteds Controller and provides apis for Billing.
*/ 
class BillingDetailsController extends Controller
{
    protected $billingRepo;

    public function __construct(BillingDetailsRepository $billingRepoCons)
    {

        $this->billingRepo = $billingRepoCons;
    }

    /*
     * add user billing detail
    */
    public function addOrUpdateBillingDetails(BillingDetailsRequest $request)
    {

        $validated = $request->validated();
        $input = $request->all();
        $data = ['first_name' => $input['first_name'], 'last_name' => $input['last_name'], 'phone' => $input['phone'], 'home_address' => $input['home_address'], 'province' => $input['province'], 'city' => $input['city']];
        $billingDetails = $this->billingRepo->addOrUpdateBillingDetailsMethod($data);


        if ($billingDetails) {

            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('billing_det.details_added');
            $param = 'BillingDetails';
            $values = $billingDetails;
        } else {

            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('billing_det.details_not_added');
            $param = 'BillingDetails';
            $values = new \stdClass();
        }
        return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
    }

    /*
     * get user billing details
    */
    public function getBillingDetails(BillingDetailsRequest $request)
    {

        $validated = $request->validated();
        $input = $request->all();
        $billingDetails = $this->billingRepo->getBillingDetailsMethod();

        if ($billingDetails) {

            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('billing_det.details_found');
            $param = 'BillingDetails';
            $values = $billingDetails;
        } else {

            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('billing_det.details_not_found');
            $param = 'BillingDetails';
            $values = new \stdClass();
        }
        return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
    }
}
