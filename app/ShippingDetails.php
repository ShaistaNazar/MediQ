<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * ShippingDetails exteds Model to provide model for ShippingDetails.
*/
class ShippingDetails extends Model
{
    protected $primaryKey = 'id';
    const DELETED_AT='deleted_at';
    protected $fillable=['id', 'first_name','last_name','phone','email','home_address','province','city'];
    protected $hidden = ['updated_at','deleted_at','created_at'];

    public function user()
    {
        return $this->belongsTo('App\User', 'foreign_key', 'other_key');
    }


}
