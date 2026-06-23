<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 02-02-2019
 * Time: 12:55
 */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title><?=$tab_title.' | '.WEBSITE_NAME?></title>
<meta name="description" content="admin panel">


<!--Data Table-->
<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/library_panel/js/DataTables/DataTables-1.10.18/css/dataTables.bootstrap.min.css"/>
<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/library_panel/js/DataTables/Buttons-1.5.6/css/buttons.bootstrap.min.css"/>
<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/library_panel/js/DataTables/Responsive-2.2.2/css/responsive.bootstrap.min.css"/>


<!--Select2-->
<link href="<?=base_url();?>assets/admin_panel/css/select2.css" rel="stylesheet">
<link href="<?=base_url();?>assets/admin_panel/css/select2-bootstrap.css" rel="stylesheet">

<!--iCheck-->
<link href="<?=base_url();?>assets/admin_panel/js/icheck/skins/all.css" rel="stylesheet">


<!-- common head -->
<?php $this->load->view('components/_common_head'); //left side menu ?>
<!-- /common head -->


<style>
    .container {
  display: block;
  position: relative;
  padding-left: 35px;
  margin-bottom: 12px;
  cursor: pointer;
  font-size: 22px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default radio button */
.container input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

/* Create a custom radio button */
.checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 25px;
  width: 25px;
  background-color: #eee;
  border-radius: 50%;
}

/* On mouse-over, add a grey background color */
.container:hover input ~ .checkmark {
  background-color: #ccc;
}

/* When the radio button is checked, add a blue background */
.container input:checked ~ .checkmark {
  background-color: #2196F3;
}

/* Create the indicator (the dot/circle - hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}


.hidden {
    display: none;
}


/* Show the indicator (dot/circle) when checked */
.container input:checked ~ .checkmark:after {
  display: block;
}

