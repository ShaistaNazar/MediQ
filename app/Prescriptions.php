<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * Prescriptions exteds Model to provide model for Prescriptions.
*/
class Prescriptions extends Model
{
    protected $fillable = [
        'prescription_path','prescription_for','product_id','name','mobile_number','email','image','ordered_by_user','product_type'
    ];
}
