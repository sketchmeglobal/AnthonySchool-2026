<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 11-01-2019
 * Time: 15:53
 */
 
 error_reporting(E_ALL);
 ini_set('display_errors', '1');
 
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

<!--            select student section-->
            <?php if(@$form_type <> 'clas_sec_fees'){ ?>
            <div class="row">
                <div class="col-lg-12">
                    <section class="panel">

<!--                        print last receipt button-->
                        <?php
                        if(isset($_GET['print'])) {
                            ?>
                            <div class="row text-center">
                                <a href="<?= base_url('admin/'.$_GET['print'].'/'.$_GET['val']); ?>" target="_blank"
                                   class="btn btn-success">Print Receipt</a>
                                <br/><br/>
                            </div>
                            <?php
                        }
                        
                        if(isset($_GET['last_student_name'])) {
                            
                            ?>
                            <div class="row text-center">
                                <h4 class="text-success"><?=$_GET['last_student_name']?></h4>
                                <br/><br/>
                            </div>
                            <?php
                        }
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
                                                <option value="<?=base_url($url_param.$std['STD_SEQ'])?>" <?=($std_id == $std['STD_SEQ'])?'selected':''?>> <?=$std['STD_REGNO'].' - '.$std['STD_FNAME'].' '.$std['STD_MNAME'].' '.$std['STD_LNAME'].' ( '.$std['Class_Name'].' '.$std['Sec_Name'].' )'?></option>
                                                <?php
                                            }
                                           ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                       
                    </section>
                </div>
            </div>
            <?php } ?>

            <?php
            if(isset($section_heading)) {
                ?>
                <div class="row">
                    <div class="col-lg-12">
                        <section class="panel">
                            <div class="row">
                                <div class="col-sm-7">
                                    <?= $section_heading; ?>
                                    <h4>Net Fees:<kbd><strong><span class="fa fa-inr"></span> <span
                                                                    class="net_fees"><?= $this->session->userdata('total_fees'); ?></span></strong></kbd></h4>
                                </div>

                                <?php if ($this->session->usertype == 1 && $form_type != 'clas_sec_fees') { ?>

                                    <div class="col-sm-5">
                                        <br/>
                                        <br/>
                                        <script>
                                            // screen.width
                                            const left = screen.width / 2;
                                             //document.write(left);

                                        </script>
                                        <form class="cmxform form-horizontal tasi-form" method="post"
                                              action="<?= base_url(); ?>admin/form_add_consc_fees" target="print_popup" onsubmit="window.open('about:blank','print_popup','scrollbars=no,menubar=no, width=800,height=800,top=250,left=document.write(left), resizable=yes,toolbar=no,status=no'); return true;">

                                            <?php
                                            $this->db->select('std_id');
                                            $this->db->where('std_id', $std_id);
                                            $this->db->where('class_id', $class_id);
                                            $result = $this->db->get('fees_concession')->result_array();

                                            if (count($result) == 0) { //if student concession fees not added yet
                                                ?>


                                                <!-- Button trigger modal-->
                                                <button type="button" class="btn btn-primary" data-toggle="modal"
                                                        data-target="#modalPush" style="float: right;">Add Concession
                                                    Fees&nbsp;&nbsp;<i class="fa fa-plus"></i></button>

                                                <!--Modal: modalPush-->
                                                <div class="modal fade" id="modalPush" tabindex="-1" role="dialog"
                                                     aria-labelledby="exampleModalLabel"
                                                     aria-hidden="true">
                                                    <div class="modal-dialog modal-notify modal-info" role="document">
                                                        <!--Content-->
                                                        <div class="modal-content text-center">
                                                            <!--Header-->
                                                            <div class="modal-header d-flex justify-content-center">
                                                                <p class="heading"></p>
                                                            </div>

                                                            <!--Body-->
                                                            <div class="modal-body">

                                                                <i class="fa fa-bell fa-4x animated rotateIn mb-4"
                                                                   style="color: #5cc691;"></i>

                                                                <p><b>Concession will be set true for this student, are
                                                                        you sure you want to proceed with that?</b></p>

                                                            </div>

                                                            <!--Footer-->
                                                            <div class="modal-footer flex-center">
                                                                <button class="btn btn-success" type="submit"
                                                                        name="submit"
                                                                        value="submit_concession_fee"
                                                                        style="float: right;">Confirm
                                                                </button>
                                                                <a type="button"
                                                                   class="btn btn-outline-info waves-effect"
                                                                   data-dismiss="modal"
                                                                   style="border: 2px solid red; margin-right: 6px;">No</a>
                                                            </div>
                                                        </div>
                                                        <!--/.Content-->
                                                    </div>
                                                </div>


                                            <?php } else { ?>

                                                <button class="btn btn-primary" type="submit" name="submit"
                                                        value="update_concession_fee" style="float: right;">Update
                                                    Concession Fees <i class="fa fa-check"></i>
                                                </button>

                                            <?php } ?>
                                            <input value="<?= $std_id; ?>" id="std_id" name="std_id" type="hidden"/>
                                            <input value="<?= $class_id; ?>" id="class_id" name="class_id"
                                                   type="hidden"/>
                                            <br/>
                                            <br/>
                                            <?php

                                            if ($form_type == 'monthly_fees') { //monthly fees form
                                                ?>
                                                <input type="hidden" name="success_val" value="monthly">
                                                <?php
                                                if($all_yearly_fees_paid == 'no') {
                                                    ?>
                                                    <button class="btn btn-success" type="submit" name="submit"
                                                            value="add_monthly_yearly_fee1" style="float: right;">Add
                                                        Yearly
                                                        Fees <i class="fa fa-check"></i>
                                                    </button><br/><br/>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <span style="float: right; color:#5cc691;">All Yearly Fees Paid</span>
                                                    <br/><br/>
                                            <?php
                                                }
                                                    ?>

                                            <?php
                                            if($all_new_adm_fees_paid == 'no') {
                                                ?>
                                                <button class="btn btn-info" type="submit" name="submit"
                                                        value="add_monthly_new_adms_fee1" style="float: right;">Add New
                                                    Admission Fees <i class="fa fa-check"></i>
                                                </button>
                                                <?php
                                            } else {
                                                ?>
                                                <span style="float: right; color:#5cc691;">All New Admission Fees Paid</span>
                                                <br/><br/>
                                                <?php
                                            }
                                                ?>

                                            <?php } elseif ($form_type == 'yearly_fees') { ?>
                                                <input type="hidden" name="success_val" value="yearly">
                                                <button class="btn btn-success" type="submit" name="submit"
                                                        value="add_yearly_monthly_fee" style="float: right;">Add Monthly
                                                    Fees <i class="fa fa-check"></i>
                                                </button><br/><br/>
                                            <?php
                                            if($all_new_adm_fees_paid == 'no') {
                                                ?>
                                                <button class="btn btn-info" type="submit" name="submit"
                                                        value="add_monthly_new_adms_fee2" style="float: right;">Add New
                                                    Admission Fees <i class="fa fa-check"></i>
                                                </button>
                                                <?php
                                            } else {
                                                ?>
                                                <span style="float: right; color:#5cc691;">All New Admission Fees Paid</span>
                                                <br/><br/>
                                                <?php
                                            }
                                                ?>
                                            <?php }
                                            elseif ($form_type == 'new_admission_fees') { ?>

                                            <?php
                                            if($all_yearly_fees_paid == 'no') {
                                                ?>
                                                <button class="btn btn-success" type="submit" name="submit"
                                                        value="add_monthly_yearly_fee2" style="float: right; ">Add Yearly
                                                    Fees <i class="fa fa-check"></i>
                                                </button><br/><br/>
                                                <?php
                                            } else {
                                                ?>
                                                <span style="float: right; color:#5cc691;">All Yearly Fees Paid</span>
                                                <br/><br/>
                                                <?php
                                            }
                                                ?>
                                                <button class="btn btn-info" type="submit" name="submit"
                                                        value="add_yearly_monthly_fee" style="float: right;">Add Monthly
                                                    Fees <i class="fa fa-check"></i>
                                                </button>
                                                <input type="hidden" name="success_val" value="newadms">
                                            <?php } ?>
                                        </form>

                                    </div>

                                <?php } ?>
                            </div>
                            <div class="panel-body">
                                <?php
                                if ($form_type == 'monthly_fees') { //monthly fees form
                                    ?>
                                    <form class="cmxform form-horizontal tasi-form" name id="add_monthly_fees_form" method="post" action="<?= base_url(); ?>admin/form_add_monthly_fees">
                                        <div class="form-group" style="text-align:right;">
                                            <div class="col-lg-offset-2 col-lg-10">
                                                 <button class="btn btn-danger" type="submit" name="submit"
                                                        value="submit_monthly_fees" id="submit_fees">  <!--Online-->
                                                    Payment<i class="fa fa-check"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="date" class="control-label col-lg-2 text-danger">Collection Date
                                                *</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-calendar"></i>
                                                <input value="<?= $date; ?>" class="form-control round-input" id="date"
                                                       name="date" type="date" required/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="" class="control-label col-lg-2 text-danger">Fees for Months
                                                *</label>
                                            <div class="col-lg-10 iconic-input">
                                                <?php
                                                foreach ($months_remain_arr as $key => $months) {
                                                    ?>
                                                    <label class="checkbox-custom check-success col-lg-2">
                                                        <input class="checkbox_months" value="<?= $months; ?>"
                                                               type="checkbox" id="checkbox_<?= $months; ?>"
                                                               name="checkbox[]">
                                                        <label for="checkbox_<?= $months; ?>"><?= $key; ?></label>
                                                    </label>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="payment_type" class="control-label col-lg-2 text-danger">Payment
                                                Type *</label>
                                            <div class="col-lg-10 iconic-input">
                                                <select class="form-control round-input" id="payment_type"
                                                        name="payment_type" required>
                                                    <option value="">Select payment type</option>
                                                    <option value="Cash" <?=($payment_type == 'Cash')?'selected':''?>>Cash</option>
                                                    <option value="Deposit" <?=($payment_type == 'Deposit')?'selected':''?>>Deposit</option>
                                                    <option value="Bank" <?=($payment_type == 'Bank')?'selected':''?>>Bank</option>
                                                    <option value="Online" <?=($payment_type == 'Online')?'selected':''?>>Online</option>
                                                </select>
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <label for="bank_name" class="control-label col-lg-2 text-danger">Bank Name *</label>
                                            <div class="col-lg-10 iconic-input">
                                                <select class="form-control round-input" id="bank_name"
                                                        name="bank_name" required>
                                                    <option value="">Select bank name</option>
                                                    <option value="FEDERAL" <?=($bank_nm == 'FEDERAL')?'selected':''?>>FEDERAL BANK</option>
                                                    <option value="CANARA" <?=($bank_nm == 'CANARA')?'selected':''?>>CANARA BANK</option>
                                                </select>
                                            </div>
                                        </div>


                                        <div class="form-group ">
                                            <label for="card_no" class="control-label col-lg-2">Card / Cheque
                                                No.</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-credit-card"></i>
                                                <input value="" class="form-control round-input" id="card_no"
                                                       name="card_no" type="text"
                                                       placeholder="Enter card or cheque details, if applicable."/>
                                            </div>
                                        </div>

                                        <div class="form-group ">
                                            <label for="encash_date" class="control-label col-lg-2">Cheque Encashment Date</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-calendar"></i>
                                                <input class="form-control round-input" id="encash_date"
                                                       name="encash_date" type="date"/>
                                            </div>
                                        </div>

                                        <div class="form-group ">
                                            <label for="late_fine" class="control-label col-lg-2">Late Fine</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-money"></i>
                                                <input value="0.00" class="form-control round-input" id="late_fine"
                                                       name="late_fine" type="number"
                                                       placeholder="Enter late fee, if any." min="0"/>
                                            </div>
                                        </div>

                                        <div class="form-group " style="display:none;">
                                            <label for="late_fine"
                                                   class="control-label col-lg-2">Concession/Discount</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-money"></i>
                                                <input value="0.00" class="form-control round-input"
                                                       id="concession_fine" name="concession_fine" type="number"
                                                       placeholder="Enter discount amount, if any." min="0"/>
                                            </div>
                                        </div>

                                        <div class="form-group ">
                                            <label for="modify_tution_fee" class="control-label col-lg-2">Modify Tution Fee</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-money"></i>
                                                <input value="0.00" class="form-control round-input" id="modify_tution_fee" name="modify_tution_fee" type="number" placeholder="Enter tution fee." min="0"/>
                                            </div>
                                        </div>

                                        <div class="form-group ">
                                            <label for="" class="control-label col-lg-2">All Monthly Fees</label>
                                            <div class="col-lg-10 iconic-input">
                                                <div class="col-sm-12">
                                                    <section class="panel">
                                                        <table class="table table-striped">
                                                            <thead>
                                                            <tr>
                                                                <th>Fees Name</th>
                                                                <th>Amount</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php
                                                            foreach ($all_fees as $fee) {
                                                                ?>
                                                                <tr>
                                                                    <td><input class="checkbox_fees_mt"
                                                                               value="<?= $fee['ACC_MASTER_CODE']; ?>"
                                                                               type="checkbox"
                                                                               id="checkbox_<?= $fee['ACC_MASTER_CODE']; ?>"
                                                                               name="checkbox_fees_mt[]" checked>&nbsp;&nbsp;<?= $fee['ACC_MASTER_NAME']; ?>
                                                                    </td>
                                                                    <td id="pymnt_m_<?= $fee['ACC_MASTER_CODE']; ?>"><?= $fee['Fees']; ?></td>
                                                                    <td class="paying_id_mt" style="text-align: right;"
                                                                        id="paying_mt_<?= $fee['ACC_MASTER_CODE']; ?>">
                                                                </tr>
                                                                <?php
                                                            }
                                                            ?>
                                                            <tr>
                                                                <th>Total Fees</th>
                                                                <th>
                                                                    <span class="total_fees"><?= $this->session->userdata('total_fees'); ?></span>
                                                                    x
                                                                    <span class="total_months">1</span> =
                                                                    <span class="total_fees_cal"
                                                                          id="paying_fee_mt"><?= $total_fees; ?></span>
                                                                </th>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </section>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group ">
                                            <label for="" class="control-label col-lg-2">Net Fees</label>
                                            <div class="col-lg-10 iconic-input">
                                                <span class="h3"><kbd><strong><span class="fa fa-inr"></span> <span
                                                                    class="net_fees"><?= $this->session->userdata('total_fees'); ?></span></strong></kbd></span>
                                            </div>
                                        </div>

                                        <div class="form-group ">
                                            <label for="" class="control-label col-lg-2">Receipt No.</label>
                                            <div class="col-lg-10 iconic-input">
                                                <span class="h3"><kbd><strong><?= $rcpt_no; ?></strong></kbd></span>
                                                <br>
                                                <small class="text-danger">(Kindly note that receipt number for future
                                                    reference.)</small>
                                            </div>
                                        </div>

                                        <input value="<?= $std_id; ?>" id="std_id" name="std_id" type="hidden"/>
                                        <!--<input value="<?= $std_id; ?>" id="TREIRB_ID" name="TREIRB_ID" type="hidden"/>-->
                                        <!--<input value="1099" id="TREIRB_AMT" name="TREIRB_AMT" type="hidden"/>-->
                                        <?php
                                        /*$amnt = 1099;
                                        $str = 'ref_no='.$std_id.'|amount='.$amnt.'|returnurl=https://www.stjemskatwa.org';
                                        $checkSum = hash('sha256', $str);
                                        $transString = 'ref_no='.$std_id.'|amount='.$amnt.'|returnurl=https://www.stjemskatwa.org|checkSum='.$checkSum;
                                        $encrypted_code = sbi_encrypt_aes256($transString, $aad='');
                                        $final_enc_code = $encrypted_code;
                                        */
                                        // echo '<hr>My decrypted code ->' .  $decrypted_text = sbi_decrypt_aes256($final_enc_code);

                                        #############################################################

                                        // $requiredData = "ref_no=657643535121qqqq11122233333|challan_no=7657|amount=10|Transaction_Date=28/12/2020|ru=htttps://www.stjemskatwa.org"; //this is sample value...added your respective parameters values
                                        // $checksum =hash( 'sha256', $requiredData );
                                        // $datawithCheckSum=$requiredData."|checkSum=".$checksum;
                                        // $encdata = sbi_encrypt_aes256($datawithCheckSum,$aad = '');

                                        // echo "Enc Data HERE---- ".$encdata."<br/>" ."<br/>"; die;
                                        // echo "<br>";
                                        // $decdata = sbi_decrypt_aes256($encdata);
                                        // echo "Decrypt Data HERE---- ".$decdata."<br/>" ."<br/>";
                                        ?>

                                        <?php
                                        /*
                                            $amnt = 999;
                                            $std_id = rand(0,9999);
                                            $str = 'ref_no='.$std_id.'|amount='.$amnt.'|returnurl=https://www.stjemskatwa.org';
                                            $checkSum = hash('sha256', $str);
                                            $transString = 'ref_no='.$std_id.'|amount='.$amnt.'|returnurl=https://www.stjemskatwa.org|checkSum='.$checkSum;
                                            $encrypted_code = sbi_encrypt_aes256($transString, $aad='');
                                            $final_enc_code = $encrypted_code;*/

                                        ?>
                                        <!--<input value="<?= $checkSum ?>" id="checkSum" name="checkSum" type="hidden"/>-->
                                        <input value="<?= $class_id; ?>" id="class_id" name="class_id" type="hidden"/>
                                        <!-- <input value="< ?= $final_enc_code; ?>" id="encdata" name="encdata" type="hidden"/> -->
                                        <input type="hidden" name="merchant_code" value="ST_JEMS">
                                        <div class="form-group">
                                            <div class="col-lg-offset-2 col-lg-10">
                                                <!--<button class="btn btn-success" type="submit" name="submit"-->
                                                <!--        value="submit_monthly_fees">Paid <i class="fa fa-check"></i>-->
                                                <!--</button>-->
                                                <button class="btn btn-danger" type="submit" name="submit"
                                                        value="submit_monthly_fees" id="submit_fees">  <!--Online-->
                                                    Payment <i class="fa fa-check"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>

                                    <!--<form class="cmxform form-horizontal tasi-form" id="" method="post" action="https://uatmerchant.onlinesbi.sbi/merchant/merchantprelogin.htm" style="background: #333;padding:2%">

                                        < ?php

                                            $amnt = 999;
                                            $std_id = rand(0,9999);
                                            $str = 'ref_no='.$std_id.'|amount='.$amnt.'|returnurl=https://www.stjemskatwa.org';
                                            $checkSum = hash('sha256', $str);
                                            $transString = 'ref_no='.$std_id.'|amount='.$amnt.'|returnurl=https://www.stjemskatwa.org|checkSum='.$checkSum;
                                            $encrypted_code = sbi_encrypt_aes256($transString, $aad='');
                                            $final_enc_code = $encrypted_code;

                                        ?>

                                        <input value="< ?= $final_enc_code; ?>" id="encdata" name="encdata" type="hidden"/>
                                        <input type="hidden" name="merchant_code" value="ST_JEMS" >
                                        <input type="submit" value="Submit" name="Submit">
                                    </form>-->

                                    <?php
                                }

                                elseif ($form_type == 'yearly_fees') { //yearly fees form
                                    ?>
                                    <form class="cmxform form-horizontal tasi-form" id="add_yearly_fees_form"
                                          method="post" action="<?= base_url(); ?>admin/form_add_yearly_fees">
                                        <div class="form-group" style="text-align:right;">
                                            <div class="col-lg-offset-2 col-lg-10">
                                                <button class="btn btn-danger w-auto md:hidden sm:block ml-auto" type="submit" name="submit"
                                                        value="submit_yearly_fees" id="submit_fees">
                                                    Payment <i class="fa fa-check"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="form-group ">
                                            <label for="date" class="control-label col-lg-2 text-danger">Collection Date
                                                *</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-calendar"></i>
                                                <input value="<?= $date; ?>" class="form-control round-input" id="date"
                                                       name="date" type="date" required/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="checkbox_select_all" class="control-label col-lg-2 text-danger">Fees
                                                for *</label>
                                            <div class="col-lg-10 iconic-input">
                                                <label class="checkbox-custom check-warning col-lg-12">
                                                    <input  class="" value="" type="checkbox" id="checkbox_select_all"
                                                           name="">
                                                    <label for="checkbox_select_all">Select All</label>
                                                </label>
                                                <?php
                                                foreach ($fees_remain_arr as $key => $fees) {
                                                    ?>
                                                    <label class="checkbox-custom check-success col-lg-4">
                                                        <input  class="checkbox_fees" value="<?= $fees; ?>"
                                                               type="checkbox" id="checkbox_<?= $fees; ?>"
                                                               name="checkbox[]">
                                                        <label for="checkbox_<?= $fees; ?>"><?= $key; ?></label>
                                                    </label>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="payment_type" class="control-label col-lg-2 text-danger">Payment
                                                Type *</label>
                                            <div class="col-lg-10 iconic-input">
                                                <select class="form-control round-input" id="payment_type" 
                                                        name="payment_type" required>
                                                    <option value="">Select payment type</option>
                                                    <option value="Cash" <?=($payment_type == 'Cash')?'selected':''?>>Cash</option>
                                                    <option value="Deposit" <?=($payment_type == 'Deposit')?'selected':''?>>Deposit</option>
                                                    <option value="Bank"  <?=($payment_type == 'Bank')?'selected':''?>>Bank</option>
                                                    <option value="Online" <?=($payment_type == 'Online')?'selected':''?>>Online</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="bank_name" class="control-label col-lg-2 text-danger">Bank Name *</label>
                                            <div class="col-lg-10 iconic-input">
                                                <select class="form-control round-input" id="bank_name"
                                                        name="bank_name" required>
                                                    <option value="">Select bank name</option>
                                                    <option value="FEDERAL" <?=($bank_nm == 'FEDERAL')?'selected':''?>>FEDERAL BANK</option>
                                                    <option value="CANARA" <?=($bank_nm == 'CANARA')?'selected':''?>>CANARA BANK</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group ">
                                            <label for="card_no" class="control-label col-lg-2">Card / Cheque
                                                No.</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-credit-card"></i>
                                                <input value="" class="form-control round-input" id="card_no" 
                                                       name="card_no" type="text"
                                                       placeholder="Enter card or cheque details, if applicable."/>
                                            </div>
                                        </div>

                                        <div class="form-group ">
                                            <label for="encash_date" class="control-label col-lg-2">Cheque Encashment Date</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-calendar"></i>
                                                <input class="form-control round-input" id="encash_date" 
                                                       name="encash_date" type="date"/>
                                            </div>
                                        </div>

                                        <div class="form-group ">
                                            <label for="late_fine" class="control-label col-lg-2 text-danger">Late Fine
                                                *</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-money"></i>
                                                <input value="0.00" class="form-control round-input"
                                                       id="late_fine_yearly" name="late_fine" type="number" 
                                                       placeholder="Enter late fee, if any." required min="0"/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="late_fine"
                                                   class="control-label col-lg-2">Concession/Discount</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-money"></i>
                                                <input value="0.00" class="form-control round-input"
                                                       id="concession_fine_yearly" name="concession_fine" type="number" 
                                                       placeholder="Enter discount amount, if any." min="0"/>
                                            </div>
                                        </div>

                                        <div class="form-group ">
                                            <label for="" class="control-label col-lg-2">All Yearly Fees</label>
                                            <div class="col-lg-10 iconic-input">
                                                <div class="col-sm-12">
                                                    <section class="panel">
                                                        <table class="table table-striped">
                                                            <thead>
                                                            <tr>
                                                                <th>All Fees Name</th>
                                                                <th style="text-align: right;">Amount</th>
                                                                <th style="text-align: center;">Edit Fee</th>
                                                                <th style="text-align: right;">Now Paying For</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php
                                                            foreach ($all_fees as $fee) {
                                                                ?>
                                                                <tr>
                                                                    <td><?= $fee['ACC_MASTER_NAME']; ?></td> 
                                                                    <td align="right"><?= $fee['Fees']; ?></td>
                                                                    <td align="center"><input id="edit_fee_<?= $fee['ACC_MASTER_CODE']; ?>" class="edit_fee" name="edit_fee[<?=$fee['ACC_MASTER_CODE']?>]" type="number" min="0" value="<?= $fee['Fees']; ?>"  required></td>
                                                                    <td class="paying_id" style="text-align: right;" 
                                                                        id="paying_<?= $fee['ACC_MASTER_CODE']; ?>">
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                            }
                                                            ?>
                                                            <tr>
                                                                <th>Total Fees</th>
                                                                <th style="text-align: right;">
                                                                    <span class="total_fees"><?= $total_fees; ?></span>
                                                                </th>
                                                                <th></th>
                                                                <th class="success" style="text-align: center;">
                                                                    <span class="total_fees" id="paying_fee">0</span>
                                                                </th>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </section>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group ">
                                            <label for="" class="control-label col-lg-2">Net Fees</label>
                                            <div class="col-lg-4 iconic-input">
                                                <span class="h3"><kbd><strong><span class="fa fa-inr"></span> <span
                                                                    class="net_fees">0</span></strong></kbd></span>
                                            </div>
                                        </div>

                                        <div class="form-group ">
                                            <label for="due_amount" class="control-label col-lg-2">Due Amount (if any)</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-money"></i>
                                                <input value="" class="form-control round-input"
                                                       id="due_amount" name="due_amount" type="number"
                                                       placeholder="Enter due fee, if any." min="0"/>
                                            </div>
                                        </div>

                                        <div class="form-group ">
                                            <label for="" class="control-label col-lg-2">Receipt No.</label>
                                            <div class="col-lg-10 iconic-input">

                                                <div class="row">
                                                    <div class="col-lg-2"> <span class="h3"><kbd><strong><?= $rcpt_no; ?></strong></kbd></span></div>
                                                </div>
                                               
                                                
                                                <br>
                                                <small class="text-danger">(Kindly note that receipt number for future
                                                    reference.)</small>
                                            </div>

                                        </div>

                                        <input value="<?= $std_id; ?>" id="std_id" name="std_id" type="hidden"/>
                                        <input value="<?= $class_id; ?>" id="class_id" name="class_id" type="hidden"/>

                                        <div class="form-group">
                                            <div class="col-lg-offset-2 col-lg-10">
                                                <button class="btn btn-danger" type="submit" name="submit"
                                                        value="submit_yearly_fees" id="submit_fees">Payment <i
                                                            class="fa fa-check"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <?php
                                }

                                elseif($form_type == 'yearly_due_fees') {
                                    ?>
                                    <form class="cmxform form-horizontal tasi-form" id="add_yearly_fees_due_form" method="post" action="<?= base_url(); ?>admin/form_add_yearly_fees_due">


                                        <div class="form-group ">
                                            <label for="due_amount" class="control-label col-lg-2">Due Amount (if any)</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-money"></i>
                                                <input value="0.00" class="form-control round-input"
                                                       id="due_amount" name="due_amount" type="number"
                                                       placeholder="Enter due fee, if any." required min="0"/>

                                                <small class="text-danger">(Previous Due Amount: <?= $fees_hdr_row->due_amount; ?> INR)</small>
                                            </div>
                                        </div>

                                        <input value="<?= $fees_hdr_row->FM_HDR_SRLNO; ?>" id="hdr_id" name="hdr_id" type="hidden"/>

                                        <div class="form-group">
                                            <div class="col-lg-offset-2 col-lg-10">
                                                <button class="btn btn-success" type="submit" name="submit"
                                                        value="submit_yearly_fees_due">Payment <i
                                                            class="fa fa-check"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <?php
                                }

                                elseif ($form_type == 'new_admission_fees') { //new admission fees form
                                    ?>
                                    <form class="cmxform form-horizontal tasi-form" id="add_new_admission_fees_form"
                                          method="post" action="<?= base_url(); ?>admin/form_add_new_admission_fees">
                                        <div class="form-group" style="text-align:right;">
                                            <div class="col-lg-offset-2 col-lg-10">
                                                 <button class="btn btn-success" type="submit" name="submit"
                                                        value="submit_new_admission_fees" id="submit_fees">Payment <i
                                                            class="fa fa-check"></i> 
                                                </button>
                                            </div>
                                        </div>
                                        <div class="form-group ">
                                            <label for="date" class="control-label col-lg-2 text-danger">Collection Date
                                                *</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-calendar"></i>
                                                <input value="<?= $date; ?>" class="form-control round-input" id="date"
                                                       name="date" type="date" required/>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="checkbox_select_all" class="control-label col-lg-2 text-danger">Fees
                                                for *</label>
                                            <div class="col-lg-10 iconic-input">
                                                <label class="checkbox-custom check-warning col-lg-12">
                                                    <input class="" value="" type="checkbox" id="checkbox_select_all"
                                                           name="">
                                                    <label for="checkbox_select_all">Select All</label>
                                                </label>
                                                <?php
                                                foreach ($fees_remain_arr as $key => $fees) {
                                                    ?>
                                                    <label class="checkbox-custom check-success col-lg-4">
                                                        <input class="checkbox_fees_adm" value="<?= $fees; ?>"
                                                               type="checkbox" id="checkbox_<?= $fees; ?>"
                                                               name="checkbox[]">
                                                        <label for="checkbox_<?= $fees; ?>"><?= $key; ?></label>
                                                    </label>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="payment_type" class="control-label col-lg-2 text-danger">Payment
                                                Type *</label>
                                            <div class="col-lg-10 iconic-input">
                                                <select class="form-control round-input" id="payment_type"
                                                        name="payment_type" required>
                                                    <option value="">Select payment type</option>
                                                    <option value="Cash" <?=($payment_type == 'Cash')?'selected':''?>>Cash</option>
                                                    <option value="Deposit" <?=($payment_type == 'Deposit')?'selected':''?>>Deposit</option>
                                                    <option value="Bank" <?=($payment_type == 'Bank')?'selected':''?>>Bank</option>
                                                    <option value="Online" <?=($payment_type == 'Online')?'selected':''?>>Online</option>
                                                </select>
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <label for="bank_name" class="control-label col-lg-2 text-danger">Bank Name *</label>
                                            <div class="col-lg-10 iconic-input">
                                                <select class="form-control round-input" id="bank_name"
                                                        name="bank_name" required>
                                                    <option value="">Select bank name</option>
                                                    <option value="FEDERAL" <?=($bank_nm == 'FEDERAL')?'selected':''?>>FEDERAL BANK</option>
                                                    <option value="CANARA" <?=($bank_nm == 'CANARA')?'selected':''?>>CANARA BANK</option>
                                                </select>
                                            </div>
                                        </div>


                                        <div class="form-group ">
                                            <label for="card_no" class="control-label col-lg-2">Card / Cheque
                                                No.</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-credit-card"></i>
                                                <input value="" class="form-control round-input" id="card_no"
                                                       name="card_no" type="text"
                                                       placeholder="Enter card or cheque details, if applicable."/>
                                            </div>
                                        </div>

                                        <div class="form-group ">
                                            <label for="encash_date" class="control-label col-lg-2">Cheque Encashment Date</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-calendar"></i>
                                                <input class="form-control round-input" id="encash_date"
                                                       name="encash_date" type="date"/>
                                            </div>
                                        </div>

                                        <div class="form-group" style="display: none;">
                                            <label for="late_fine" class="control-label col-lg-2 text-danger">Late Fine
                                                *</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-money"></i>
                                                <input value="0.00" class="form-control round-input"
                                                       id="late_fine_adm" name="late_fine" type="number"
                                                       placeholder="Enter late fee, if any." required min="0"/>
                                            </div>
                                        </div>

                                        <div class="form-group " style="display:none;">
                                            <label for="late_fine"
                                                   class="control-label col-lg-2">Concession/Discount</label>
                                            <div class="col-lg-10 iconic-input">
                                                <i class="fa fa-money"></i>
                                                <input value="0.00" class="form-control round-input"
                                                       id="concession_fine_adm" name="concession_fine" type="number"
                                                       placeholder="Enter discount amount, if any." min="0"/>
                                            </div>
                                        </div>

                                        <div class="form-group ">
                                            <label for="" class="control-label col-lg-2">All New Admission Fees</label>
                                            <div class="col-lg-10 iconic-input">
                                                <div class="col-sm-12">
                                                    <section class="panel">
                                                        <table class="table table-striped">
                                                            <thead>
                                                            <tr>
                                                                <th>All Fees Name</th>
                                                                <th style="text-align: right;">Amount</th>
                                                                <th style="text-align: center;">Edit Fee</th>
                                                                <th style="text-align: right;">Now Paying For</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            <?php
                                                            foreach ($all_fees as $fee) {
                                                                ?>
                                                                <tr>
                                                                    <td><?= $fee['ACC_MASTER_NAME']; ?></td>
                                                                    <td align="right"><?= $fee['Fees']; ?></td>
                                                                    <td align="center"><input id="edit_fee_adm_<?= $fee['ACC_MASTER_CODE']; ?>" class="edit_fee_adm" name="edit_fee_adm[<?=$fee['ACC_MASTER_CODE']?>]" type="number" min="0" value="<?= $fee['Fees']; ?>" required></td>
                                                                    <td class="paying_id" style="text-align: right;"
                                                                        id="paying_<?= $fee['ACC_MASTER_CODE']; ?>">
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                            }
                                                            ?>
                                                            <tr>
                                                                <th>Total Fees</th>
                                                                <th style="text-align: right;">
                                                                    <span class="total_fees"><?= $total_fees; ?></span>
                                                                </th>
                                                                <th></th>
                                                                <th class="success" style="text-align: right;">
                                                                    <span class="total_fees" id="paying_fee">0</span>
                                                                </th>
                                                            </tr>
                                                            </tbody>
                                                        </table>
                                                    </section>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group ">
                                            <label for="" class="control-label col-lg-2">Net Fees</label>
                                            <div class="col-lg-10 iconic-input">
                                                <span class="h3"><kbd><strong><span class="fa fa-inr"></span> <span
                                                                    class="net_fees">0</span></strong></kbd></span>
                                            </div>
                                        </div>

                                        <div class="form-group ">
                                            <label for="" class="control-label col-lg-2">Receipt No.</label>
                                            <div class="col-lg-10 iconic-input">
                                                <span class="h3"><kbd><strong><?= $rcpt_no; ?></strong></kbd></span>
                                                <br>
                                                <small class="text-danger">(Kindly note that receipt number for future
                                                    reference.)</small>
                                            </div>
                                        </div>

                                        <input value="<?= $std_id; ?>" id="std_id" name="std_id" type="hidden"/>
                                        <input value="<?= $class_id; ?>" id="class_id" name="class_id" type="hidden"/>

                                        <div class="form-group">
                                            <div class="col-lg-offset-2 col-lg-10">
                                                <button class="btn btn-success" type="submit" name="submit"
                                                        value="submit_new_admission_fees" id="submit_fees">Payment <i
                                                            class="fa fa-check"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>

                                <?php }

                                elseif ($form_type == 'clas_sec_fees') { ?>
                                <form class="form-horizontal tasi-form" method="post"
                                      action="<?= base_url(); ?>admin/class_sec_fees_edit">
                                    <?php if (@count($fees_data) > 0) { ?>
                                        <?php foreach ($fees_data as $index => $fees_data_vla) { ?>
                                            <div class="form-group ">
                                                <label for="edit_fees<?= $index ?>"
                                                       class="control-label col-lg-2 text-danger"><?= $fees_data_vla->Fees_name ?>
                                                    *</label>
                                                <div class="col-lg-10 iconic-input">
                                                    <i class="fa fa-money"></i>
                                                    <input value="<?= $fees_data_vla->fees ?>"
                                                           class="form-control round-input" id="edit_fees<?= $index ?>"
                                                           name="edit_fees[<?= $fees_data_vla->fees_id ?>]"
                                                           type="number"
                                                           placeholder="Enter <?= $fees_data_vla->Fees_name ?> fee, if any."
                                                           min="0" required/>
                                                </div>
                                            </div>

                                        <?php } ?>


                                        <div class="form-group">
                                            <input type="hidden" name="class_id" value="<?= $class_id ?>">
                                            <div class="col-lg-offset-2 col-lg-10">
                                                <button class="btn btn-success" type="submit" name="submit"
                                                        value="clas_sec_fees_edit"> Update
                                                </button>
                                            </div>
                                        </div>
                                        </form>
                                    <?php } else { ?>
                                        <h1>Fees Not enter</h1>
                                    <?php } ?>

                                    <?php if ($form_type != 'clas_sec_fees') { ?>
                                        <div class="form-group ">
                                            <label for="" class="control-label col-lg-2">Receipt No.</label>
                                            <div class="col-lg-10 iconic-input">
                                                <span class="h3"><kbd><strong><?= $rcpt_no; ?></strong></kbd></span>
                                                <br>
                                                <small class="text-danger">(Kindly note that receipt number for future
                                                    reference.)</small>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-lg-offset-2 col-lg-10">
                                                <button class="btn btn-success" type="submit" name="submit"
                                                        value="submit_monthly_fees" id="submit_fees"> Update
                                                </button>
                                            </div>
                                        </div>

                                        </form>

                                    <?php } ?>


                                <?php }

                                if ($form_type != 'clas_sec_fees') { ?>
                                    <div class="col-sm-8"></div>
                                    <div class="col-sm-4">
                                        <input type="text" id="basicCalculator" style="width: 60%;">
                                        <br/><span class="input-group-addon btn-success" style="width: 100%;"><u>Click to open calculator&nbsp;&nbsp;<i
                                                        class="fa fa-calculator"></i></u></span>
                                    </div>

                                <?php }
                                ?>

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

<!-- Placed js at the end of the document so the pages load faster -->
<script src="<?=base_url();?>assets/admin_panel/js/jquery-1.10.2.min.js"></script>
<script src="<?=base_url();?>assets/admin_panel/js/jquery-migrate.js"></script>

<!--form validation-->
<script src="<?=base_url();?>assets/admin_panel/js/jquery.validate.min.js" type="text/javascript"></script>
<!--form validation init-->
<script src="<?=base_url();?>assets/admin_panel/js/form-validation-init.js"></script>

<!-- common js -->
<?php $this->load->view('components/_common_js'); //left side menu ?>

<link href="https://cdn.jsdelivr.net/npm/jquery.calculator@2.0.1/jquery.calculator.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery.calculator@2.0.1/jquery.plugin.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery.calculator@2.0.1/jquery.calculator.min.js"></script>

<!-- /common js -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        //alert();
        $('.select2').select2();

        // click on 'select all' checkbox on page load
        setTimeout(function() {
            $('#checkbox_select_all').trigger('click');
        }, 500);


        if ($('#pymnt_m_4').length) {

            //

            //alert();

            $('#modify_tution_fee').val($('#pymnt_m_4').text());

        }
    });

$('#basicCalculator').calculator({
  showOn: 'operator',
});

$('.input-group-addon').on('click', function() {
  $('#basicCalculator').calculator('show');
});

    //checking if at least one month is selected
    $("#add_monthly_fees_form").submit(function(){
        if($(".checkbox_months:checked").length == 0) {
            alert("Please select at least one month!");
            return false;
        }
    });
    $("#add_monthly_fees_form").submit(function(){
        if($(".checkbox_fees_mt:checked").length == 0) {
            alert("Please select at least one fees!");
            return false;
        }
    });
    $("#add_yearly_fees_form").submit(function(){
        if($(".checkbox_fees:checked").length == 0) {
            alert("Please select at least one fees!");
            return false;
        }
    });
    $("#add_new_admission_fees_form").submit(function(){
        if($(".checkbox_fees_adm:checked").length == 0) { 
            alert("Please select at least one fees!");
            return false;
        }
    });

    //update net-fees on month selection
    $('.checkbox_months').click(function (){
        late_fine = $('#late_fine').val();
        concession_fine = $('#concession_fine').val();
        modify_tution_fee = $('#modify_tution_fee').val();
        total_months = $(".checkbox_months:checked").length;
        data = {
            'late_fine': late_fine,
            'concession_fine': concession_fine,
            'total_months': total_months,
            'modify_tution_fee': modify_tution_fee,
        };
        $.ajax({
            url: "<?=base_url();?>admin/ajax_net_fee",
            type: "post",
            data: data,
            success: function(data) {
                data = $.parseJSON(data);
                $('.total_months').html(data['total_months']);
                $('.total_fees_cal').html(data['total_fees_cal']);
                $('.net_fees').html(data['net_fees']);
            }
        });
    });
    //update net-fees on late-fine modification
    $('#late_fine, #concession_fine, #modify_tution_fee').on('input', function(){
        late_fine = $('#late_fine').val();
        concession_fine = $('#concession_fine').val();
        modify_tution_fee = $('#modify_tution_fee').val();
        total_months = $(".checkbox_months:checked").length;
        data = {
            'late_fine': late_fine,
            'concession_fine': concession_fine,
            'total_months': total_months,
            'modify_tution_fee': modify_tution_fee,
        };
        $.ajax({
            url: "<?=base_url();?>admin/ajax_net_fee",
            type: "post",
            data: data,
            success: function(data) {
                data = $.parseJSON(data);
                $('.total_months').html(data['total_months']);
                $('.total_fees_cal').html(data['total_fees_cal']);
                $('.total_fees').html(data['total_fees']);
                if($('#pymnt_m_4').length && modify_tution_fee != '0.00'){
                    $('#pymnt_m_4').html(parseFloat(data['tution_fees']).toFixed(2));
                }
                
                
                $('.net_fees').html(data['net_fees']);
            }
        });
    });
    
    $('#payment_type').change(function(){
        $payment_ty = $('#payment_type').val();
        if($payment_ty == 'Bank') {
            $('#bank_name').val('FEDERAL').change();
        } else {
            $('#bank_name').val('').change();
        }
    });

    //update net-fees on fees selection
    $('.checkbox_fees, .edit_fee').change(function (){
        late_fine = $('#late_fine_yearly').val();
        concession_fine_yearly = $('#concession_fine_yearly').val();
        fees_id_arr = $(".checkbox_fees:checked").map(function(){ return $(this).val(); }).get();
        edited_fee_total = 0;
        $.each(fees_id_arr, function (key, val) {
            edited_fee_total += parseInt($('#edit_fee_'+val).val()) || 0.00;
        });
        data = {
            'late_fine': late_fine,
            'concession_fine_yearly': concession_fine_yearly, 
            'fees_id_arr': fees_id_arr,
            'edited_fee_total': edited_fee_total,
        };
        $.ajax({
            url: "<?=base_url();?>admin/ajax_net_fee_yearly",
            type: "post",
            data: data,
            success: function(data) {
                data = $.parseJSON(data);
                $('.net_fees').html(data['net_fees']);
                if(data['paying_id']) {
                    $('.paying_id').html('');
                    $.each(data['paying_id'], function(index, value) {
                        $('#paying_'+value).html('<span class="fa fa-check-circle"></span>');
                    });
                    $('#paying_fee').html(data['total_fees']);
                } else {
                    $('.paying_id').html('');
                    $('#paying_fee').html('0');
                }
            }
        });
    });

    //update net-fees on fees selection
    $('.checkbox_fees_adm, .edit_fee_adm').change(function (){
        late_fine_adm = $('#late_fine_adm').val();
        concession_fine_adm = $('#concession_fine_adm').val();
        fees_id_arr = $(".checkbox_fees_adm:checked").map(function(){ return $(this).val(); }).get();
        edited_fee_total = 0;
        $.each(fees_id_arr, function (key, val) {
            edited_fee_total += parseInt($('#edit_fee_adm_'+val).val()) || 0.00;
        });
        data = {
            'late_fine_adm': late_fine_adm,
            'concession_fine_adm': concession_fine_adm,
            'fees_id_arr': fees_id_arr,
            'edited_fee_total': edited_fee_total,
        };
        $.ajax({
            url: "<?=base_url();?>admin/ajax_net_fee_adm",
            type: "post",
            data: data,
            success: function(data) {
                data = $.parseJSON(data);
                $('.net_fees').html(data['net_fees']);
                if(data['paying_id']) {
                    $('.paying_id').html('');
                    $.each(data['paying_id'], function(index, value) {
                        $('#paying_'+value).html('<span class="fa fa-check-circle"></span>');
                    });
                    $('#paying_fee').html(data['total_fees']);
                } else {
                    $('.paying_id').html('');
                    $('#paying_fee').html('0');
                }
            }
        });
    });
    
    $('.checkbox_fees_mt').change(function (){
        late_fine = $('#late_fine').val();
        concession_fine = $('#concession_fine').val();
        modify_tution_fee = $('#modify_tution_fee').val();
        fees_id_arr = $(".checkbox_fees_mt:checked").map(function(){ return $(this).val(); }).get();
        data = {
            'late_fine': late_fine,
            'concession_fine': concession_fine,
            'fees_id_arr': fees_id_arr,
            'modify_tution_fee': modify_tution_fee,
        };
        $.ajax({
            url: "<?=base_url();?>admin/ajax_net_fee_monthly",
            type: "post",
            data: data,
            success: function(data) {
                data = $.parseJSON(data);
                $('.net_fees').html(data['net_fees']);
                if(data['paying_id_mt']) {
                    $('.paying_id_mt').html('');
                    $.each(data['paying_id_mt'], function(index, value) {
                        $('#paying_mt_'+value).html('<span class="fa fa-check-circle"></span>');
                    });
                    $('#paying_fee_mt').html(data['total_fees']);
                } else {
                    $('.paying_id_mt').html('');
                    $('#paying_fee_mt').html('0');
                }
            }
        });
    });
    
    
    //update net-fees on late-fine modification
    $('#late_fine_yearly, #concession_fine_yearly').change(function(){
        late_fine = $('#late_fine_yearly').val();
        concession_fine_yearly = $('#concession_fine_yearly').val();
        fees_id_arr = $(".checkbox_fees:checked").map(function(){ return $(this).val(); }).get();
        edited_fee_total = 0;
        $.each(fees_id_arr, function (key, val) {
            edited_fee_total += parseInt($('#edit_fee_'+val).val()) || 0.00;
        });
        data = {
            'late_fine': late_fine,
            'concession_fine_yearly': concession_fine_yearly,
            'fees_id_arr': fees_id_arr,
            'edited_fee_total': edited_fee_total,
        };
        $.ajax({
            url: "<?=base_url();?>admin/ajax_net_fee_yearly",
            type: "post",
            data: data,
            success: function(data) {
                data = $.parseJSON(data);
                $('.net_fees').html(data['net_fees']);
            }
        });
    });

    $('#late_fine_adm, #concession_fine_adm').change(function(){
        late_fine_adm = $('#late_fine_adm').val();
        concession_fine_adm = $('#concession_fine_adm').val();
        fees_id_arr = $(".checkbox_fees_adm:checked").map(function(){ return $(this).val(); }).get();
        edited_fee_total = 0;
        $.each(fees_id_arr, function (key, val) {
            edited_fee_total += parseInt($('#edit_fee_adm_'+val).val()) || 0.00;
        });
        data = {
            'late_fine_adm': late_fine_adm,
            'concession_fine_adm': concession_fine_adm,
            'fees_id_arr': fees_id_arr,
            'edited_fee_total': edited_fee_total,
        };
        $.ajax({
            url: "<?=base_url();?>admin/ajax_net_fee_adm",
            type: "post",
            data: data,
            success: function(data) {
                data = $.parseJSON(data);
                $('.net_fees').html(data['net_fees']);
            }
        });
    });

    //select all checkbox
    $('#checkbox_select_all').click(function (){
        if($(this).prop("checked")) {
            $(".checkbox_fees, .checkbox_fees_adm").prop('checked', true).change();
        } else {
            $(".checkbox_fees, .checkbox_fees_adm").prop('checked', false).change();
        }
    });
    
window.addEventListener("keydown", checkKeyPress, false);

function checkKeyPress(key) {
    if (key.ctrlKey && key.shiftKey && key.keyCode == 74) {
         $("#checkbox_7").prop("checked", true);
      }
    else if(key.shiftKey && key.keyCode == "74") {
        $("#checkbox_6").prop("checked", true);
    }
    else if(key.keyCode == "74") {
        $("#checkbox_1").prop("checked", true);
    }
    if (key.shiftKey && key.keyCode == 77) {
         $("#checkbox_5").prop("checked", true);
      }
    else if(key.keyCode == "77") {
        $("#checkbox_3").prop("checked", true);
    }
    if(key.shiftKey && key.keyCode == "65") {
        $("#checkbox_8").prop("checked", true);
    }
    else if(key.keyCode == "65") {
        $("#checkbox_4").prop("checked", true);
    }
    if (key.shiftKey && key.keyCode == 83) {
         $("#submit_fees").click();
      }
    else if(key.keyCode == "83") {
        $("#checkbox_9").prop("checked", true);
    }
    if(key.keyCode == "79") {
        $("#checkbox_10").prop("checked", true);
    }
    if(key.keyCode == "78") {
        $("#checkbox_11").prop("checked", true);
    }
    if(key.keyCode == "68") {
        $("#checkbox_12").prop("checked", true);
    }
    if(key.keyCode == "70") {
        $("#checkbox_2").prop("checked", true);
    }
}

</script>
<?php


function sbi_encrypt_aes256($data,$aad)
 {
    $key= file_get_contents("payment-key/ST_JEMS.key", true);
    $iv = '1234567890123456';
    $encryptedString = openssl_encrypt($data, "aes-256-gcm", $key,$options=OPENSSL_RAW_DATA, $iv,$aad);
    $finalEncryption= base64_encode($iv . $encryptedString . $aad);
    return $finalEncryption;
    
}

function sbi_decrypt_aes256($data)
{
    $key= file_get_contents("payment-key/ST_JEMS.key", true);
    $c = base64_decode($data);
    $datalength=strlen($c);
    $ivlen = openssl_cipher_iv_length($cipher="aes-256-gcm");
    $iv ='1234567890123456';
    //$iv=substr($c,0,16); --Added
    $ciphertext_raw = substr($c,16,$datalength-32);
    $aad=substr($c,$datalength-16,16); //////IMP
    $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA,$iv,$aad);  
    // echo "Decrypted Text new: $original_plaintext\n";
    return $original_plaintext;

}


// $requiredData = "ref_no=657643535121qqqq11122233333|challan_no=7657|amount=10|Transaction_Date=28/12/2020|ru=htttp://www.test.com"; //this is sample value...added your respective parameters values
// echo "Plain String : = ".$requiredData . "<br>";


// $checksum =hash( 'sha256', $requiredData );
// echo "Checksum value ---- ".$checkSum."<br/>";
// $datawithCheckSum=$requiredData."|checkSum=".$checksum;


// $encdata = sbi_encrypt_aes256($datawithCheckSum,$aad);
// echo "Enc Data HERE---- ".$encdata."<br/>" ."<br/>";
// echo "<br>";

// $decdata = sbi_decrypt_aes256($encdata);
// echo "Decrypt Data HERE---- ".$decdata."<br/>" ."<br/>";


?>
</body>
</html>