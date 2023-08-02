<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * CityEquipments exteds Model to provide model for CityEquipments.
*/
class CityEquipments extends Model
{
	public function city()
	{
    	return $this->hasOne(City::class,'id','city_id');
	}
	
	public function equipment()
	{
    	return $this->hasOne(Equipments::class,'id','equipment_id');
	}
}
