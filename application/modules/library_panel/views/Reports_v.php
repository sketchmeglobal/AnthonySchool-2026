<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 17-01-2019
 * Time: 14:33
 */
 ?>
 <?php //die(); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?=$tab_title.' | '.WEBSITE_NAME?></title>
<meta name="description" content="admin panel">

<!-- common head -->
<?php $this->load->view('components/_common_head'); //left side menu ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/multi-select/0.9.12/css/multi-select.min.css" />
<!-- /common head -->

<style type="text/css">
    .ms-container{
        width: 100% !important;
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
                             <form id="form_book_filter" method="post" action="<?=base_url('library/generate_details_print_format')?>" class="cmxform form-horizontal tasi-form" target="_blank">
                                <div class="form-group ">
                                    <div class="col-lg-1"></div>
                                    <label class="control-label col-lg-1">Select Book</label>
                                    <div class="col-lg-2">
                                        <select name="filter_book_id" class="select2 form-control round-input">
                                            <option value="">Select Book</option>
                                            <?php
                                            foreach($book_master as $val) {
                                                ?>
                                                <option value="<?=$val['BOOK_SEQ']?>"><?=   $val['Book_Name']  ?> - <?=  $val['Accession_No']  ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <label class="control-label col-lg-1">Select Class</label>
                                    <div class="col-lg-2">
                                        <select name="filter_class_id" class="select2 form-control round-input">
                                            <option value="">Select Class</option>
                                            <?php
                                            foreach($class_sec_hdr as $val) {
                                                ?>
                                                <option value="<?=$val['CS_SEQ']?>"><?=   $val['class_sec']  ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>


                                    <label class="control-label col-lg-2">Select Student</label>
                                    <div class="col-lg-2">
                                        <select name="filter_student_id" class="select2 form-control round-input">
                                            <option value="">Select Student</option>
                                            <?php
                                            foreach($student_details as $val) {
                                                ?>
                                                <option value="<?=$val['STD_SEQ']?>"><?=   $val['ST_FULL_NAME']   ?> - <?=   $val['STD_REGNO']   ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    </div>

                                    <div class="form-group ">
                                        <div class="col-sm-2"></div>
                                        <div class="col-sm-2">
                                                <input type="checkbox" id="returned" name="returned" value="1">
                                                &nbsp;<label for="returned">Returned</label><br>
                                        </div>
                                        <div class="col-sm-2">
                                                <input type="checkbox" id="not_returned" name="returned" value="0">
                                                &nbsp;<label for="returned">Not Returned</label><br>
                                        </div>
                                        <div class="col-sm-2">
                                                <input type="checkbox" id="with_fine" name="with_fine" value="1">
                                                &nbsp;<label for="returned">With Fine</label><br>
                                        </div>
                                        <div class="col-sm-2">
                                                <input type="checkbox" id="without_fine" name="with_fine" value="0">
                                                &nbsp;<label for="returned">Without Fine</label><br>
                                        </div>
                                    </div>

                                    <div class="col-lg-12" style="text-align: center;">
                                        <button name="submit" value="inst_filter" class="btn btn-primary" type="submit"> Generate Report</i></button>
                                    </div>
                            </form>
                        </div>
                        <br><br>
                        <section class="panel">
                            <header class="panel-heading"> Student Related Reports <small>(Print)</small>
                            </header>
                            <div class="panel-body">
                                <div class="col-lg-2">
                                    <a href="https://stanthonyschooledu.org/admin/student_strength">
                                        <section class="panel bg-success text-center" style="padding: 20px;">
                                            <div class="symbol">
                                                <i class="fa fa-file-pdf-o" style="font-size: 36px;"></i>
                                            </div>
                                            <div class="value white">
                                                <h3 class="timer" style="font-size: 12px; color: black;">Class Wise Strength</h3>
                                                <p></p>
                                            </div>
                                        </section>
                                    </a>
                                </div>
                                <div class="col-lg-2">
                                    <a href="https://stanthonyschooledu.org/admin/student_list">
                                        <section class="panel bg-success text-center" style="padding: 20px;">
                                            <div class="symbol">
                                                <i class="fa fa-file-pdf-o" style="font-size: 36px;"></i>
                                            </div>
                                            <div class="value white">
                                                <h3 class="timer" style="font-size: 12px; color: black;">Student List</h3>
                                                <p></p>
                                            </div>
                                        </section>
                                    </a>
                                </div>
                            </div>
                        </section>
                        
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
<script src="<?=base_url();?>assets/admin_panel/js/jquery-1.10.2.min.js"></script>
<!--<script src="--><?//=base_url();?><!--assets/admin_panel/js/jquery-migrate.js"></script>-->

<!--form validation-->
<!--<script src="--><?//=base_url();?><!--assets/admin_panel/js/jquery.validate.min.js" type="text/javascript"></script>-->
<!--form validation init-->
<!--<script src="--><?//=base_url();?><!--assets/admin_panel/js/form-validation-init.js"></script>-->

<!-- common js -->
<?php $this->load->view('components/_common_js'); //left side menu ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/multi-select/0.9.12/js/jquery.multi-select.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.4/jquery.inputmask.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.quicksearch/2.4.0/jquery.quicksearch.min.js"></script>
<!-- /common js -->

</body>
</html>

