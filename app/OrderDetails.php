<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * OrderDetails exteds Model to provide model for OrderDetails.
*/
class OrderDetails extends Model
{
     protected $table = 'order_details';
     protected $hidden = ['updated_at', 'deleted_at'];

     protected $fillable = [
          'order_number', 'status', 'product_id', 'quantity', 'product_type'
     ];

     public function warnings()
     {
          return $this->hasMany(MedicineWarning::class, 'medicine_id', 'id');
     }

     public function Medicines()
     {
          return $this->hasOne(Medicines::class, 'id', 'product_id');
     }

     public function Equipments()
     {
          return $this->hasOne('App\Equipments', 'id', 'product_id');
     }

     public function LabsTest()
     {
          return $this->hasOne('App\Tests', 'id', 'product_id');
     }

     public function HomeSerivce()
     {
          return $this->hasOne('App\ServiceProviders', 'id', 'product_id');
     }

     public function inDemandTests()
     {
          return $this->hasOne('App\Tests', 'id', 'MAX(product_id)');
     }

     public function inDemandMedicines()
     {
          return $this->hasOne('App\Medicines', 'id', 'MAX(product_id)');
     }

     public function inDemandEquipments()
     {
          return $this->hasOne('App\Equipments', 'id', 'MAX(product_id)');
     }

     public function medProducts()
     {
          return $this->hasOne(Medicines::class, 'id', 'product_id');
     }
     
     public function OtherMedicines()
     {
          return $this->hasOne(OtherMedicines::class, 'id', 'product_id');
     }
}
