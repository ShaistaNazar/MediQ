<?php
// include('header.html');
//  include('Credentials.php'); 
// print_r($order_id);echo '---';
// print_r($total_amount);echo '---';
// print_r($method);
// die();
$MerchantID    = "00160248"; //Your Merchant from transaction Credentials
$Password      = "u311907tg3"; //Your Password from transaction Credentials
$ReturnURL     = "https://mediq.com.pk:44380/api/jazzCashResponse"; //Your Return URL
$HashKey = "1t2ud1sv8t"; //Your HashKey integrity salt from transaction Credentials	
$PostURL = "https://payments.jazzcash.com.pk/CustomerPortal/transactionmanagement/merchantform";

date_default_timezone_set("Asia/karachi");
$Amount = $total_amount * 100; //Last two digits will be considered as Decimal
$BillReference = $order_id;
$Description = "Thankyou for using Jazz Cash";
$Language = "EN";
$TxnCurrency = "PKR";
$TxnDateTime = date('YmdHis');
$TxnExpiryDateTime = date('YmdHis', strtotime('+8 Days'));
$TxnRefNumber = "TXN" . date('YmdHis');
$TxnType = $method;
$Version = '1.1';
$SubMerchantID = "";
$DiscountedAmount = "";
$DiscountedBank = "";
$ppmpf_1 = "03315035877";
$ppmpf_2 = "";
$ppmpf_3 = "";
$ppmpf_4 = "";
$ppmpf_5 = "";

$HashArray = [$Amount, $BillReference, $Description, $DiscountedAmount, $DiscountedBank, $Language, $MerchantID, $Password, $ReturnURL, $TxnCurrency, $TxnDateTime, $TxnExpiryDateTime, $TxnRefNumber, $TxnType, $Version, $ppmpf_1, $ppmpf_2, $ppmpf_3, $ppmpf_4, $ppmpf_5];

$SortedArray = $HashKey;
for ($i = 0; $i < count($HashArray); $i++) {
	if ($HashArray[$i] != 'undefined' and $HashArray[$i] != null and $HashArray[$i] != "") {

		$SortedArray .= "&" . $HashArray[$i];
	}
}
$Securehash = hash_hmac('sha256', $SortedArray, $HashKey);
?>
<div id="header"></div>
<form method="post" action="<?php echo $PostURL; ?>" />

<input type="hidden" name="pp_Version" value="<?php echo $Version; ?>" />
<input type="hidden" name="pp_TxnType" value="<?php echo $TxnType; ?>" />
<input type="hidden" name="pp_Language" value="<?php echo $Language; ?>" />
<input type="hidden" name="pp_MerchantID" value="<?php echo $MerchantID; ?>" />
<input type="hidden" name="pp_SubMerchantID" value="<?php echo $SubMerchantID; ?>" />
<input type="hidden" name="pp_Password" value="<?php echo $Password; ?>" />
<input type="hidden" name="pp_TxnRefNo" value="<?php echo $TxnRefNumber; ?>" />
<input type="hidden" name="pp_Amount" value="<?php echo $Amount; ?>" />
<input type="hidden" name="pp_TxnCurrency" value="<?php echo $TxnCurrency; ?>" />
<input type="hidden" name="pp_TxnDateTime" value="<?php echo $TxnDateTime; ?>" />
<input type="hidden" name="pp_BillReference" value="<?php echo $BillReference ?>" />
<input type="hidden" name="pp_Description" value="<?php echo $Description; ?>" />
<input type="hidden" id="pp_DiscountedAmount" name="pp_DiscountedAmount" value="<?php echo $DiscountedAmount ?>">
<input type="hidden" id="pp_DiscountBank" name="pp_DiscountBank" value="<?php echo $DiscountedBank ?>">
<input type="hidden" name="pp_TxnExpiryDateTime" value="<?php echo  $TxnExpiryDateTime; ?>" />
<input type="hidden" name="pp_ReturnURL" value="<?php echo $ReturnURL; ?>" />
<input type="hidden" name="pp_SecureHash" value="<?php echo $Securehash; ?>" />
<input type="hidden" name="ppmpf_1" value="<?php echo $ppmpf_1; ?>" />
<input type="hidden" name="ppmpf_2" value="<?php echo $ppmpf_2; ?>" />
<input type="hidden" name="ppmpf_3" value="<?php echo $ppmpf_3; ?>" />
<input type="hidden" name="ppmpf_4" value="<?php echo $ppmpf_4; ?>" />
<input type="hidden" name="ppmpf_5" value="<?php echo $ppmpf_5; ?>" />
<button id="submit" type="submit" class="jazzCash"><h4 class="heightFixes">Pay via JazzCash</h4></button>
</form>
<style>
	button#submit {
	color: white;
    border: 2px solid;
    background-color: #432f76;
    border-radius: 70px;
    width: 65%;
    padding: 0px;
    font-size: 16px;
    margin: 26px auto;
    display: block;
    font-family: sans-serif;
    font-weight: bold;
    height: 15%
	}
    .heightFixes{
        margin: 10 auto;
    }

    @media screen and (min-width: 600px) {
        button#submit {
    font-size: 40px;
	}
}

</style>