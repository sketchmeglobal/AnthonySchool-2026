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

                            <?php
                            if($form_type == 'admit_card') { //admit card print form
                                ?>
                                <form class="cmxform form-horizontal tasi-form" id="admit_card" method="post" action="<?= base_url(); ?>admin/print_admit_card" target="_blank">
                                    <div class="form-group">
                                        <label for="class" class="control-label col-lg-2">Class & Sec *</label>
                                        <div class="col-lg-4 iconic-input">
                                            <select class="form-control round-input" id="class" name="class" >
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

                                        <label for="test_name" class="control-label col-lg-2 text-danger">Test Details *</label>
                                        <div class="col-lg-4 iconic-input">
                                            <i class="fa fa-tag"></i>
                                            <input name="test_name" id="test_name" class="form-control round-input" value="" placeholder="Test name" type="text" required />
                                        </div>

                                    </div>

                                    <div class="form-group">
                                        <label for="std_reg_no" class="control-label col-lg-2">Student Reg. No.</label>
                                        <div class="col-lg-8 iconic-input">
                                            <i class="fa fa-search"></i>
                                            <input id="std_reg_no" name="std_reg_no" class="form-control round-input" type="text" placeholder="Student Registration Number" />
                                        </div>
                                        <button class="btn btn-primary" type="button" id="std_reg_no_btn">Search</button>
                                    </div>

                                    <div class="form-group">
                                        <label for="" class="control-label col-lg-2 text-danger">
                                            Select Students *
                                            <br/>
                                            <label for="select_std">Select All</label>
                                            <input id="select_std" type="checkbox" checked style="width: 20px; height: 20px;">
                                        </label>
                                        <div class="col-lg-10 iconic-input" id="std_details">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="print_admit_card"><i class="fa fa-file-pdf-o"></i> Print Admit Card
                                            </button>
                                            <button class="btn btn-warning" type="submit" name="submit"
                                                    value="print_admit_card_blank"><i class="fa fa-file-pdf-o"></i> Print Blank Admit Card
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            }
                            elseif($form_type == 'leaving_certificate') {
                                ?>
                                <form class="cmxform form-horizontal tasi-form" id="admit_card" method="post" action="<?= base_url(); ?>admin/print_leaving_certificate" target="_blank">
                                    <div class="form-group">
                                        <label for="class" class="control-label col-lg-2 text-danger">Class & Sec *</label>
                                        <div class="col-lg-10 iconic-input">
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
                                    </div>

                                    <div class="form-group">
                                        <label for="" class="control-label col-lg-2 text-danger">Select Students *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input" id="student_name" name="student_name" required >

                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="class" class="control-label col-lg-2 text-danger">District *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <input type="text" class="form-control round-input" name="district" id="" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="class" class="control-label col-lg-2 text-danger">Was in class up to (Date) *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <input type="date" class="form-control round-input" name="upto" id="" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="class" class="control-label col-lg-2 text-danger">Reason For Leaving *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <input type="text" class="form-control round-input" name="reason_for_leaving" id="reason_for_leaving" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="class" class="control-label col-lg-2 text-danger">Certificate Remarks *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <input type="text" class="form-control round-input" name="certificate_remarks" id="certificate_remarks" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="class" class="control-label col-lg-2 text-danger">Promotion Has Been *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <input type="text" class="form-control round-input" name="promotion_has_been" id="promotion_has_been" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="class" class="control-label col-lg-2 text-danger">Certificate Date *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <input type="date" class="form-control round-input" name="certificate_date" id="certificate_remarks" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="print_transfer_certificate"><i class="fa fa-file-pdf-o"></i> Print
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            }
                            elseif($form_type == 'character_certificate') {
                                ?>
                                <form class="cmxform form-horizontal tasi-form" id="admit_card" method="post" action="<?= base_url(); ?>admin/print_character_certificate" target="_blank">
                                    <div class="form-group">
                                        <label for="class" class="control-label col-lg-2 text-danger">Class & Sec *</label>
                                        <div class="col-lg-10 iconic-input">
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
                                    </div>

                                    <div class="form-group">
                                        <label for="" class="control-label col-lg-2 text-danger">Select Students *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input" id="student_name" name="student_name" required >

                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="class" class="control-label col-lg-2 text-danger">District *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <input type="text" class="form-control round-input" name="district" id="" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="class" class="control-label col-lg-2 text-danger">Was in class up to (Date) *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <input type="date" class="form-control round-input" name="upto" id="" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="class" class="control-label col-lg-2 text-danger">Certificate Remarks *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <input type="text" class="form-control round-input" name="certificate_remarks" id="certificate_remarks" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="class" class="control-label col-lg-2 text-danger">Promotion Has Been *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <input type="text" class="form-control round-input" name="promotion_has_been" id="promotion_has_been" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="class" class="control-label col-lg-2 text-danger">Certificate Date *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <input type="date" class="form-control round-input" name="certificate_date" id="certificate_remarks" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="print_character_certificate"><i class="fa fa-file-pdf-o"></i> Print
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            }
                            elseif($form_type == 'general_letter') {
                                ?>
                                <form class="cmxform form-horizontal tasi-form" id="admit_card" method="post" action="<?= base_url(); ?>admin/print_general_letter" target="_blank">
                                    <div class="form-group">
                                        <label for="class" class="control-label col-lg-2 text-danger">Class & Sec *</label>
                                        <div class="col-lg-10 iconic-input">
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
                                    </div>

                                    <div class="form-group">
                                        <label for="" class="control-label col-lg-2 text-danger">Select Students *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input" id="student_name" name="student_name" required >

                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="class" class="control-label col-lg-2 text-danger">Certificate Date</label>
                                        <div class="col-lg-10 iconic-input">
                                            <input type="date" class="form-control round-input" name="certificate_date" id="certificate_remarks" required>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="print_general_letter"><i class="fa fa-file-pdf-o"></i> Print
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            }
                            elseif ($form_type == 'identity_card') { //identity card print form
                                ?>
                                <form class="cmxform form-horizontal tasi-form" id="identity_card" method="post" action="<?= base_url(); ?>admin/print_identity_card" target="_blank">
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
                                        <label for="" class="control-label col-lg-2 text-danger">
                                            Select Students *
                                            <br/>
                                            <label for="select_std">Select All</label>
                                            <input id="select_std" type="checkbox" checked style="width: 20px; height: 20px;">
                                        </label>
                                        <div class="col-lg-10 iconic-input" id="std_details">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="print_identity_card"><i class="fa fa-file-pdf-o"></i> Print Identity Card
                                            </button>
                                            <button class="btn btn-warning" type="submit" name="submit"
                                                    value="print_identity_card_blank"><i class="fa fa-file-pdf-o"></i> Print Blank Identity Card
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            }
                            elseif ($form_type == 'add_routine') { //add routine form
                                ?>
                                <form class="cmxform form-horizontal tasi-form" id="add_routine" method="post" action="<?= base_url(); ?>admin/form_add_routine">
                                    <?php
                                    //days loop - mon,tue,wed...
                                    $day_counter = 1;
                                    foreach ($days as $day) {
                                        ?>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2"><strong>Day <?=$day_counter++?></strong></label>
                                            <div class="col-sm-10 iconic-input">
                                                <?php
                                                //periods loop - 1st period,2nd period...
                                                for ($i=1; $i<=8; $i++) {
                                                    ?>
                                                    <div class="col-sm-3">
                                                        <div class="text-center"><strong>Period <?=$i;?> (Sub 1)</strong></div>

                                                        <!--classes 1-->
                                                        <select id="sub_<?=$day.'_'.$i;?>" class="subject form-control round-input" name="subject[<?=$day?>][<?=$i?>][1]">
                                                            <option value="">-</option>
                                                            <?php
                                                            foreach ($subjects as $sub) {
                                                                ?>
                                                                <option value="<?=$sub['sub_id'];?>"><?=$sub['sub_name'];?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>

                                                        <!--teachers 1-->
                                                        <select class="teacher form-control round-input" name="teacher[<?=$day?>][<?=$i?>][1]" >
                                                            <option value="" day="" period="">-</option>
                                                            <?php
                                                            foreach ($teachers as $tch) {
                                                                ?>
                                                                <option value="<?=$tch['TCH_SRLNO'];?>" day="<?=$day?>" period="<?=$i;?>" ><?=$tch['TCH_NAME'];?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>

                                                        <div class="text-center"><strong>Period <?=$i;?> (Sub 2)</strong></div>

                                                        <!--classes 2-->
                                                        <select id="sub_<?=$day.'_'.$i;?>" class="subject form-control round-input" name="subject[<?=$day?>][<?=$i?>][2]" >
                                                            <option value="">-</option>
                                                            <?php
                                                            foreach ($subjects as $sub) {
                                                                ?>
                                                                <option value="<?=$sub['sub_id'];?>"><?=$sub['sub_name'];?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>

                                                        <!--teachers 2-->
                                                        <select class="teacher form-control round-input" name="teacher[<?=$day?>][<?=$i?>][2]" >
                                                            <option value="" day="" period="">-</option>
                                                            <?php
                                                            foreach ($teachers as $tch) {
                                                                ?>
                                                                <option value="<?=$tch['TCH_SRLNO'];?>" day="<?=$day?>" period="<?=$i;?>" ><?=$tch['TCH_NAME'];?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>

                                    <input id="cls_rtn" name="class" value="<?=$class;?>" type="hidden" >

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="submit_add_routine">Create Routine <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            }
                            elseif ($form_type == 'edit_routine') { //edit routine form
                                ?>
                                <form class="cmxform form-horizontal tasi-form" id="edit_routine" method="post" action="<?= base_url(); ?>admin/form_edit_routine">
                                    <input type="hidden" name="class" value="<?php echo $class?>" /> 
                                    <?php
                                    //days loop - mon,tue,wed...
                                    $day_counter = 1;
                                    foreach ($days as $day) {
                                        ?>
                                        <div class="form-group">
                                            <label class="control-label col-sm-2"><strong>Day <?=$day_counter++?></strong></label>
                                            <div class="col-sm-10 iconic-input">
                                                <?php
                                                //periods loop - 1st period,2nd period...
                                                for ($i=1; $i<=8; $i++) {
                                                    //searching for that day and period in routine-array
                                                    foreach($routine as $key => $val) {
                                                        if ( $val['day'] == $day && $val['period'] == $i && $val['sub_no'] == 1) {
                                                            $index = $key;
                                                            break;
                                                        }
                                                    }

                                                    $rtn_id = $routine[$index]['rtn_id'];
                                                    $selected_sub_id = $routine[$index]['sub_id'];
                                                    $selected_tch_id = $routine[$index]['tch_id'];
                                                    ?>
                                                    <div class="col-sm-3">
                                                        <div class="text-center"><strong>Period <?=$i;?> (Sub 1)</strong></div>

                                                        <!--subjects 1-->
                                                        <select id="sub_<?=$day.'_'.$i;?>" class="subject form-control round-input" name="subject[<?=$rtn_id;?>]" >
                                                            <option value="">-</option>
                                                            <?php
                                                            foreach ($subjects as $sub) {
                                                                $selected = '';
                                                                if($sub['sub_id'] == $selected_sub_id){$selected = 'selected';}
                                                                ?>
                                                                <option <?=$selected;?> value="<?=$sub['sub_id'];?>"><?=$sub['sub_name'];?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>

                                                        <!--teachers 1-->
                                                        <select class="teacher form-control round-input" name="teacher[<?=$rtn_id;?>]" >
                                                            <option value="" day="" period="">-</option>
                                                            <?php
                                                            foreach ($teachers as $tch) {
                                                                $selected = '';
                                                                if($tch['TCH_SRLNO'] == $selected_tch_id){$selected = 'selected';}
                                                                ?>
                                                                <option <?=$selected;?> value="<?=$tch['TCH_SRLNO'];?>" day="<?=$day?>" period="<?=$i;?>" ><?=$tch['TCH_NAME'];?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>

                                                        <input name="rtn[]" value="<?=$rtn_id;?>" type="hidden" >

                                                        <?php
                                                        //searching for that day and period in routine-array
                                                        foreach($routine as $key => $val) {
                                                            if ( $val['day'] == $day && $val['period'] == $i && $val['sub_no'] == 2) {
                                                                $index = $key;
                                                                break;
                                                            }
                                                        }

                                                        $rtn_id = $routine[$index]['rtn_id'];
                                                        $selected_sub_id = $routine[$index]['sub_id'];
                                                        $selected_tch_id = $routine[$index]['tch_id'];
                                                        ?>

                                                        <div class="text-center"><strong>Period <?=$i;?> (Sub 2)</strong></div>

                                                        <!--subjects 2-->
                                                        <select id="sub_<?=$day.'_'.$i;?>" class="subject form-control round-input" name="subject[<?=$rtn_id;?>]" >
                                                            <option value="">-</option>
                                                            <?php
                                                            foreach ($subjects as $sub) {
                                                                $selected = '';
                                                                if($sub['sub_id'] == $selected_sub_id){$selected = 'selected';}
                                                                ?>
                                                                <option <?=$selected;?> value="<?=$sub['sub_id'];?>"><?=$sub['sub_name'];?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>

                                                        <!--teachers 2-->
                                                        <select class="teacher form-control round-input" name="teacher[<?=$rtn_id;?>]" >
                                                            <option value="" day="" period="">-</option>
                                                            <?php
                                                            foreach ($teachers as $tch) {
                                                                $selected = '';
                                                                if($tch['TCH_SRLNO'] == $selected_tch_id){$selected = 'selected';}
                                                                ?>
                                                                <option <?=$selected;?> value="<?=$tch['TCH_SRLNO'];?>" day="<?=$day?>" period="<?=$i;?>" ><?=$tch['TCH_NAME'];?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>

                                                        <input name="rtn[]" value="<?=$rtn_id;?>" type="hidden" >
                                                    </div>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                    ?>

                                    <input id="cls_rtn" name="class" value="<?=$class;?>" type="hidden" >

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="submit_edit_routine">Update Routine <i class="fa fa-refresh"></i>
                                            </button>
                                            <button class="btn btn-danger" type="submit" name="delete"
                                                    value="delete_edit_routine">Delete <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            }
                            elseif($form_type == 'create_account') { //create account form
                                ?>
                                <!--Create Operator-->
                                <h2 class="text-center text-primary">Create Operator</h2>
                                <form class="cmxform form-horizontal tasi-form" method="post" action="<?= base_url(); ?>admin/add_create_account_operator">
                                    <div class="form-group">
                                        <label for="firstname" class="control-label col-lg-1 text-danger">Full Name *</label>
                                        <div class="col-lg-2 iconic-input">
                                            <input name="firstname" class="form-control round-input" placeholder="Firstname" type="text" required />
                                        </div>
                                        <div class="col-lg-1">
                                            <input name="lastname" class="form-control round-input" placeholder="Lastname" type="text" required />
                                        </div>

                                        <label for="username" class="control-label col-lg-1 text-danger">Username *</label>
                                        <div class="col-lg-3 iconic-input">
                                            <input name="username" id="username" class="form-control round-input" placeholder="Give an unique username" type="text" required />
                                        </div>

                                        <label for="pass" class="control-label col-lg-1 text-danger">Password *</label>
                                        <div class="col-lg-3 iconic-input">
                                            <input name="pass" id="pass" class="form-control round-input" placeholder="Type a strong password" type="password" required />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-primary" type="submit" name="submit"
                                                    value="add_create_account_operator"><i class="fa fa-user"> Create Operator</i>
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                <hr>

                                <!--Create Accountant-->
                                <h2 class="text-center text-success">Create Accountant</h2>
                                <form class="cmxform form-horizontal tasi-form" method="post" action="<?= base_url(); ?>admin/add_create_account">
                                    <div class="form-group">
                                        <label for="emp_id" class="control-label col-lg-1 text-danger">Accountant Name *</label>
                                        <div class="col-lg-3 iconic-input">
                                            <select class="form-control round-input" id="emp_id" name="emp_id" required >
                                                <option value="">Select accountant</option>
                                                <?php
                                                foreach($employees as $val){
                                                    ?>
                                                    <option value="<?=$val['EMP_SEQ'];?>"><?=$val['EMP_CODE'].' - '.$val['EMP_NAME'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <label for="username" class="control-label col-lg-1 text-danger">Username *</label>
                                        <div class="col-lg-3 iconic-input">
                                            <input name="username" id="username" class="form-control round-input" value="" placeholder="Give an unique username" type="text" required />
                                        </div>

                                        <label for="pass" class="control-label col-lg-1 text-danger">Password *</label>
                                        <div class="col-lg-3 iconic-input">
                                            <input name="pass" id="pass" class="form-control round-input" value="" placeholder="Type a strong password" type="password" required />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="add_create_account"><i class="fa fa-user"> Create Accountant</i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            }
                            elseif($form_type == 'copy_fees') { //copy fees form
                                ?>
                                <form action="<?=base_url();?>admin/form_copy_fees" method="post" class="cmxform form-horizontal tasi-form" >
                                    <div class="form-group">
                                        <label for="from_class" class="control-label col-lg-2 text-danger">Copy From *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control select2" id="from_class" name="from_class" required >
                                                <option value="">Select a class</option>
                                                <?php
                                                foreach($from_cls as $val){
                                                    ?>
                                                    <option value="<?=$val['CS_SEQ'];?>"><?=$val['Class_Name'].' - '.$val['Sec_Name'];?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="to_class" class="control-label col-lg-2 text-danger">Copy To *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select multiple class="form-control select2" data-placeholder="-- Select Classes --" id="to_class" name="to_class[]" required >
                                                <?php
                                                foreach($to_cls as $val){
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
                                                    value="submit_copy_fees_form">Copy <i class="fa fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            }
                            //add voucher entry form
                            elseif($form_type == 'add_voucher_entry') {
                                ?>
                                <form id="form_add_voucher_entry" action="<?=base_url();?>admin/form_add_voucher_entry" method="post" class="cmxform form-horizontal tasi-form" >

                                    <div class="form-group">
                                        <label for="ref_no" class="control-label col-lg-2 text-danger">Voucher Ref. No. *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <input name="ref_no" value="<?=$ref_no?>" type="text" disabled required class="form-control round-input" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="remark" class="control-label col-lg-2 text-danger">Remark *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <input name="remark" placeholder="Type narration" type="text" required class="form-control round-input" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="date" class="control-label col-lg-2 text-danger">Date *</label>
                                        <div class="col-lg-4 iconic-input">
                                            <input name="date" type="date" required class="form-control round-input" />
                                        </div>
                                    </div>


                                    <div class="form-group ">
                                        <label class="control-label col-lg-2 text-danger">Particulars of Accounts *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <table class="table table-striped table-responsive">
                                                <thead>
                                                <tr>
                                                    <th class="col-lg-4" style="text-align: center">Particulars</th>
                                                    <th class="col-lg-4" style="text-align: center">Credit/Debit</th>
                                                    <th class="col-lg-4" style="text-align: center">Amount</th>
                                                </tr>
                                                </thead>
                                                <tbody id="particulars_table">
                                                <tr>
                                                    <td>
                                                        <select name="acc_master[]" required class="form-control round-input" >
                                                            <option value="">Select a particular</option>
                                                            <?php
                                                            foreach($rs_acc_master as $val){
                                                                ?>
                                                                <option value="<?=$val['ACC_MASTER_CODE']?>"><?=$val['ACC_MASTER_NAME']?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="cr_dr[]" required class="form-control round-input" >
                                                            <option value="">Select an amount type</option>
                                                            <option value="1">Credit</option>
                                                            <option value="0">Debit</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input name="amount[]" type="number" min="1" required placeholder="Enter credit/debit amount" class="form-control round-input" />
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-warning" type="button" id="add_row">Add Row <i class="fa fa-plus"></i>
                                            </button>
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="submit_add_voucher_entry_form">Save <i class="fa fa-check"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            }
                            //edit voucher entry form
                            elseif($form_type == 'edit_voucher_entry') {
                                ?>
                                <form id="form_edit_voucher_entry" action="<?=base_url();?>admin/form_edit_voucher_entry" method="post" class="cmxform form-horizontal tasi-form" >

                                    <div class="form-group">
                                        <label for="ref_no" class="control-label col-lg-2 text-danger">Voucher Ref. No. *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <input name="ref_no" value="<?=$row_vchr_hdr->ref_no?>" type="text" disabled required class="form-control round-input" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="remark" class="control-label col-lg-2 text-danger">Remark *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <input name="remark" value="<?=$row_vchr_hdr->remark?>" placeholder="Type narration" type="text" required class="form-control round-input" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="date" class="control-label col-lg-2 text-danger">Date *</label>
                                        <div class="col-lg-4 iconic-input">
                                            <input name="date" value="<?=date('Y-m-d', strtotime($row_vchr_hdr->date))?>" type="date" required class="form-control round-input" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label col-lg-2 text-danger">Particulars of Accounts *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <table class="table table-striped table-responsive">
                                                <thead>
                                                <tr>
                                                    <th class="col-lg-4" style="text-align: center">Particulars</th>
                                                    <th class="col-lg-4" style="text-align: center">Credit/Debit</th>
                                                    <th class="col-lg-4" style="text-align: center">Amount</th>
                                                </tr>
                                                </thead>
                                                <tbody id="particulars_table">
                                                <?php
                                                foreach ($rs_vchr_dtl as $vchr_dtl) {
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <select name="acc_master[]" disabled required class="form-control round-input">
                                                                <option value="">Select a particular</option>
                                                                <?php
                                                                foreach ($rs_acc_master as $val) {
                                                                    if($val['ACC_MASTER_CODE'] == $vchr_dtl['ACC_MASTER_CODE']) $selected='selected'; else $selected='';
                                                                    ?>
                                                                    <option <?=$selected?> value="<?= $val['ACC_MASTER_CODE'] ?>"><?= $val['ACC_MASTER_NAME'] ?></option>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <select name="cr_dr[]" required class="form-control round-input">
                                                                <option value="">Select an amount type</option>
                                                                <option <?php if($vchr_dtl['cr_dr']==1) echo 'selected'; ?> value="1">Credit</option>
                                                                <option <?php if($vchr_dtl['cr_dr']==0) echo 'selected'; ?> value="0">Debit</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input name="amount[]" value="<?=$vchr_dtl['amount']?>" type="number" min="1" required placeholder="Enter credit/debit amount" class="form-control round-input"/>
                                                        </td>
                                                    </tr>
                                                    <input name="vchr_dtl[]" value="<?=$vchr_dtl['vchr_dtl_id']?>" type="hidden" />
                                                    <?php
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <input name="vchr_hdr" value="<?=$row_vchr_hdr->vchr_hdr_id?>" type="hidden" />

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="submit_edit_voucher_entry_form">Update <i class="fa fa-refresh"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            }
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
<!-- /common js -->

<script>
    //select/unselect all students
    $('#select_std').change(function() {
        if (this.checked) {
            $(".select_std").each(function() {
                $(this).prop('checked', true);
            });
        } else {
            $(".select_std").each(function() {
                $(this).prop('checked', false);
            });
        }
    });

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

    // fetch student reg. no for admit card
    $('#std_reg_no_btn').click(function(){
        std_reg_no = $('#std_reg_no').val();
        data = {
            'std_reg_no': std_reg_no
        };
        $.ajax({
            url: "<?=base_url();?>admin/ajax-std-reg-no-on-admit-card",
            type: "post",
            data: data,
            success: function(data) {
                data = $.parseJSON(data);
                console.log(data)
                if(data.html_std == ''){
                    $('#std_details').html('Nothing Found');
                }else{
                    $('#std_details').html(data['html_std']);
                }
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