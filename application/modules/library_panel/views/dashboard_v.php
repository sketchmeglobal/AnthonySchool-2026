    <!DOCTYPE html>
    <html lang="en">
    <head>
    <title>Dashboard | <?=WEBSITE_NAME;?></title>
    <meta name="keyword" content="user dashboard">
    <meta name="description" content="account statistic">

    <!-- common head -->
    <?php $this->load->view('components/_common_head'); ?>
    <!-- /common head -->

    <!--easy pie chart-->
    <link href="js/jquery-easy-pie-chart/jquery.easy-pie-chart.css" rel="stylesheet" type="text/css" media="screen" />

    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">

    <style>
    ui-accordion-header ui-helper-reset ui-state-default form-title >> background: transparent!important;
    border: none!important;
    ui-widget-content ui-corner-all datatables >> width: 100%;
    form-control >> height: 30px;
    .ui-button, .ui-button:link, .ui-button:visited, .ui-button:hover, .ui-button:active >> border-radius: 0!important;
    ul.for_institute_ul           {
    list-style-type: none!important;
    }

    ul.for_institute_ul li:before {
    content: "\00BB \0020";
    font-size: 20px;
    }
    ul.for_institute_ul li {
    font-size: 16px;
    font-weight: bold;
    line-height: 26px;
    }
    .box_shadow {
    margin-top: 6px;
    font-size: 16px;
    font-weight: bold;
    line-height: 26px;
    padding : 20px;
    box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 2px 6px 2px;
    height: 92px;
    background: white;
    text-align: center;
    }
    .box_shadow a {
    position: relative;
    top: 20%;
    }
    </style>
    </head>

    <body class="sticky-header">

    <section>
    <!-- sidebar left start (Menu)-->
    <?php $this->load->view('components/left_sidebar'); //left side menu ?>
    <!-- sidebar left end (Menu)-->

    <!-- body content start-->
    <div class="body-content" style="min-height: 1500px;">

    <!-- header section start-->
    <?php $this->load->view('components/top_menu'); ?>
    <!-- header section end-->

    <!-- page head start-->
    <!-- page head end-->

    <!--body wrapper start-->
    <div class="wrapper">

    <?php
    $this->db->select("
    COUNT(*) as 'total',
    COUNT(IF(returned_or_not = '1', 1, NULL)) as 'returned',
    COUNT(IF(returned_or_not = '0', 1, NULL)) as 'pending'
    ");
    $book_issued_count_values = $this->db->get('book_issued')->row();

    $this->db->select("
    COUNT(*) as 'total_book'");
    $book_values = $this->db->get('book_master')->row();
    $this->db->select("
    COUNT(*) as 'total_available_book_number'");
    $book_available_count_values = $this->db->get_where('book_master', array('Available_Copies >' => 0))->row();
    ?>
    <div class="row state-overview">
    <div class="col-lg-3 col-sm-6">
    <section class="panel" style="background: #102542; color: white;">
    <div class="symbol">
    <i class="fa fa-book"></i>
    </div>
    <div class="value">
    <h1 class="timer" data-from="0" data-to="<?=$book_values->total_book?>" data-speed="2000"><?=$book_values->total_book?></h1>
    <p>Total Books<br/></p>
    </div>
    </section>
    </div>

    <div class="col-lg-3 col-sm-6">
    <section class="panel" style="background: #2f9c66; color: white;">
    <div class="symbol">
    <i class="fa fa-server"></i>
    </div>
    <div class="value">
    <h1 class="timer" data-from="0" data-to="<?=$book_available_count_values->total_available_book_number?>" data-speed="2000"><?=$book_available_count_values->total_available_book_number?></h1>
    <p>Available Books</p>
    </div>
    </section>
    </div>

    <div class="col-lg-3 col-sm-6">
    <section class="panel" style="background: #2290f4; color: white;">
    <div class="symbol">
    <i class="fa fa-check"></i>
    </div>
    <div class="value">
    <h1 class="timer" data-from="0" data-to="<?=$book_issued_count_values->total?>" data-speed="2000"><?=$book_issued_count_values->total?></h1>
    <p>Book Issued</p>
    </div>
    </section>
    </div>

    <div class="col-lg-3 col-sm-6">
    <section class="panel" style="background: #ff4d00; color: white;">
    <div class="symbol">
    <i class="fa fa-exchange"></i>
    </div>
    <div class="value">
    <h1 class="timer" data-from="0" data-to="<?=$book_issued_count_values->returned?>" data-speed="2000"><?=$book_issued_count_values->returned?></h1>
    <p>Book Returned</p>
    </div>
    </section>
    </div>
    </div>

    </div>
    <!--body wrapper end-->

    <!--footer section start-->
    <?php $this->load->view('components/footer'); ?>
    <!--footer section end-->

    </div>
    <!-- body content end-->
    </section>

    <!-- Placed js at the end of the document so the pages load faster -->
    <script src="<?=base_url()?>assets/admin_panel/js/jquery-1.10.2.min.js"></script>
    <!-- common js -->
    <?php $this->load->view('components/_common_js'); //left side menu ?>

    <!--confirmation-->
    <script src="<?=base_url();?>assets/admin_panel/js/bootstrap-confirmation.min.js"></script>
    <!--jquery countTo-->
    <script src="<?=base_url();?>assets/admin_panel/js/jquery-countTo/jquery.countTo.js"  type="text/javascript"></script>
    <!--easy pie chart-->
    <!--<script src="--><?//=base_url();?><!--assets/admin_panel/js/flot-chart/jquery.flot.pie.js"></script>-->
    <script src="<?=base_url();?>assets/admin_panel/js/jquery-easy-pie-chart/jquery.easy-pie-chart.js"></script>
    <script src="<?=base_url();?>assets/admin_panel/js/easy-pie-chart.js"></script>

    <script>
    $('.timer').countTo();
    </script>

    </body>
    </html>