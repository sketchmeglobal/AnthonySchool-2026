<?php


// echo $encdata;
$mainData = sbi_decrypt_aes256($encdata);

//echo "<br><pre>";



$mainData1 = explode('|', $mainData);




//echo $mainData1[0];


  $sbi_ref_no = str_replace("sbi_ref_no=","",$mainData1[0]);
 
  $status_desc = str_replace("status_desc=","",$mainData1[1]);
 
  $status = str_replace("status=","",$mainData1[2]);
 
  $amount = str_replace("amount=","",$mainData1[3]);
 
  $ref_no = str_replace("ref_no=","",$mainData1[4]);
 
  $checkSum = str_replace("checkSum=","",$mainData1[5]);
 
 
    /*foreach (explode('|', $mainData) as $key => $p){
        echo "$p<br>";
    }*/




    function sbi_decrypt_aes256($data)
{
    $key= file_get_contents("payment-key/ST_JEMS.key", true);
    $c = base64_decode($data);
    $datalength=strlen($c);
    $ivlen = openssl_cipher_iv_length($cipher="aes-256-gcm");
    $iv ='1234567890123456';
    //$iv=substr($c,0,16); --Added
    $ciphertext_raw = substr($c,16,$datalength-32);
    $aad=substr($c,$datalength-16,16); //////IMP
    $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA,$iv,$aad);  
    // echo "Decrypted Text new: $original_plaintext\n";
    return $original_plaintext;

}




?>





<div style="display:none">
    
    <form method="post" action="<?= base_url(); ?>admin/monthly_fees_payment_complete">
  <input type="hidden" id="sbi_ref_no" name="sbi_ref_no" value="<?=$sbi_ref_no?>"><br>
  
  <input type="hidden" id="status_desc" name="status_desc" value="<?=$status_desc?>"><br><br>
  
  
  <input type="hidden" id="status" name="status" value="<?=$status?>"><br><br>
  
  <input type="hidden" id="amount" name="amount" value="<?=$amount?>"><br><br>
  
  <input type="hidden" id="ref_no" name="ref_no" value="<?=$ref_no?>"><br><br>
  
  <input type="hidden" id="checkSum" name="checkSum" value="<?= trim($checkSum) ?>"><br><br>
  
  <input type="submit" id="pameny_final_insert" name="pameny_final_insert" value="Submit">
</form> 
     
</div>


<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
	
$(document).ready(function () {
    $("#pameny_final_insert").click();
});
</script>







































