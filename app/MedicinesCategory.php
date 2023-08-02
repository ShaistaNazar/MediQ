<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * MedicinesCategory exteds Model to provide model for MedicinesCategory.
*/
class MedicinesCategory extends Model
{
    protected $gaurded = [];
    protected $table='medicines_categories';
}
