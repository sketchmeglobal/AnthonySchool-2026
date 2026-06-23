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
                            <?php if($form_type == 'fees_related_transfer'){ ?>
                                <form class="cmxform form-horizontal tasi-form" id="std_consc_report" method="post" action="<?= base_url(); ?>admin/form_fees_related_transfer">
                                    <div class="form-group">
                                        <label for="class_student_list" class="control-label col-lg-2 text-danger">From Date *</label>
                                        <div class="col-lg-2 iconic-input">
                                            <input class="form-control inputmaskDate" onblur="get_fees_data()" style="padding: 10px !important;" id="from_date" value="<?=$this->session->userdata('fees_related_transfer_form_date')?>" name="from_date" required type="text" placeholder="dd/mm/yyyy"/>
                                        </div>

                                        <label for="class_student_list" class="control-label col-lg-1 text-danger">To Date *</label>
                                        <div class="col-lg-2 iconic-input">
                                            <input class="form-control inputmaskDate" onblur="get_fees_data()" style="padding: 10px !important;" id="to_date" value="<?=$this->session->userdata('fees_related_transfer_to_date')?>" name="to_date" required type="text" placeholder="dd/mm/yyyy"/>
                                        </div>
                                        <div class="col-lg-3 iconic-input">
                                            <select class="form-control select2" onchange="get_fees_data()" id="school" name="school">
                                                <option value="all">All</option>
                                                <?php foreach ($class_type as $key => $class_type_value) {?>
                                                <option value="<?=$class_type_value['ct_id']?>"><?=$class_type_value['name']?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-lg-2 iconic-input">
                                            <select class="form-control select2" id="fees_type" required onchange="get_fees_data()" name="fees_type">
                                                <option value="">-- Select --</option>
                                                <option value="M">Monthly Fees</option>
                                                <option value="Y">Annual Fees</option>
                                                <option value="N">Admission Fees</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group ">
                                        <div class="col-lg-12 col-lg-offset-1">
                                            <label for="class_student_list" class="control-label col-lg-2">Select Bank</label>
                                        <div class="col-lg-2">
                                            <select class="form-control" id="bank" name="bank">
                                                <option value=""></option>
                                                <option value="FEDERAL">FEDERAL BANK</option>
                                                <option value="CANARA">CANARA BANK</option>
                                            </select>
                                        </div>

                                        <label for="class_student_list" class="control-label col-lg-1">New Date</label>
                                        <div class="col-lg-2">
                                            <input class="form-control inputmaskDate" style="padding: 10px !important;" id="new_date" name="new_date" type="text" placeholder="dd/mm/yyyy"/>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="form-group row"  id="st_name_list_div">
                                        <div class="col-lg-12">
                                            <div class="text-center">
                                                <a href='#' id='add_all_st_id_list'>Add All</a> / 
                                            <a href='#' id='remove_all_st_id_list'>Remove All</a>
                                            </div>
                                            <select class="form-control fees_list" multiple id="fees_list" name="fees_list[]">
                                                
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group row text-center">
                                        <div class="col-lg-12">
                                            <button class="btn btn-success" id="form_fees_related_transfer" type="submit" name="form_fees_related_transfer"
                                                    value="form_fees_related_transfer" disabled><i class="fa fa-refresh"></i> Submit
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


    function get_fees_data() {
        var form_date = $("#from_date").val();
        var to_date = $("#to_date").val();
        var school = $("#school").val();
        var fees_type = $("#fees_type").val();
        if (fees_type == '') {
            $('.fees_list').html('');
            $('.fees_list').multiSelect('refresh');
        }

        if (form_date != '' && to_date != '' && fees_type != '') {
            data = {
                'form_date': form_date,
                'to_date': to_date,
                'fees_type': fees_type,
                'school': school,
            };
            $.ajax({
                url: "<?=base_url();?>admin/get_fees_data",
                type: "post",
                data: data,
                success: function(data) {
                    data = $.parseJSON(data);
                    $('.fees_list').html(data['html_fees']);
                    $('.fees_list').multiSelect('refresh');
                }
            });
        }
    }

    $(document).ready(function() {
      $(".inputmaskDate").inputmask("dd/mm/yyyy");
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

    
    $('#fees_list').multiSelect({

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
      $('#fees_list').multiSelect('select_all');
      return false;
    });
    $('#remove_all_st_id_list').click(function(){
      $('#fees_list').multiSelect('deselect_all');
      return false;
    });


    $('#fees_list').on('change', function() {
        if ($(this).val()) {
            $('#form_fees_related_transfer').prop("disabled", false);
        } else {
            $('#form_fees_related_transfer').prop("disabled", true);
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
    
</script>

</body>
</html>

