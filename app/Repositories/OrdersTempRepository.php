<?php

namespace App\Repositories;

use App\BillingDetails;
use App\Equipments;
use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;
use App\Jobs\Notifications;
use Carbon\Carbon;
use App\OrderTemp;
use App\OrderDetails;
use App\InDemandProducts;
use App\Medicines;
use App\Orders;
use App\ServiceOrder;
use App\ShippingDetails;
use App\LabsTestCategory;
use App\Tests;
use App\User;
use App\UserTestReports;

use App\TestCategory;
use App\MedicinesCategory;

use Auth;
use DateTime;
use PHPUnit\Framework\Constraint\Count;

/**
 * Class UserRepository
 * @package 
 * @version 
 */

/*
 * OrdersTempRepository exteds BaseRepository and provide repository layer for OrdersTemp.
*/
class OrdersTempRepository extends BaseRepository
{
    /**
     * Configure the Model
     **/
    public function model()
    {
        return OrderTemp::class;
    }

    public function getAllOrders()
    {
        return $this->model::all();
    }

    public function createNewOrder($inputs)
    {
        foreach ($inputs['order'] as $key => $value) 
        {
            $rowOrder = OrderDetails::create([
                'order_number' => $inputs['order_number'],
                'product_is_prescription_req' => $value['product_type'],
                'product_id' => $value['product_id'],
                'product_type' => $value['product_type'],
                'quantity' => $value['quantity'],
                'status' => 3
            ]);
            if ($value['product_type'] == 'LabsTest') 
            {
                UserTestReports::create(['user_id' => Auth::id(), 'test_id' => $value['product_id']]);
            }
        }

        $Shipping = [
            'first_name' => $inputs['shipping']['first_name'],
            'last_name' => $inputs['shipping']['last_name'],
            'phone' => $inputs['shipping']['phone'],
            'email' => $inputs['shipping']['email'],
            'home_address' => $inputs['shipping']['home_address'],
            'province' => $inputs['shipping']['province'],
            'city' => $inputs['shipping']['city']
        ];
        $Billing = [
            'first_name' => $inputs['shipping']['first_name'],
            'last_name' => $inputs['shipping']['last_name'],
            'phone' => $inputs['shipping']['phone'],
            'home_address' => $inputs['shipping']['home_address'],
            'province' => $inputs['shipping']['province'],
            'city' => $inputs['shipping']['city']
        ];


        $inputs['user_id'] = Auth::id();
        $user = User::find($inputs['user_id']);
        $userShipping = ShippingDetails::where('id', $user->shipping_id)->first();
        if ($userShipping) 
        {
            $shippingInsert = ShippingDetails::where('id', $user->shipping_id)->update($Shipping);
        } 
        else 
        {
            $shippingInsert = ShippingDetails::Create($Shipping);
            User::where('id', $inputs['user_id'])->update(['shipping_id' => $shippingInsert->id]);
        }
        $userBilling = BillingDetails::where('id', $user->billing_id)->first();
        if ($userBilling) 
        {
            $billingInsert = BillingDetails::where('id', $userBilling['id'])->update($Billing);
        } 
        else 
        {
            $billingInsert = BillingDetails::Create($Billing);
            User::where('id', $inputs['user_id'])->update(['billing_id' => $billingInsert->id]);
        }
        return $orderTemp = OrderTemp::create([
            'user_id' => $inputs['user_id'],
            'order_number' => $inputs['order_number'],
            'shipping_id' => $user->shipping_id,
            'billing_id' => $user->billing_id,
            'payment_method_id' => $inputs['payment_method_id'],
            'total_amount' => $inputs['total_amount'],
            'status' => 1,
        ]);
        return true;
    }

    public function getMedOrdersByUserId($auth, $page, $limit)
    {
        $offset = ($page * $limit) - $limit;
        $orders = OrderTemp::with('order_details')
            ->where('user_id', $auth)->offset($offset)->limit($limit)->Select(
                'id',
                'order_number',
                'total_amount',
                'delivery_type_id',
                'created_at',
                'payment_method_id'
            )->where('status','!=',3)->where('status','!=',1)
            ->orderByDesc('created_at')->get();

        $meds = [];
        foreach ($orders as $key1 => $value) 
        {
            foreach ($value['order_details'] as $key => $val) 
            {
                $relationship = $val->product_type;
                if ($relationship == 'Medicines') 
                {
                    $val->load('Medicines.warnings');
                }
                if ($relationship == 'Equipments') 
                {
                    $val->load($relationship);
                }
                if ($relationship == 'LabsTest') 
                {
                    $val->load($relationship);
                }
                if ($relationship == 'OtherMedicines') 
                {
                    $val->load($relationship);
                }
            }
        }
        return $orders;
    }

    public function getEquOrdersByUserId($id, $page, $limit)
    {
        $offset = ($page * $limit) - $limit;
        $orders = $this->model::with(['order_details' => function ($q) {
            $q->where('product_type', 'Equipments');
        }])
            ->where('user_id', $id)->offset($offset)->limit($limit)->orderByDesc('created_at')->get();
        $equ = [];
        foreach ($orders as $key1 => $value) 
        {
            foreach ($value['order_details'] as $key2 => $val) 
            {
                $relationship = $val->product_type;
                if ($relationship == 'Equipments') 
                {
                    $val->load($relationship . ':id,equipment_name,price,brand,image');
                }
                $equ['medicine_single_order'][$key2] = $val;
            }
            $orders[$key1] = $equ;
        }

        return $orders;
    }

