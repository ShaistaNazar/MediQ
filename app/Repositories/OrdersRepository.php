<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Orders;
use App\OrderTemp;
use Illuminate\Support\Facades\DB;
use App\Repositories\BaseRepository;
use App\Jobs\Notifications;
use Auth;

/**
 * Class UserRepository
 * @package 
 * @version 
 */

/*
 * OrdersRepository exteds BaseRepository and provide repository layer for Orders.
*/
class OrdersRepository extends BaseRepository
{
    /**
     * Configure the Model
     **/
    public function model()
    {
        return Orders::class;
    }

    public function getAllOrders()
    {
        return Orders::all();
    }
    
    public function makePromotion($request)
    {
        $orderInWeek = $request['orderInWeek'];
        $percentage = $request['percentage'];
        $date = new Carbon; //  DateTime string will be 2014-04-03 13:57:34
        $date->subDays(7); // or $date->subDays(7),  2014-03-27 13:58:25

        $inDemand = Orders::SELECT('user_id', DB::raw("COUNT(user_id) as total"))->where('created_at', '>', $date->toDateTimeString())
            ->groupBy('user_id')->orderByDesc('created_at')->get();
        $promotionCode = mt_rand(100000, 999999);
        foreach ($inDemand as $key => $value) {
            if ($value['total'] > $orderInWeek) {
                $addedPromotion = Promotion::create([
                    'user_id' => $value['user_id'],
                    'code' => $promotionCode,
                    'percentage' => $percentage
                ]);
            }
        }
        return $inDemand;
    }
}
