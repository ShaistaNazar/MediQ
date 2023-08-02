<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * UserPrescriptions exteds Model to provide model for UserPrescriptions.
*/
class UserPrescriptions extends Model
{
    protected $table='user_prescriptions';
     protected $fillable = [
        'user_id','prescription_id'
    ];
}
