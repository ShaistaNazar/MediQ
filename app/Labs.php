<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * Labs exteds Model to provide model for Labs.
*/
class Labs extends Model
{
	protected $table='labs';
    protected $fillable=['id','lab_name', 'logo'];
    const DELETED_AT='deleted_at';
    protected $hidden = ['updated_at','deleted_at','created_at'];
}
