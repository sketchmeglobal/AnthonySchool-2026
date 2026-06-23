<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 29-01-2019
 * Time: 10:49
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
                            //attendance add form
                            if ($form_type == 'attendance') {
                            ?>
                                <form action="<?= base_url(); ?>admin/form_attendance" method="post" class="cmxform form-horizontal tasi-form">
                                    <div class="form-group">
                                        <label for="class1" class="control-label col-lg-2 text-danger">Class & Sec *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input" id="class1" name="class" required >
                                                <option value="">Select a Class</option>
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
                                        <label for="class1" class="control-label col-lg-2 text-danger">Date *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <input type="date" class="form-control round-input" name="attendance_date" id="attendance_date" required />
                                           
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-2">
                                            <label for="" class="control-label text-danger">Tick Present Students *</label>
                                            <br>
                                            <label for="select_std">Select All</label>
                                            <input id="select_std_attendance" type="checkbox" checked="" style="width: 20px; height: 20px;">
                                        </div>
                                        <div class="col-lg-10 iconic-input" id="std_details">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="submit_attendance_form"><i class="fa fa-save"></i> Save
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            <?php
                            }
                            else if ($form_type == 'view_attendance') {
                            ?>
                                <div class="cmxform form-horizontal tasi-form">
                                   <div class="row col-md-12" style="padding:10px;">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="class1" class="control-label text-danger">Class & Sec *</label>
                                            <select class="form-control round-input" id="att_class" name="class" required>
                                                <option value="">Select a Class</option>
                                                <?php foreach($class as $val) { ?>
                                                    <option value="<?=$val['CS_SEQ'];?>"><?=$val['Class_Name'].' - '.$val['Sec_Name'];?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4" style="margin-left:10px">
                                        <div class="form-group">
                                            <label for="attendance_date" class="control-label text-danger">Date *</label>
                                            <input type="date" class="form-control round-input" name="attendance_date" id="attendance_date" />
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-left" style="margin-top:10px;">
                                        <button class="btn btn-success" type="button" onclick="searchAttendance()">
                                            <i class="fa fa-save"></i> Search
                                        </button>
                                    </div>
                                </div>
                                </div>
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sl. No</th>
                                            <th>Class Name</th>
                                            <th>Date</th>
                                             <th>Count</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="attendance_table_data">
                                       
                                    </tbody>
                                </table>

                            <?php
                            }
                             else if ($form_type == 'edit_attendance') { 
                            ?>
                                <form method="POST" action="<?php echo base_url('admin/attendance_update'); ?>">
                                    <input type="hidden" name="header_id" value="<?php echo $hdr_id?>" />
                                   <div class="row col-md-12" style="padding:10px;">
                                    <div class="col-lg-10 iconic-input" id="std_details">
                                        <?php
                                        if(!empty($std)){
                                            // echo "<pre>";
                                            // print_r($std);
                                            // die;
                                            foreach($std as $s){
                                                $isChecked = true;
                                                if (!empty($attdata)) {
                                                    foreach ($attdata as $att) {
                                                        if ($att->STD_SEQ == $s['STD_SEQ']) { // Assuming `STD_SEQ` is present in `attdata`
                                                            $isChecked = false;
                                                            break;
                                                        }
                                                    }
                                                }
                                                ?>
                                                <label class="checkbox-custom check-success col-lg-4">
                                                    <input <?php echo $isChecked ? 'checked' : ''; ?> value="<?php echo $s['STD_SEQ'] ?>" name="std[]" id="std_<?php echo $s['STD_SEQ'] ?>" type="checkbox" class="selected_std_attendance">
                                                    <label for="std_<?php echo $s['STD_SEQ'] ?>"><?php echo $s['STD_ROLLNO'] ?> - <?php echo $s['STD_FNAME'] ?> <?php echo $s['STD_MNAME'] ?> <?php echo $s['STD_LNAME'] ?></label>
                                                </label>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </div>
                                    <div class="col-md-4 text-left" style="margin-top:10px;">
                                        <button class="btn btn-success" type="submit" name="update_attendance">
                                            <i class="fa fa-save"></i> Update
                                        </button>
                                    </div>
                                </div>
                               </form>
                              

                            <?php
                            }
                            elseif ($form_type == 'add_marks') { //marks add form
                                ?>
                                <form class="cmxform form-horizontal tasi-form" id="add_marks" method="post"
                                      action="<?= base_url(); ?>admin/form_add_marks">

                                    <div class="form-group">
                                        <label for="class" class="control-label col-lg-2 text-danger">Class & Sec *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" id="class" name="class" required >
                                                <option value="">Select a class</option>
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
                                        <label for="term" class="control-label col-lg-2 text-danger">Select Term *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input" id="term" name="term" required >
                                                <option value="">Select term</option>
                                                <?php
                                                foreach($exam_terms as $term){
                                                    ?>
                                                    <option value="<?=$term['et_id'];?>"><?=$term['term_title'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="exam" class="control-label col-lg-2 text-danger">Exam Name *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input" id="exam" name="exam" required >
                                                <option value="" maximum="0">Select examination</option>
                                                <?php
                                                foreach($exam as $val){
                                                    ?>
                                                    <option value="<?=$val['EXAM_SEQ'];?>" maximum="<?=$val['Full_Marks'];?>"><?=$val['Exam_Name'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="sub" class="control-label col-lg-2 text-danger">Subject Name *</label>
                                        <div class="col-lg-10 iconic-input" id="subject_dropdown">
                                        </div>
                                    </div>

                                    <div class="form-group ">
                                        <div class="col-lg-10 col-lg-offset-1 iconic-input">
                                            <table class="table table-striped table-responsive">
                                                <thead>
                                                <tr>
                                                    <th class="col-lg-2">Roll No</th>
                                                    <th class="col-lg-5">Student Name</th>
                                                    <th class="col-lg-3">Marks</th>
                                                    <th class="col-lg-2">Grade</th>
                                                </tr>
                                                </thead>
                                                <tbody id="std_table">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="submit_add_marks">Add <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            }
                            elseif ($form_type == 'edit_marks') { //marks edit form
                                ?>
                                <form class="cmxform form-horizontal tasi-form" id="edit_marks" method="post"
                                      action="<?= base_url(); ?>admin/form_edit_marks">

                                    <div class="form-group ">
                                        <div class="col-lg-10 col-lg-offset-1 iconic-input">
                                            <table class="table table-striped table-responsive">
                                                <thead>
                                                <tr>
                                                    <th class="col-lg-2">Roll No</th>
                                                    <th class="col-lg-5">Student Name</th>
                                                    <th class="col-lg-3">Marks</th>
                                                    <th class="col-lg-2">Grade</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                foreach($marks_dtl as $dtl) {
                                                    ?>
                                                    <tr>
                                                        <td><?=$dtl['STD_ROLLNO'];?></td>
                                                        <td><?=$dtl['STD_FNAME'].' '.$dtl['STD_MNAME'].' '.$dtl['STD_LNAME'];?></td>
                                                        <td><input name="marks[<?=$dtl['MD_DTL_SRLNO'];?>]" class="marks form-control round-input" value="<?=$dtl['MD_MARKS'];?>" min="0" max="<?=$max_mark;?>" placeholder="Max marks: <?=$max_mark;?>" type="number" /></td>
                                                        <td>
                                                            <select name="grade[<?=$dtl['MD_DTL_SRLNO'];?>]" class="grade form-control round-input" id="">
                                                                <option <?=($dtl['MD_GRADE'] == 'AA') ? 'selected' : ''?> value="AA">AA</option>
                                                                <option <?=($dtl['MD_GRADE'] == 'A+') ? 'selected' : ''?> value="A+">A+</option>
                                                                <option <?=($dtl['MD_GRADE'] == 'A') ? 'selected' : ''?> value="A">A</option>
                                                                <option <?=($dtl['MD_GRADE'] == 'B+') ? 'selected' : ''?> value="B+">B+</option>
                                                                <option <?=($dtl['MD_GRADE'] == 'B') ? 'selected' : ''?> value="B">B</option>
                                                                <option <?=($dtl['MD_GRADE'] == 'C') ? 'selected' : ''?> value="C">C</option>
                                                                <option <?=($dtl['MD_GRADE'] == 'D') ? 'selected' : ''?> value="D">D</option>
                                                            </select>
                                                        </td>
                                                        <input name="marks_dtl[]" value="<?=$dtl['MD_DTL_SRLNO'];?>" type="hidden" />
                                                        <input name="grade_dtl[]" value="<?=$dtl['MD_DTL_SRLNO'];?>" type="hidden" />
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="submit_edit_marks">Update <i class="fa fa-refresh"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            }
                            elseif ($form_type == 'progress_report') { //progress report form
                                ?>
                                <form class="cmxform form-horizontal tasi-form" id="progress_report" method="post" action="<?= base_url(); ?>admin/print_progress_report" target="_blank">
                                    <div class="form-group">
                                        <label for="class1" class="control-label col-lg-2 text-danger">Class & Sec *</label>
                                        <div class="col-lg-2 iconic-input">
                                            <select class="form-control round-input" id="class1" name="class" required >
                                                <option value="">Select Class</option>
                                                <?php
                                                foreach($class as $val){
                                                    ?>
                                                    <option value="<?=$val['CS_SEQ'];?>"><?=$val['Class_Name'].' - '.$val['Sec_Name'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-lg-2">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="print_progress_report_1"><i class="fa fa-file-pdf-o"></i> Print (Type 1)
                                            </button>
                                        </div>
                                        <div class="col-lg-2">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="print_progress_report_2"><i class="fa fa-file-pdf-o"></i> Print (Class I-X)
                                            </button>
                                        </div>
                                        <div class="col-lg-2">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="print_progress_report_3"><i class="fa fa-file-pdf-o"></i> Print (Class XI-XII)
                                            </button>
                                        </div>
                                        <div class="col-lg-2">
                                            <button class="btn btn-primary" type="submit" name="submit"
                                                    value="print_progress_report_4"><i class="fa fa-file-pdf-o"></i> Print (New)
                                            </button>
                                        </div>
                                        <div class="form-group">
                                            <label for="to_class" class="control-label col-lg-2 text-danger">Select Student *</label>
                                            <div class="col-lg-10 iconic-input" id="std_progress_report">
                                              
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        
                                    </div>
                                </form>
                                <?php
                            }
                            elseif ($form_type == 'marksheet') { //marksheet form
                                ?>
                                <form class="cmxform form-horizontal tasi-form" id="marksheet" method="post" action="<?= base_url(); ?>admin/print_marksheet" target="_blank">
                                    <div class="form-group">
                                        <label for="class_marksheet" class="control-label col-lg-2 text-danger">Class & Sec *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input" id="class_marksheet" name="class" required >
                                                <option value="">Select Class</option>
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
                                        <label for="" class="control-label col-lg-2 text-danger">Select Students *</label>
                                        <div class="col-lg-10 iconic-input" id="std_details">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="print_marksheet"><i class="fa fa-file-pdf-o"></i> Print
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            }elseif ($form_type == 'student_class_update'){
                            ?>
                            <form class="cmxform form-horizontal tasi-form" id="form_student_class_update" method="post" action="<?= base_url('admin/form_update_student_class'); ?>">
                                    <div class="form-group">
                                        <label for="from_class" class="control-label col-lg-2 text-danger">Class *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" id="from_class" name="from_class" required >
                                                <option value="">Select From Class</option>
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
                                        <label for="from_class_session" class="control-label col-lg-2 text-danger">Class Session *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" id="from_class_session" name="from_class_session" required >
                                                <option value="">Select Session</option>
                                                <?php
                                                date_default_timezone_set("Asia/Kolkata");
                                                $current_year = date("Y");
                                                $next_year = strtotime($current_year);
                                                $next_year = strtotime("+1 year", $next_year);
                                                $next_year = date("Y", $next_year);
                                                $next_year = $next_year;

                                                $current_year = $current_year;
                                                ?>
                                                    <option value="<?=$next_year;?>"><?=$next_year?></option>
                                                    <option value="<?=$current_year;?>"><?=$current_year?></option>
                                                    <?php
                                                    for ($i=1; $i <=2 ; $i++) {
                                                    $last_year = strtotime(date("Y"));
                                                    $last_year = strtotime('-'.$i.' year', $last_year);
                                                    $last_year = date("Y", $last_year);
                                                    $last_year = $last_year;
                                                    ?>
                                                    <option value="<?=$last_year;?>"><?=$last_year;?></option>
                                                    <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="to_class" class="control-label col-lg-2 text-danger">Promotion Next Class *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" id="to_class" name="to_class" required >
                                                <option value="">Select Class</option>
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
                                        <label for="to_class_session" class="control-label col-lg-2 text-danger">Next Class Session*</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" id="to_class_session" name="to_class_session" required >
                                                <option value="">Select Session</option>
                                                <?php
                                                date_default_timezone_set("Asia/Kolkata");
                                                $current_year = date("Y");
                                                $next_year = strtotime($current_year);
                                                $next_year = strtotime("+1 year", $next_year);
                                                $next_year = date("Y", $next_year);
                                                $next_year = $next_year;

                                                $current_year = $current_year;
                                                ?>
                                                <option value="<?=$next_year;?>"><?=$next_year?></option>
                                                <option value="<?=$current_year;?>"><?=$current_year?></option>
                                                <?php
                                                     for ($i=1; $i <=2 ; $i++) {
                                                     $last_year = strtotime(date("Y"));
                                                     $last_year = strtotime('-'.$i.' year', $last_year);
                                                     $last_year = date("Y", $last_year);
                                                     $last_year = $last_year;
                                                ?>

                                                <option value="<?=$last_year;?>"><?=$last_year;?></option>
                                                <?php } ?>


                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="to_class" class="control-label col-lg-2 text-danger">Select Student *</label>
                                        <div class="col-lg-10 iconic-input" id="std_class_update">
                                          
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="class_update"> Update
                                            </button>
                                        </div>
                                    </div>
                            </form>
                            <?php } ?>
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

<!-- common js -->
<?php $this->load->view('components/_common_js'); //left side menu ?>
<!-- /common js -->

<script>
 function searchAttendance(){
        var class_id = $('#att_class').val();
        var att_date = $('#attendance_date').val();
        $.ajax({
            url:'<?php echo base_url()?>admin/ajax_search_attendance',
            type:'post',
            dataType:'json',
            data:{class_id:class_id,att_date:att_date},
            success:function(response){
                $('#attendance_table_data').html(response.html);
            }
        });
    }
    //update student table data on class change
    $('#class').change(function(){
        class_id = $('#class').val();
        exam_id = $('#exam').val();
        data = {
            'class_id': class_id,
            'exam_id': exam_id
        };
        $.ajax({
            url: "<?=base_url();?>admin/ajax_update_std_marks_table",
            type: "post",
            data: data,
            success: function(data) {
                data = $.parseJSON(data);
                $('#subject_dropdown').html(data['html_sub']);
                $('#std_table').html(data['html_std']);
            }
        });
    });

    $('#from_class_session, #from_class').change(function(){
        var from_class = $('#from_class').val();

        var from_class_session = $("#from_class_session").val();
        data = {
            'from_class': from_class,
            'from_class_session': from_class_session,

        };
        $.ajax({
            url: "<?=base_url();?>admin/ajax_update_std_class_table",
            type: "post",
            data: data,
            success: function(data) {
                data = $.parseJSON(data);
                $('#std_class_update').html(data['html_std']);
                $('#checkbox_select_all').on('change', function () {
                    const isChecked = $(this).prop('checked');
                    $('input[name="std_class[]"]').prop('checked', isChecked);
                });
            }
        });
    });
    
     $('#class1').change(function(){
        var from_class = $('#class1').val();

        var from_class_session = <?php echo CURRENT_YEAR?>;
        data = {
            'from_class': from_class,
            'from_class_session': from_class_session,

        };
        $.ajax({
            url: "<?=base_url();?>admin/ajax_update_std_class_table",
            type: "post",
            data: data,
            success: function(data) {
                data = $.parseJSON(data);
                $('#std_progress_report').html(data['html_std']);
                $('#checkbox_select_all').on('change', function () {
                    const isChecked = $(this).prop('checked');
                    $('input[name="std_class[]"]').prop('checked', isChecked);
                });
            }
        });
    });
    
     

    
   
    
    

    //change all mark's max value on exam change
    $('#exam').change(function(){
        max = $('#exam option:selected').attr('maximum');
        $('.marks').attr('max', max);
        $('.marks').attr('placeholder', 'Max marks: '+max);
    });

    // hide student list if marks already entered on subject select
    $("#exam, #term, #class").on('change', function(){
        hide_std_on_entered_marks();
    });

    $(document).on('change', "#class_subjects", function(){
        hide_std_on_entered_marks()    
    })

    function hide_std_on_entered_marks(){
        var classs = $('#class').val();
        var term = $('#term').val();
        var exam = $('#exam').val();
        var subject = $("#class_subjects").val();
        checker = 1;

        if(classs != '' && term != '' && exam != '' && subject != ''){
            data = {
                'class': classs,
                'term': term,
                'exam': exam,
                'subject': subject
            };
            $.ajax({
                url: "<?=base_url();?>admin/ajax_fetch_std_on_marks_entry",
                type: "post",
                data: data,
                dataType: 'json',
                success: function(rdata) {
                    console.log(rdata)
                    if(rdata == 'entered'){
                        alert('Data already entered. Please go to edit.');
                        $(".marks").attr('readonly',true)
                        $(".grade").attr('disabled', true)
                        $("button[name='submit']").attr('disabled', true);
                        checker = 0
                    }else{
                        $(".marks").attr('readonly',false)
                        $(".grade").attr('disabled', false)
                        $("button[name='submit']").attr('disabled', false);
                    }
                }, 
                complete:function(){
                    if(checker == 1){
                        //decide grade or marks
                        data = {
                            'subid': subject,
                        };
                        $.ajax({
                            dataType: 'json',
                            url: "<?=base_url();?>admin/ajax_mark_type_on_subject",
                            type: "post",
                            data: data,
                            success: function(rdata) {
                                console.log(rdata)
                                if(rdata == 'Marks'){
                                    $(".marks").attr('readonly', false)
                                    $(".grade").attr('disabled', true)
                                }else{
                                    $(".marks").attr('readonly', true)
                                    $(".grade").attr('disabled', false)
                                }
                            }
                        });
                    }
                }
            });
        }
    }

    //update student table data on class change
    $('#class_marksheet').change(function(){
        class_id = $('#class_marksheet').val();
        data = {
            'class_id': class_id
        };
        $.ajax({
            url: "<?=base_url();?>admin/ajax_update_std_admit_card",
            type: "post",
            data: data,
            success: function(data) {
                data = $.parseJSON(data);
                $('#std_details').html(data['html_std']);
            }
        });
    });

    //update student table data on class change
    $('#class1').change(function(){
        class_id = $('#class1').val();
        data = {
            'class_id': class_id
        };
        $.ajax({
            url: "<?=base_url();?>admin/ajax_update_std_attendance",
            type: "post",
            data: data,
            success: function(data) {
                data = $.parseJSON(data);
                $('#std_details').html(data['html_std']);
            }
        });
    });

    $(document).on("click","#checkbox_select_all",function() {
        if($(this).prop("checked")) {
            $(".checkbox_msksht").prop('checked', true).change();
        } else {
            $(".checkbox_msksht").prop('checked', false).change();
        }
    });

    $(document).on("click","#select_std_attendance",function() {
        if($(this).prop("checked")) {
            $(".selected_std_attendance").prop('checked', true).change();
        } else {
            $(".selected_std_attendance").prop('checked', false).change();
        }
    });

    $('#add_marks').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) { 
            console.log('Disabled')
            e.preventDefault();
            return false;
        }
    });
    
    $(document).on("blur",".marks",function() {
        mark = $(this).val()
        if(mark<101 && mark>89){
            $(this).parent().next('td').find('select').val("AA")
        } else if(mark<91 && mark>79){
            $(this).parent().next('td').find('select').val("A+")
        } else if(mark<81 && mark>69){
            $(this).parent().next('td').find('select').val("A")
        } else if(mark<71 && mark>59){
            $(this).parent().next('td').find('select').val("B+")
        } else if(mark<61 && mark>49){
            $(this).parent().next('td').find('select').val("B")
        } else if(mark<51 && mark>39){
            $(this).parent().next('td').find('select').val("C")
        } else if(mark<41){
            $(this).parent().next('td').find('select').val("D")
        }
    })
   
</script>
</body>
</html>