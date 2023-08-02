<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * BillingDetails exteds Model to provide model for BillingDetails.
*/
class BillingDetails extends Model
{    
    const DELETED_AT='deleted_at';
    protected $fillable=['first_name','last_name','phone','home_address','province','city'];
    protected $hidden = ['updated_at','deleted_at','created_at'];

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

}
