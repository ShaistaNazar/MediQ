<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/*
 * Orders exteds Model to provide model for Orders.
*/
class Orders extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number', 'status', 'user_id', 'quantity', 'delivery_id', 'order_model', 'model_id'
    ];
    protected $hidden = ['updated_at', 'deleted_at'];
    const DELETED_AT = 'deleted_at';

    public function user()
    {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function delivery()
    {
        return $this->hasOne('App\DeliveryTypes', 'id', 'delivery_id');
    }

    public function Medicines()
    {
        return $this->hasOne('App\Medicines', 'id', 'model_id');
    }

    public function Equipments()
    {
        return $this->hasOne('App\Medicines', 'id', 'model_id');
    }
    
    public function order_details()
    {
        return $this->hasMany('App\OrderDetails', 'order_number', 'order_number');
    }
}
