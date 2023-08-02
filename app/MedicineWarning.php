<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * MedicineWarning exteds Model to provide model for MedicineWarning.
*/
class MedicineWarning extends Model
{
    protected $table='medicine_warnings';
    protected $fillable=['id','medicine_id', 'warning'];
    const DELETED_AT='deleted_at';
    protected $hidden = ['updated_at','deleted_at','created_at'];
}
