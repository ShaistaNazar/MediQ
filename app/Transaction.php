<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/*
 * Transaction exteds Model to provide model for Transaction.
*/
class Transaction extends Model
{
    protected $fillable=['user_id','merchant_id','hash_key','amount','bill_reference','description','language','currency','expiry_date','ref_number','txt_number','sub_merchant_id','discounted_amount','discounted_bank','customer_card_expiry','card_cvv','instrument_type','pmpf_1','pmpf_2','pmpf_3','response_msg','response_code','retrival_ref','order_id'];
}
