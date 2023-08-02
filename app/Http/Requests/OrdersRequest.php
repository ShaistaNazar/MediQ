<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/*
 *  OrdersRequest exteds FormRequest and provides request for Orders.
*/
class OrdersRequest extends FormRequest
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
        
        if($request->path()=='api/new_order'){
            $rules = [
             'order' => 'required|array',            
            //  'shipping'=>'required',
            //  'billing'=>'required',
             'payment_method_id'=>'required',
             'total_amount'=>'required'
             ];
         }
         if($request->path()=='api/re_order'){
            $rules = [
             'order_number' => 'required',
             'user_id' => 'required',
             'product_type' => 'required',
             'product_id' => 'required' 
             ];
         }
         if($request->path()=='api/serviceOrderHistory'){
            $rules = [
             'page' => 'required',
             'limit' => 'required', 
             ];
         }
         if($request->path()=='api/order_history'){
            $rules = [
                'page' => 'required',
                'limit' => 'required'
             ];
         }
         if($request->path()=='api/get_notifications'){
            $rules = [
                'page' => 'required',
                'limit' => 'required'
             ];
         }
        if($request->path()=='api/read_notifications'){
            $rules = [
                'notification_id' => 'required',
                
             ];
        }
        if($request->path()=='api/check_promotion'){
            $rules = [
             'coupon_code' => 'required',
             ];
         }
         if($request->path()=='api/in_demand_equipment'){
            $rules = [
                'page' => 'required',
                'limit' => 'required'
             ];
         }

        return $rules;
    }
    
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
       
        $message=$validator->errors()->first();
        $rescode=\Config::get('constants.response.ResponseCode_precondition_required');
        $param='Data';
        $values= new \stdClass();
        $response = new JsonResponse([
            'ResponseHeader' => [
              'ResponseCode' => $rescode,
              'ResponseMessage' =>  $message
              ],
            ], \Config::get('constants.response.ResponseCode_precondition_required'));

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
