<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * City exteds Model to provide model for City.
*/
class City extends Model
{    
    protected $table = 'cities';
    protected $primaryKey = 'id';
    protected $fillable = [
        'city_names','province','is_show'
    ];
    protected $hidden = ['updated_at'];
   


}
