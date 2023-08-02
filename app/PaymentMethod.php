<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * PaymentMethod exteds Model to provide model for PaymentMethod.
*/
class PaymentMethod extends Model
{
    protected $fillable = ['name'];

    protected $hidden = ['updated_at', 'deleted_at', 'created_at'];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