/* Style the indicator (dot/circle) */
.container .checkmark:after {
  top: 9px;
  left: 9px;
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background: white;
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



          



            <!--Filter-->
            <div class="row">
                <div class="col-sm-12">
                    <section class="panel">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-2"></div>
                            <div class="col-sm-4">
                                <label class="container">For Individual Students
                                <input type="radio" id="studns" name="studns" value="<?=base_url($url_param1)?>" onchange="javascript:location.href = this.value;">
                                <span class="checkmark"></span>
                                </label>
                            </div>
                            <div class="col-sm-4">
                                <label class="container">For Multiple Students Of A Class
                                <input type="radio" id="clss" name="clss" value="<?=base_url($url_param2)?>" onchange="javascript:location.href = this.value;">
                                <span class="checkmark"></span>
                                </label>
                            </div>
                        </div>
                        <br/>
                        <?php
                        //For Individual Students
                        if($segment_val == 1) {
                            ?>
                            <div class="row">
                                <div class="form-group">
                                    <?php
                                    $this->db->select('class_sec_hdr.Class_Name, class_sec_hdr.Sec_Name, student_details.STD_FNAME,
                                    student_details.STD_MNAME,student_details.STD_LNAME, student_details.STD_REGNO,student_details.STD_SEQ');
                                    $this->db->join('class_sec_hdr','class_sec_hdr.CS_SEQ=student_details.STD_CS_SEQ');
                                    $this->db->order_by('STD_CS_SEQ,STD_ROLLNO', 'asc');
                                    $result = $this->db->get_where('student_details',array('STD_LEFT' => 0, 'STD_STATUS' => 0))->result_array();
                                    ?>
                                    <label class="control-label col-lg-2 text-danger">Select Student *</label>
                                    <div class="col-lg-10 iconic-input">
                                        <select class="form-control select2" onchange="javascript:location.href = this.value;" >
                                            <option value="">Select student</option>
                                            <?php if(!isset($std_id)){ $std_id = 0; } foreach ($result as $std) {
                                                ?>
                                                <option value="<?=base_url($url_param1.'/'.$std['STD_SEQ'])?>" <?=($std_id == $std['STD_SEQ'])?'selected':''?>> <?=$std['STD_REGNO'].' - '.$std['STD_FNAME'].' '.$std['STD_MNAME'].' '.$std['STD_LNAME'].' ( '.$std['Class_Name'].' '.$std['Sec_Name'].' )'?></option>
                                                <?php
                                            }
                                           ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }

                        //For Multiple Students Of A Class
                        elseif($segment_val == 2) {
                            ?>
                            <div class="row">
                            <div class="form-group">
                            <?php
                            $result_class = $this->db->get('class_sec_hdr')->result_array();
                            ?>
                            <label class="control-label col-lg-2 text-danger">Select Class *</label>
                            <div class="col-lg-10 iconic-input">
                                <select class="form-control select2" onchange="javascript:location.href = this.value;" >
                                    <option value="">Select Class</option>
                                    <?php foreach ($result_class as $stds) {
                                        ?>
                                        <option value="<?=base_url($url_param2.'/'.$stds['CS_SEQ'])?>" <?=($class_id == $stds['CS_SEQ'])?'selected':''?>> <?=$stds['Class_Name'] . ' - ' . $stds['Sec_Name']?></option>
                                        <?php
                                    }
                                   ?>
                                </select>
                            </div>
                            </div>
                            </div>
                            <?php
                        }

                        if (isset($section_heading)) {
                            ?>
                            <hr/>
                            <div class="row">
                                <div class="col-sm-12" style="text-align: center;">
                                    <?= $section_heading ?>
                                </div>
                            </div>
                            <?php
                            if (isset($book_list)) {
                                ?>
                                <hr/>
                                <div class="row">
                                    <div class="col-sm-12" style="background-color: lightgray">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <h4 class="text-center text-primary"><strong>Available Books of <?= $class_sect ?></strong></h4>

                                                <label class="control-label col-lg-2">Search by Book Name</label>
                                                <div class="col-lg-4 iconic-input">
                                                    <i class="fa fa-search"></i>
                                                    <input type="text" onchange="javascript:location.href = '<?=base_url()."library/add_issue_books/".$this->uri->segment(3)."/".$this->uri->segment(4)."?b="?>'+this.value" class="form-control round-input">
                                                </div>

                                                <label class="control-label col-lg-2">Search by Accession No.</label>
                                                <div class="col-lg-4 iconic-input">
                                                    <i class="fa fa-search"></i>
                                                    <input type="text" onchange="javascript:location.href = '<?=base_url()."library/add_issue_books/".$this->uri->segment(3)."/".$this->uri->segment(4)."?acn="?>'+this.value" class="form-control round-input">
                                                </div>
                                            </div>
                                        </div>
                                        <br/>
                                    </div>

                                    <div class="col-sm-12 change_available_book_class_values">
                                        <div class="row">
                                            <form id="form_book_add_filter" method="post" action="<?= base_url('library/form_book_add_filter') ?>" class="cmxform form-horizontal tasi-form">
                                                <?php
                                                foreach ($book_list as $b_l) {
                                                    ?>
                                                    <div class="col-sm-2" style="text-align: center;height: 200px;">
                                                        <div class="card">
                                                            <?php if ($b_l['img'] == '') { ?>
                                                                <img class="card-img-top" src="<?= base_url('assets/img/book_images/default_books.jpeg') ?>" alt="Card image cap" style="height: 80px;">
                                                            <?php } else { ?>
                                                                <img class="card-img-top" src="<?= base_url('assets/img/book_images/' . $b_l['img']) ?>" alt="Card image cap" style="height: 80px;">
                                                            <?php } ?>
                                                            <div class="card-body">
                                                                <h5 class="card-title"><strong><?= $b_l['Book_Name'] ?></strong></h5>
                                                            </div>
                                                            <div class="card-body" style="text-align: center;">
                                                                <strong><?= $b_l['Accession_No'] ?></strong>
                                                                <input type="checkbox" id="book_select" name="book_select" class="book_select" value="<?= $b_l['BOOK_SEQ'] ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="col-sm-5 hidden_table hidden" style="border: 1px solid black;">
                                        <?php
                                        if ($segment_val == 1) {
                                        ?>
                                        <form id="form_book_list" target="_blank" method="post"
                                              action="<?= base_url('library/add_book_issue_detail') ?>">
                                            <input type="hidden" name="student_id" value="<?= $std_id ?>"/>
                                            <?php } elseif ($segment_val == 2) { ?>
                                            <form id="form_book_list" target="_blank" method="post"
                                                  action="<?= base_url('library/add_book_issue_detail_for_multiple_student_lists') ?>">
                                                <input type="hidden" name="class_id" value="<?= $class_id ?>"/>
                                                <?php } ?>
                                                <table class="table data-table dataTable">
                                                    <thead>
                                                    <tr>
                                                        <th>Book <br/> Name</th>
                                                        <th>Issue <br/> Date</th>
                                                        <th>Return <br/> Date</th>
                                                        <th class="text-center">Action
                                                            <small><input type="checkbox" id="approve_all"
                                                                          class="iCheck-square-green"> (Select
                                                                All)</small>
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody id="book_issue_details_tables">

                                                    </tbody>
                                                </table>
                                                <div class="col-lg-offset-4 col-lg-4">
                                                    <button name="submit" value="print"
                                                            class="hidden hidden_obj btn btn-success btn-block"
                                                            type="submit"><i class="fa fa-check"></i>Proceed
                                                    </button>
                                                    <br/>
                                                </div>
                                            </form>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                            <?php
                        }
                        ?>
                    </div>
                    </section>
                </div>
            </div>
        </div>


        </div>
        <!--body wrapper end-->


        <!--footer section start-->
<!--        --><?php //$this->load->view('components/footer'); ?>
        <!--footer section end-->

    </div>
    <!-- body content end-->
</section>

<!-- Placed js at the end of the document so the pages load faster -->
<script src="<?=base_url();?>assets/admin_panel/js/jquery-1.10.2.min.js"></script>
<!--<script src="--><?//=base_url();?><!--assets/library_panel/js/jquery-migrate.js"></script>-->

<!--form validation-->
<!--<script src="--><?//=base_url();?><!--assets/library_panel/js/jquery.validate.min.js" type="text/javascript"></script>-->
<!--form validation init-->
<!--<script src="--><?//=base_url();?><!--assets/library_panel/js/form-validation-init.js"></script>-->

<!-- common js -->
<?php $this->load->view('components/_common_js'); //left side menu ?>
<!--Data Table-->
<script type="text/javascript" src="<?=base_url()?>assets/library_panel/js/DataTables/JSZip-2.5.0/jszip.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/library_panel/js/DataTables/pdfmake-0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/library_panel/js/DataTables/pdfmake-0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/library_panel/js/DataTables/DataTables-1.10.18/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/library_panel/js/DataTables/DataTables-1.10.18/js/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/library_panel/js/DataTables/Buttons-1.5.6/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/library_panel/js/DataTables/Buttons-1.5.6/js/buttons.bootstrap.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/library_panel/js/DataTables/Buttons-1.5.6/js/buttons.colVis.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/library_panel/js/DataTables/Buttons-1.5.6/js/buttons.flash.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/library_panel/js/DataTables/Buttons-1.5.6/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/library_panel/js/DataTables/Buttons-1.5.6/js/buttons.print.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/library_panel/js/DataTables/Responsive-2.2.2/js/dataTables.responsive.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>assets/library_panel/js/DataTables/Responsive-2.2.2/js/responsive.bootstrap.min.js"></script>
<!--Select2-->
<script src="<?=base_url();?>assets/admin_panel/js/select2.js" type="text/javascript"></script>
<!--Icheck-->
<script src="<?=base_url();?>assets/admin_panel/js/icheck/skins/icheck.min.js"></script>
<script src="<?=base_url();?>assets/admin_panel/js/icheck-init.js"></script>
<!--form validation-->
<script src="<?=base_url();?>assets/admin_panel/js/jquery.validate.min.js"></script>
<!--ajax form submit-->
<script src="<?=base_url();?>assets/admin_panel/js/jquery.form.min.js"></script>
<!-- /common js -->
<script>
    $(document).ready(function(){
    $("#form_book_add_filter").on("change", "input:checkbox", function(){
        if ($(this).prop('checked')==true){
        $("#form_book_add_filter").submit();
        $(this).siblings('input[type="checkbox"]').attr('checked', false);
        $(this).hide();
    }
    });
    });
    $("#form_book_add_filter").validate({
        rules: {

        },
        messages: {

        }
    });
    $('#form_book_add_filter').ajaxForm({
        beforeSubmit: function () {
            return $("#form_book_add_filter").valid(); // TRUE when form is valid, FALSE will cancel submit
        },
        success: function (returnData) {
            $('.hidden_table').removeClass('hidden');
            $('.change_available_book_class_values').removeClass('col-sm-12');
            $('.change_available_book_class_values').addClass('col-sm-7');
            obj = JSON.parse(returnData);
            $('#book_issue_details_tables').append(obj.html);
            $('#book_issue_details_tables .iCheck').iCheck({
                checkboxClass: 'icheckbox_square-green',
                radioClass: 'iradio_square-green',
                increaseArea: '20%' // optional
            });

            if(obj.status == 'error'){
                $('.hidden_obj').addClass('hidden');
                notification(obj);
            } else {
                $('.hidden_obj').removeClass('hidden');
            }

            //uncheck approve_all checkbox without triggering change event
            $("#approve_all").attr('checked', false);
            $('#approve_all').iCheck('update');
        }
    });


    //form_pm_internship_completed validation and submit
    $("#form_book_list").validate({
        rules: {

        },
        messages: {

        }
    });
    $('#form_book_list').ajaxForm({
        beforeSubmit: function () {
            return $("#form_book_list").valid(); // TRUE when form is valid, FALSE will cancel submit
        },
        success: function (returnData) {
            obj = JSON.parse(returnData);
            notification(obj);
            $('#book_issue_details_tables').html('');
            $('.hidden_table').addClass('hidden');
            $('.change_available_book_class_values').addClass('col-sm-12');
            $('.change_available_book_class_values').removeClass('col-sm-7');
        }
    });
</script>    

</body>
</html>