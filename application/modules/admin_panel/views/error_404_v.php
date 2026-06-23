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
    <title>404 - Page Not Found | <?=WEBSITE_NAME;?></title>
    <meta name="keyword" content="404">
    <meta name="description" content="Page Not Found!">

    <!-- common head -->
    <?php $this->load->view('components/_common_head'); ?>
    <!-- /common head -->
</head>

<body class="body-404">

<section class="error-wrapper">
  <i class="icon-404"></i>
  <div class="text-center">
      <h2 class="green-bg">page not found</h2>
  </div>
  <p>Something went wrong or that page doesn’t exist</p>
  <a href="<?=base_url();?>" class="back-btn"><i class="fa fa-home"></i> Home</a>
</section>

</body>
</html>