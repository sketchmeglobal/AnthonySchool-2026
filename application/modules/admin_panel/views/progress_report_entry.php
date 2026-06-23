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
                    <li><a href="<?=base_url('admin/dashboard');?>">Home</a></li>
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

<!-- common js -->
<!-- <script src="https://code.jquery.com/jquery-3.6.1.min.js"></script> -->
<?php $this->load->view('components/_common_js'); //left side menu ?>
<!-- /common js -->

<script>

    $("#field-GENERAL_REMARKS_FILLED").chosen().change(function(){
        $("#field-GENERAL_REMARKS").val($(this).val())
    })

    $("#field-CLASS_SEC").chosen().change(function(){
        $cs = $(this).val()
        $term = $("#field-EXAM_TERM").chosen().val()
        // console.log("..." +$term)
        $.ajax({
            url: "<?=base_url();?>admin/fetch_student_on_class_sec",
            type: "post",
            data: {cs: $cs, term: $term},
            dataType: 'json',
            success: function(rdata) {
                console.log(rdata)
                $('#field-STUDENT').chosen('destroy');  
                $('#field-STUDENT').prop("selectedIndex", -1);   
                $('#field-STUDENT').html('');
                $.each(rdata, function (index, itemData) {
                    $html = "<option value="+itemData.STD_SEQ+">"+itemData.STD_ROLLNO + ' - ' + itemData.ST_FULL_NAME +"<option>";
                    $("#field-STUDENT").append($html);
                });
                $('#field-STUDENT').chosen();
            },
            error: function(){},
            complete: function(){}
        });
    })
</script>


</body>
</html>