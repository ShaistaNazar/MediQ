<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Medicines;
use App\MedicinesCategory;
use App\OtherMedicines;
use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;
use App\Prescriptions;
use App\UserPrescriptions;
use Faker\Provider\ms_MY\Miscellaneous;
use stdClass;

// use App\MedicinesCategory;
/**
 * Class PharmacyRepository
 * @package 
 * @version 
 */

/*
 * MedicinesRepository exteds BaseRepository and provide repository layer for Medicines.
*/
class MedicinesRepository extends BaseRepository
{
    /**
     * Configure the Model
     **/
    public function model()
    {
        return Medicines::class;
    }

    public function getAllMedicines()
    {
        return $this->model::all();
    }

    public function searchMedicineMethod($request)
    {
        $searchTerm = $request['medicine_name'];
        if ($request['cat_id']) {
            return $result = $this->model::with('warnings')->where('category_id', $request['cat_id'])
            ->where('medicine_name', 'LIKE', "{$searchTerm}%")->orderByDesc('created_at')->get();
        } else {
            $result = $this->model::with('warnings')->where('medicine_name', 'LIKE', "{$searchTerm}%")->orderByDesc('created_at')->get();
            $misMeds = OtherMedicines::where('medicine_name', 'LIKE', "{$searchTerm}%")->orderByDesc('created_at')->get();
            $allMeds = new \stdClass();
            $allMeds->medicines = $result;
            $allMeds->miscellaneous = $misMeds;
            return $allMeds;
        }
    }

    public function getALlMedCategories($page, $limit)
    {
        $offset = ($page * $limit) - $limit;
        if ($page == -1 && $limit == -1) 
        {
            $medCat = MedicinesCategory::all();
        } 
        else 
        {
            $medCat = MedicinesCategory::offset($offset)->limit($limit)->orderByDesc('created_at')->get();
        }
        return $medCat;
    }

    public function getAllPharMedicines($input)
    {
        $offset = ($input['page'] * $input['limit']) - $input['limit'];
        if ($input['category_id'] == 0)
            return   $medicines = $this->model::with('warnings')->orderByDesc('created_at')->get();
        if ($input['page'] == -1 && $input['limit'] == -1) {
            return   $medicines = $this->model::with('warnings')->where('category_id', $input['category_id'])->orderByDesc('created_at')->get();
        }
        else 
        {
            return   $medicines = $this->model::with('warnings')->where('category_id', $input['category_id'])->offset($offset)->limit($input['limit'])->orderByDesc('created_at')->get();
        }
    }

    public function savePrescription($imagePathAndName, $request, $user)
    {
        $addPrescription = Prescriptions::create([

            'prescription_path' => 'prescriptions/' . $imagePathAndName,
            'prescription_for' => $request['prescription_for'],
            'name' => $request['name'],
            'email' => $request['email'],
            'mobile_number' => $request['phone'],
            'ordered_by_user' => $user->id
        ]);
        if ($addPrescription) 
        {
            return $addPrescription;
        }
        return false;
    }

    public function getMedicineDetailsMethod($input)
    {
        $medicine_id = $input['medicine_id'];
        $medicineDetails = Medicines::with('category')->where('id', $medicine_id)->orderByDesc('created_at')->get();
        return $medicineDetails;
    }
}
