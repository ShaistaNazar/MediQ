<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/*
 * Medicines exteds Model to provide model for Medicines.
*/
class Medicines extends Model
{
    use SoftDeletes;
    
    protected $table='medicines';
    public $fillable = ['id',
    'medicine_name', 'status', 'available_quantity','price','expairy_date','medicine_image','brand','unit','is_prescription_req'
    ];
    protected $hidden = ['updated_at','deleted_at','expairy_date','created_at'];
    public $guarded = [];
    const DELETED_AT = 'deleted_at';

    public function category()
    {
        return $this->hasOne('App\MedicinesCategory','id','category_id');
    }

    // public function pharmacy(){
    // 	return $this->hasOne('App\Pharmacy','id','pharmacy_id');
    // }

    public function PrimaryUse()
    {
        return $this->hasMany(MedPrimaryUse::class,'medicine_id','id');
    }
    
    public function warnings()
    {
        return $this->hasMany(MedicineWarning::class,'medicine_id','id');
    }
   
}