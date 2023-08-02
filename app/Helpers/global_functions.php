<?php

  // use Illuminate\Support\Facades\Log;
  use Carbon\Carbon;
  // use Auth;
  // use App\Models\FeedBack;
  // use App\Models\User;
  // $userName  = "923456899831";        
  // $password  = "Blueocean91@";
  // $planetbeyondApiUrl = "https://telenorcsms.com.pk:27677/corporate_sms2/api/auth.jsp?msisdn=923456899831&password=Waqaskhanpitafidevbatch123";
  // $planetbeyondApiSendSmsUrl="https://telenorcsms.com.pk:27677/corporate_sms2/api/sendsms.jsp?session_id=#session_id#&to=#to_number#&text=#message_to_send#";
 
 
 
  function responseMsg($rescode , $message , $param , $values = false)
  {
    if(!$values){
      $values = new \stdClass();
    }
    $message=ucfirst($message);
    
    return response()->json([
        'ResponseHeader' => [
          'ResponseCode' => $rescode,
          'ResponseMessage' =>  $message

          ],
          
          'data' => $values
          
        ], 200);
  }

  function orderNumber(){

    return $orderNumber=Auth::id().time();
  }
 


