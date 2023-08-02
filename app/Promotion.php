<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * Promotion exteds Model to provide model for Promotion.
*/
class Promotion extends Model
{
    protected $table='promotion';
    protected $fillable=['id','user_id', 'code', 'created_at','is_applied', 'updated_at'];
}
