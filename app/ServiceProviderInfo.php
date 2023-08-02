<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * ServiceProviderInfo exteds Model to provide model for ServiceProviderInfo.
*/
class ServiceProviderInfo extends Model
{
	protected $table='service_provider_info';
    protected $hidden = ['updated_at','deleted_at','created_at'];
    
    public function service()
    {
    	return $this->hasOne('App\Services','id','services_id');
    }
}
