<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 16-02-2019
 * Time: 10:57
 */
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Change Password | <?=WEBSITE_NAME_SHORT;?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Change Password">
    <meta name="author" content="Pran Krishna Das">

    <link href="<?=base_url();?>assets/img/favicon.ico"  rel="shortcut icon"/>
    <link href="<?=base_url();?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?=base_url();?>assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?=base_url();?>assets/login/css/login-css.css" rel="stylesheet" type="text/css"/>
</head>
<body>

<div class="wrapper">
    <div class="container">

        <image src="<?=base_url();?>assets/login/img/change_pass_logo.png" width="75"></image>
        <h1>Change Password</h1>

        <form class="form" action="<?=base_url('update_password');?>" method="post">
            <input type="text" name="otp" value="<?=$otp;?>" placeholder="OTP" required="">
            <input type="password" name="new_pass" placeholder="New Password" required="">
            <input type="password" name="conf_pass" placeholder="Confirm Password" required="">
            <button type="submit" name="submit" value="change_pass">Change Password <i class="fa fa-key"></i></button>
        </form>

        <div class="small">
            <?php echo date('Y'); ?> &copy; <?=WEBSITE_NAME;?>
        </div>
    </div>

    <ul class="bg-bubbles">
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
    </ul>
</div>

<!--core jquery-->
<script src="<?=base_url();?>assets/login/js/jquery-2.1.4.min.js" type="text/javascript"></script>
<!--/.core jquery-->
<!--notification components-->
<link href="<?=base_url();?>assets/login/css/jquery.gritter.css" rel="stylesheet" type="text/css" />
<script src="<?=base_url();?>assets/login/js/jquery.gritter.min.js" type="text/javascript"></script>
<!--/.notification components-->

<?php
if($this->session->flashdata('msg')) {
    ?>
    <script> //notification pop-up
        $.gritter.add({
            title: '<?=$this->session->flashdata('title');?>',
            text: '<?=$this->session->flashdata('msg');?>',
            image: '<?=base_url();?>assets/login/img/info.png',
            sticky: false,
        });
    </script>
    <?php
}

//notification for validation errors during login
if(validation_errors()) {
    ?>
    <script> //notification pop-up
        $.gritter.add({
            title: 'Knock Knock',
            text: '<?=form_error('new_pass','<i class="fa fa-exclamation-triangle"></i> ')
            .form_error('conf_pass','<br /><i class="fa fa-exclamation-triangle"></i> ');?>',
            image: '<?=base_url();?>assets/login/img/info.png',
            sticky: false,
            time: '30000' //30 second
        });
    </script>
    <?php
}
?>

</body>
</html>