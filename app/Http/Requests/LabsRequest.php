<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/*
 *  LabsRequest exteds FormRequest and provides request for Labs.
*/
class LabsRequest extends FormRequest
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
        // if($request->path()=='api/labs_list'){
        //    $rules = [
        //     'city_id' => 'required'
        //     ];
        // }
        if($request->path()=='api/test_category_list'){
           $rules = [
            'lab_id' => 'required',
            // 'page' => 'required',
            // 'limit' => 'required'
            ];
        }
        if($request->path()=='api/nearby_labs'){
           $rules = [
            'lat' => 'required',
            'long' => 'required',
            'lab_id' => 'required'
            ];
        }
        if($request->path()=='api/lab_reports'){
           $rules = [
            'page' => 'required',
            'limit' => 'required'
            ];
        }
        if($request->path()=='api/test_list'){
           $rules = [
            'lab_id' => 'required',
            'category_id' => 'required',
            'page' => 'required',
            'limit' => 'required',
            ];
        }
        if($request->path()=='api/search_test'){
            $rules = [
            //  'test_name'=>'required',
             ];
         }
         if($request->path()=='api/get_user_test_reports'){
            $rules = [
                'page' => 'required',
                'limit' => 'required',
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
