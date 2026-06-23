<?php
//echo "Payment Complete"."<br>";




// echo $encdata;

/*print_r (sbi_decrypt_aes256($encdata));

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
*/
?>
<html>
  <head>
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,400i,700,900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  </head>
    <style>
      body {
        text-align: center;
        padding: 40px 0;
        background: #EBF0F5;
      }
      
      button {
  background-color: #008CBA; /* Blue */
  border: none;
  color: white;
  padding: 20px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 4px 2px;
  cursor: pointer;
  border-radius: 8px;
}
        h1 {
          color: #88B04B;
          font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
          font-weight: 900;
          font-size: 40px;
          margin-bottom: 10px;
        }
        p {
          color: #404F5E;
          font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
          font-size:20px;
          margin: 0;
        }
      i {
        color: #9ABC66;
        font-size: 100px;
        line-height: 200px;
        margin-left:-15px;
      }
      .card {
        background: white;
        padding: 60px;
        border-radius: 4px;
        box-shadow: 0 2px 3px #C8D0D8;
        display: inline-block;
        margin: 0 auto;
      }
    </style>
    <body>
      <div class="card">
      <div style="border-radius:200px; height:200px; width:200px; background: #F8FAF5; margin:0 auto;">
        <i class="fa fa-check" style="font-size: 11rem;"></i>
      </div>
        <h1>Payment Completed <br> Thankyou </h1> 
        
        <p>Your refarence number: <b><?=$sbi_ref_no?></b>  for this payment .</p>
        <!--<p>Your refarence number: for this payment ;<br/> we'll be in touch shortly!</p>-->
        <a href="<?=base_url('admin/dashboard')?>"><button>Back</button> </a>
      </div>
      
      <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
	
$(document).ready(function () {
    //$("#pameny_final_insert").click();
});
</script>
    </body>
</html>