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
<html lang="en">
<head>
    <title>JS Disabled | <?=WEBSITE_NAME;?></title>
    <meta name="keyword" content="js disabled">
    <meta name="description" content="javascript is disabled in this browser, kindly enable javascript!">

    <!-- common head -->
    <?php $this->load->view('components/_common_head'); //left side menu ?>
    <!-- /common head -->
</head>

<body class="body-500">

<div class="container">

    <section class="error-wrapper">
        <i class="icon-no_js"></i>
        <div class="text-center">
            <h2 class="purple-bg">JavaScript Disabled</h2>
        </div>
        <p>
            Why did you disable JavaScript in that browser?
            <br/>
            Kindly enable JavaScript for smoother performance &amp; full functionality!
            <br/>
            <a href="https://www.google.com/search?q=enable+javascript+in+browser" target="_blank">Here are the instructions how to enable JavaScript in your web browser</a>
        </p>
        <?php
        if (isset($referrer_url)) {
            ?>
            <a href="<?=$referrer_url;?>" class="back-btn">Previous Page</a>
            <?php
        }
        ?>
    </section>

</div>

</body>
</html>
