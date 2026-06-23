<?php
/**
 * Coded by: Pran Krishna Das
 * Social: https://sketchmeglobal.com
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

                            <?php
                            if($form_type == 'student_related_report') {
                                $user_type = $this->session->usertype;
                                //fetch user access permission
                                $this->db->where('user_id', $this->session->user_id);
                                $rs_user_prm = $this->db->get('user_permissions')->result_array();
                                $prm_arr = array_column($rs_user_prm, 'permission', 'menu_id');

                                if ($user_type == 1 || $prm_arr[6] == 1) {
                                    ?>
                                    <div class="col-lg-2">
                                        <a href="<?= base_url(); ?>admin/std_reg_report">
                                            <section class="panel bg-success text-center" style="padding: 20px;">
                                                <div class="symbol">
                                                    <i class="fa fa-file-pdf-o" style="font-size: 36px;"></i>
                                                </div>
                                                <div class="value white">
                                                    <h3 class="timer" style="font-size: 12px; color: black;">Student Reg.
                                                        Report</h3>
                                                    <p></p>
                                                </div>
                                            </section>
                                        </a>
                                    </div>
                                    <?php
                                }
                                if ($user_type == 1 || $prm_arr[7] == 1) {
                                    ?>
                                    <div class="col-lg-2">
                                        <a href="<?= base_url(); ?>admin/std_consc_report">
                                            <section class="panel bg-success text-center" style="padding: 20px;">
                                                <div class="symbol">
                                                    <i class="fa fa-file-pdf-o" style="font-size: 36px;"></i>
                                                </div>
                                                <div class="value white">
                                                    <h3 class="timer" style="font-size: 12px; color: black;">Concession
                                                        Report</h3>
                                                    <p></p>
                                                </div>
                                            </section>
                                        </a>
                                    </div>
                                    <?php
                                }
                                if ($user_type == 1 || $prm_arr[8] == 1) {
                                    ?>
                                    <div class="col-lg-2">
                                        <a href="<?= base_url(); ?>admin/std_fees_ledger_report">
                                            <section class="panel bg-success text-center" style="padding: 20px;">
                                                <div class="symbol">
                                                    <i class="fa fa-file-pdf-o" style="font-size: 36px;"></i>
                                                </div>
                                                <div class="value white">
                                                    <h3 class="timer" style="font-size: 12px; color: black;">Paid/Due Fees Ledger</h3>
                                                    <p></p>
                                                </div>
                                            </section>
                                        </a>
                                    </div>
                                    <?php
                                }
                                if ($user_type == 1 || $prm_arr[9] == 1) {
                                    ?>
                                    <div class="col-lg-2">
                                        <a href="<?= base_url(); ?>admin/student_strength">
                                            <section class="panel bg-success text-center" style="padding: 20px;">
                                                <div class="symbol">
                                                    <i class="fa fa-file-pdf-o" style="font-size: 36px;"></i>
                                                </div>
                                                <div class="value white">
                                                    <h3 class="timer" style="font-size: 12px; color: black;">Class Wise
                                                        Strength</h3>
                                                    <p></p>
                                                </div>
                                            </section>
                                        </a>
                                    </div>
                                    <?php
                                }
                                if ($user_type == 1 || $prm_arr[10] == 1) {
                                    ?>
                                    <div class="col-lg-2">
                                        <a href="<?= base_url(); ?>admin/student_list">
                                            <section class="panel bg-success text-center" style="padding: 20px;">
                                                <div class="symbol">
                                                    <i class="fa fa-file-pdf-o" style="font-size: 36px;"></i>
                                                </div>
                                                <div class="value white">
                                                    <h3 class="timer" style="font-size: 12px; color: black;">Student
                                                        List</h3>
                                                    <p></p>
                                                </div>
                                            </section>
                                        </a>
                                    </div>
                                    <?php
                                }
                               if ($user_type == 1 || $prm_arr[10] == 1) {
                                    ?>
                                    <div class="col-lg-2">
                                        <a href="<?= base_url(); ?>admin/student_rank_list">
                                            <section class="panel bg-success text-center" style="padding: 20px;">
                                                <div class="symbol">
                                                    <i class="fa fa-file-pdf-o" style="font-size: 36px;"></i>
                                                </div>
                                                <div class="value white">
                                                    <h3 class="timer" style="font-size: 12px; color: black;">Student Rank
                                                        List</h3>
                                                    <p></p>
                                                </div>
                                            </section>
                                        </a>
                                    </div>
                                    <?php
                                }
                                if ($user_type == 1 || $prm_arr[10] == 1) {
                                    ?>
                                    <div class="col-lg-2">
                                        <a href="<?= base_url(); ?>admin/class_subject_topper">
                                            <section class="panel bg-success text-center" style="padding: 20px;">
                                                <div class="symbol">
                                                    <i class="fa fa-file-pdf-o" style="font-size: 36px;"></i>
                                                </div>
                                                <div class="value white">
                                                    <h3 class="timer" style="font-size: 12px; color: black;">Class Wise Subject Topper</h3>
                                                    <p></p>
                                                </div>
                                            </section>
                                        </a>
                                    </div>
                                    <?php
                                }
                                if ($user_type == 1 || $prm_arr[66] == 1) {
                                    ?>
                                    <div class="col-lg-2">
                                        <a href="<?= base_url(); ?>admin/due_undertaking_report">
                                            <section class="panel bg-success text-center" style="padding: 20px;">
                                                <div class="symbol">
                                                    <i class="fa fa-file-pdf-o" style="font-size: 36px;"></i>
                                                </div>
                                                <div class="value white">
                                                    <h3 class="timer" style="font-size: 12px; color: black;">Due Undertaking Report</h3>
                                                    <p></p>
                                                </div>
                                            </section>
                                        </a>
                                    </div>
                                    <?php
                                }
                                if ($user_type == 1 || $prm_arr[66] == 1) {
                                    ?>
                                    <div class="col-lg-2">
                                        <a href="<?= base_url(); ?>admin/notice_report">
                                            <section class="panel bg-success text-center" style="padding: 20px;">
                                                <div class="symbol">
                                                    <i class="fa fa-file-pdf-o" style="font-size: 36px;"></i>
                                                </div>
                                                <div class="value white">
                                                    <h3 class="timer" style="font-size: 12px; color: black;">Notice Report</h3>
                                                    <p></p>
                                                </div>
                                            </section>
                                        </a>
                                    </div>
                                    <?php
                                }
                            }
                            ?>



                            <?php
                            if($form_type == 'std_reg_report') { //student registration report
                                ?>
                                <form class="cmxform form-horizontal tasi-form" id="std_reg_report" method="post" action="<?= base_url(); ?>admin/print_std_reg_report" >
                                    <div class="form-group">
                                        <label for="rpt_type" class="control-label col-lg-2">Report Type <small>(Optional)</small></label>
                                        <div class="col-lg-3 iconic-input">
                                            <select class="form-control" id="rpt_type" name="rpt_type" onchange="(this.value == 'dob')?document.getElementById('mnth').style.display = 'block':document.getElementById('mnth').style.display = 'none'">
                                                <option value="">-- Select --</option>
                                                <option value="tel">Telephone Register</option>
                                                <option value="dob">Date of Birth</option>
                                                <option value="pro">Promotion Status</option>
                                                <option value="aadhaar">Aadhaar & Banglar Shiksha ID</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-3 iconic-input" id="mnth" style="display: none;">
                                            <select class="form-control" id="dob_month" name="dob_month">
                                                <option value="all">All</option>
                                                <option value="01">January</option>
                                                <option value="02">February</option>
                                                <option value="03">March</option>
                                                <option value="04">April</option>
                                                <option value="05">May</option>
                                                <option value="06">June</option>
                                                <option value="07">July</option>
                                                <option value="08">August</option>
                                                <option value="09">September</option>
                                                <option value="10">October</option>
                                                <option value="11">November</option>
                                                <option value="12">December</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- CLEAN FILTER DESIGN -->
                                    <div class="form-group" style="border-top: 1px solid #eee; padding-top: 20px; margin-top: 25px;">
                                        <label class="control-label col-lg-2" style="color: #666; font-weight: 600;">Search Filters</label>
                                        <div class="col-lg-3">
                                            <input type="text" class="form-control" id="reg_number" name="reg_number" placeholder="Registration Number" style="border-radius: 4px;">
                                        </div>
                                        <div class="col-lg-3">
                                            <input type="text" class="form-control" id="telephone" name="telephone" placeholder="Telephone Number" style="border-radius: 4px;">
                                        </div>
                                        <div class="col-lg-3">
                                            <input type="text" class="form-control" id="official_telephone" name="official_telephone" placeholder="Official Telephone" style="border-radius: 4px;">
                                        </div>
                                    </div>
                                    <!-- END CLEAN FILTER DESIGN -->
                                    
                                    <div class="form-group">
                                        <label class="control-label col-lg-2">Admission Year</label>
                                        <div class="col-lg-1">
                                            <input type="radio" name="adm_year" id="adm_yr_24" value="<?=CURRENT_YEAR_SHORT?>" class="form-control">
                                        </div>
                                        <label for="adm_yr_24" class="control-label col-lg-1"><?=CURRENT_YEAR?></label>
                                    </div>
                                    <div class="form-group">
                                        <label for="class" class="control-label col-lg-2 text-danger">Select Class & Sec *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" multiple data-placeholder="-- Select Class & Section --" id="class" name="class[]">
                                                <option value="all">All</option>
                                                <?php
                                                foreach($class as $val){
                                                    ?>
                                                    <option value="<?=$val['CS_SEQ'];?>"><?=$val['Class_Name'].' - '.$val['Sec_Name'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                            
                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="print_std_reg_report"><i class="fa fa-file-pdf-o"></i> Generate Report
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            }
                            elseif($form_type == 'std_consc_report') { //student concession report
                                ?>
                                <label style="width: 100%; text-align: center; font-size: 20px; font-weight: bold;">Default Report</label> <br>
                                <form target="_blank" class="cmxform form-horizontal tasi-form" id="std_consc_report" method="post" action="<?= base_url(); ?>admin/print_std_consc_report">
                                    <div class="form-group">
                                        <label for="class_type" class="control-label col-lg-2 text-danger">Select Class Type *</label>
                                        <div class="col-lg-2 iconic-input">
                                            <select class="form-control round-input select2" id="class_type" name="class_type[]" multiple required >
                                                <option value="all">All</option>
                                                <?php
                                                foreach($class_type as $val){
                                                    ?>
                                                    <option value="<?=$val['ct_id'];?>"><?=$val['name'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="col-lg-4 iconic-input">
                                            <select class="form-control round-input select2" multiple id="class" name="class[]" required >

                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="print_std_consc_report"><i class="fa fa-file-pdf-o"></i> Generate Report
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <hr>

                                <label style="width: 100%; text-align: center; font-size: 20px; font-weight: bold;">Report Type (2)</label> <br>
                                <form target="_blank" class="cmxform form-horizontal tasi-form" id="std_consc_report_2" method="post" action="<?= base_url(); ?>admin/print_std_consc_report_2">
                                    <div class="form-group">
                                        <label for="class_type" class="control-label col-lg-2 text-danger">Select Class Type *</label>
                                        <div class="col-lg-2 iconic-input">
                                            <select class="form-control round-input select2" id="class_type2" name="class_type[]" multiple required >
                                                <option value="all">All</option>
                                                <?php
                                                foreach($class_type as $val){
                                                    ?>
                                                    <option value="<?=$val['ct_id'];?>"><?=$val['name'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="col-lg-4 iconic-input">
                                            <select class="form-control round-input select2" multiple id="class2" name="class[]" required >

                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="print_std_consc_report_2"><i class="fa fa-file-pdf-o"></i> Generate Report
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <hr>

                                <label style="width: 100%; text-align: center; font-size: 20px; font-weight: bold;">Report Type (3)</label> <br>
                                <form target="_blank" class="cmxform form-horizontal tasi-form" id="std_consc_report_3" method="post" action="<?= base_url(); ?>admin/print_std_consc_report_3">
                                    <div class="form-group">
                                        <label for="class_type" class="control-label col-lg-2 text-danger">Select Class Type *</label>
                                        <div class="col-lg-2 iconic-input">
                                            <select class="form-control round-input select2" id="class_type3" name="class_type[]" multiple required >
                                                <option value="all">All</option>
                                                <?php
                                                foreach($class_type as $val){
                                                    ?>
                                                    <option value="<?=$val['ct_id'];?>"><?=$val['name'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="col-lg-4 iconic-input">
                                            <select class="form-control round-input select2" multiple id="class3" name="class[]" required >

                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="print_std_consc_report_percentage_wise"><i class="fa fa-file-pdf-o"></i> % Wise Report
                                            </button>

                                            <button class="btn btn-info" type="submit" name="submit"
                                                    value="print_std_consc_report_class_wise"><i class="fa fa-file-pdf-o"></i> Class Wise Report
                                            </button>
                                            <button class="btn" style="background-color: #7da1c2 !important;border-color:#7da1c2 !important;color:#fff;" type="submit" name="submit"
                                                    value="print_std_consc_report_new_percentage_wise"><i class="fa fa-file-pdf-o"></i> New % Wise Report
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <hr>

                                <label style="width: 100%; text-align: center; font-size: 20px; font-weight: bold;">Concession Report for Current Session</label> <br>
                                <form target="_blank" class="cmxform form-horizontal tasi-form" id="session_consc_report" method="post" action="<?= base_url(); ?>admin/print_session_consc_report">
                                    <div class="form-group">
                                        <label for="class_type4" class="control-label col-lg-2 text-danger">Select Class Type *</label>
                                        <div class="col-lg-2 iconic-input">
                                            <select class="form-control round-input select2" id="class_type4" name="class_type[]" multiple required >
                                                <option value="all">All</option>
                                                <?php
                                                foreach($class_type as $val){
                                                    ?>
                                                    <option value="<?=$val['ct_id'];?>"><?=$val['name'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="col-lg-4 iconic-input">
                                            <select class="form-control round-input select2" multiple id="class4" name="class[]" required >

                                            </select>
                                        </div>

                                        <div class="col-lg-2 iconic-input">
                                            <input type="number" name="total_months" value="12" min="1" max="12" class="form-control" required placeholder="Total month">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="print_session_consc_report"><i class="fa fa-file-pdf-o"></i> Generate Report
                                            </button>
                                            <button class="btn btn-info" type="submit" name="submit"
                                                    value="print_consc_range_report"><i class="fa fa-file-pdf-o"></i> % Range Report
                                            </button>
                                        </div>
                                    </div>
                                </form>


                                <script src="<?=base_url();?>assets/admin_panel/js/jquery-1.10.2.min.js"></script>
                                <script>
                                    $(document).ready(function() {
                                        //fetch classes by class-type
                                        $("#class_type2").on('change', function () {
                                            class_type_id = $(this).val();

                                            $.ajax({
                                                url: "<?= base_url('admin/ajax_fetch_classes_by_class_type') ?>",
                                                method: "post",
                                                dataType: 'json',
                                                data: {'class_type_id': class_type_id,},
                                                success: function (returnData) {
                                                    $("#class2").html(returnData.class_option_html);
                                                },
                                            });
                                        });
                                        $("#class_type3").on('change', function () {
                                            class_type_id = $(this).val();

                                            $.ajax({
                                                url: "<?= base_url('admin/ajax_fetch_classes_by_class_type') ?>",
                                                method: "post",
                                                dataType: 'json',
                                                data: {'class_type_id': class_type_id,},
                                                success: function (returnData) {
                                                    $("#class3").html(returnData.class_option_html);
                                                },
                                            });
                                        });
                                        $("#class_type4").on('change', function () {
                                            class_type_id = $(this).val();

                                            $.ajax({
                                                url: "<?= base_url('admin/ajax_fetch_classes_by_class_type') ?>",
                                                method: "post",
                                                dataType: 'json',
                                                data: {'class_type_id': class_type_id,},
                                                success: function (returnData) {
                                                    $("#class4").html(returnData.class_option_html);
                                                },
                                            });
                                        });
                                    });
                                </script>
                                <?php
                            }
                            elseif ($form_type == 'due_undertaking_report') { //students all fees ledger report form
                                ?>
                                <form target="_blank" class="cmxform form-horizontal tasi-form" id="due_undertaking_report" method="post" action="<?= base_url(); ?>admin/print_due_undertaking_report">
                                    <div class="form-group">
                                        <label for="class" class="control-label col-lg-2 text-danger">Select Class & Sec *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" id="undertaking_class" name="undertaking_class" onchange="getStudentByClass()" required >
                                                <option value="">Select</option>
                                                <?php
                                                foreach($class as $val){
                                                    ?>
                                                    <option value="<?=$val['CS_SEQ'];?>"><?=$val['Class_Name'].' - '.$val['Sec_Name'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        
                                    </div> 
                                     <div class="form-group">
                                        <label for="class" class="control-label col-lg-2 text-danger">Student *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input" id="undertaking_student" name="undertaking_student" required >
                                                <option value="">Select</option>
                                               
                                            </select>
                                        </div>
                                        
                                    </div> 
                                    <div class="form-group">
                                        <label for="class" class="control-label col-lg-2 text-danger">Enter Month/Year *</label>
                                        <div class="col-lg-2 iconic-input">
                                            <input class="form-control" type="text" placeholder="mm/yyyy" required style="padding: 10px !important;" id="month" name="month" >
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="class" class="control-label col-lg-2 text-danger">Paid By Or Before *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <input type="date" name="paid_date" id="paid_date" required />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="print_due_undertaking_report"><i class="fa fa-file-pdf-o"></i> Generate Report
                                            </button>

                                            
                                        </div>
                                    </div>
                                </form>
                                <?php
                            }
                            elseif ($form_type == 'all_tran_report') { //all transaction report form
                                ?>
                                <form target="_blank" class="cmxform form-horizontal tasi-form" id="all_tran_report_form" method="post" action="<?= base_url(); ?>admin/print_all_tran_report">
                                    <div class="form-group">
                                        <label for="class_type" class="control-label col-lg-2">Select Payment Type</label>
                                        <div class="col-lg-4 iconic-input">
                                            <select class="form-control round-input select2" data-placeholder="-- Select Type --" id="fees_type_filter" name="fees_type_filter" >
                                                <option value="all">All</option>
                                                <option value="monthly">Monthly</option>
                                                <option value="yearly">Yearly</option>
                                                <option value="nadmission">New Admission</option>
                                            </select>
                                        </div>

                                        <label for="class_type" class="control-label col-lg-2">Select Class Type</label>
                                        <div class="col-lg-4 iconic-input">
                                            <select class="form-control round-input select2" data-placeholder="-- Select School --" multiple="" id="class_type" name="class_type[]" >
                                                <option value="all">All</option>
                                                <?php
                                                foreach($class_type as $val){
                                                    ?>
                                                    <option value="<?=$val['ct_id'];?>"><?=$val['name'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="class" class="control-label col-lg-2 text-danger">Select Class & Sec *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" id="class" name="class[]" multiple required >
                                                <option value="all">All</option>
                                                <?php
                                                foreach($class as $val){
                                                    ?>
                                                    <option value="<?=$val['CS_SEQ'];?>"><?=$val['Class_Name'].' - '.$val['Sec_Name'];?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="month" class="control-label col-lg-2 text-danger">Select Month *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" id="month" name="month" required >
                                                <option value="all">All</option>
                                                <option value="1">January</option>
                                                <option value="2">February</option>
                                                <option value="3">March</option>
                                                <option value="4">April</option>
                                                <option value="5">May</option>
                                                <option value="6">June</option>
                                                <option value="7">July</option>
                                                <option value="8">August</option>
                                                <option value="9">September</option>
                                                <option value="10">October</option>
                                                <option value="11">November</option>
                                                <option value="12">December</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group ">
                                        <label for="" class="control-label col-lg-2">Collection Date</label>
                                        <div class="col-lg-10">
                                            <label for="date_from" class="control-label col-lg-1">From</label>
                                            <div class="col-lg-5 iconic-input">
                                                <i class="fa fa-calendar"></i>
                                                <input value="" class="form-control round-input" id="date_from" name="date_from" type="date" />
                                            </div>

                                            <label for="date_to" class="control-label col-lg-1">To</label>
                                            <div class="col-lg-5 iconic-input">
                                                <i class="fa fa-calendar"></i>
                                                <input value="" class="form-control round-input" id="date_to" name="date_to" type="date" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="print_all_tran_report"><i class="fa fa-file-pdf-o"></i> Generate Report
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            } elseif ($form_type == 'all_fees_type1_report') { //all fees type(1) report form
                                ?>
                                <form target="_blank" class="cmxform form-horizontal tasi-form" id="all_fees_type1_report" method="post" action="<?= base_url(); ?>admin/print_all_fees_type1_report">

                                    <div class="form-group">
                                        <label for="class_type" class="control-label col-lg-2">Select Class Type</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" data-placeholder="-- Select School --" multiple="" id="class_type" name="class_type[]" >
                                                <option value="all">All</option>
                                                <?php
                                                foreach($class_type as $val){
                                                    ?>
                                                    <option value="<?=$val['ct_id'];?>"><?=$val['name'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="month" class="control-label col-lg-2 text-danger">Select Month *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" id="month" name="month" required >
                                                <option value="all">All</option>
                                                <option value="1">January</option>
                                                <option value="2">February</option>
                                                <option value="3">March</option>
                                                <option value="4">April</option>
                                                <option value="5">May</option>
                                                <option value="6">June</option>
                                                <option value="7">July</option>
                                                <option value="8">August</option>
                                                <option value="9">September</option>
                                                <option value="10">October</option>
                                                <option value="11">November</option>
                                                <option value="12">December</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group ">
                                        <label for="" class="control-label col-lg-2">Collection Date</label>
                                        <div class="col-lg-10">
                                            <label for="date_from" class="control-label col-lg-1">From</label>
                                            <div class="col-lg-5 iconic-input">
                                                <i class="fa fa-calendar"></i>
                                                <input value="" class="form-control round-input" id="date_from" name="date_from" type="date" />
                                            </div>

                                            <label for="date_to" class="control-label col-lg-1">To</label>
                                            <div class="col-lg-5 iconic-input">
                                                <i class="fa fa-calendar"></i>
                                                <input value="" class="form-control round-input" id="date_to" name="date_to" type="date" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="print_all_fees_type1_report"><i class="fa fa-file-pdf-o"></i> Generate Report
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            } elseif ($form_type == 'all_fees_type2_report') { //all fees type(2) report form
                                ?>
                                <form target="_blank" class="cmxform form-horizontal tasi-form" id="all_fees_type2_report" method="post" action="<?= base_url(); ?>admin/print_all_fees_type2_report">

                                    <div class="form-group">
                                        <label for="class_type" class="control-label col-lg-2">Select Class Type</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" data-placeholder="-- Select School --" multiple="" id="class_type" name="class_type[]" >
                                                <option value="all">All</option>
                                                <?php
                                                foreach($class_type as $val){
                                                    ?>
                                                    <option value="<?=$val['ct_id'];?>"><?=$val['name'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="month" class="control-label col-lg-2 text-danger">Select Month *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" id="month" name="month" required >
                                                <option value="all">All</option>
                                                <option value="1">January</option>
                                                <option value="2">February</option>
                                                <option value="3">March</option>
                                                <option value="4">April</option>
                                                <option value="5">May</option>
                                                <option value="6">June</option>
                                                <option value="7">July</option>
                                                <option value="8">August</option>
                                                <option value="9">September</option>
                                                <option value="10">October</option>
                                                <option value="11">November</option>
                                                <option value="12">December</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group ">
                                        <label for="" class="control-label col-lg-2">Collection Date</label>
                                        <div class="col-lg-10">
                                            <label for="date_from" class="control-label col-lg-1">From</label>
                                            <div class="col-lg-5 iconic-input">
                                                <i class="fa fa-calendar"></i>
                                                <input value="" class="form-control round-input" id="date_from" name="date_from" type="date" />
                                            </div>

                                            <label for="date_to" class="control-label col-lg-1">To</label>
                                            <div class="col-lg-5 iconic-input">
                                                <i class="fa fa-calendar"></i>
                                                <input value="" class="form-control round-input" id="date_to" name="date_to" type="date" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="print_all_fees_type2_report"><i class="fa fa-file-pdf-o"></i> Generate Report
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            } elseif ($form_type == 'std_fees_ledger_report') { //students all fees ledger report form
                                ?>
                                <form target="_blank" class="cmxform form-horizontal tasi-form" id="std_fees_ledger_report" method="post" action="<?= base_url(); ?>admin/print_std_fees_ledger_report">
                                    <div class="form-group">
                                        <label for="class" class="control-label col-lg-2 text-danger">Select Class & Sec *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" id="class" name="class" required >
                                                <option value="all">All</option>
                                                <?php
                                                foreach($class as $val){
                                                    ?>
                                                    <option value="<?=$val['CS_SEQ'];?>"><?=$val['Class_Name'].' - '.$val['Sec_Name'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="print_std_paid_fees_ledger_report"><i class="fa fa-file-pdf-o"></i> Paid Fees Report
                                            </button>

                                            <button class="btn btn-danger" type="submit" name="submit"
                                                    value="print_std_due_fees_ledger_report"><i class="fa fa-file-pdf-o"></i> Due Fees Report
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            }elseif ($form_type == 'std_ranklist_report') { 
                                ?>
                                <form target="_blank" class="cmxform form-horizontal tasi-form" id="std_ranklist_report" method="post" action="<?= base_url(); ?>admin/print_ranklist_report">
                                    <div class="form-group">
                                        <label for="class" class="control-label col-lg-2 text-danger">Select Class & Sec *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" id="rank_class" name="rank_class" onchange="getSubjectByClass()" required >
                                                <option value="">Select</option>
                                                <?php
                                                foreach($class as $val){
                                                    ?>
                                                    <option value="<?=$val['CS_SEQ'];?>"><?=$val['Class_Name'].' - '.$val['Sec_Name'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        
                                    </div> 
                                     <div class="form-group">
                                        <label for="class" class="control-label col-lg-2 text-danger">Subject *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input" id="rank_subject" name="rank_subject" required >
                                                <option value="">Select</option>
                                               
                                            </select>
                                        </div>
                                        
                                    </div> 
                                    
                                    <div class="form-group">
                                        <label for="report_type" class="control-label col-lg-2 text-danger">Report Type *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input" id="report_type" name="report_type" required>
                                                <option value="">Select</option>
                                                <option value="rank_wise">Rank-wise</option>
                                                <option value="roll_wise">Roll-wise</option>
                                            </select>
                                        </div>
                                    </div>
                                   

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="print_ranklist_report"><i class="fa fa-file-pdf-o"></i> Generate Report
                                            </button>

                                            
                                        </div>
                                    </div>
                                </form>
                                <?php
                            }elseif ($form_type == 'class_subject_topper_report') { //students all fees ledger report form
                                ?>
                                <form target="_blank" class="cmxform form-horizontal tasi-form" id="class_subject_topper_report" method="post" action="<?= base_url(); ?>admin/print_class_subject_topper_report">
                                    <div class="form-group">
                                        <label for="class" class="control-label col-lg-2 text-danger">Select Class & Sec *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" id="rank_class" name="rank_class" onchange="getSubjectByClass()" required >
                                                <option value="">Select</option>
                                                <?php
                                                foreach($class as $val){
                                                    ?>
                                                    <option value="<?=$val['CS_SEQ'];?>"><?=$val['Class_Name'].' - '.$val['Sec_Name'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        
                                    </div> 
                                     <div class="form-group">
                                        <label for="class" class="control-label col-lg-2 text-danger">Subject *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input" id="rank_subject" name="rank_subject" required >
                                                <option value="">Select</option>
                                               
                                            </select>
                                        </div>
                                        
                                    </div> 
                                   

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="print_class_subject_topper_report"><i class="fa fa-file-pdf-o"></i> Generate Report
                                            </button>

                                            
                                        </div>
                                    </div>
                                </form>
                                <?php
                            }
                            
                            
                            elseif ($form_type == 'single_month_dues_report') { //single month dues report form
                            ?>
                            <form target="_blank" class="cmxform form-horizontal tasi-form" id="single_month_dues_report" method="post" action="<?= base_url(); ?>admin/print_single_month_dues_report">
                                
                                <!-- Class Type and Classes Row -->
                                <div class="form-group row">
                                    <label for="class_type" class="control-label col-lg-2 text-danger">Select Class Type *</label>
                                    <div class="col-lg-3 iconic-input">
                                        <select class="form-control round-input select2" id="class_type" name="class_type" required>
                                            <option>---Select Class Type---</option>
                                            <?php
                                            foreach($class_type as $val){
                                            ?>
                                                <option value="<?=$val['ct_id'];?>"><?=$val['name'];?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    
                                    <label for="class" class="control-label col-lg-1 text-danger">Classes *</label>
                                    <div class="col-lg-6 iconic-input">
                                        <select class="form-control round-input select2" multiple id="class" name="class[]" required>
                                        </select>
                                    </div>
                                </div>
                            
                                <!-- Month/Year and Payment Date Row -->
                                <div class="form-group row">
                                    <label for="month" class="control-label col-lg-2 text-danger">Enter Month/Year *</label>
                                    <div class="col-lg-3 iconic-input">
                                        <input class="form-control" type="text" placeholder="mm/yyyy" required style="padding: 10px !important;" id="month" name="month">
                                    </div>
                                    
                                    <label for="payment_date" class="control-label col-lg-2 text-danger">Payment Deadline *</label>
                                    <div class="col-lg-3 iconic-input">
                                        <input class="form-control" type="date" required style="padding: 10px !important;" id="payment_date" name="payment_date">
                                    </div>
                                </div>
                            
                                <!-- Letter Type and Multi Month Row -->
                                <div class="form-group row">
                                    <label for="letter" class="control-label col-lg-2">Letter Format</label>
                                    <div class="col-lg-3 iconic-input">
                                        <select name="letter" id="letter" class="form-control">
                                            <option value="one">Individual Pages</option>
                                            <option value="two">Multiple Per Page</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-lg-4">
                                        <label class="checkbox-custom check-success">
                                            <input class="checkbox_months" value="yes" type="checkbox" id="multi_month" name="multi_month" style="display: inline">
                                            <span class="checkmark"></span>
                                            Multiple Months (All dues upto selected month)
                                        </label>
                                    </div>
                                </div>
                            
                                <!-- Submit Button -->
                                <div class="form-group row">
                                    <div class="col-lg-offset-2 col-lg-10">
                                        <button class="btn btn-success btn-lg" type="submit" name="submit" value="print_single_month_dues_report">
                                            <i class="fa fa-file-pdf-o"></i> Generate Report
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <?php
                            }
                            
                            
                            elseif ($form_type == 'all_dues_report') { //all dues report form
                                ?>
                                <form target="_blank" class="cmxform form-horizontal tasi-form" id="all_dues_report" method="post" action="<?= base_url(); ?>admin/print_all_dues_report">
                                    <div class="form-group">
                                        <label for="class_type" class="control-label col-lg-2 text-danger">Select Class Type *</label>
                                        <div class="col-lg-2 iconic-input">
                                            <select class="form-control round-input select2" id="class_type" name="class_type" required >
                                                <option>---Select Class Type---</option>
                                                <?php
                                                foreach($class_type as $val){
                                                    ?>
                                                    <option value="<?=$val['ct_id'];?>"><?=$val['name'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="col-lg-4 iconic-input">
                                            <select class="form-control round-input select2" multiple id="class" name="class[]" required >

                                            </select>
                                        </div>

                                        <label for="class" class="control-label col-lg-2 text-danger">Enter Effective Date *</label>
                                        <div class="col-lg-2">
                                            <input class="form-control" type="text" placeholder="dd/mm/yyyy" required style="padding: 10px !important;" id="eff_date" name="eff_date" >
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="checkbox-custom check-success">
                                            <input class="" value="yes" type="checkbox" id="without_amount" name="without_amount" >
                                            <label class="col-lg-offset-2 col-lg-2" for="without_amount">Without Amount</label>
                                        </label>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="print_all_dues_report"><i class="fa fa-file-pdf-o"></i> Generate Report
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            }
                             elseif ($form_type == 'outstanding_total_report') { //all dues report form
                                ?>
                                <form target="_blank" class="cmxform form-horizontal tasi-form" id="outstanding_total_report" method="post" action="<?= base_url(); ?>admin/print_outstanding_total_report">
                                    <div class="form-group">
                                        <label for="class_type" class="control-label col-lg-2 text-danger">Select Class *</label>
                                        <div class="col-lg-2 iconic-input">
                                            <select class="form-control round-input select2" multiple id="class" name="class[]" required >
                                                <option>---Select Class ---</option>
                                                <?php
                                                foreach($class as $val){
                                                    ?>
                                                    <option value="<?=$val['CS_SEQ'];?>"><?=$val['class_sec'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <!--<div class="col-lg-4 iconic-input">-->
                                        <!--    <select class="form-control round-input select2" multiple id="class" name="class[]" required >-->

                                        <!--    </select>-->
                                        <!--</div>-->

                                        <label for="class" class="control-label col-lg-2 text-danger">Enter Effective Date *</label>
                                        <div class="col-lg-2">
                                            <input class="form-control" type="text" placeholder="dd/mm/yyyy" required style="padding: 10px !important;" id="eff_date" name="eff_date" >
                                        </div>
                                    </div>

                                   

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="print_outstanding_total_report"><i class="fa fa-file-pdf-o"></i> Generate Report
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            }
                            elseif ($form_type == 'payment_type_report') { //Payment Type Report form
                                ?>
                                <form target="_blank" class="cmxform form-horizontal tasi-form" id="payment_type_report" method="post" action="<?= base_url(); ?>admin/print_payment_type_report">

                                    <div class="form-group">
                                        <label for="class_type" class="control-label col-lg-2">Select Class Type</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" data-placeholder="-- Select School --" multiple="" id="class_type" name="class_type[]" >
                                                <option value="all">All</option>
                                                <?php
                                                foreach($class_type as $val){
                                                    ?>
                                                    <option value="<?=$val['ct_id'];?>"><?=$val['name'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group ">
                                        <label for="" class="control-label col-lg-2">Date Range</label>
                                        <div class="col-lg-10">
                                            <label for="date_from" class="control-label col-lg-1">From</label>
                                            <div class="col-lg-5 iconic-input">
                                                <i class="fa fa-calendar"></i>
                                                <input value="" class="form-control round-input" id="date_from" name="date_from" type="date" />
                                            </div>

                                            <label for="date_to" class="control-label col-lg-1">To</label>
                                            <div class="col-lg-5 iconic-input">
                                                <i class="fa fa-calendar"></i>
                                                <input value="" class="form-control round-input" id="date_to" name="date_to" type="date" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="print_payment_type_report"><i class="fa fa-file-pdf-o"></i> Generate Report
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            } elseif ($form_type == 'library_register') { //library register form
                                ?>
                                <form target="_blank" class="cmxform form-horizontal tasi-form" id="library_register" method="post" action="<?= base_url(); ?>admin/print_library_register">

                                    <div class="form-group ">
                                        <label for="" class="control-label col-lg-2">Issue Date</label>
                                        <div class="col-lg-10">
                                            <label for="date_from" class="control-label col-lg-1">From</label>
                                            <div class="col-lg-5 iconic-input">
                                                <i class="fa fa-calendar"></i>
                                                <input value="" class="form-control round-input" id="date_from" name="date_from" type="date" />
                                            </div>

                                            <label for="date_to" class="control-label col-lg-1">To</label>
                                            <div class="col-lg-5 iconic-input">
                                                <i class="fa fa-calendar"></i>
                                                <input value="" class="form-control round-input" id="date_to" name="date_to" type="date" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="print_library_register"><i class="fa fa-file-pdf-o"></i> Generate Report
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            } elseif ($form_type == 'books_register') { //books register form
                                ?>
                                <form target="_blank" class="cmxform form-horizontal tasi-form" id="books_register" method="post" action="<?= base_url(); ?>admin/print_books_register">
                                    <div class="form-group">
                                        <label for="book" class="control-label col-lg-2 text-danger">Select Book *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input" id="book" name="book" required >
                                                <option value="all">All</option>
                                                <?php
                                                foreach($books as $val){
                                                    ?>
                                                    <option value="<?=$val['BOOK_SEQ'];?>"><?=$val['Accession_No'].' - '.$val['Book_Name'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="print_books_register"><i class="fa fa-file-pdf-o"></i> Generate Report
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            } elseif ($form_type == 'class_routine') { //class routine form
                                ?>
                                <form target="_blank" class="cmxform form-horizontal tasi-form" id="class_routine" method="post" action="<?= base_url(); ?>admin/print_class_routine">
                                    <div class="form-group">
                                        <label for="class" class="control-label col-lg-2 text-danger">Select Class & Sec *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" id="class" name="class[]" multiple required >
                                                <?php
                                                foreach($class as $val){
                                                    ?>
                                                    <option value="<?=$val['CS_SEQ'];?>"><?=$val['Class_Name'].' - '.$val['Sec_Name'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="print_class_routine"><i class="fa fa-file-pdf-o"></i> Generate Routine
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            } 
                            elseif ($form_type == 'teacher_routine') { //teacher routine form
                                ?>
                                <form target="_blank" class="cmxform form-horizontal tasi-form" id="teacher_routine" method="post" action="<?= base_url(); ?>admin/print_teacher_routine">
                                    <div class="form-group">
                                        <label for="teacher" class="control-label col-lg-2 text-danger">Select Teacher *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" id="teacher" name="teacher[]" multiple required >
                                                <?php
                                                foreach($teachers as $val){
                                                    ?>
                                                    <option value="<?=$val['TCH_SRLNO'];?>"><?=$val['TCH_NAME'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="print_teacher_routine"><i class="fa fa-file-pdf-o"></i> Generate Routine
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            }
                            elseif ($form_type == 'master_routine') { //teacher routine form
                                ?>
                                <form target="_blank" class="cmxform form-horizontal tasi-form" id="teacher_routine" method="post" action="<?= base_url(); ?>admin/print_master_routine">
                                    <div class="form-group">
                                         <label for="class_type" class="control-label col-lg-1 text-danger">Select Class Type</label>
                                        <div class="col-lg-2 iconic-input">
                                            <select class="form-control round-input select2" data-placeholder="-- Select School --"  id="class_type" name="class_type" required>
                                                <option value="all">All</option>
                                                <?php
                                                foreach($class_type as $val){
                                                    ?>
                                                    <option value="<?=$val['ct_id'];?>"><?=$val['name'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                         <label for="class_student_list" class="control-label col-lg-1 text-danger">Select Class & Sec *</label>
                                        <div class="col-lg-2 iconic-input">
                                            <select class="form-control round-input select2" id="class" name="category_class[]" multiple  >
                                                <option value="all">All</option>
                                                <?php
                                                foreach($class as $val){
                                                    ?>
                                                    <option value="<?=$val['CS_SEQ'];?>"><?=$val['Class_Name'].' - '.$val['Sec_Name'];?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                          
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="print_master_routine"><i class="fa fa-file-pdf-o"></i> Generate Routine
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            }
                            else if($form_type == 'std_strength_report'){
                            ?>
                            <form target="_blank" class="cmxform form-horizontal tasi-form" id="std_consc_report" method="post" action="<?= base_url(); ?>admin/print_student_strength_report">
                                    <div class="form-group">
                                        <label for="class_type" class="control-label col-lg-2 text-danger">Select Class Type *</label>
                                        <div class="col-lg-2 iconic-input">
                                            <select class="form-control round-input select2" id="class_type" name="class_typep[]" multiple required >
                                                <option value="all">All</option>
                                                <?php
                                                foreach($class_type as $val){
                                                    ?>
                                                    <option value="<?=$val['ct_id'];?>"><?=$val['name'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="col-lg-4 iconic-input">
                                            <select class="form-control round-input select2" multiple id="class" name="class[]" required >

                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="print_student_strength_report"><i class="fa fa-file-pdf-o"></i> Total Strength
                                            </button>
                                            <button class="btn btn-info" type="submit" name="submit"
                                                    value="print_catholic_report"><i class="fa fa-file-pdf-o"></i> Catholic/Non-Catholic
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            <?php }
                            elseif($form_type == 'class_wise_blank_mark_sheet'){ ?>
                                <form target="_blank" class="cmxform form-horizontal tasi-form" id="std_consc_report" method="post" action="<?= base_url(); ?>admin/print_student_list_report">
                                    <div class="form-group">
                                        <label for="class_student_list" class="control-label col-lg-2 text-danger">Select Class & Sec *</label>
                                        <div class="col-lg-2 iconic-input">
                                            <select class="form-control select2cssjs" data-placeholder="-- Select Class & Sec --" id="class_student_list" name="class[]">
                                                <!--<option value="all" selected>All</option>-->
                                                <?php print_r($class); ?>
                                                <?php
                                                foreach($class as $val){
                                                    ?>
                                                    <option value="<?=$val['CS_SEQ'];?>"><?=$val['Class_Name'].' - '.$val['Sec_Name'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-lg-2 iconic-input">
                                            <select class="form-control select2cssjs" data-placeholder="-- Select Term --" id="term" name="term">
                                                <!--<option value="all" selected>All</option>-->
                                                <?php
                                                foreach($exam_terms as $et){
                                                    ?>
                                                    <option value="<?=$et['et_id'];?>"><?=$et['term_title']?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-lg-2 iconic-input">
                                            <select class="form-control" id="type1" name="type1">
                                                <option value="summary">Summary</option>
                                                <option value="summary_blank">Summary Blank</option>
                                                <!-- <option value="consolidated" selected>Consolidated</option> -->
                                                <option value="tel_no">Telephone Number</option>
                                                <option value="attendence">Attendence</option>
                                                <option value="st_list">Student List</option>
                                                <option value="mark_sheet_1">Mark Sheet Type 1 (80/20)</option>
                                                <option value="mark_sheet_1_blank">Mark Sheet Type 1 (Blank)</option>
                                                <option value="mark_sheet_2">Mark Sheet Type 2 (90/10)</option>
                                                <option value="mark_sheet_2_blank">Mark Sheet Type 2 (Blank)</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-2 iconic-input" id="typ2">
                                            <label class="checkbox-custom check-success col-lg-4">
                                                <input value="" name="reg_no_wise_st" id="reg_no_wise_st" type="checkbox" >
                                                <label for="reg_no_wise_st">Regd No. Wise</label>
                                            </label>                        
                                        </div>
                                        <div class="col-lg-2 iconic-input" id="typ2">
                                            <label class="checkbox-custom check-success col-lg-4">
                                                <input value="yes" name="single_page" id="single_page" type="checkbox" >
                                                <label for="single_page">Single Page</label>
                                            </label>                        
                                        </div>
                                    </div>
                                    <div class="form-group row"  id="st_name_list_div">
                                        <label for="class" class="control-label col-lg-2"></label>
                                        <div class="col-lg-10">
                                            <div class="text-center">
                                                <a href='#' id='add_all_st_id_list'>Add All</a> / 
                                            <a href='#' id='remove_all_st_id_list'>Remove All</a>
                                            </div>
                                            <!--rtrtrtr-->
                                            <!--< ?php print_r($student); ?>-->
                                            <select class="form-control st_id_list" multiple data-placeholder="-- Select Class & Sec --" id="st_id_list" name="st_id_list[]">
                                                <?php
                                                foreach($student as $val){
                                                    ?>
                                                    <option value="<?=$val['STD_SEQ'];?>"><?=$val['STD_ROLLNO'].' - '.$val['ST_FULL_NAME'].' - '.$val['STD_REGNO'].' ('.$val['class_sec'].')';?></option>
                                                    <?php
                                                    }
                                                    ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" id="print_student_list_report" type="submit" name="print_student_list_report"
                                                    value="print_student_list_report" disabled><i class="fa fa-file-pdf-o"></i> Generate Report
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                <br>
                                <br>
                                <br>

                                 <form target="_blank" class="cmxform form-horizontal tasi-form" id="std_consc_report" method="post" action="<?= base_url(); ?>admin/print_student_category_list_report">
                                    <div class="form-group">
                                         <label for="class_type" class="control-label col-lg-1 text-danger">Select Class Type</label>
                                        <div class="col-lg-2 iconic-input">
                                            <select class="form-control round-input select2" data-placeholder="-- Select School --"  id="class_type" name="class_type" multiple required> 
                                               <option value="all">All</option>
                                                <?php
                                                foreach($class_type as $val){
                                                    ?>
                                                    <option value="<?=$val['ct_id'];?>"><?=$val['name'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                         <label for="class_student_list" class="control-label col-lg-1 text-danger">Select Class & Sec *</label>
                                        <div class="col-lg-2 iconic-input">
                                            <select class="form-control round-input select2" id="class" name="category_class[]" multiple required >
                                                <option value="all">All</option>
                                                <?php
                                                foreach($class as $val){
                                                    ?>
                                                    <option value="<?=$val['CS_SEQ'];?>"><?=$val['Class_Name'].' - '.$val['Sec_Name'];?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                          
                                        </div>
                                        <label for="class_student_list" class="control-label col-lg-1 text-danger">Select Category</label>
                                        <div class="col-lg-2 iconic-input">
                                            <select class="form-control" id="category_list" onchange="(this.value == 'religion')?document.getElementById('multi_religion').style.display = 'block':document.getElementById('multi_religion').style.display = 'none'" name="category_list">
                                                <option value="compact">Compact</option>
                                                <option value="catholic">Catholic</option>
                                                <option value="non_catholic">Non Catholic</option>
                                                <option value="religion">Religion</option>
                                                <option value="REL_VAL">REL & VAL</option>
                                                <option value="language">Language</option>
                                                <option value="house">House</option>  
                                                <option value="house_yellow">House Yellow</option>
                                                <option value="house_green">House Green</option>
                                                <option value="house_red">House Red</option>
                                                <option value="house_blue">House Blue</option> 
                                                <option value="aadhar_card">Aadhar Card</option>
                                                <option value="bangla_shiksha_id">Banglar Shiksha Id</option> 
                                            </select>
                                        </div>
                                          
                                       
                                        <div class="col-lg-2 iconic-input" id="multi_religion" style="display:none;">
                                            <select class="form-control" id="multi_religion_list" name="multi_religion_list">
                                                <?php
                                                foreach($religion as $val){
                                                    ?>
                                                    <option value="<?=$val['religion_id'];?>"><?=$val['name'];?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                    <option value="345">Christion Muslim</option>
                                            </select>                    
                                        </div>
                                        <div class="col-lg-1 iconic-input"  >
                                            <input type="checkbox" name="mobile_check" id="mobile_check"  /> Mobile No
                                        </div>
                                    </div>
                                    <div class="form-group" >
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" id="print_student_category_list_report" type="submit" name="print_student_category_list_report"
                                                    value="print_student_category_list_report"><i class="fa fa-file-pdf-o"></i> Generate Report
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            <?php }
                            else if($form_type == 'notice_report'){
                                ?>
                                <a style="display: table;margin-bottom: 10px;width: 100px;" href="<?=base_url()?>admin/notice_report/add" class="btn btn-primary">Add</a>
                                <table class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>Sr. #</th>
                                        <th>Notice Date</th>
                                        <th>Subject</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                   <?php
                                   if(!empty($notices)){
                                       $i=0;
                                       foreach($notices as $row){
                                           $i++;
                                           ?>
                                           <tr>
                                               <td><?php echo $i?></td>
                                               <td><?php echo date('d-M-Y', strtotime($row['notice_date'])); ?></td> 
                                               <td><?php echo $row['subject']?></td>
                                               <td><a href="<?php echo base_url()?>admin/notice_report/edit/<?php echo $row['id']?>" class="btn btn-primary">Edit</a> &nbsp;&nbsp; <a href="<?php echo base_url()?>admin/notice_report/delete/<?php echo $row['id']?>" class="btn btn-danger">Delete</a> &nbsp;&nbsp; <a target="_blank" href="<?php echo base_url()?>admin/notice_report/print/<?php echo $row['id']?>" class="btn btn-success">Print</a></td>
                                           </tr>
                                           <?php
                                       }
                                   }
                                   ?>
                                    </tbody>
                                </table>
                                <?php
                            }
                             elseif($form_type == 'add_notice_report'){ ?>
                                <form class="cmxform form-horizontal tasi-form" id="notice_report" method="post" action="<?= base_url(); ?>admin/submit_notice_report">
                                    <div class="form-group">
                                        <label for="class_student_list" class="control-label col-lg-2 text-danger">Date *</label>
                                        <div class="col-lg-4 iconic-input">
                                            <input type="date" class="form-control" name="date" id="date" required />
                                        </div>
                                         
                                        <label for="class_student_list" class="control-label col-lg-2 text-danger">Department *</label>
                                        <div class="col-lg-4 iconic-input">
                                            <select class="form-control select2cssjs" data-placeholder="-- Select Department --" id="department_list" name="department[]" multiple>
                                                <!--<option value="all" selected>All</option>-->
                                                <?php
                                                foreach($dept as $val){
                                                    ?>
                                                    <option value="<?=$val['DEPT_CODE'];?>"><?=$val['DEPT_NAME'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                       
                                    </div>
                                    <div class="form-group">
                                        <label for="class_student_list" class="control-label col-lg-2 text-danger">Subject *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <input type="text" class="form-control" name="subject" id="subject" required />
                                        </div>
                                        
                                    </div>
                                     <div class="form-group">
                                       
                                        <label for="class_student_list" class="control-label col-lg-2 text-danger">Message</label>
                                        <div class="col-lg-10 iconic-input">
                                            <textarea class="form-control" name="message" id="message" style="height:250px !important"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row"  id="st_name_list_div">
                                        <label for="class" class="control-label col-lg-2"></label>
                                        <div class="col-lg-10">
                                            <div class="text-center">
                                                <a href='#' id='add_all_st_id_list'>Add All</a> / 
                                            <a href='#' id='remove_all_st_id_list'>Remove All</a>
                                            </div>
                                            <select class="form-control st_id_list" multiple data-placeholder="-- Select Class & Sec --" id="st_id_list" name="st_id_list[]">
                                                <?php
                                                foreach($teacher as $val){
                                                    ?>
                                                    <option value="<?=$val['TCH_SRLNO'];?>">
                                                        <?=$val['TCH_NAME'];?>
                                                    </option>
                                                    <?php
                                                    }
                                                    ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" id="print_student_list_report" type="submit" name="print_notice_report"
                                                    value="print_student_list_report" disabled><i class="fa fa-file-pdf-o"></i> Submit Report
                                            </button>
                                        </div>
                                    </div>
                                </form>

                               

                                 
                            <?php }
                            elseif($form_type == 'edit_notice_report'){ ?>
                                <form class="cmxform form-horizontal tasi-form" id="notice_report" method="post" action="<?= base_url(); ?>admin/update_notice_report">
                                    <input type="hidden" name="header_id" value="<?php echo $header_data->id?>" />
                                    <div class="form-group">
                                        <label for="class_student_list" class="control-label col-lg-2 text-danger">Date *</label>
                                        <div class="col-lg-4 iconic-input">
                                            <input type="date" class="form-control" name="date" id="date" value="<?php echo $header_data->notice_date?>" required />
                                        </div>
                                         
                                        <label for="class_student_list" class="control-label col-lg-2 text-danger">Department *</label>
                                        <div class="col-lg-4 iconic-input">
                                            <select class="form-control select2cssjs" data-placeholder="-- Select Department --" id="department_list" name="department[]" multiple>
                                                <!--<option value="all" selected>All</option>-->
                                                <?php
                                                foreach($dept as $val){
                                                    $dept = explode(",",$header_data->department);
                                                    $select = '';
                                                    if(!empty($dept)){
                                                        foreach($dept as $row){
                                                            if($row == $val['DEPT_CODE']){
                                                                echo $select = "selected";
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                    <option <?php echo $select?> value="<?=$val['DEPT_CODE'];?>"><?=$val['DEPT_NAME'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                       
                                    </div>
                                    <div class="form-group">
                                        <label for="class_student_list" class="control-label col-lg-2 text-danger">Subject *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <input type="text" class="form-control" name="subject" id="subject" value="<?php echo $header_data->subject?>" required />
                                        </div>
                                        
                                    </div>
                                     <div class="form-group">
                                       
                                        <label for="class_student_list" class="control-label col-lg-2 text-danger">Message</label>
                                        <div class="col-lg-10 iconic-input">
                                            <textarea class="form-control" name="message" id="message" style="height:250px !important"><?php echo $header_data->message?></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row"  id="st_name_list_div">
                                        <label for="class" class="control-label col-lg-2"></label>
                                        <div class="col-lg-10">
                                            <div class="text-center">
                                                <a href='#' id='add_all_st_id_list'>Add All</a> / 
                                            <a href='#' id='remove_all_st_id_list'>Remove All</a>
                                            </div>
                                            <select class="form-control st_id_list" multiple data-placeholder="-- Select Class & Sec --" id="st_id_list" name="st_id_list[]">
                                                <?php
                                                foreach($teacher as $val){
                                                    ?>
                                                    <option value="<?=$val['TCH_SRLNO'];?>">
                                                        <?=$val['TCH_NAME'];?>
                                                    </option>
                                                    <?php
                                                    }
                                                    ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" id="print_student_list_report" type="submit" name="print_notice_report"
                                                    value="print_student_list_report" disabled><i class="fa fa-file-pdf-o"></i> Update Report
                                            </button>
                                        </div>
                                    </div>
                                </form>

                               

                                 
                            <?php }
                           elseif($form_type == 'teacher_related_report'){ ?>
                               
                                 <form target="_blank" class="cmxform form-horizontal tasi-form" id="std_consc_report" method="post" action="<?= base_url(); ?>admin/print_teacher_related_report">
                                    <div class="form-group">
                                         <label for="class_type" class="control-label col-lg-1 text-danger">Select Class Type</label>
                                        <div class="col-lg-1 iconic-input">
                                            <select class="form-control round-input select2" data-placeholder="-- Select School --"  id="class_type" name="class_type" required>
                                                <option value="">Select</option>
                                                <option value="all">All</option>
                                                <?php
                                                foreach($class_type as $val){
                                                    ?>
                                                    <option value="<?=$val['ct_id'];?>"><?=$val['name'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                         <label for="class_student_list" class="control-label col-lg-1 text-danger">Select Class & Sec *</label>
                                        <div class="col-lg-2 iconic-input">
                                            <select class="form-control round-input select2" id="class" name="category_class[]" multiple required >
                                                <option value="all">All</option>
                                                <?php
                                                foreach($class as $val){
                                                    ?>
                                                    <option value="<?=$val['CS_SEQ'];?>"><?=$val['Class_Name'].' - '.$val['Sec_Name'];?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                          
                                        </div>
                                        <label for="class_student_list" class="control-label col-lg-1 text-danger">Department</label>
                                        <div class="col-lg-2 iconic-input">
                                            <select class="form-control select2cssjs" data-placeholder="-- Select Department --" id="department_list" name="department[]" multiple>
                                                <!--<option value="all" selected>All</option>-->
                                                <?php
                                                foreach($dept as $val){
                                                    ?>
                                                    <option value="<?=$val['DEPT_CODE'];?>"><?=$val['DEPT_NAME'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <label for="class_student_list" class="control-label col-lg-1 text-danger">Select Category</label>
                                        <div class="col-lg-2 iconic-input">
                                            <select class="form-control" id="report_category"  name="report_category">
                                              
                                                <option value="class_teacher_name_list">Class Teacher Name List</option>
                                                <option value="all_teacher_list">All Teacher List</option>
                                                <option value="pan_aadhar">Pan & Aadhar</option>
                                               
                                            </select>
                                        </div>
                                          
                                       
                                        
                                        <div class="col-lg-1 iconic-input"  >
                                            <input type="checkbox" name="mobile_check" id="mobile_check"  /> Mobile No 
                                        </div>
                                    </div>
                                    <div class="form-group" >
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" id="" type="submit" name=""
                                                    ><i class="fa fa-file-pdf-o"></i> Generate Report
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            <?php }
                             elseif($form_type == 'staff_leave_report'){ ?>
                               
                                  <form target="_blank" action="<?php echo base_url()?>admin/print_staff_leave_report" class="cmxform form-horizontal tasi-form" id="form_student_class_update" style="padding:10px;" method="post"> 
                                 
                                    <div class="form-group">
                                        <label for="from_class" class="control-label col-lg-2 text-danger">Staff *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" id="staff" name="staff"   >
                                                <option value="">All</option>
                                                <?php
                                                foreach($staff as $val){
                                                    ?>
                                                    <option value="<?=$val['TCH_SRLNO'];?>"><?=$val['TCH_SALUTATION'].$val['TCH_NAME'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                     <div class="form-group">
                                        <label for="from_class" class="control-label col-lg-2 text-danger">Leave Category *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" id="leave_category" name="leave_category"   >
                                                <option value="">All</option>
                                                <option value="Casual Leave">Casual Leave</option>
                                                <option value="Annual Leave">Annual Leave</option>
                                                <option value="Medical Leave">Medical Leave</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="from_class" class="control-label col-lg-2 text-danger">From date *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <input type="date" name="from_date" id="from_date" class="form-control" value=""  />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="from_class" class="control-label col-lg-2 text-danger">To Date *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <input type="date" name="to_date" id="to_date" class="form-control" value=""  />
                                        </div>
                                    </div>
                                    
                                   

                                   

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit" 
                                                    > Generate
                                            </button>
                                           
                                        </div>
                                    </div>
                                   
                            </form>
                            <?php }
                            ?>
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

<script>

    $(document).ready(function() {
      $("#eff_date").inputmask("dd/mm/yyyy");
      $("#month").inputmask("mm/yyyy");

        //fetch classes by class-type
        $("#class_type").on('change', function() {
            class_type_id = $(this).val();

            $.ajax({
                url: "<?= base_url('admin/ajax_fetch_classes_by_class_type') ?>",
                method: "post",
                dataType: 'json',
                data: {'class_type_id':class_type_id,},
                success: function(returnData){
                    $("#class").html(returnData.class_option_html);
                },
            });
        });
        
        
        var st_list = <?= json_encode(@$teacher)  ?>;
        var st_name_list = '';
        var reg_no_list = '';
        var class_student_list = $('#department_list').val();
        //console.log(class_student_list);
        // st_list = JSON.parse(JSON.stringify(st_list));

        
            $.each(st_list, function(key,val) {
                if(class_student_list == 'all'){
                    st_name_list = st_name_list + '<option value="'+val.TCH_SRLNO+'">'+val.TCH_NAME+' </option>';

                }else{
                    if (class_student_list.includes(val.DEPT_CODE)) {
                        st_name_list = st_name_list + '<option value="'+val.TCH_SRLNO+'">'+val.TCH_NAME+' </option>';
                    }
                }
            });
            $('#st_id_list').html(st_name_list);

            $('#st_id_list').multiSelect('refresh');
    });
    //update from date & to date, on month selection
    $('#month').change(function(){
        month = $('#month').val();
        if(month == 'all') {
            $('#date_from').val('');
            $('#date_to').val('');
        } else {
            d = new Date();
            FirstDay = new Date(d.getFullYear(), month -1, 2).toISOString().slice(0,10);
            LastDay = new Date(d.getFullYear(), month, 1).toISOString().slice(0,10);
            $('#date_from').val(FirstDay);
            $('#date_to').val(LastDay);
        }
    });

    
    $('#st_id_list').multiSelect({

        selectableHeader: "<input type='text' class='form-control search-input' placeholder='Enter Search' autocomplete='off'>",
        selectionHeader: "<input type='text' class='form-control search-input' placeholder='Enter Search' autocomplete='off'>",

          afterInit: function(ms){
    var that = this,
        $selectableSearch = that.$selectableUl.prev(),
        $selectionSearch = that.$selectionUl.prev(),
        selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
        selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

    that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
    .on('keydown', function(e){
      if (e.which === 40){
        that.$selectableUl.focus();
        return false;
      }
    });

    that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
    .on('keydown', function(e){
      if (e.which == 40){
        that.$selectionUl.focus();
        return false;
      }
    });
  },
  afterSelect: function(){
    this.qs1.cache();
    this.qs2.cache();
  },
  afterDeselect: function(){
    this.qs1.cache();
    this.qs2.cache();
  }
    });

    $('#add_all_st_id_list').click(function(){
      $('#st_id_list').multiSelect('select_all');
      return false;
    });
    $('#remove_all_st_id_list').click(function(){
      $('#st_id_list').multiSelect('deselect_all');
      return false;
    });


    //function ex_div_list(value) {
    //
    //    var cls = $("#class").val();
    //    var st_list = <?//= json_encode($student)  ?>//;
    //    var st_name_list = '';
    //    var reg_no_list = '';
    //    if(value == 'regd_no_wise'){
    //        document.getElementById('reg_no_list_div').style.display = 'block';
    //        document.getElementById('st_name_list_div').style.display = 'none';
    //
    //        $.each(st_list, function(key,val) {
    //            if(cls == 'all'){
    //                reg_no_list = reg_no_list + '<option value="'+val.STD_SEQ+'">'+val.STD_REGNO+' '+val.class_sec+'</option>';
    //
    //            }else{
    //                if (cls == val.CS_SEQ) {
    //                    reg_no_list = reg_no_list + '<option value="'+val.STD_SEQ+'">'+val.STD_REGNO+' '+val.class_sec+'</option>';
    //                }
    //            }
    //        });
    //        $('#reg_no_list').html(reg_no_list);
    //
    //        $('#reg_no_list').multiSelect('refresh');
    //
    //    }else{
    //        document.getElementById('reg_no_list_div').style.display = 'none';
    //        document.getElementById('st_name_list_div').style.display = 'block';
    //
    //        $.each(st_list, function(key,val) {
    //            if(cls == 'all'){
    //                st_name_list = st_name_list + '<option value="'+val.STD_SEQ+'">'+val.STD_ROLLNO+' '+val.ST_FULL_NAME+' '+val.class_sec+'</option>';
    //
    //            }else{
    //                if (cls == val.CS_SEQ) {
    //                    st_name_list = st_name_list + '<option value="'+val.STD_SEQ+'">'+val.STD_ROLLNO+' '+val.ST_FULL_NAME+' '+val.class_sec+'</option>';
    //                }
    //            }
    //        });
    //        $('#st_name_list').html(st_name_list);
    //
    //        $('#st_name_list').multiSelect('refresh');
    //    }
    //}
    //
    //function ex_list_div_change(value) {
    //
    //    var st_list = <?//= json_encode($student)  ?>//;
    //    var st_name_list = '';
    //    var reg_no_list = '';
    //
    //    // st_list = JSON.parse(JSON.stringify(st_list));
    //
    //    if($('#type2').val() == 'regd_no_wise'){
    //
    //        $.each(st_list, function(key,val) {
    //            if(value == 'all'){
    //                reg_no_list = reg_no_list + '<option value="'+val.STD_SEQ+'">'+val.STD_REGNO+' '+val.class_sec+'</option>';
    //
    //            }else{
    //                if (value == val.CS_SEQ) {
    //                    reg_no_list = reg_no_list + '<option value="'+val.STD_SEQ+'">'+val.STD_REGNO+' '+val.class_sec+'</option>';
    //                }
    //            }
    //        });
    //        $('#reg_no_list').html(reg_no_list);
    //
    //        $('#reg_no_list').multiSelect('refresh');
    //
    //    }else{
    //        $.each(st_list, function(key,val) {
    //            if(value == 'all'){
    //                st_name_list = st_name_list + '<option value="'+val.STD_SEQ+'">'+val.STD_ROLLNO+' '+val.ST_FULL_NAME+' '+val.class_sec+'</option>';
    //
    //            }else{
    //                if (value == val.CS_SEQ) {
    //                    st_name_list = st_name_list + '<option value="'+val.STD_SEQ+'">'+val.STD_ROLLNO+' '+val.ST_FULL_NAME+' '+val.class_sec+'</option>';
    //                }
    //            }
    //        });
    //        $('#st_name_list').html(st_name_list);
    //
    //        $('#st_name_list').multiSelect('refresh');
    //    }
    //}


    

    $('#st_id_list').on('change', function() {
        if ($(this).val()) {
            $('#print_student_list_report').prop("disabled", false);
        } else {
            $('#print_student_list_report').prop("disabled", true);
        }
    });


    $('#class_student_list, #reg_no_wise_st').on('change', function() {

        <?php  ?>
      
        var st_list = <?= json_encode(@$student)  ?>;
        var st_name_list = '';
        var reg_no_list = '';
        var class_student_list = $('#class_student_list').val();

        // st_list = JSON.parse(JSON.stringify(st_list));

        if($("#reg_no_wise_st").is(':checked')){

            $.each(st_list, function(key,val) {
                if(class_student_list == 'all'){
                    reg_no_list = reg_no_list + '<option value="'+val.STD_SEQ+'">'+val.STD_REGNO+' '+val.class_sec+'</option>';

                }else{
                    if (class_student_list == val.CS_SEQ) {
                        reg_no_list = reg_no_list + '<option value="'+val.STD_SEQ+'">'+val.STD_REGNO+' '+val.class_sec+'</option>';
                    }
                }
            });
            $('#st_id_list').html(reg_no_list);

            $('#st_id_list').multiSelect('refresh');

        }else{
            $.each(st_list, function(key,val) {
                if(class_student_list == 'all'){
                    st_name_list = st_name_list + '<option value="'+val.STD_SEQ+'">'+val.STD_ROLLNO+' '+val.ST_FULL_NAME+' '+val.class_sec+'</option>';

                }else{
                    if (class_student_list == val.CS_SEQ) {
                        st_name_list = st_name_list + '<option value="'+val.STD_SEQ+'">'+val.STD_ROLLNO+' '+val.ST_FULL_NAME+' '+val.class_sec+'</option>';
                    }
                }
            });
            $('#st_id_list').html(st_name_list);

            $('#st_id_list').multiSelect('refresh');
        }
    });
    
    function getStudentByClass(){
        var undertaking_class = $('#undertaking_class').val();
        if(undertaking_class > 0){
            $.ajax({
               url:'<?php echo base_url()?>admin/get_studentlist_ajax',
               type:'post',
               dataType:'json',
               data:{undertaking_class:undertaking_class},
               success:function(response){
                   $('#undertaking_student').html(response.html);
               }
            });
        }
    }
    function getSubjectByClass(){
        var rank_class = $('#rank_class').val();
        if(rank_class > 0){
            $.ajax({
               url:'<?php echo base_url()?>admin/get_subjectlist_ajax',
               type:'post',
               dataType:'json',
               data:{rank_class:rank_class},
               success:function(response){
                   $('#rank_subject').html(response.html);
               }
            });
        }
    }
    
       $('#department_list').on('change', function() {

        <?php  ?>
      
        var st_list = <?= json_encode(@$teacher)  ?>;
        var st_name_list = '';
        var reg_no_list = '';
        var class_student_list = $('#department_list').val();
        //console.log(class_student_list);
        // st_list = JSON.parse(JSON.stringify(st_list));

        
            $.each(st_list, function(key,val) {
                if(class_student_list == 'all'){
                    st_name_list = st_name_list + '<option value="'+val.TCH_SRLNO+'">'+val.TCH_NAME+' </option>';

                }else{
                    if (class_student_list.includes(val.DEPT_CODE)) {
                        st_name_list = st_name_list + '<option value="'+val.TCH_SRLNO+'">'+val.TCH_NAME+' </option>';
                    }
                }
            });
            $('#st_id_list').html(st_name_list);

            $('#st_id_list').multiSelect('refresh');
        
    });
//     $('#multi_religion_list').on('change', function(){
//     console.log('Selected value:', $(this).val());
// });
</script>

</body>
</html>

