<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/*
 * OrderTemp exteds Model to provide model for OrderTemp.
*/
class OrderTemp extends Model
{
     use SoftDeletes;

     protected $fillable = [
          'order_number', 'status', 'user_id', 'quantity', 'delivery_type_id', 'shipping_id', 'billing_id', 'payment_method_id', 'total_amount'
     ];
     protected $hidden = ['updated_at', 'deleted_at'];
     const DELETED_AT = 'deleted_at';

     public function user()
     {
          return $this->hasOne('App\User', 'id', 'user_id');
     }

     public function delivery()
     {
          return $this->hasOne('App\DeliveryTypes', 'id', 'delivery_type_id');
     }

     public function Medicines()
     {
          return $this->hasOne('App\Medicines', 'id', 'product_id');
     }

     public function Equipments()
     {
          return $this->hasOne('App\Medicines', 'id', 'product_id');
     }

     public function Shipping_details()
     {
          return $this->hasOne('App\ShippingDetails', 'id', 'shipping_id');
     }

     public function billing_details()
     {
          return $this->hasOne('App\BillingDetails', 'id', 'billing_id');
     }

     public function payment_details()
     {
          return $this->hasOne('App\PaymentMethod', 'id', 'payment_method_id');
     }

     public function order_details()
     {
          return $this->hasMany('App\OrderDetails', 'order_number', 'order_number');
     }

     public function labsTest()
     {
          return $this->hasOne('App\Tests', 'id', 'product_id');
     }

     public function homeSerivce()
     {
          return $this->hasOne('App\ServiceProviders', 'id', 'product_id');
     }
}
