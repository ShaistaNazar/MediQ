<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * InDemandProducts exteds Model to provide model for InDemandProducts.
*/
class InDemandProducts extends Model
{
    protected $table = 'in_demands';
    protected $fillable = [
        'product_id', 'product_type'
    ];

    public function labsTest()
    {
        return $this->hasOne('App\Tests', 'id', 'product_id');
    }

    public function Medicines()
    {
        return $this->hasOne('App\Medicines', 'id', 'product_id');
    }
    
    public function Equipments()
    {
        return $this->hasOne('App\Equipments', 'id', 'product_id');
    }
}
