<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * Payment exteds Model to provide model for Payment.
*/
class Payment extends Model
{
    protected $fillable=['payment_id','customer_id','amount','payment_date'];

    public function payment_methods()
    {
        return $this->hasMany(PymentMethod::class);
    }
}
