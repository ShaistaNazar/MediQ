<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\ServiceProviders;
use App\Equipments;
use App\CityService;
use App\Services;
use App\ServiceOrder;
use App\CityEquipments;
use App\RentalEquipment;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;

/**
 * Class PharmacyRepository
 * @package 
 * @version 
 */

/*
 * HomeServiceRepository exteds BaseRepository and provide repository layer for Home Services.
*/
class HomeServiceRepository extends BaseRepository
{
    /**
     * Configure the Model
     **/
    public function model()
    {
        return ServiceProviders::class;
    }

    public function getHomeServicesTypesByCity($inputs)
    {
        if (isset($inputs['city_id'])) {
            $servicetypes = CityService::where('city_id', $inputs['city_id'])->with('city', 'serviceTypes')->whereHas('serviceTypes')->orderByDesc('created_at')->get();
        } else {
            $servicetypes = CityService::where('city_id', '!=', 1)->with('serviceTypesWC')->whereHas('serviceTypesWC')->groupBy('service_id')->orderByDesc('created_at')->get();
        }
        foreach ($servicetypes as $key => $value) {
            if (isset($inputs['city_id'])) {
                $value->id = $value['serviceTypes']['id'];
                $value->service_name = $value['serviceTypes']['service_name'];
                $value->image = $value['serviceTypes']['image'];
                $value->in_demand = $value['serviceTypes']['in_demand'];
            } else {
                $value->id = $value['serviceTypesWC']['id'];
                $value->service_name = $value['serviceTypesWC']['service_name'];
                $value->image = $value['serviceTypesWC']['image'];
                $value->in_demand = $value['serviceTypesWC']['in_demand'];
            }
        }
        return $servicetypes;
    }

    public function orderService($inputs, $id, $order_id)
    {
        $servicetypes = ServiceOrder::create([
            'service_id' => $inputs['service_id'],
            'order_number' => $order_id,
            'order_from_user' => $id,
            'comments' => $inputs['comment'],
            'status' => 0,
            'city_id' => $inputs['city_id'],
        ]);
        return $servicetypes;
    }

    public function searchServiceMethod($city_id, $searchTerm)
    {

        $serviceId = Services::where('service_name', 'LIKE', "{$searchTerm}%")->first();
        $specificServiceProviders_with_cities = [];
        if ($serviceId) {
            $serviceId = $serviceId->id;

            return $specificServiceProviders_with_cities = $this->model::whereHas(
                'serviceProviders.service',
                function ($query) use ($serviceId) {
                    $query->where('id', $serviceId);
                }
            )->with('city:id,city_name', 'serviceProviders.service:id,service_name')->where('city_id', $city_id)->get(['id', 'service_provider_id', 'city_id', 'price_per_hour', 'available_from', 'available_to', 'status', 'created_at']);
        }
        return $specificServiceProviders_with_cities;
    }
    
    public function searchEquipment($inputs)
    {
        $searchTerm = $inputs['equipment_name'];
        return $allEquipment = Equipments::where('equipment_name', 'LIKE', "{$searchTerm}%")->where('is_rental', '!=', 1)->orderByDesc('created_at')->get();
        if ($allEquipment) {
            return $equipment = CityEquipments::with('city:id,city_name', 'equipment:id,equipment_name,brand,image,price,quantity')
            ->where('city_id', $inputs['city_id'])->orderByDesc('created_at')->get();
        }
        return $equipment;
    }

    public function orderRent($inputs, $id, $order_id)
    {
        $servicetypes = RentalEquipment::create([
            'equipment_id' => $inputs['equipment_id'],
            'order_number' => $order_id,
            'user_id' => $id,
            'comment' => $inputs['comment'],
            'status' => 0,
            'city_id' => $inputs['city_id'],
        ]);
        return $servicetypes;
    }
}
