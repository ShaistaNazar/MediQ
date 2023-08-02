<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * Services exteds Model to provide model for Services.
*/
class Services extends Model
{
    protected $fillable=['name,created_at,updated_at'];
    protected $hidden = ['updated_at','deleted_at','created_at'];
    protected $table='services';

    public function serviceType()
    {
        return $this->hasOne(ServiceType::class);
    }
}
