<?php

namespace App\Http\Controllers;

use App\Repositories\CityRepository;
use Illuminate\Http\Request;
use App\Http\Services\MessageService;
use App\City;

/*
 * CityController controller exteds Controller and provides apis for cities.
*/
class CityController extends Controller
{
    protected $CityRepo;
    protected $msgService;

    function __construct(CityRepository $CityRepository, Request $request, MessageService $msgService)
    {

        $this->CityRepo = $CityRepository;
        $this->msgService = $msgService;
    }

    public function cities(Request $request)
    {

        //getting all cities
        // return $request->header->all();
        if (isset($request['province'])) {
            $cities = City::where('province', $request['province'])->where('is_show', 1)->orderByDesc('created_at')->get();
        } else {
            $cities = City::where('is_show', 1)->orderByDesc('created_at')->get();
        }

        //responseCode
        $ResponseCode = \Config::get('constants.response.ResponseCode_success');
        $ResponseMessage = __('cities.all_cities');
        $param = 'City';
        $values = $cities;

        return responseMsg($ResponseCode, $ResponseMessage, $param, $values);
    }
}
