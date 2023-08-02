<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * CityRental exteds Model to provide model for CityRental.
*/
class CityRental extends Model
{
    protected $table='rental_cities';
    public $gaurded = [];

    public function equipment()
    {
    	return $this->hasOne(Equipments::class,'id','rental_equipment_id')->where('is_rental', 1);
	}
}
