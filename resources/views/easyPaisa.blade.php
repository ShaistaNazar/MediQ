<?php 

$hashRequest = '';
$hashKey = 'ee6bc08e57a0a6452f8eca6bb86737a1'; // generated from easypay account
$storeId="9752";
$amount="30.0" ;
$postBackURL="https://mediq.com.pk:44380/api/jazzCashResponse";
$orderRefNum="4545454545400";
$expiryDate="20200721 112300";
$autoRedirect=0 ;
$paymentMethod='CC_PAYMENT_METHOD';
$emailAddr='khizer.987@gmail.com';
$mobileNum="03458509233";

///starting encryption///
$paramMap = array();
$paramMap['amount']  = $amount;
$paramMap['autoRedirect']  = $autoRedirect;
$paramMap['hashKey']  = $hashKey;
$paramMap['emailAddr']  = $emailAddr;
$paramMap['expiryDate'] = $expiryDate;
$paramMap['mobileNum'] =$mobileNum;
$paramMap['orderRefNum']  = $orderRefNum;
$paramMap['paymentMethod']  = $paymentMethod;
$paramMap['postBackURL'] = $postBackURL;
$paramMap['storeId']  = $storeId;
// exit;
//Creating string to be encoded
$mapString = '';
foreach ($paramMap as $key => $val) {
      $mapString .=  $key.'='.$val.'&';
}
$mapString  = substr($mapString , 0, -1);

// Encrypting mapString
function pkcs5_pad($text, $blocksize) {
      $pad = $blocksize - (strlen($text) % $blocksize);
      return $text . str_repeat(chr($pad), $pad);
}

$ivlen = openssl_cipher_iv_length($cipher="AES-128-ECB");
                    $iv = openssl_random_pseudo_bytes($ivlen);

                  //   $crypttext = openssl_encrypt($mapString, $cipher,OPENSSL_RAW_DATA, $iv);
                  $crypttext = openssl_encrypt($mapString, $cipher, $hashKey,OPENSSL_RAW_DATA, $iv);


                    $hashRequest = base64_encode($crypttext);
// end encryption;
?>
<html>
<title>Easy Pay</title>
<body>
<form action=" https://43.224.236.206/easypay/Index.jsf" method="POST" id="easyPayStartForm">
<input name="storeId" value="<?php echo $storeId; ?>" hidden = "true"/>
<input name="amount" value="<?php echo $amount; ?>" hidden = "true"/>
<input name="postBackURL" value="<?php echo $postBackURL; ?>" hidden = "true"/>
<input name="orderRefNum" value="<?php echo $orderRefNum; ?>" hidden = "true"/>
<input type ="hidden" name="expiryDate" value="<?php echo $expiryDate; ?>">
<input type="hidden" name="autoRedirect" value="<?php echo $autoRedirect; ?>" >
<input type ="hidden" name="paymentMethod" value="<?php echo $paymentMethod; ?>">
<input type ="hidden" name="merchantHashedReq" value="<?php echo $hashRequest; ?>">
<button type="submit">Pay via EasyPaisa</button>

</form>
</body>
</html>

