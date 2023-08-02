<?php 
 $HashKey= "821u87t8w8"; //Your Hash Key
 
 $ResponseCode =$_POST['pp_ResponseCode'];
 $ResponseMessage = $_POST['pp_ResponseMessage'];
 $Response="";$comment="";
	$ReceivedSecureHash =$_POST['pp_SecureHash'];
	$sortedResponseArray = array();
			if (!empty($_POST)) {
				foreach ($_POST as $key => $val) {
					$comment .= $key . "[" . $val . "],<br/>";
					$sortedResponseArray[$key] = $val;
				}
			}
			ksort($sortedResponseArray);			
			unset($sortedResponseArray['pp_SecureHash']);
			$Response=$HashKey;
			foreach ($sortedResponseArray as $key => $val) {		
						if ($val!=null and $val!="") {
							$Response.='&'.$val;				
						}
			}	
				$GeneratedSecureHash= hash_hmac('sha256', $Response, $HashKey);					
					if (strtolower($GeneratedSecureHash) == strtolower($ReceivedSecureHash)) {
							$txnRefNo = $_POST['pp_TxnRefNo'];
							$reqAmount = $_POST['pp_Amount']/100;
							$reqDatetime = $_POST['pp_TxnDateTime'];
							$reqBillref = $_POST['pp_BillReference'];
							$reqRetrivalRefNo = $_POST['pp_RetreivalReferenceNo'];
				
							
						 if($ResponseCode == '000'||$ResponseCode == '121'||$ResponseCode == '200'){
								echo "Thanks for your Order, You JazzCash Payment of RS:".$reqAmount."has beed Successfull. Your Order ID is".$txnRefNo;
								echo $ResponseCode."Transaction Message=".$ResponseMessage;
							 // do your handling for success
							} 
							else  if($ResponseCode == '124'||$ResponseCode == '210') {
								echo "Your voucher No is:".$reqRetrivalRefNo." of amount ".$reqAmount." has been successfully generated. Visit any JazzCash shop and pay the amount before the expiry date";
								echo $ResponseCode."Transaction Message=".$ResponseMessage;
							// do your handling for pending
							}
							else {
								  echo "Sorry, your Payment of RS:".$reqAmount."against order no".$txnRefNo."has been Failed. please try again.";
								  echo $ResponseCode."Transaction Message=".$ResponseMessage;
								  // do your handling for faliure
							}														 
							
						}
										
					else {
						echo "mismatched, marked it suspicious or reject it";				
						}							 
?>