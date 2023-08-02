<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * Equipments exteds Model to provide model for Equipments.
*/
class Equipments extends Model
{
     protected $hidden = ['updated_at','deleted_at','created_at'];

     public function equipment()
     {
          return $this->hasOne(CityEquipments::class,'equipment_id','id');
     }

}
