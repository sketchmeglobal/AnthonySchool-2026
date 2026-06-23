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
            <div class="col-sm-12 text-center">
            <a href="<?= base_url('library/add_issue_books') ?>" class="btn btn-success"><i class="fa fa-plus"></i>    Issue Books   </a>
            </div>
            </div>
            <br/>


            <!--Filter-->
            <div class="row">
                <div class="col-sm-12">
                    <section class="panel">
                        <header class="panel-heading">
                            Filter Details
                            <span class="tools pull-right">
                                <a class="t-collapse fa fa-chevron-down" href="javascript:;"></a>
                            </span>
                        </header>
                        <div class="panel-body">
                                <div class="form-group ">
                                    <label class="control-label col-lg-1">Select Book</label>
                                    <div class="col-lg-2">
                                        <select id="filter_book_id" name="filter_book_id" class="select2 form-control round-input">
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
                                        <select id="filter_class_id" name="filter_class_id" class="select2 form-control round-input">
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
                                        <select id="filter_student_id" name="filter_student_id" class="select2 form-control round-input">
                                            <option value="">Select Student</option>
                                            <?php
                                            foreach($student_details as $val) {
                                                ?>
                                                <option value="<?=$val['STD_SEQ']?>"><?=   $val['ST_FULL_NAME']   ?> - <?=   $val['STD_REGNO']   ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>


                                    <div class="col-lg-2">
                                        <button value="inst_filter" class="btn btn-primary button_search" type=""><i class="fa fa-search"> Search</i></button>
                                    </div>
                                </div>

                            </br>
                        </br>
                            <div style="text-align: center; border-top: 1px solid black">
                                <div style="display: inline-block; position: relative; top: -10px; background-color: white; padding: 0px 10px">OR</div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>


            <div class="row">
                <div class="col-lg-12">
                    <section class="panel">
                        <header class="panel-heading">
                            <?=$section_heading;?>
                        </header>
                        <div class="panel-body">
                            <table id="book_issue_details_tables" class="table data-table dataTable">
                                <thead>
                                    <tr>
                                        <th>Book Name</th>
                                        <th>Student Name</th>
                                        <th>Issue Date</th>
                                        <th>Return Date</th>
                                        <th>Have Returned?</th>
                                        <th>Fine Amount</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
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
    $(document).ready(function() {
        $('#book_issue_details_tables').DataTable( {
            "processing": true,
            "language": {
                processing: '<img src="<?=base_url('assets/img/ellipsis.gif')?>"><span class="sr-only">Processing...</span>',
            },
            "serverSide": true,
            "ajax": {
                "url": "<?=base_url('ajax_book_issue_table_data')?>",
                "type": "POST",
                "dataType": "json",
            },
            //will get these values from JSON 'data' variable
            "columns": [
                { "data": "Book_Name" },
                { "data": "ST_FULL_NAME" },
                { "data": "issue_date" },
                { "data": "return_date" },
                { "data": "returned_or_not" },
                { "data": "fine_amount" },
                { "data": "actions" },
            ],
            //column initialisation properties
            "columnDefs": [{
                "targets": [6], //disable 'Actions' column sorting
                "orderable": false,
            }]
        } );

        $("#book_issue_details_tables").on("click", ".delete_issued_book", function () {
            id = $(this).attr('id');
            $.ajax({
                url: "<?= base_url('ajax_delete_issued_book') ?>",
                method: "post",
                dataType: 'json',
                data: {'id':id,},
                success: function(returnData){
                    notification(returnData);
                },
            });

            //refresh table
            $('#book_issue_details_tables').DataTable().ajax.reload();
        });
    } );

    $(document).on('click', '.button_search', function(){
        $filter_book_id = $('#filter_book_id').val();
        $filter_class_id = $('#filter_class_id').val();
        $filter_student_id = $('#filter_student_id').val();      
        $('#book_issue_details_tables').DataTable().destroy();
        $('#book_issue_details_tables').DataTable( {
            "processing": true,
            "language": {
                processing: '<img src="<?=base_url('assets/img/ellipsis.gif')?>"><span class="sr-only">Processing...</span>',
            },
            "serverSide": true,
            "ajax": {
                "url": "<?=base_url('library/form_issue_book_filter')?>",
                "type": "POST",
                "data": {
                "filter_book_id": $filter_book_id,
                "filter_class_id": $filter_class_id,
                "filter_student_id": $filter_student_id
                },
                "dataType": "json",
            },
            //will get these values from JSON 'data' variable
            "columns": [
                { "data": "Book_Name" },
                { "data": "ST_FULL_NAME" },
                { "data": "issue_date" },
                { "data": "return_date" },
                { "data": "returned_or_not" },
                { "data": "fine_amount" },
            ],
            //column initialisation properties
            "columnDefs": [{
                "targets": [3,4], //disable 'Image','Actions' column sorting
                "orderable": false,
            }]
        } );
        

    });   
</script>    

</body>
</html>