    public function getTestOrdersByUserId($id, $page, $limit)
    {
        $offset = ($page * $limit) - $limit;
        $orders = $this->model::with(['order_details' => function ($q) {
            $q->where('product_type', 'LabsTest');
        }])
            ->where('user_id', $id)->offset($offset)->limit($limit)->orderByDesc('created_at')->get();
        $test = [];
        foreach ($orders as $key1 => $value) 
        {
            foreach ($value['order_details'] as $key => $val) 
            {
                $relationship = $val->product_type;
                if ($relationship == 'LabsTest') 
                {
                    $val->load($relationship);
                }
                $test['test_single_order'][$key] = $val;
            }
            $orders[$key] = $test;
        }
        return $orders;
    }

    public function UserReorder($input, $newOrderNumber)
    {
        $order = Orders::where('order_number', $input['order_number'])->first();
        
        try 
        {
            if ($order) 
            {
                $orderDetails = OrderDetails::where('order_number', $order->order_number)->where('product_type', $order->product_type)
                    ->where('product_id', $order->product_id)->orderByDesc('created_at')->get();

                $date = Carbon::now()->toDateTimeString();

                if (count($orderDetails) > 0) 
                {
                    foreach ($orderDetails as $key => $value) 
                    {
                        $value->order_number = $newOrderNumber;
                        $value->created_at = $date;
                    }
                }

                $order->order_number = $newOrderNumber;
                $order->makeHidden('id');
                $orderDetails->makeHidden('id');

                $insert = [0 => $order->toarray(), 1 => $orderDetails->toarray()];

                $insertData = DB::transaction(function () use ($insert) {

                    DB::table('order_temps')->insert($insert[0]);
                    DB::table('order_details')->insert($insert[1]);
                });

                Notifications::dispatch()->delay(now()->addSeconds(1));
                return true;
            }
        } 
        catch (\Exception $e) 
        {
            return false;
        }
    }

    public function getOrderByNumber($orderNumber)
    {

        $orders = $this->model::with('user:id,full_name,email,phone', 'delivery:id,delivery_types', 'Shipping_details', 'billing_details', 'payment_details:id,name', 'order_details')
            ->where('order_number', $orderNumber)->orderByDesc('created_at')->get();

        $orderDetail = $this->getOrderDetails($orders);

        return $orderDetail;
    }

    public function getOrderDetails($orders)
    {
        foreach ($orders as $key => $value) 
        {
            foreach ($value->order_details as $key => $val) 
            {
                $relationship = $val->product_type;
                if ($relationship == 'Medicines') 
                {
                    $val->load($relationship . ':id,medicine_name,price');
                }
                if ($relationship == 'Equipments') 
                {
                    $val->load($relationship . ':id,equipment_name,price,brand');
                }
                if ($relationship == 'LabsTest') 
                {
                    $val->load($relationship);
                    $val->load($relationship . '.labs');
                }
                if ($relationship == 'OtherMedicines') 
                {
                    $val->load($relationship);
                }
                if ($relationship == 'HomeSerivce') 
                {
                    $val->load($relationship);
                    $val->load($relationship . '.serviceProviders');
                    $val->load($relationship . '.serviceProviders.service');
                }
            }
        }
        return $orders;
    }

    public function InDemand()
    {
        $medicines = Medicines::where('in_demand', 1)->with('warnings')->orderByDesc('created_at')->get();
        $equipment = Equipments::where('in_demand', 1)->orderByDesc('created_at')->get();
        $tests = Tests::where('in_demand', 1)->orderByDesc('created_at')->get();
        $testsCat = MedicinesCategory::where('in_demand', 1)->orderByDesc('created_at')->get();
        $medCat = TestCategory::where('in_demand', 1)->orderByDesc('created_at')->get();
        $inDemand = new \stdClass();
        $inDemand->medicines = $medicines;
        $inDemand->equipments = $equipment;
        $inDemand->tests = $tests;
        $inDemand->testsCat = $testsCat;
        $inDemand->medCat = $medCat;
        return $inDemand;
    }

    public function InDemandTestCat()
    {
        return $tests = LabsTestCategory::with('testCategory')->whereHas('testCategory', function ($q) {
            $q->where('in_demand', 1);
        })->get(['id', 'lab_id', 'testcategory_id']);
    }

    public function InDemandMedCat()
    {
        return $testsCat = MedicinesCategory::where('in_demand', 1)->orderByDesc('created_at')->get();
    }
    
    public function InDemandEquipment($input)
    {
        $page = $input['page'];
        $limit = $input['limit'];
        $offset = ($page * $limit) - $limit;
        return $InDemandEquipment = Equipments::where('in_demand', 1)->offset($offset)->limit($limit)->orderByDesc('created_at')->get();
    }
}
