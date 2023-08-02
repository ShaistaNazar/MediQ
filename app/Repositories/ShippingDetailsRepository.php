<?php

namespace App\Repositories;

use Illuminate\Support\Str;
use Carbon\Carbon;
use App\ShippingDetails;
use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;
use Auth;
use App\User;

/**
 * Class UserRepository
 * @package 
 * @version 
 */

/*
 * ShippingDetailsRepository exteds BaseRepository and provide repository layer for Shipping Details.
*/
class ShippingDetailsRepository extends BaseRepository
{
  /**
   * Configure the Model
   **/
  public function model()
  {
    return ShippingDetails::class;
  }

  public function getAllShippingDetails()
  {
    return $this->model::all();
  }

  public function addOrUpdateShippingDetailsMethod($data)
  {

    $user = Auth::user();

    if ($user->shipping_id == null) 
    {
      //Create
      $shippingDetails = ShippingDetails::Create($data);
      $user->shipping_id = $shippingDetails->id;
      $user->save();

      if ($shippingDetails) 
      {
        return $shippingDetails;
      } 
      else 
      {
        return false;
      }
    }

    //Update whole record
    $shippingDetails = ShippingDetails::find($user->shipping_id);
    $shippingDetails->first_name = $data['first_name'];
    $shippingDetails->last_name = $data['last_name'];
    $shippingDetails->phone = $data['phone'];
    $shippingDetails->email = $data['email'];
    $shippingDetails->home_address = $data['home_address'];
    $shippingDetails->province = $data['province'];
    $shippingDetails->city = $data['city'];

    if ($shippingDetails->save()) 
    {
      return $shippingDetails;
    }
    return false;
  }

  public function getShippingDetailsMethod()
  {
    //current user
    $current_user = Auth::user();
    //shipping_id
    $shipping_id = $current_user->shipping_id;
    $allShippingDetails = ShippingDetails::find($shipping_id);

    if ($allShippingDetails) 
    {
      return $allShippingDetails;
    } 
    else 
    {
      return false;
    }
  }
}
