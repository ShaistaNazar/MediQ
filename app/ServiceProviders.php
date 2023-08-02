<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * ServiceProviders exteds Model to provide model for ServiceProviders.
*/
class ServiceProviders extends Model
{
	protected $hidden = ['updated_at','deleted_at','created_at'];
    
    public function city()
    {
    	return $this->hasOne('App\City','id','city_id');
    }

    public function service()
    {
    	return $this->hasOne('App\Services','id','service_id');
    }

    public function serviceProviders()
    {
    	return $this->hasOne('App\ServiceProviderInfo','id','service_provider_id');
    }    
}
