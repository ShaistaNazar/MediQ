<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * Tests exteds Model to provide model for Tests.
*/
class Tests extends Model
{
	protected $hidden = ['updated_at','deleted_at'];

    public function labs()
    {
    	return $this->hasOne('App\Labs','id','lab_id');
    }

    public function category()
    {
    	return $this->hasOne('App\TestCategory','id','testcategory_id');
    }
}
