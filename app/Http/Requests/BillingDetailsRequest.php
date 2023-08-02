<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/*
 *  BillingDetailsRequest exteds FormRequest and provides request for Billing.
*/
class BillingDetailsRequest extends FormRequest
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
        if($request->path()=='api/add_billing_details'){
           $rules = [    
             'first_name' => 'required', 
             'last_name'=>'required',
             'phone'=>'required|min:11|max:12',
             'home_address'=>'required',
             'province'=>'required',
             'city'=>'required'
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
              'ResponseMessage' =>  $message,
              ],
            ], \Config::get('constants.response.ResponseCode_precondition_required'));

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
