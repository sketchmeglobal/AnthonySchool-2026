<?php
/**
 * Coded by: Pran Krishna Das
 * Web: pran.dev
 * Social: twitter.com/MrPran93
 * CI: 3.0.6
 * Date: 21-03-2023
 * Time: 17:56
 * Project: school-management-system-stanthony
 */
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title><?=$tab_title.' | '.WEBSITE_NAME;?></title>
    <meta name="description" content="admin panel">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- common head -->
    <?php $this->load->view('components/_common_head'); //left side menu ?>
    <!-- /common head -->

    <!--switchery-->
    <link href=<?=base_url('assets/admin_panel/js/switchery/switchery.min.css')?> rel="stylesheet" type="text/css"
        media="screen" />
    <style>
    .spinner_image {
        position: relative;
        top: -320px;
        left: 23%;
    }

    .opacity_class {
        opacity: 0.6;
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
                <h3 class="m-b-less"><?=$menu_name;?> (for <strong><?=$usertype?></strong>)</h3>
                <div class="state-information">
                    <ol class="breadcrumb m-b-less bg-less">
                        <li><a href="<?=base_url('admin/dashboard');?>">Home</a></li>
                        <li class="active"> <?=$menu_name;?></li>
                    </ol>
                </div>
            </div>
            <!-- page head end-->

            <!--body wrapper start-->
            <div class="wrapper">

                <?php
            //set user permissions
            if($section == 'set_user_permissions') {
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <section class="panel">
                            <header class="panel-heading">
                                Grant / Deny Permissions
                            </header>
                            <div class="panel-body">
                                <form id="sample_form" action="<?=base_url();?>admin/form_set_user_permissions"
                                    method="post" class="cmxform form-horizontal tasi-form">
                                    <div class="form-group">
                                        <div class="col-lg-12">
                                            <?php
                                            foreach ($menus as $menu) {
                                                ?>
                                            <label class="control-label col-lg-4"><?=$menu->menu_name?>
                                                <br />
                                                <input name="permission[<?=$menu->menu_id?>]"
                                                    <?php if(isset($prm_arr[$menu->menu_id]) && $prm_arr[$menu->menu_id] == 1) echo 'checked';?>
                                                    type="checkbox" class="js-switch-style1_pran" />
                                            </label>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                    </div>

                                    <input type="hidden" name="user_id" value="<?=$user_id?>">

                                    <div class="form-group">
                                        <div class="col-lg-12 text-center">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                value="submit_set_user_permissions_form"><i class="fa fa-key"></i> Set
                                                Permissions</i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <img class="spinner_image" src="<?=base_url();?>assets/admin_panel/img/loading_spinner.gif"
                                style="display: none;" />
                        </section>
                    </div>
                </div>
                <?php
            }

            elseif ($section == 'student_control') {
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <section class="panel">
                            <header class="panel-heading">
                                Manage Students
                            </header>
                            <div class="panel-body">
                                <form action="<?= base_url(); ?>admin/form_student_control" method="post"
                                    class="cmxform form-horizontal tasi-form">
                                    <div class="form-group">
                                        <label for="select_class" class="control-label col-lg-2 text-danger">Class & Sec
                                            *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input" id="select_class" name="class"
                                                required>
                                                <option value="">Select a Class</option>
                                                <?php
                                                foreach($class as $val){
                                                    ?>
                                                <option value="<?=$val['CS_SEQ'];?>">
                                                    <?=$val['Class_Name'].' - '.$val['Sec_Name'];?></option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-2">
                                            <label for="" class="control-label text-danger">Select Students *</label>
                                            <br>
                                            <label for="select_all">Select All</label>
                                            <input id="select_all" type="checkbox" style="width: 20px; height: 20px;">
                                        </div>

                                        <div class="col-lg-10 iconic-input" id="std_details">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-danger" type="submit" name="submit"
                                                value="block_login"><i class="fa fa-ban"></i> Block Login
                                            </button>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-2">
                                            <label for="" class="control-label text-danger">Select Students *</label>
                                            <br>
                                            <label for="select_all2">Select All</label>
                                            <input id="select_all2" type="checkbox" style="width: 20px; height: 20px;">
                                        </div>

                                        <div class="col-lg-10 iconic-input" id="std_details2">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-warning" type="submit" name="submit"
                                                value="block_marksheet"><i class="fa fa-ban"></i> Block Progress Report
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <form action="<?= base_url(); ?>admin/update_fees_month" method="post"
                                            class="cmxform form-horizontal tasi-form">
                                            <div class="form-group">
                                                <div class="col-lg-6">
                                                    <label for="" class="control-label text-danger">Fees Clear Till
                                                    </label>
                                                    <br>

                                                    <select class="form-control" name="month">
                                                        <option value="">Select</option>
                                                        <option value="1"
                                                            <?php if ($settings->student_due_fees_month == "1") { echo "selected"; } ?>>
                                                            January</option>
                                                        <option value="2"
                                                            <?php if ($settings->student_due_fees_month == "2") { echo "selected"; } ?>>
                                                            February</option>
                                                        <option value="3"
                                                            <?php if ($settings->student_due_fees_month == "3") { echo "selected"; } ?>>
                                                            March</option>
                                                        <option value="4"
                                                            <?php if ($settings->student_due_fees_month == "4") { echo "selected"; } ?>>
                                                            April</option>
                                                        <option value="5"
                                                            <?php if ($settings->student_due_fees_month == "5") { echo "selected"; } ?>>
                                                            May</option>
                                                        <option value="6"
                                                            <?php if ($settings->student_due_fees_month == "6") { echo "selected"; } ?>>
                                                            June</option>
                                                        <option value="7"
                                                            <?php if ($settings->student_due_fees_month == "7") { echo "selected"; } ?>>
                                                            July</option>
                                                        <option value="8"
                                                            <?php if ($settings->student_due_fees_month == "8") { echo "selected"; } ?>>
                                                            August</option>
                                                        <option value="9"
                                                            <?php if ($settings->student_due_fees_month == "9") { echo "selected"; } ?>>
                                                            September</option>
                                                        <option value="10"
                                                            <?php if ($settings->student_due_fees_month == "10") { echo "selected"; } ?>>
                                                            October</option>
                                                        <option value="11"
                                                            <?php if ($settings->student_due_fees_month == "11") { echo "selected"; } ?>>
                                                            November</option>
                                                        <option value="12"
                                                            <?php if ($settings->student_due_fees_month == "12") { echo "selected"; } ?>>
                                                            December</option>


                                                    </select>
                                                </div>

                                            </div>
                                            <div class="form-group">
                                                <div class=" col-lg-6">
                                                    <button class="btn btn-primary" type="submit" name="submit"
                                                        value="submit_months"><i class="fa fa-ban"></i> Submit
                                                    </button>
                                                </div>
                                            </div>


                                        </form>
                                    </div>
                                    <div class="col-lg-6">
                                        <div id="msg-box"></div>

                                        <form id="studentForm" class="form-horizontal">
                                            <label>Select Student by Registration Number</label>
                                            <select name="search" id="search" class="form-control" required>
                                                <option value="">-- Choose Student --</option>
                                                <?php foreach ($students as $student): ?>
                                                <option value="<?= $student['STD_SEQ'] ?>"
                                                    data-dob="<?= $student['STD_DOB'] ?>"
                                                    data-reg="<?= $student['STD_REGNO'] ?>">
                                                    <?= $student['STD_REGNO'] ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>

                                            <div class="form-group mt-3" style="width:100%;">
                                                <label>DOB</label>
                                                <input type="text" class="form-control" id="dob" readonly>
                                            </div>
                                            <div class="form-group" style="width:100%;">
                                                <label>Reg No</label>
                                                <input type="text" class="form-control" id="regno" readonly>
                                            </div>

                                            <button type="submit" class="btn btn-primary mt-3">Submit</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
                <?php
            }
            ?>

            </div>
            <!--body wrapper end-->


            <!--footer section start-->
            <?php $this->load->view('components/footer'); ?>
            <!--footer section end-->

        </div>
        <!-- body content end-->
    </section>
    <!-- jQuery (required by Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Placed js at the end of the document so the pages load faster -->
    <script src="<?=base_url();?>assets/admin_panel/js/jquery-1.10.2.min.js"></script>

    <!-- common js -->
    <?php $this->load->view('components/_common_js'); //left side menu ?>
    <!--form validation-->
    <script src="<?=base_url();?>assets/admin_panel/js/jquery.validate.min.js"></script>
    <!--ajax form submit-->
    <script src="<?=base_url();?>assets/admin_panel/js/jquery.form.min.js"></script>

    <!--switchery-->
    <script src=<?=base_url('assets/admin_panel/js/switchery/switchery.min.js')?>></script>
    <script src=<?=base_url('assets/admin_panel/js/switchery/switchery-init.js')?>></script>

    <script>
    $(document).ready(function() {
        $('#sample_form').ajaxForm({
            beforeSubmit: function() {
                $('.spinner_image').show();
                $('.panel-body').addClass('opacity_class');
                return $("#sample_form")
                    .valid(); // TRUE when form is valid, FALSE will cancel submit
            },
            success: function(returnData) {
                $('.spinner_image').hide();
                $('.panel-body').removeClass('opacity_class');
                obj = JSON.parse(returnData);
                notification(obj);
            }
        });

        //update student table data on class change
        $('#select_class').change(function() {
            class_id = $('#select_class').val();
            data = {
                'class_id': class_id
            };
            $.ajax({
                url: "<?=base_url();?>admin/ajax_fetch_students_by_class",
                type: "post",
                data: data,
                success: function(data) {
                    data = $.parseJSON(data);
                    $('#std_details').html(data['html_std']);
                    $('#std_details2').html(data['html_std2']);
                }
            });
        });

        //select all students
        $(document).on("click", "#select_all", function() {
            if ($(this).prop("checked")) {
                $(".select_all").prop('checked', true).change();
            } else {
                $(".select_all").prop('checked', false).change();
            }
        });
        $(document).on("click", "#select_all2", function() {
            if ($(this).prop("checked")) {
                $(".select_all2").prop('checked', true).change();
            } else {
                $(".select_all2").prop('checked', false).change();
            }
        });

    });
    </script>
    <script>
    $(document).ready(function() {
        $('#search').select2({
            placeholder: "Search by Reg No",
            allowClear: true
        });

        $('#search').on('change', function() {
            const selected = $(this).find(':selected');
            $('#dob').val(selected.data('dob'));
            $('#regno').val(selected.data('reg'));
        });
    });
    </script>
    <script>
    $(document).ready(function() {
        $('#search').on('change', function() {
            const selected = $(this).find(':selected');
            $('#dob').val(selected.data('dob'));
            $('#regno').val(selected.data('reg'));
        });

        $('#studentForm').on('submit', function(e) {
            e.preventDefault();
            const formData = $(this).serialize();

            $.ajax({
                url: "<?= base_url('admin/permission_student_for_result') ?>",
                type: "POST",
                data: formData,
                success: function(response) {
                    $('#msg-box').html('<div class="alert alert-info">' + response +
                        '</div>');
                    $('#studentForm')[0].reset();
                    $('#dob').val('');
                    $('#regno').val('');
                },
                error: function() {
                    $('#msg-box').html(
                        '<div class="alert alert-danger">Something went wrong.</div>');
                }
            });
        });
    });
    </script>

</body>

</html>