<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * CityServices exteds Model to provide model for CityServices.
*/
class CityServices extends Model
{
    public function city()
    {
        return $this->hasOne(City::class, 'id', 'city_id');
    }

    public function serviceTypes()
    {
        return    $this->hasOne(ServicesType::class, 'id', 'service_id');
    }
}
