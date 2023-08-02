<?php

namespace App\Http\Services;

use OneSignal;
use Illuminate\Support\Facades\DB;
use App\Repositories\NotificationsRepository;
use Exception;

/*
 * NotificationService service provides services for Notifications.
*/
class NotificationService
{
    
    public function updateStatus($id, $status)
    {

        $notification = DB::table('user_notifications')->find($id);

        if(!$notification)
        {
            return false;
        }
        if($notification->note_status != $status)
        {
            DB::table('user_notifications')->where('id', $id)->update(['note_status' => $status]);
        }
        $notification->note_status = $status;
        return $notification;
    }

    static public function notify($user,$data,$type)
    {
        $order_number    =    $data['order_number'];
        if(isset($data['price']))
        {
            $total_amount    =    $data['price'];
        }
        if(isset($data['total_amount']))
        {
            $total_amount    =    $data['total_amount'];
        }
        else
        {
            $total_amount    =    0;
        }
        // echo $total_amount;die('00');
        $data['content_available'] = true;
        $data['type'] = $type;
        $data['note_body'] = "mediQ: Your New Order has been placed successfully";
        // die('00');
        if(isset($user->player_id) && $user->player_id!='')
        {
            // $user_plyerId =  'e7cc91d1-2580-4f6a-8a67-43921f183b72';
            $user_plyerId = $user->player_id;
            try
            {
              OneSignal::sendNotificationToUser(

                $data['type'],
                $user_plyerId,
                $url = null,
                $data = $data,
                $buttons = null,
                $schedule = null
            );
            
        }
        catch(Exception $e)
        {
            return $e;
        }
        
        $input=['note_type'=>1,'user_id'=>$user->id,'note_title'=>$data['note_body'],'note_body'=>$data['note_body'],'status_id'=>'order', 'note_heading' => $order_number,'total_amount' => $total_amount];

        NotificationsRepository::createNew($input);        
        return true;
    }
}

    public function reMatchNotify($user, $data, $type = 'rematch_request', $fromUser,$is_unmatched,$is_request) 
    {    
        $data['type'] = snake_case($type);
        $data['from_id'] = $fromUser->id;
        $data['is_unmatched'] = $is_unmatched;
        $data['is_request'] = $is_request;
        // $plyer_idm='d1dc855d-2e46-4352-9b81-ae7461c4c51b';
        $plyer_idm=$user->plyer_id;
        
        if($plyer_idm !='')
        {
            return OneSignal::sendNotificationToUser(
                "Mingles: " . $fromUser->user_name . ' has requested with you ',
                $plyer_idm,
                $url = null,    
                $data = $data,
                $buttons = null,
                $schedule = null
 
            );
        }       
    }
}
