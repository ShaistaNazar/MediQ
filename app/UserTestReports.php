<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * UserTestReports exteds Model to provide model for UserTestReports.
*/
class UserTestReports extends Model
{
    protected $guarded = [];
    
    // public function labs(){
    // 	return $this->hasOne('App\Labs','id','lab_id');
    // }
    public function user()
    {
    	return $this->hasOne('App\User','id','user_id');
    }

    public function test()
    {
    	return $this->hasOne('App\Tests','id','test_id');
    }
}
