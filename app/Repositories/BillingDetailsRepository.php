<?php

namespace App\Repositories;

use Auth;
use Carbon\Carbon;
use App\BillingDetails;
use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;

/**
 * Class UserRepository
 * @package 
 * @version 
 */
/*
 * BillingDetailsRepository exteds BaseRepository and provide repository layer for Billing Details.
*/
class BillingDetailsRepository extends BaseRepository
{
  /**
   * Configure the Model
   **/
  public function model()
  {
    return BillingDetails::class;
  }

  public function getAllBillingDetails()
  {
    return $this->model::all();
  }

  public function addOrUpdateBillingDetailsMethod($data)
  {
    $first_name = $data['first_name'];
    $last_name = $data['last_name'];
    $phone = $data['phone'];
    $home_address = $data['home_address'];
    $province = $data['province'];
    $city = $data['city'];

    //user
    $user = Auth::user();
    if ($user->billing_id == 0 || $user->billing_id == null) 
    {
      //create a whole record
      $billingModel = BillingDetails::Create($data);
      $user->billing_id = $billingModel->id;

      $user->save();
      $billingModel->save();
      if ($billingModel && $user) 
      {
        return $billingModel;
      } 
      else 
      {
        return false;
      }
    }
    //update existing record
    $billingModel = BillingDetails::find($user->billing_id);
    $billingModel->first_name = $first_name;
    $billingModel->last_name = $last_name;
    $billingModel->phone = $phone;
    $billingModel->home_address = $home_address;
    $billingModel->province = $province;
    $billingModel->city = $city;

    if ($billingModel->save()) 
    {
      return $billingModel;
    }
    return false;
  }

  public function getBillingDetailsMethod()
  {
    $user = Auth::user();

    $billing_id = $user->billing_id;
    $allBillingDetails = BillingDetails::find($billing_id);

    if ($allBillingDetails) 
    {
      return $allBillingDetails;
    } 
    else 
    {
      return false;
    }
  }
}
