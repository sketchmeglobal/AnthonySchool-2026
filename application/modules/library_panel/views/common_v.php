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
    <title><?=$tab_title.' | '.WEBSITE_NAME?></title>
    <meta name="description" content="admin panel">

    <!-- common head -->
    <?php $this->load->view('components/_common_head'); //left side menu ?>
    <!-- /common head -->

    <!-- Start grocerycrud JS & STYLES -->
    <?php
    if(!empty($output)){
    foreach($css_files as $file):
        ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
    <?php
    endforeach;
    foreach($js_files as $file):
    ?>
        <script src="<?php echo $file; ?>"></script>
        <?php
    endforeach;
    }
    ?>
    <!--  End grocerycrud JS & STYLES  -->
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
        <div class="page-head">
            <h3 class="m-b-less">
                <?=$menu_name;?>
            </h3>
            <!--<span class="sub-title">Welcome to Static Table</span>-->
            <div class="state-information">
                <ol class="breadcrumb m-b-less bg-less">
                    <li><a href="<?=base_url('library/dashboard');?>">Home</a></li>
                    <li class="active"> <?=$menu_name;?> </li>
                </ol>
            </div>
        </div>
        <!-- page head end-->

        <!--body wrapper start-->
        <div class="wrapper">

            <div class="row">
                <div class="col-lg-12">
                    <section class="panel">
                        <header class="panel-heading">
                            <?=$section_heading;?>
                        </header>
                        <div class="panel-body">
                            <?php echo $add_button; ?>
                            <?php echo $output; ?>
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
<!--<script src="--><?//=base_url();?><!--assets/admin_panel/js/jquery-1.10.2.min.js"></script>-->
<!--<script src="--><?//=base_url();?><!--assets/admin_panel/js/jquery-migrate.js"></script>-->
<?php
//open print page
if(isset($print)){
    ?>
    <script>window.open("<?=base_url($print);?>");</script>
    <?php
}
?>
<!-- common js -->
<!-- <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script> -->
<?php $this->load->view('components/_common_js'); //left side menu ?>
<!-- /common js -->


<script>
    //making required fields label color red
    $("span.required").parents('div.form-display-as-box').css("color", "red");

    $(document).ready(function() {
        
        if($('#field-Full_Marks').length){
                $('#field-Exam_Year').attr("onkeypress", "return event.charCode >= 48 && event.charCode <= 57");
                $('#field-Full_Marks').attr("onkeypress", "return event.charCode >= 48 && event.charCode <= 57");
        }


        //$("#crud_search").attr('type', 'submit');
        
    });


    // confirm

    $(document).on('click','.confirm', function () {
        return confirm('Are you sure want to continue?');
    });
</script>

</body>
</html>