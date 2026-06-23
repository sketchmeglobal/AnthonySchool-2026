

 <?php  ?>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
</head>
<body>
	<!--https://uatmerchant.onlinesbi.sbi/merchant/merchantprelogin.htm-->

	<form class="cmxform form-horizontal tasi-form" id="" method="post" action="https://merchant.onlinesbi.sbi/merchant/merchantprelogin.htm ">
                                    
    <?php


    // echo $result; $std_id
    
        $amnt = $net_fees;
        $std_id = rand(0,1000);
        $str = 'ref_no='.$std_id.'|amount='.$amnt.'|returnurl=https://www.stjemskatwa.org/portal/admin/monthly_fees_payment_insert';
        $checkSum = hash('sha256', $str);
        $transString = 'ref_no='.$std_id.'|amount='.$amnt.'|returnurl=https://www.stjemskatwa.org/portal/admin/monthly_fees_payment_insert|checkSum='.$checkSum;
        $encrypted_code = sbi_encrypt_aes256($transString, $aad='');
        $final_enc_code = $encrypted_code;
    
    ?>
    
    <input value="<?= $final_enc_code; ?>" id="encdata" name="encdata" type="hidden"/>
    <input type="hidden" name="merchant_code" value="ST_JEMS" >
    <input type="submit" id="sub" value="Submit" style="display:none;"  name="Submit">
</form>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
	
$(document).ready(function () {
    $("#sub").click();
});
</script>




<?php 
function sbi_encrypt_aes256($data,$aad)
 {
    $key= file_get_contents("payment-key/ST_JEMS.key", true);
    $iv = '1234567890123456';
    $encryptedString = openssl_encrypt($data, "aes-256-gcm", $key,$options=OPENSSL_RAW_DATA, $iv,$aad);
    $finalEncryption= base64_encode($iv . $encryptedString . $aad);
    return $finalEncryption;
    
}

?>
</body>
</html>