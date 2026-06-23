<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 24-01-2019
 * Time: 17:34
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
                            if ($form_type == 'add_library_tran') { //add library tran form
                                ?>
                                <form class="cmxform form-horizontal tasi-form" id="add_library_tran" method="post"
                                      action="<?= base_url(); ?>admin/form_add_library_tran">

                                    <div class="form-group">
                                        <label for="class_lib" class="control-label col-lg-2 text-danger">Class & Sec *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input" id="class_lib" name="class" required >
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

                                    <div class="form-group ">
                                        <label for="issue_date" class="control-label col-lg-2 text-danger">Issuing Date *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <i class="fa fa-calendar"></i>
                                            <input value="<?=date('Y-m-d');?>" class="form-control round-input" id="issue_date" name="issue_date" type="date" required />
                                        </div>
                                    </div>

                                    <div class="form-group ">
                                        <label for="return_date_all" class="control-label col-lg-2">Return Date</label>
                                        <div class="col-lg-10 iconic-input">
                                            <i class="fa fa-calendar"></i>
                                            <input value="" class="form-control round-input" id="return_date_all" name="return_date_all" type="date" />
                                        </div>
                                    </div>

                                    <div class="form-group ">
                                        <div class="col-lg-10 col-lg-offset-1 iconic-input">
                                            <table class="table table-striped table-responsive">
                                                <thead>
                                                <tr>
                                                    <th class="col-lg-1">Roll No</th>
                                                    <th class="col-lg-4">Student Name</th>
                                                    <th class="col-lg-4" style="text-align: center">Book Name</th>
                                                    <th class="col-lg-3" style="text-align: center">Return Date</th>
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
                                                    value="submit_add_library_tran">Add <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            } elseif ($form_type == 'edit_library_tran') { //edit library tran form
                                ?>
                                <form class="cmxform form-horizontal tasi-form" id="edit_library_tran" method="post"
                                      action="<?= base_url(); ?>admin/form_edit_library_tran">

                                    <div class="form-group ">
                                        <label for="issue_date" class="control-label col-lg-2 text-danger">Issuing Date *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <i class="fa fa-calendar"></i>
                                            <input value="<?=$lb_hdr->date_issue;?>" class="form-control round-input" id="issue_date" name="issue_date" type="date" required />
                                        </div>
                                    </div>

                                    <div class="form-group ">
                                        <label for="return_date_all" class="control-label col-lg-2">Return Date</label>
                                        <div class="col-lg-10 iconic-input">
                                            <i class="fa fa-calendar"></i>
                                            <input value="" class="form-control round-input" id="return_date_all" name="return_date_all" type="date" />
                                        </div>
                                    </div>

                                    <div class="form-group ">
                                        <div class="col-lg-10 col-lg-offset-1 iconic-input">
                                            <table class="table table-striped table-responsive">
                                                <thead>
                                                <tr>
                                                    <th class="col-lg-1">Roll No</th>
                                                    <th class="col-lg-4">Student Name</th>
                                                    <th class="col-lg-4" style="text-align: center">Book Name</th>
                                                    <th class="col-lg-3" style="text-align: center">Return Date</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                foreach($lb_dtl as $dtl) {
                                                    ?>
                                                <tr>
                                                    <td><?=$dtl['STD_ROLLNO'];?></td>
                                                    <td><?=$dtl['STD_FNAME'].' '.$dtl['STD_MNAME'].' '.$dtl['STD_LNAME'];?></td>
                                                    <td>
                                                        <select name="book_id[<?=$dtl['lb_dtl_id'];?>]" class="form-control round-input" >
                                                            <option value="">Select book</option>
                                                            <?php
                                                            foreach($books as $b) {
                                                                $selected = '';
                                                                if($b['BOOK_SEQ'] == $dtl['BOOK_SEQ']){$selected = 'selected';}
                                                                ?>
                                                            <option <?=$selected;?> value="<?=$b['BOOK_SEQ'];?>"><?=$b['Accession_No'].' - '.$b['Book_Name'];?></option>;
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>
                                                    <td><input name="return_date[<?=$dtl['lb_dtl_id'];?>]" class="return_date form-control round-input" value="<?=$dtl['date_return'];?>" type="date" /></td>

                                                    <input name="lb_dtl[]" value="<?=$dtl['lb_dtl_id'];?>" type="hidden" />
                                                </tr>
                                                    <?php
                                                    }
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <input name="lb_hdr" value="<?=$lb_hdr->lb_hdr_id;?>" type="hidden" />

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="submit_edit_library_tran">Update <i class="fa fa-refresh"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            }  elseif ($form_type == 'student_auto_roll') { //assign student roll automatically
                                ?>
                                <form action="<?=base_url();?>admin/form_student_auto_roll" method="post" class="cmxform form-horizontal tasi-form">
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
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="submit_student_auto_roll"><i class="fa fa-magic"></i> Assign Roll No.
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <?php
                            }else if($form_type == 'student_add'){ ?>
                                <form action="<?=base_url();?>admin/form_add_student" method="post" enctype="multipart/form-data" class="cmxform form-horizontal tasi-form" id="st_form">
                                        <div class="form-group row" style="position: ;">
                                            
                                            <div class="col-lg-12" >
                                                <div class="row">
                                                    <label for="STD_IMAGE_PATH" class="control-label col-lg-2">Photograph</label>
                                                    <div class="col-lg-8">
                                                        <input type="file" class="form-control" onchange="document.getElementById('st_img').src = window.URL.createObjectURL(this.files[0]);document.getElementById('st_img').style.display = 'block';" name="STD_IMAGE_PATH" accept="image/png, image/jpg, image/jpeg" id="STD_IMAGE_PATH">
                                                    </div>
                                                </div>
                                                <div class="row" style="margin-top: 21px;">
                                                    <label for="STD_REGNO" class="control-label col-lg-2 text-danger">Registration No.</label>
                                                    <div class="col-lg-8">
                                                        <input type="text" class="form-control" placeholder="Reg.No" name="STD_REGNO" id="STD_REGNO">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="">
                                                <img src="" alt="Student Photo" id="st_img" style="width: 7%;border: 1px solid;position: absolute;right: 58px;top: 67px;text-align: center;height: 89px; display: none;">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label for="ST_FULL_NAME" class="control-label col-lg-2 text-danger">Student Name</label>
                                            <div class="col-lg-4">
                                                <input type="text" class="form-control" required title="Please enter the first name" name="STD_FNAME" placeholder="First Name" id="STD_FNAME">
                                            </div>
                                            <div class="col-lg-2">
                                                <input type="text" class="form-control" name="STD_MNAME" placeholder="Middle Name" id="STD_MNAME">
                                            </div>
                                            <div class="col-lg-4">
                                                <input type="text" class="form-control" required title="Please enter the last name" name="STD_LNAME" placeholder="Last Name" id="STD_LNAME">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            
                                            <label for="STD_EMAIL" class="control-label col-lg-2">Email Address</label>
                                            <div class="col-lg-2">
                                                <input type="email" class="form-control" placeholder="example@gmail.com" name="STD_EMAIL" id="STD_EMAIL">
                                            </div>
                                            
                                            <label for="STD_CS_SEQ" class="control-label col-lg-2 text-danger">Class / Section</label>
                                            <div class="col-lg-2">
                                                <select class="form-control round-input select2cssjs" id="STD_CS_SEQ" name="STD_CS_SEQ" required>
                                                    <option value="">-- Select --</option>
                                                    <?php
                                                    foreach($class as $val){
                                                    ?>
                                                    <option value="<?=$val['CS_SEQ'];?>"><?=$val['Class_Name'].' - '.$val['Sec_Name'];?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            
                                            <label for="STD_BLDGRP" class="control-label col-lg-2">Blood Group</label>
                                            <div class="col-lg-2">
                                                <select class="form-control select2cssjs" name="STD_BLDGRP">
                                                    <option value="unknown" selected>Unknown</option>
                                                    <option value="A+">A+</option>
                                                    <option value="A-">A-</option>
                                                    <option value="B+">B+</option>
                                                    <option value="B-">B-</option>
                                                    <option value="O+">O+</option>
                                                    <option value="O-">O-</option>
                                                    <option value="AB+">AB+</option>
                                                    <option value="AB-">AB-</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="STD_SEX" class="control-label col-lg-2 ">Gender</label>
                                            <div class="col-lg-2">
                                                <select class="form-control" name="STD_SEX" id="STD_SEX">
                                                    <option value="1">Male</option>
                                                    <option value="2">Female</option>
                                                </select>
                                            </div>
                                            <label for="STD_DOB" class="control-label col-lg-2 ">Date of Birth</label>
                                            <div class="col-lg-2">
                                                <input type="date" class="form-control" name="STD_DOB" id="STD_DOB">
                                            </div>
                                            <label for="STD_DOA" class="control-label col-lg-2">Date of Admission</label>
                                            <div class="col-lg-2">
                                                <input type="date" class="form-control" value="<?=$date_doa?>" name="STD_DOA" id="STD_DOA">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="STD_ROLLNO" class="control-label col-lg-2 text-danger">Roll Number</label>
                                            <div class="col-lg-2">
                                                <input type="text" class="form-control" style="font-weight: bold; text-align:center;" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" required name="STD_ROLLNO" placeholder="Roll No." id="STD_ROLLNO" title="Please enter roll no.">
                                            </div>
                                            
                                            <label for="STD_STATE" class="control-label col-lg-2 text-danger">State</label>
                                            <div class="col-lg-2">
                                                <select class="form-control round-input select2cssjs" id="STD_STATE" name="STD_STATE" required>
                                                    <option value="">-- Select --</option>
                                                    <?php
                                                    foreach($states as $val){
                                                    ?>
                                                    <option value="<?=$val->state_id;?>" <?=($val->state_id == 24?'selected':'')?>><?=$val->name;?></option>
                                                    <?php
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            
                                            <label for="STD_RC" class="control-label col-lg-2 text-danger">Student Type</label>
                                            <div class="col-lg-2">
                                                <select class="form-control" id="STD_RC" name="STD_RC" >
                                                    <option value="1">Cathlic</option>
                                                    <option value="0" selected>Non Cathlic</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label for="STD_CAT" class="control-label col-lg-2 text-danger">Student Category / Physical Disaible</label>
                                            <div class="col-lg-3">
                                                <select class="form-control" id="STD_CAT" name="STD_CAT" >
                                                    <option value="General" selected>General</option>
                                                    <option value="SC">SC</option>
                                                    <option value="ST">ST</option>
                                                    <option value="OBC">OBC</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-2">
                                                <select class="form-control" id="STD_PHYDSBL" name="STD_PHYDSBL" >
                                                    <option value="1">Yes</option>
                                                    <option value="0" selected>No</option>
                                                    
                                                </select>
                                            </div>
                                            <label for="STD_CONSC" class="control-label col-lg-3 text-danger">Allow Concession</label>
                                            <div class="col-lg-2">
                                                <select class="form-control" id="STD_CONSC" name="STD_CONSC" >
                                                    <option value="1">True</option>
                                                    <option selected value="0" selected>False</option>
                                                    
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="STD_NAT" class="control-label col-lg-2">Nationality</label>
                                            <div class="col-lg-4">
                                                <input type="text" value="INDIAN" style="text-transform: uppercase;" class="form-control" name="STD_NAT" id="STD_NAT">
                                            </div>
                                            <label for="STD_RLGN" class="control-label col-lg-1 text-danger">Religion</label>
                                            <div class="col-lg-5">
                                                <select class="form-control" id="STD_RLGN" name="STD_RLGN">
                                                    <?php foreach ($religion as $Indx => $rlgnr) {?>
                                                    <option value="<?=$rlgnr->religion_id?>" <?=($rlgnr->name == 'Islam')?'selected':''?>><?=$rlgnr->name?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="aadhaar_id" class="control-label col-lg-1">Aadhaar ID</label>
                                            <div class="col-lg-4">
                                                <input type="text" class="form-control" name="aadhaar_id" placeholder="Student's Aadhaar Number" id="aadhaar_id">
                                            </div>
                                            <label for="banglar_shiksha_id" class="control-label col-lg-2">Banglar Shiksha ID</label>
                                            <div class="col-lg-5">
                                                <input type="text" class="form-control" name="banglar_shiksha_id" placeholder="Student's Banglar Shiksha ID" id="banglar_shiksha_id">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="STD_FTH_NAME" class="control-label col-lg-2">Father's Name</label>
                                            <div class="col-lg-10">
                                                <input type="text" class="form-control" onkeyup="document.getElementById('STD_GRD_NAME').value = this.value" placeholder="Fatner Name" name="STD_FTH_NAME" id="STD_FTH_NAME">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="STD_MTH_NAME" class="control-label col-lg-2">Mother's Name</label>
                                            <div class="col-lg-4">
                                                <input type="text" class="form-control" name="STD_MTH_NAME" placeholder="Mother Name" id="STD_MTH_NAME">
                                            </div>
                                            <label for="STD_GRD_NAME" class="control-label col-lg-1">Gurdian Name</label>
                                            <div class="col-lg-5">
                                                <input type="text" class="form-control" name="STD_GRD_NAME" placeholder="Gurdian Name" id="STD_GRD_NAME">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="STD_ADDR_0" class="control-label col-lg-2">Student Address</label>
                                            <div class="col-lg-10">
                                                <textarea name="STD_ADDR_0" id="STD_ADDR_0" placeholder="Residental Address" cols="50" style="height:auto !important;" rows="10" class="form-control">KOLKATA 700</textarea>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="STD_PH_NO" class="control-label col-lg-2">Phone (Residence)</label>
                                            <div class="col-lg-4">
                                                <input type="text" class="form-control" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" minlength="10" maxlength="10" title="10 digit phone number" placeholder="Residental Phone Number" name="STD_PH_NO" id="STD_PH_NO">
                                            </div>
                                            <label for="STD_FTH_PHNO" class="control-label col-lg-1">Phone (Office)</label>
                                            <div class="col-lg-5">
                                                <input type="text" class="form-control" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" minlength="10" maxlength="10" title="10 digit phone number"  placeholder="Office / Father Phone Number" name="STD_FTH_PHNO" id="STD_FTH_PHNO">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="STD_FTH_OCP" class="control-label col-lg-2">Gurdian Ocupation</label>
                                            <div class="col-lg-4">
                                                <input type="text" class="form-control" placeholder="Gurdian Ocupation" name="STD_FTH_OCP" id="STD_FTH_OCP">
                                            </div>
                                            <label for="STD_2LANG" class="control-label col-lg-1">2nd Language</label>
                                            <div class="col-lg-2">
                                                <select class="form-control select2cssjs" id="STD_2LANG" name="STD_2LANG">
                                                    <option value="">-- Select --</option>
                                                    <?php foreach ($sub as $subr) {?>
                                                    <option value="<?=$subr->sub_id?>"><?=$subr->sub_name?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <label for="STD_3LANG" class="control-label col-lg-1">3rd Language</label>
                                            <div class="col-lg-2">
                                                <select class="form-control select2cssjs" id="STD_3LANG" name="STD_3LANG">
                                                    <option value="">-- Select --</option>
                                                    <?php foreach ($sub as $subr) {?>
                                                    <option value="<?=$subr->sub_id?>"><?=$subr->sub_name?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="STD_LAST_SCHOOL" class="control-label col-lg-2"> Last School Name</label>
                                            <div class="col-lg-4">
                                                <input type="text" class="form-control" placeholder="Last School Name" name="STD_LAST_SCHOOL" id="STD_LAST_SCHOOL">
                                            </div>

                                            <label for="STD_CURRENT_SESSION" class="control-label col-lg-1 text-danger">Current Session</label>
                                            <div class="col-lg-1">
                                            <?php 
                                                    $year = array();
                                                    $year['2022'] = '2022';
                                                    $year['2023'] = '2023';
                                                    $year['2024'] = '2024';
                                                    $year['2025'] = '2025';
                                                    $year['2026'] = '2026';
                                                     ?>
                                                     <select class="form-control" placeholder="Last School Name" name="STD_CURRENT_SESSION" id="STD_CURRENT_SESSION" required>
                                                        <?php foreach ($year as $year_key=>$year_val) { ?>
                                                            <option value="<?=$year_key;?>" <?=($year_key == 2024)?'selected':''?>><?=$year_val;?></option>
                                                        <?php } ?>

                                                     </select>
                                               
                                            </div>

                                            <label for="pot" class="control-label col-lg-1">PoT</label>
                                            <div class="col-lg-1">
                                                <input type="radio" name="pot_pow" id="pot" value="PoT" class="form-control">
                                            </div>
                                            <label for="pow" class="control-label col-lg-1">PoW</label>
                                            <div class="col-lg-1">
                                                <input type="radio" name="pot_pow" id="pow" value="PoW" class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="STD_LAST_CLASS" class="control-label col-lg-2">Last Class Antd.</label>
                                            <div class="col-lg-2">
                                                <select class="form-control round-input select2cssjs" id="STD_LAST_CLASS" name="STD_LAST_CLASS">
                                                    <option value="">--  Select --</option>
                                                    <?php
                                                    foreach($class as $val){
                                                    ?>
                                                    <option value="<?=$val['CS_SEQ'];?>"><?=$val['Class_Name'].' - '.$val['Sec_Name'];?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <label for="STD_TC_NO" class="control-label col-lg-1">T.C No.</label>
                                            <div class="col-lg-3">
                                                <input type="text" class="form-control" placeholder="T.C No." name="STD_TC_NO" id="STD_TC_NO">
                                            </div>
                                            <label for="STD_TC_DT" class="control-label col-lg-1"> T.C Date</label>
                                            <div class="col-lg-3">
                                                <input type="date" class="form-control" name="STD_TC_DT" id="STD_TC_DT">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="STD_LEFT" class="control-label col-lg-2">Left From School</label>
                                            <div class="checkbox-custom check-success col-lg-1" style="margin: 7px 0 10px 0; display: block; padding-left: 16px;">
                                                <select class="form-control" id="STD_LEFT" name="STD_LEFT" required>
                                                    <option value="0" selected="">No</option>
                                                    <option value="1">Yes</option>                                                    
                                                </select>
                                            </div>
                                            <label for="STD_DT_LV" class="control-label col-lg-1">Date Of Leaving</label>
                                            <div class="col-lg-2">
                                                <input type="date" class="form-control" name="STD_DT_LV" id="STD_DT_LV">
                                            </div>
                                            <label for="STD_PROMOTED" class="control-label col-lg-1 text-danger"> Promoted</label>
                                            <div class="col-lg-2">
                                                <select class="form-control" id="STD_PROMOTED" name="STD_PROMOTED" required>
                                                    <option value="1">Yes</option>
                                                    <option value="0">No</option>
                                                    
                                                </select>
                                            </div>
                                            <label for="STD_PRM" class="control-label col-lg-1 text-danger"> Promotion</label>
                                            <div class="col-lg-2">
                                                <select class="form-control" id="STD_PRM" name="STD_PRM" required>
                                                    <option value="1" selected>Granted</option>
                                                    <option value="0">Not Granted</option>
                                                    
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="" class="control-label col-lg-2"> 2nd Language</label>
                                            <div class="col-lg-2">
                                                <select class="form-control select2cssjs" name="second_language">
                                                    <option value="" selected>Select</option>
                                                    <option value="Bengali">Bengali</option>
                                                    <option value="Hindi">Hindi</option>
                                                </select>
                                            </div>
                                            <label for="" class="control-label col-lg-2"> 3rd Language</label>
                                            <div class="col-lg-2">
                                                <select class="form-control select2cssjs" name="third_language">
                                                    <option value="" selected>Select</option>
                                                    <option value="Bengali">Bengali</option>
                                                    <option value="Hindi">Hindi</option>
                                                </select>
                                            </div>
                                        </div>
                                       
                                            
                                      
                                        <div class="form-group text-center">
                                            <div class="col-lg-12">
                                                <button class="btn btn-success" type="submit" name="submit"
                                                value="add_student">  Submit
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                            <?php }else if($form_type == 'student_edit'){ ?>
                                <form action="<?=base_url();?>admin/form_edit_student" method="post" enctype="multipart/form-data" id="st_form" class="cmxform form-horizontal tasi-form">
                                        <input type="hidden" value="<?=$student_details->STD_SEQ?>" name="STD_SEQ" id="STD_SEQ">
                                        <div class="form-group row" style="position: ;">
                                            <div class="col-lg-12" >
                                                <div class="row">
                                                    <label for="STD_IMAGE_PATH" class="control-label col-lg-2">Photograph</label>
                                                    <div class="col-lg-8">
                                                        <input type="file" class="form-control" onchange="document.getElementById('st_img').src = window.URL.createObjectURL(this.files[0]);" name="STD_IMAGE_PATH" accept="image/png, image/jpg, image/jpeg" id="STD_IMAGE_PATH">
                                                    </div>
                                                </div>
                                                <div class="row" style="margin-top: 21px;">
                                                    <label for="STD_REGNO" class="control-label col-lg-2 text-danger">Registration No.</label>
                                                    <div class="col-lg-8">
                                                        <input type="text" class="form-control" required placeholder="Reg.No" value="<?=$student_details->STD_REGNO?>" name="STD_REGNO" id="STD_REGNO">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            
                                            <div class="">
                                                <img src="<?=base_url('assets/img/students')?>/<?=$student_details->STD_IMAGE_PATH?>" alt="Student Photo" id="st_img" style="width: 7%;border: 1px solid;position: absolute;right: 58px;top: 67px;text-align: center;height: 89px;">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="STD_FNAME" class="control-label col-lg-2 text-danger">Student Name</label>
                                            <div class="col-lg-4">
                                                <input type="text" class="form-control" required name="STD_FNAME" title="Please enter the first name" value="<?=$student_details->STD_FNAME?>" placeholder="First Name" id="STD_FNAME">
                                            </div>
                                            <div class="col-lg-2">
                                                <input type="text" class="form-control" name="STD_MNAME" value="<?=$student_details->STD_MNAME?>" placeholder="Middle Name" id="STD_MNAME">
                                            </div>
                                            <div class="col-lg-4">
                                                <input type="text" class="form-control" required name="STD_LNAME" title="Please enter the last name" value="<?=$student_details->STD_LNAME?>" placeholder="Last Name" id="STD_LNAME">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="STD_EMAIL" class="control-label col-lg-2">Email Address</label>
                                            <div class="col-lg-2">
                                                <input type="email" class="form-control" value="<?=$student_details->STD_EMAIL?>" placeholder="example@gmail.com" name="STD_EMAIL" title="Please enter a valid email address." id="STD_EMAIL">
                                            </div>
                                            <label for="STD_CS_SEQ" class="control-label col-lg-2 text-danger">Class / Section</label>
                                            <div class="col-lg-2">
                                                <select class="form-control round-input select2cssjs" id="STD_CS_SEQ" name="STD_CS_SEQ" required>
                                                    <option value="">-- Select --</option>
                                                    <?php
                                                    foreach($class as $val){
                                                    ?>
                                                    <option <?=($student_details->STD_CS_SEQ == $val['CS_SEQ'])?'selected':''?> value="<?=$val['CS_SEQ'];?>"><?=$val['Class_Name'].' - '.$val['Sec_Name'];?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <label for="STD_BLDGRP" class="control-label col-lg-2 text-danger">Blood Group</label>
                                            <div class="col-lg-2">
                                                <select class="form-control round-input select2cssjs" id="STD_BLDGRP" name="STD_BLDGRP" required>
                                                    <option value="unknown">Unknown</option>
                                                    <option value="A+" <?=($student_details->STD_BLDGRP == 'A+')?'selected':''?>>A+</option>
                                                    <option value="A-" <?=($student_details->STD_BLDGRP == 'A-')?'selected':''?>>A-</option>
                                                    <option value="B+" <?=($student_details->STD_BLDGRP == 'B+')?'selected':''?>>B+</option>
                                                    <option value="B-" <?=($student_details->STD_BLDGRP == 'B-')?'selected':''?>>B-</option>
                                                    <option value="AB+" <?=($student_details->STD_BLDGRP == 'AB+')?'selected':''?>>AB+</option>
                                                    <option value="AB-" <?=($student_details->STD_BLDGRP == 'AB-')?'selected':''?>>AB-</option>
                                                    <option value="O+" <?=($student_details->STD_BLDGRP == 'O+')?'selected':''?>>O+</option>
                                                    <option value="O-" <?=($student_details->STD_BLDGRP == 'O-')?'selected':''?>>O-</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="STD_SEX" class="control-label col-lg-2 ">Gender</label>
                                            <div class="col-lg-2">
                                                <select class="form-control" id="STD_SEX" name="STD_SEX" >
                                                    <option value="">-- Select --</option>
                                                    <option value="1" <?=($student_details->STD_SEX == 1)?'selected':''?> >Male</option>
                                                    <option value="0" <?=($student_details->STD_SEX == 0)?'selected':''?> >Female</option>
                                                </select>
                                            </div>
                                            <label for="STD_DOB" class="control-label col-lg-2 ">Date of Birth</label>
                                            <div class="col-lg-2">
                                                <input type="date" value="<?=$student_details->STD_DOB?>" class="form-control" name="STD_DOB" id="STD_DOB">
                                            </div>
                                            <label for="STD_DOA" class="control-label col-lg-2">Date of Admission</label>
                                            <div class="col-lg-2">
                                                <input type="date" value="<?=$student_details->STD_DOA?>" class="form-control" name="STD_DOA" id="STD_DOA">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="STD_ROLLNO" class="control-label col-lg-2 text-danger">Roll Number</label>
                                            <div class="col-lg-2">
                                                <input type="text" value="<?=$student_details->STD_ROLLNO?>" class="form-control" style="font-weight: bold; text-align:center;" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" required name="STD_ROLLNO" placeholder="Roll No." title="Please enter roll no." id="STD_ROLLNO">
                                            </div>
                                            <label for="STD_STATE" class="control-label col-lg-2 text-danger">State</label>
                                            <div class="col-lg-2">
                                                <select class="form-control round-input select2cssjs" id="STD_STATE" name="STD_STATE" required>
                                                    <option value="">-- Select --</option>
                                                    <?php
                                                    foreach($states as $val){
                                                    ?>
                                                    <option value="<?=$val->state_id;?>" <?=($student_details->STD_STATE == $val->state_id)?'selected':''?>><?=$val->name;?></option>
                                                    <?php
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                            
                                            <label for="STD_RC" class="control-label col-lg-2 text-danger">Student Type</label>
                                            <div class="col-lg-2">
                                                <select class="form-control" id="STD_RC" name="STD_RC" >
                                                    <option value="1" <?=($student_details->STD_RC == 1)?'selected':''?> >Cathlic</option>
                                                    <option value="0" <?=($student_details->STD_RC == 0)?'selected':''?> >Non Cathlic</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="STD_CAT" class="control-label col-lg-2 text-danger">Student Category / Physical Disaible</label>
                                            <div class="col-lg-3">
                                                <select class="form-control" id="STD_CAT" name="STD_CAT" >
                                                    <option value="General"  <?=($student_details->STD_CAT == 'General')?'selected':''?> >General</option>
                                                    <option value="SC" <?=($student_details->STD_CAT == 'SC')?'selected':''?> >SC</option>
                                                    <option value="ST" <?=($student_details->STD_CAT == 'ST')?'selected':''?> >ST</option>
                                                    <option value="OBC" <?=($student_details->STD_CAT == 'OBC')?'selected':''?> >OBC</option>
                                                    <option value="Other" <?=($student_details->STD_CAT == 'Other')?'selected':''?> >Other</option>
                                                </select>
                                            </div>
                                            <div class="col-lg-1">
                                                <select class="form-control" id="STD_PHYDSBL" name="STD_PHYDSBL" >
                                                    <option value="1" <?=($student_details->STD_PHYDSBL == 1)?'selected':''?>>Yes</option>
                                                    <option value="0" <?=($student_details->STD_PHYDSBL == 0)?'selected':''?>>No</option>
                                                    
                                                </select>
                                            </div>
                                            <label for="STD_CONSC" class="control-label col-lg-2 text-danger">Allow Concession</label>
                                            <div class="col-lg-4">
                                                <select class="form-control" id="STD_CONSC" name="STD_CONSC" >
                                                    <option value="1" <?=($student_details->STD_CONSC == 1)?'selected':''?>>True</option>
                                                    <option value="0" <?=($student_details->STD_CONSC == 0)?'selected':''?>>False</option>
                                                    
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="STD_NAT" class="control-label col-lg-2">Nationality</label>
                                            <div class="col-lg-4">
                                                <input type="text" value="<?=$student_details->STD_NAT?>" style="text-transform: uppercase;" class="form-control" name="STD_NAT" id="STD_NAT">
                                            </div>
                                            <label for="STD_RLGN" class="control-label col-lg-1 text-danger">Religion</label>
                                            <div class="col-lg-5">
                                                <select class="form-control" id="STD_RLGN" name="STD_RLGN">
                                                    <?php foreach ($religion as $Indx => $rlgnr) {?>
                                                    <option value="<?=$rlgnr->religion_id?>" <?=($student_details->STD_RLGN == $rlgnr->religion_id)?'selected':''?>><?=$rlgnr->name?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="aadhaar_id" class="control-label col-lg-1">Aadhaar ID</label>
                                            <div class="col-lg-4">
                                                <input type="text" value="<?=$student_details->aadhaar_id?>" class="form-control" name="aadhaar_id" placeholder="Student's Aadhaar Number" id="aadhaar_id">
                                            </div>
                                            <label for="banglar_shiksha_id" class="control-label col-lg-2">Banglar Shiksha ID</label>
                                            <div class="col-lg-5">
                                                <input type="text" value="<?=$student_details->banglar_shiksha_id?>" class="form-control" name="banglar_shiksha_id" placeholder="Student's Banglar Shiksha ID" id="banglar_shiksha_id">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="STD_FTH_NAME" class="control-label col-lg-2">Father's Name</label>
                                            <div class="col-lg-10">
                                                <input type="text" class="form-control" onkeyup="document.getElementById('STD_GRD_NAME').value = this.value" value="<?=$student_details->STD_FTH_NAME?>" placeholder="Fatner Name" name="STD_FTH_NAME" id="STD_FTH_NAME">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="STD_MTH_NAME" class="control-label col-lg-2">Mother's Name</label>
                                            <div class="col-lg-4">
                                                <input type="text" class="form-control" value="<?=$student_details->STD_MTH_NAME?>" name="STD_MTH_NAME" placeholder="Mother Name" id="STD_MTH_NAME">
                                            </div>
                                            <label for="STD_GRD_NAME" class="control-label col-lg-1">Gurdian Name</label>
                                            <div class="col-lg-5">
                                                <input type="text" class="form-control" value="<?=$student_details->STD_GRD_NAME?>" name="STD_GRD_NAME" placeholder="Gurdian Name" id="STD_GRD_NAME">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="STD_ADDR_0" class="control-label col-lg-2">Student Address</label>
                                            <div class="col-lg-10">
                                                <textarea name="STD_ADDR_0" id="STD_ADDR_0" placeholder="Residental Address" cols="50" style="height:auto !important;" rows="10" class="form-control"><?=(empty($student_details->STD_ADDR_0)?'Kolkata 700':$student_details->STD_ADDR_0)?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="STD_PH_NO" class="control-label col-lg-2">Phone (Residence)</label>
                                            <div class="col-lg-4">
                                                <input type="text" class="form-control" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" minlength="10" maxlength="10" title="10 digit phone number" value="<?=$student_details->STD_PH_NO?>" placeholder="Residental Phone Number" name="STD_PH_NO" id="STD_PH_NO">
                                            </div>
                                            <label for="STD_FTH_PHNO" class="control-label col-lg-1">Phone (Office)</label>
                                            <div class="col-lg-5">
                                                <input type="text" class="form-control" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" minlength="10" maxlength="10" title="10 digit phone number" value="<?=$student_details->STD_FTH_PHNO?>" placeholder="Office / Father Phone Number" name="STD_FTH_PHNO" id="STD_FTH_PHNO">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="STD_FTH_OCP" class="control-label col-lg-2">Gurdian Ocupation</label>
                                            <div class="col-lg-4">
                                                <input type="text" class="form-control" value="<?=$student_details->STD_FTH_OCP?>" placeholder="Gurdian Ocupation" name="STD_FTH_OCP" id="STD_FTH_OCP">
                                            </div>
                                            <label for="STD_2LANG" class="control-label col-lg-1">2nd Language</label>
                                            <div class="col-lg-2">
                                                <select class="form-control select2cssjs" id="STD_2LANG" name="STD_2LANG">
                                                    <option value="">-- Select --</option>
                                                    <?php foreach ($sub as $subr) {?>
                                                    <option value="<?=$subr->sub_id?>" <?=($student_details->STD_2LANG == $subr->sub_id)?'selected':''?>><?=$subr->sub_name?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <label for="STD_3LANG" class="control-label col-lg-1">3rd Language</label>
                                            <div class="col-lg-2">
                                                <select class="form-control select2cssjs" id="STD_3LANG" name="STD_3LANG">
                                                    <option value="">-- Select --</option>
                                                    <?php foreach ($sub as $subr) {?>
                                                    <option value="<?=$subr->sub_id?>" <?=($student_details->STD_3LANG == $subr->sub_id)?'selected':''?>><?=$subr->sub_name?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="STD_LAST_SCHOOL" class="control-label col-lg-2"> Last School Name</label>
                                            <div class="col-lg-4">
                                                <input type="text" class="form-control" value="<?=$student_details->STD_LAST_SCHOOL?>" placeholder="Last School Name" name="STD_LAST_SCHOOL" id="STD_LAST_SCHOOL">
                                            </div>

                                            <label for="STD_CURRENT_SESSION" class="control-label col-lg-1 text-danger">Current Session</label>
                                            <div class="col-lg-1">
                                                <?php 
                                                    $year = array();
                                                    $year['2022'] = '2022';
                                                    $year['2023'] = '2023';
                                                    $year['2024'] = '2024';
                                                    $year['2025'] = '2025';
                                                    $year['2026'] = '2026';
                                                     ?>
                                                     <select class="form-control" placeholder="Last School Name" name="STD_CURRENT_SESSION" id="STD_CURRENT_SESSION" required>
                                                        <?php foreach ($year as $year_key=>$year_val) { ?>
                                                            <option value="<?=$year_key;?>" <?=($student_details->STD_CURRENT_SESSION == $year_key)?'selected':''?>><?=$year_val;?></option>
                                                        <?php } ?>

                                                     </select>
                                               
                                            </div>

                                            <label for="pot" class="control-label col-lg-1">PoT</label>
                                            <div class="col-lg-1">
                                                <input type="radio" name="pot_pow" id="pot" value="PoT" <?=($student_details->PoT_PoW == 'PoT')?'checked':''?> class="form-control">
                                            </div>
                                            <label for="pow" class="control-label col-lg-1">PoW</label>
                                            <div class="col-lg-1">
                                                <input type="radio" name="pot_pow" id="pow" value="PoW" <?=($student_details->PoT_PoW == 'PoW')?'checked':''?> class="form-control">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="STD_LAST_CLASS" class="control-label col-lg-2">Last Class Antd.</label>
                                            <div class="col-lg-2">
                                                <select class="form-control round-input select2cssjs" id="STD_LAST_CLASS" name="STD_LAST_CLASS">
                                                    <option value="">--  Select --</option>
                                                    <?php
                                                    foreach($class as $val){
                                                    ?>
                                                    <option <?=($student_details->STD_LAST_CLASS == $val['CS_SEQ'])?'selected':''?> value="<?=$val['CS_SEQ'];?>"><?=$val['Class_Name'].' - '.$val['Sec_Name'];?></option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <label for="STD_TC_NO" class="control-label col-lg-1">T.C No.</label>
                                            <div class="col-lg-3">
                                                <input type="text" class="form-control" value="<?=$student_details->STD_TC_NO?>" placeholder="T.C No." name="STD_TC_NO" id="STD_TC_NO">
                                            </div>
                                            <label for="STD_TC_DT" class="control-label col-lg-1"> T.C Date</label>
                                            <div class="col-lg-3">
                                                <input type="date" class="form-control" value="<?=$student_details->STD_TC_DT?>" name="STD_TC_DT" id="STD_TC_DT">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="STD_LEFT" class="control-label col-lg-2">Left From School</label>
                                            <div class="checkbox-custom check-success col-lg-1" style="margin: 7px 0 10px 0; display: block; padding-left: 16px;">
                                                <select class="form-control" id="STD_LEFT" name="STD_LEFT" required>
                                                    <option value="0" <?=($student_details->STD_LEFT == 0)?'selected':''?>>No</option>
                                                    <option value="1" <?=($student_details->STD_LEFT == 1)?'selected':''?>>Yes</option>
                                                </select>
                                            </div>
                                            <label for="STD_DT_LV" class="control-label col-lg-1">Date Of Leaving</label>
                                            <div class="col-lg-2">
                                                <input type="date" class="form-control" name="STD_DT_LV" value="<?=$student_details->STD_DT_LV?>" id="STD_DT_LV">
                                            </div>
                                            <label for="STD_PROMOTED" class="control-label col-lg-1 text-danger"> Promoted</label>
                                            <div class="col-lg-2">
                                                <select class="form-control" id="STD_PROMOTED" name="STD_PROMOTED" required>
                                                    <option value="1" <?=($student_details->STD_PROMOTED == 1)?'selected':''?>>Yes</option>
                                                    <option value="0" <?=($student_details->STD_PROMOTED == 0)?'selected':''?>>No</option>
                                                    
                                                </select>
                                            </div>
                                            
                                            <label for="STD_PRM" class="control-label col-lg-1 text-danger"> Promotion</label>
                                            <div class="col-lg-2">
                                                <select class="form-control" id="STD_PRM" name="STD_PRM" required>
                                                    <option value="1" <?=($student_details->STD_PRM == 1)?'selected':''?>>Granted</option>
                                                    <option value="0" <?=($student_details->STD_PRM == 0)?'selected':''?>>Not Granted</option>
                                                    
                                                </select>
                                            </div>
                                           
                                        </div>
                                         <div class="form-group row">
                                            <label for="STD_PRM" class="control-label col-lg-1 text-danger"> Status</label>
                                            <div class="col-lg-2">
                                                <select class="form-control" id="STD_STATUS" name="STD_STATUS" required>
                                                    <option value="0" <?=($student_details->STD_STATUS == 0)?'selected':''?>>Active</option>
                                                    <option value="1" <?=($student_details->STD_STATUS == 1)?'selected':''?>>Delete</option>
                                                    
                                                </select>  
                                            </div>
                                         </div>
                                          <div class="form-group row">
                                            <label for="" class="control-label col-lg-2"> 2nd Language</label>
                                            <div class="col-lg-2">
                                                <select class="form-control select2cssjs" name="second_language">
                                                    <option value="" selected>Select</option>
                                                    <option value="Bengali" <?=($student_details->STD_SECOND_LANG == "Bengali")?'selected':''?>>Bengali</option>
                                                    <option value="Hindi" <?=($student_details->STD_SECOND_LANG == "Hindi")?'selected':''?>>Hindi</option>
                                                </select>
                                            </div>
                                            <label for="" class="control-label col-lg-2"> 3rd Language</label>
                                            <div class="col-lg-2">
                                                <select class="form-control select2cssjs" name="third_language">
                                                    <option value="" selected>Select</option>
                                                    <option value="Bengali" <?=($student_details->STD_THIRD_LANG == "Bengali")?'selected':''?>>Bengali</option>
                                                    <option value="Hindi" <?=($student_details->STD_THIRD_LANG == "Hindi")?'selected':''?>>Hindi</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group text-center">
                                            <div class="col-lg-12">
                                                <button class="btn btn-success" type="submit" name="submit"
                                                value="edit_student">  Submit
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.js"></script>
<script>
    $("#st_form").validate({
        rules: {
            STD_REGNO: {
                required: true,
                remote: {
                    url: "<?=base_url('admin/check_reg_no')?>",
                    type: "post",
                    data: {
                        STD_REGNO: function() {
                          return $("#STD_REGNO").val();
                        },
                        <?php if($form_type == 'student_edit'){?>
                           STD_SEQ: function() {
                            return $("#STD_SEQ").val();
                        } 
                        <?php } ?>
                    },
                },
            },
        }
    });
    //update student table data on class change
    $('#class_lib').change(function(){
        class_id = $('#class_lib').val();
        data = {
            'class_id': class_id
        };
        $.ajax({
            url: "<?=base_url();?>admin/ajax_update_std_table_data",
            type: "post",
            data: data,
            success: function(data) {
                data = $.parseJSON(data);
                $('#std_table').html(data);
            }
        });
    });

    //change all return date
    $('#return_date_all').change(function(){
        return_date = $('#return_date_all').val();
        $('.return_date').val(return_date);
    });
</script>
</body>
</html>

