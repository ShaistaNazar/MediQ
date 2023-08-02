<?php

namespace App;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

/*
 * User exteds Model to provide model for User.
*/
class User extends Authenticatable
{
    use HasApiTokens,Notifiable,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','full_name','phone','gender','activation_code','is_active','login_type','social_access_token','social_id','player_id'
    ];
    const DELETED_AT = 'deleted_at';
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','updated_at','deleted_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function shipping_details()
    {
        return $this->hasOne(ShippingDetails::class, 'id', 'shipping_id');        
    }

    public function billing_details()
    {
        return $this->hasOne(BillingDetails::class, 'id', 'shipping_id');
    }    
}
