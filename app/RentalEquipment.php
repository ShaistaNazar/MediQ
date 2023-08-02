<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * RentalEquipment exteds Model to provide model for RentalEquipment.
*/
class RentalEquipment extends Model
{
    protected $table='rental_equipment';
    protected $guarded=[];

    public function Equipments()
    {
        return $this->hasOne(Equipments::class,'id','equipment_id');
    }
}
