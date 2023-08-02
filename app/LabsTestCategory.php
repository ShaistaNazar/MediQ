<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * LabsTestCategory exteds Model to provide model for LabsTestCategory.
*/
class LabsTestCategory extends Model
{
	protected $hidden = ['created_at','updated_at'];
	
    public function labs()
    {
    	return $this->hasOne('App\Labs','id','lab_id');
    }

    public function testCategory()
    {
    	return $this->hasOne('App\TestCategory','id','testcategory_id');
    }
}
