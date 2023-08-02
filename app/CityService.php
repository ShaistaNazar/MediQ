<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * CityService exteds Model to provide model for CityService.
*/
class CityService extends Model
{
    protected $table = 'city_services';
    public $fillable = [
        'service_id', 'city_id'
    ];

    public function city()
    {
        return $this->hasOne(City::class, 'id', 'city_id');
    }

    public function serviceTypes()
    {
        return    $this->hasOne(Services::class, 'id', 'service_id');
    }

    public function serviceTypesWC()
    {
        return    $this->hasOne(Services::class, 'id', 'service_id')->where('in_demand', 1);
    }

    public function allServices()
    {
        return    $this->hasOne(City::class, 'id', 'city_id');
    }
}
