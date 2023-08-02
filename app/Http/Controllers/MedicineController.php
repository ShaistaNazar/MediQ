<?php

namespace App\Http\Controllers;

use Storage;
use File;
use Auth;
use App\Medicines;
use App\City;
use App\OtherMedicines;
use Illuminate\Http\Request;
use App\Http\Services\MessageService;
use App\Http\Services\NotificationService;
use App\Http\Requests\MedicineRequest;
use App\Repositories\MedicinesRepository;
use Validator;

/*
 * MedicineController controller exteds Controller and provides apis for Medicines.
*/
class MedicineController extends Controller
{
    protected $MedRepo;

    function __construct(MedicinesRepository $MedRepository, MessageService $msgService)
    {

        $this->MedRepo = $MedRepository;
        $this->msgService = $msgService;
    }

    /*
     * Search Medicines
    */
    public function searchMedicine(MedicineRequest $request)
    {

        $input = $request->all();
        $validated = $request->validated();
        $result = $this->MedRepo->searchMedicineMethod($request);

        if ($result) {

            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('medicines.medicine_found');
            $param = 'Medicine';
            $values = $result;
        } else {

            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('medicines.medicine_not_found');
            $param = 'Medicine';
            $values = new \stdClass();
        }

        return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
    }

    /*
     * Search Medicine Categories
    */
    public function getMedicineCategories(MedicineRequest $request)
    {
        $validated = $request->validated();
        $input = $request->all();
        $offset = $input['page'];
        $limit = $input['limit'];

        $medicine_categories = $this->MedRepo->getALlMedCategories($offset, $limit);

        if ($medicine_categories) {

            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('medicine_categories.cat_found');
            $param = 'Medicine_Category';
            $values = $medicine_categories;
        } else {

            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('medicine_categories.cat_not_found');
            $param = 'Medicine Category';
            $values = new \stdClass();
        }

        return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
    }

    //other medicine
    public function otherMedicines(MedicineRequest $request)
    {
        $input = $request->all();
        $offset = ($request['page'] * $request['limit']) - $request['limit'];
        $medicine_categories = OtherMedicines::skip($offset)->take($request['limit'])->orderBy('medicine_name')->get();
        return responseMsg(\Config::get('constants.response.ResponseCode_success'), __('medicines.medicine_found'), 'medicines', $medicine_categories);
    }

    public function searchOtherMedicines(MedicineRequest $request)
    {

        $input = $request->all();
        $searchTerm = $request['medicine_name'];
        $result = OtherMedicines::where('medicine_name', 'LIKE', "{$searchTerm}%")->orderByDesc('created_at')->get();
        return responseMsg(\Config::get('constants.response.ResponseCode_success'), __('medicines.medicine_found'), 'medicines', $result);
    }

    /*
     * get Medicines
    */
    public function medicinesList(MedicineRequest $request)
    {

        $validated = $request->validated();
        $input = $request->all();
        $medicineParams = $request->all();
        $pharmacyMedicines = $this->MedRepo->getAllPharMedicines($input);

        if (count($pharmacyMedicines) > 0) {
            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('medicines.medicine_found');
            $param = 'Medicines';
            $values = $pharmacyMedicines;
        } else {
            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('medicines.medicine_found');
            $param = 'Medicine';
            $values = $pharmacyMedicines;
        }

        return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
    }

    /*
     * upload user prescription
    */
    public function UploadPrescription(MedicineRequest $request)
    {
        $input = $request->all();
        $validated = $request->validated();
        $file = $request->file('image');
        $extension = $request->file('image')->extension();
        $filename  = uniqid('image');
        $fileToStore = time() . "-" . $filename . "." . $extension;
        $SavedImage = Storage::disk('prescriptions')->put($fileToStore, File::get($file));
        $imagePathAndName = $fileToStore;
        if ($SavedImage) {
            $user = Auth::user();
            $resp = $this->MedRepo->savePrescription($imagePathAndName, $request->all(), $user);
            $maskName = "Home Medics";
            $textMessage = "We have recieved your prescription, our representative will contact you shortly, Thanks";
            $sendMsg = $this->msgService->sendSmsMessage($textMessage, $request['phone'], $maskName);

            if ($resp) {
                $ResponseCode = \Config::get('constants.response.ResponseCode_success');
                $ResponseMessage = __('medicines.prescription_uploaded');
                $param = 'Prescription';
                $values = $resp;
            } else {
                $ResponseCode = \Config::get('constants.response.ResponseCode_fail');
                $ResponseMessage = __('pharmacy.prescription_error');
                $param = 'Prescription';
                $values = new \stdClass();
            }
        } else {
            $ResponseCode = \Config::get('constants.response.ResponseCode_fail');
            $ResponseMessage = __('pharmacy.prescription_error');
            $param = 'Prescription';
            $values = new \stdClass();
        }

        return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
    }

    /*
     * get Medicines Detail
    */
    public function getMedicineDetails(MedicineRequest $request)
    {
        $input = $request->all();
        $validated = $request->validated();
        $medicineDetails = $this->MedRepo->getMedicineDetailsMethod($input);


        if ($medicineDetails) {
            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('medicines.medicine_found');
            $param = 'Medicines';
            $values = $medicineDetails;
        } else {
            $ResponseCode = \Config::get('constants.response.ResponseCode_success');
            $ResponseMessage = __('medicines.medicine_found');
            $param = 'Medicine';
            $values = new \stdClass();
        }

        return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
    }
}
