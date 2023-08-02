<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/*
 *  HomeServiceRequest exteds FormRequest and provides request for Home Services.
*/
class HomeServiceRequest extends FormRequest
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
        
        if($request->path()=='api/order_service'){
           $rules = [
            'city_id' => 'required',
            'service_id' => 'required',
            'comment' => 'required|min:20'
            ];
        }
        if($request->path()=='api/order_rent'){
            $rules = [
             'equipment_id' => 'required',
             'comment' => 'required|min:20',
             'city_id' => 'required'
             ];
         }
        if($request->path()=='api/search_home_service_providers'){
           $rules = [
            'city_id' => 'required',
            'service_name'=>'required',
            ];
        }
        if($request->path()=='api/search_medical_equipment'){
           $rules = [
            'city_id' => 'required',
            // 'equipment_name'=>'required',
            ];
        }
        if($request->path()=='api/medical_equipment'){
            $rules = [
             'page' => 'required',
             'limit'=>'required',
             ];
         }
         if($request->path()=='api/rental_history'){
            $rules = [
             'page' => 'required',
             'limit'=>'required',
             ];
         }
        if($request->path()=='api/home_medical_services_list'){
            $rules = [
            //  'city_id' => 'required',
             ];
         }
         if($request->path()=='api/get_all_rental'){
            $rules = [
             'city_id' => 'required',
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
