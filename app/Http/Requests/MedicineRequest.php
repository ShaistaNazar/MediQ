<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/*
 * MedicineRequest  exteds FormRequest and provides request for Medicine.
*/
class MedicineRequest extends FormRequest
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
        if($request->path()=='api/search_medicine'){
           $rules = [    
            // 'medicine_name' => 'required',
            ];
        }
        if($request->path()=='api/upload_prescription'){
           $rules = [
                
            'image' => 'required',
            'prescription_for'=>'required',
            'name'=>'required',
			'phone' => 'required|numeric|min:12',
			'email' => 'required|email',
            ];
        }
       
         if($request->path()=='api/medicines_list'){
            $rules = [
                 
                // 'pharmacy_id' => 'required',
                'category_id'=>'required',
                'page' => 'required',
                'limit' => 'required'
             ];
         }
        if($request->path()=='api/get_medicine_categories'){
            $rules = [
                'page' => 'required',
                'limit' => 'required'
             ];
         }
         if($request->path()=='api/other_medicines'){
            $rules = [
                'page' => 'required',
                'limit' => 'required'
             ];
         }
         if($request->path()=='api/search_other_medicines'){
            $rules = [
                // 'medicine_name' => 'required',
             ];
         }
         if($request->path()=='api/get_medicine_details'){
            $rules = [

                'medicine_id' => 'required',
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
