<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 09-07-17
 * Time: xx:xx
 */
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login | <?=WEBSITE_NAME_SHORT;?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login">
    <meta name="author" content="Pran Krishna Das">

    <link href="<?=base_url();?>assets/img/favicon.ico"  rel="shortcut icon"/>
    <link href="<?=base_url();?>assets/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?=base_url();?>assets/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?=base_url();?>assets/login/css/login-css.css" rel="stylesheet" type="text/css"/>
</head>
<body>

<div class="wrapper">
    <div class="container">

        <image src="<?=base_url();?>assets/login/img/admin_login_logo.png" width="75"></image>
        <h1>LIBRARY</h1>

        <form class="form" action="<?=base_url('library');?>" method="post">
            <input type="text" name="username" placeholder="Username / Email" required="">
            <input type="password" name="pass" placeholder="Password" required="">
            <button type="submit" name="submit" value="login">Log Me In <i class="fa fa-sign-in"></i></button>
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
            text: '<?=form_error('username','<i class="fa fa-exclamation-triangle"></i> ')
            .form_error('pass','<br /><i class="fa fa-exclamation-triangle"></i> ');?>',
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
