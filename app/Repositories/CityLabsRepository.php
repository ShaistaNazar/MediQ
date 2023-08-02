<?php

namespace App\Repositories;

use Auth;
use Carbon\Carbon;
use App\LabsTestCategory;
use App\UserTestReports;
use App\Tests;
use App\Labs;
use App\CityLabs;
use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;

/**
 * Class CityLabsRepository
 * @package 
 * @version 
*/

/*
 * CityLabsRepository exteds BaseRepository and provide repository layer for City Labs.
*/
class CityLabsRepository extends BaseRepository
{

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CityLabs::class;
    }

    public function getAllLabs()
    {
        return $this->model::all();
    }

    public function getLabsByCity()
    {
        return Labs::orderByDesc('created_at')->get();
    }

    public function getNearbyLabsMethod($data)
    {        
        $lat = $data['lat'];
        $lon = $data['long'];
   
        return $nearByLabs=$this->model::where('lab_id',$data['lab_id'])
                ->with('labs:id,lab_name')
                ->select("city_labs.id",'lab_id'
                    ,DB::raw("6371 * acos(cos(radians(" . $lat . ")) 
                    * cos(radians(city_labs.lat)) 
                    * cos(radians(city_labs.long) - radians(" . $lon . ")) 
                    + sin(radians(" .$lat. ")) 
                    * sin(radians(city_labs.lat))) AS distance"))
                    ->orderByDesc('created_at')->get();                  
    }

    public function getTestCategories($input)
    {
        $offset = ($input['page'] * $input['limit']) - $input['limit'];
        return $tests=LabsTestCategory::get();
    }
    
    public function getLabReports($inputs)
    {
        
        $offset = ($inputs['page'] * $inputs['limit']) - $inputs['limit'];
        return $reports=UserTestReports::with('test')->where('user_id',Auth::id())->skip($offset)->take($inputs['limit'])->orderByDesc('created_at')->get();
    }

    public function getTests($inputs)
    {
                $offset = ($inputs['page'] * $inputs['limit']) - $inputs['limit'];
        return $tests=Tests::where('lab_id',$inputs['lab_id'])->where('testcategory_id',$inputs['category_id'])->skip($offset)->take($inputs['limit'])->get();
    }

    public function searchTestMethod($searchTerm,$lab)
    {
       return $result = Tests::with('category:id,category_name')->where('test_name','LIKE', "{$searchTerm}%")->get(['id','test_name','logo','description','lab_id','testcategory_id','price','is_prescription_req','created_at']);   
    }
}
