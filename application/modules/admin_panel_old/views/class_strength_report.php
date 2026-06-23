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

                                <form class="cmxform form-horizontal tasi-form" id="admit_card" method="post" action="<?= base_url(); ?>admin/print_admit_card" target="_blank">
                                    <div class="form-group">
                                        <label for="class" class="control-label col-lg-2 text-danger">Class & Sec *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input" id="class" name="class" required >
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
                                        <label for="test_name" class="control-label col-lg-2 text-danger">Test Details *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <i class="fa fa-tag"></i>
                                            <input name="test_name" id="test_name" class="form-control round-input" value="" placeholder="Test name" type="text" required />
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
                                                    value="print_admit_card"><i class="fa fa-file-pdf-o"></i> Print
                                            </button>
                                        </div>
                                    </div>
                                </form>

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
<!-- /common js -->

<script>
    //update student table data on class change
    $('#class').change(function(){
        class_id = $('#class').val();
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
    
    $('#class1').change(function(){
        class_id = $('#class1').val();
        data = {
            'class_id': class_id
        };
        $.ajax({
            url: "<?=base_url();?>admin/ajax_update_std_admit_card1",
            type: "post",
            data: data,
            success: function(data) {
                data = $.parseJSON(data);
                $('#student_name').html(data['html_std']);
            }
        });
    });

    //on teacher change check if that teacher is available for that period
    $(".teacher").on('focus', function(){
        previous = this.value; //Store the current value on focus
    }).change(function(){
        tch_id = $(this).val();
        cls_id = $("#cls_rtn").val();
        day = $('option:selected', this).attr('day');
        period = $('option:selected', this).attr('period');
        sub_id = $("#sub_"+day+"_"+period).val();
        this_obj = this;

        data = {
            'tch_id': tch_id,
            'cls_id': cls_id,
            'day': day,
            'period': period,
            'sub_id': sub_id
        };
        $.ajax({
            url: "<?=base_url();?>admin/ajax_teacher_availability",
            type: "post",
            data: data,
            success: function(data) {
                data = $.parseJSON(data);
                if(data['status'] == 'booked'){
                    alert(data['msg']);
                    $(this_obj).val(previous);
                }
            }
        });
    });

    //add row in Add Voucher Entry table
    row_html = $('#particulars_table').html();
    $("#add_row").click(function(){
        $('#particulars_table').append(row_html);
    });

    $('#form_add_voucher_entry, #form_edit_voucher_entry').submit(function() {
        tot_cr = 0; tot_dr = 0;
        $('#particulars_table tr').each(function() {
            cr_dr = $(this).find('td').eq(1).find('select').val();
            amount = $(this).find('td').eq(2).find('input').val();
            if(cr_dr == 1) { //credit amount
                tot_cr += +parseFloat(amount);
            } else { //debit amount
                tot_dr += +parseFloat(amount);
            }
        });
    
        //Check if credit amount = debit amount
        if (tot_cr  == tot_dr) {
            return true;
        } else {
                alert('Total Credit Amount (cr: '+tot_cr+') must be equals to Total Debit Amount (dr: '+tot_dr+').');
            return false;
        }
    });
</script>

</body>
</html>