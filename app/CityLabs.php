<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * CityLabs exteds Model to provide model for CityLabs.
*/
class CityLabs extends Model
{
    protected $hidden = ['updated_at','deleted_at','created_at'];

    public function labs()
    {
    	return $this->hasOne('App\Labs','id','lab_id');
    }
}
