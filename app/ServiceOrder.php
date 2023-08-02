<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * ServiceOrder exteds Model to provide model for ServiceOrder.
*/
class ServiceOrder extends Model
{
    protected $table = 'service_orders';
    protected $guarded = [];

    public function service()
    {
        return $this->hasOne('App\Services', 'id', 'service_id');
    }

    public function service_type()
    {
        return $this->hasOne('App\Services', 'id', 'service_id');
    }

    public function serviceProviders()
    {
        return $this->hasOne('App\ServiceProviderInfo', 'id', 'provider_id');
    }
    
    public function cityServices()
    {
        return $this->hasMany(CityServices::class, 'city_id', 'city_id');
    }
}
