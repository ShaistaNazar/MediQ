<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * TestCategory exteds Model to provide model for TestCategory.
*/
class TestCategory extends Model
{
   protected $gaurded = [];
   protected $table='test_categories';
}
