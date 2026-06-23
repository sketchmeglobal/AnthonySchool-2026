<?php // echo '<pre>', print_r($all_account_groups), '</pre>'; die; ?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=$menu_name?></title>
    <!-- common head -->
    <?php $this->load->view('components/_common_head'); //left side menu ?>
    <!-- /common head -->

    <!--<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.2.0/css/bootstrap.min.css" rel="stylesheet">-->
    <link href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>

<body class="sticky-header">

<section>
    <!-- sidebar left start (Menu)-->
    <?php $this->load->view('components/left_sidebar'); //left side menu ?>
    <!-- sidebar left end (Menu)-->

    <!-- body content start-->
    <div class="body-content" style="min-height: 1500px;">

        <!-- page head start-->
        <div class="page-head">
            <h3 class="m-b-less">
                <?=$menu_name?>
            </h3>
            <!--<span class="sub-title">Welcome to Static Table</span>-->
            <div class="state-information">
                <ol class="breadcrumb m-b-less bg-less">
                    <li><a href="<?=base_url('admin/dashboard');?>">Home</a></li>
                    <li class="active"> <?=$menu_name?> </li>
                </ol>
            </div>
        </div>
        <!-- page head end-->

        <!-- header section start-->
        <?php $this->load->view('components/top_menu'); ?>

        <!--body wrapper start-->
        <div class="wrapper" style="padding:0px;">
            <div class="container-fluid">
                <div class="col-lg-12">
                    <section class="panel">
                        <?php

                        if($menu_name == 'Account Group'){
                            ?>
                            <a style="display: table;margin:auto;width: 100px;" href="<?=base_url()?>admin_panel/Masters/account_group_edit/add" class="btn btn-primary">Add</a>
                            <table class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Sr. #</th>
                                    <th>Account Group Name</th>
                                    <th>Account Group PLBS</th>
                                    <th>Account Group PLBS PART</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php

                                $iter=1;
                                foreach($all_account_groups as $op){
                                    ?>
                                    <tr>
                                        <td><?=$iter++?></td>
                                        <td><?=$op->ACC_GROUP_NAME?></td>
                                        <td><?=($op->ACC_GROUP_PLBS == 1) ? 'Profit and Loss' : 'Balance Sheet' ?></td>
                                        <td><?php
                                            if($op->ACC_GROUP_PLBS_PART == 1){
                                                echo 'Income';
                                            }else if($op->ACC_GROUP_PLBS_PART == 2){
                                                echo 'Expenditure';
                                            }else if($op->ACC_GROUP_PLBS_PART == 3){
                                                echo 'Assets';
                                            }else{
                                                echo 'Liabilities';
                                            }
                                            ?></td>
                                        <td nowrap>
                                            <a href="<?=base_url()?>admin_panel/Masters/account_group_edit/edit/<?=$op->ACC_GROUP_CODE?>" class="btn btn-warning">Edit</a>
                                            <!--<a href="" class="btn btn-danger">Delete</a>-->
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                            <?php
                        }
                        else if($menu_name == 'Account Master'){
                            ?>
                            <a style="display: table;margin-bottom: 10px;width: 100px;" href="<?=base_url()?>admin_panel/Masters/account_master_edit/add" class="btn btn-primary">Add</a>
                            <table class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Sr. #</th>
                                    <th>Account Group </th>
                                    <th>Account Name</th>
                                    <th>Fees Type</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php

                                $iter=1;
                                foreach($all_account_masters as $op){
                                    ?>
                                    <tr>
                                        <td><?=$iter++?></td>
                                        <td><?=$op->ACC_GROUP_NAME?></td>
                                        <td><?= $op->ACC_MASTER_NAME ?></td>
                                        <td><?= $op->name ?></td>
                                        <td nowrap>
                                            <a href="<?=base_url()?>admin_panel/Masters/account_master_edit/edit/<?=$op->ACC_MASTER_CODE?>" class="btn btn-warning">Edit</a>
                                            <!--<a href="" class="btn btn-danger">Delete</a>-->
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                            <?php
                        }
                        else if($menu_name == 'Class Section'){
                            ?>
                            <a style="display: table;margin-bottom: 10px;width: 100px;" href="<?=base_url()?>admin_panel/Masters/class_section_edit/add" class="btn btn-primary">Add</a>
                            <table class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Sr. #</th>
                                    <th>Class name</th>
                                    <th>Section name</th>
                                    <th>School</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php

                                $iter=1;
                                foreach($all_class_sections as $op){
                                    ?>
                                    <tr>
                                        <td><?=$iter++?></td>
                                        <td><?=$op->Class_Name?></td>
                                        <td><?= $op->Sec_Name ?></td>
                                        <td><?= $op->name ?></td>
                                        <td nowrap>
                                            <a href="<?=base_url()?>admin_panel/Masters/class_section_edit/edit/<?=$op->CS_SEQ?>" class="btn btn-warning">Edit</a>
                                            <a href="<?=base_url()?>admin/edit_class_fess/<?=$op->CS_SEQ?>" class="btn btn-info">Fee Structure</a>
                                            <!--<a href="" class="btn btn-danger">Delete</a>-->
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                            <?php
                        }
                        else if($menu_name == 'Student Details'){
                            ?>
                            <a style="display: table;margin-bottom: 10px;width: 100px;" href="<?=base_url("admin/add_student")?>" class="btn btn-primary">Add</a>
                            <table class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Sr. #</th>
                                    <th>Class &amp; Section</th>
                                    <th>Roll No.</th>
                                    <th>Reg. No.</th>
                                    <th>Adm. No.</th>
                                    <th>Full Name</th>
                                    <th>Gender</th>
                                    <th>Phone No.</th>
                                    <th>Picture</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php

                                $iter=1;
                                foreach($all_stdnt_details as $op){
                                    ?>
                                    <tr>
                                        <td><?=$iter++?></td>

                                        <td><?=$op->class_sec?></td>
                                        <td><?=$op->STD_ROLLNO ?></td>
                                        <td><?=$op->STD_REGNO?></td>
                                        <td><?=$op->STD_SRLNO ?></td>
                                        <td><?=$op->ST_FULL_NAME?></td>
                                        <td><?=($op->STD_SEX == 1)?'M':'F'?></td>
                                        <td><?=$op->STD_PH_NO ?></td>
                                        <?php if (!empty($op->STD_IMAGE_PATH)) {?>
                                            <td><img src="<?=base_url('assets/img/students')?>/<?=$op->STD_IMAGE_PATH ?>" style="width: 52px;" /></td>
                                        <?php }else{ ?>
                                            <td></td>
                                        <?php } ?>
                                        <td>
                                        <?php
                                        if($op->STD_STATUS == 0){
                                            echo "Active";
                                        }else if($op->STD_STATUS == 1){
                                            echo "Deleted";
                                        }
                                        ?>
                                        </td>

                                        <td nowrap>
                                            <a href="<?=base_url("admin/edit_student")?>/<?=$op->STD_SEQ?>" class="btn btn-warning">Edit</a>
                                            <a href="<?=base_url()?>admin/print_certificate/<?=$op->STD_SEQ?>" class="btn btn-info">Certificate</a>
                                            <a href="<?=base_url()?>admin/delete_student/<?=$op->STD_SEQ?>" onclick="return confirm('you want to delete?');" class="btn btn-danger">Delete</a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                            <?php
                        }
                        else if($menu_name == 'Teachers & Staff'){
                            ?>
                            <a style="display: table;margin-bottom: 10px;width: 100px;" href="<?=base_url()?>admin_panel/Masters/teachers/add" class="btn btn-primary">Add</a>
                            <table class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Sr. #</th>
                                    <th>Photograph</th>
                                    <th>Teacher code </th>
                                    <th>Teacher name</th>
                                    <th>Phone no</th>
                                    <th>Class Teacher of</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $iter=1;
                                foreach($all_teachers as $op){
                                    ?>
                                    <tr>
                                        <td><?=$iter++?></td>
                                        <td><img src="<?= base_url('assets/img/employees') . '/' . $op->TCH_PICTURE ?>" style="width:75px" /></td>
                                        <td><?=$op->TCH_CODE?></td>
                                        <td><?= $op->TCH_NAME ?></td>
                                        <td><?= $op->TCH_PHONE ?></td>
                                        <td><?=$op->cto?></td>
                                        <td nowrap>
                                            <a href="<?=base_url()?>admin_panel/Masters/teachers_edit/edit/<?=$op->TCH_SRLNO?>" class="btn btn-warning">Edit</a>
                                            <a href="<?=base_url()?>admin_panel/Masters/teachers/delete/<?=$op->TCH_SRLNO?>" class="btn btn-danger">Delete</a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                            <?php
                        }
                        else if($menu_name == 'Subjects'){
                            ?>
                            <a style="display: table;margin-bottom: 10px;width: 100px;" href="<?=base_url()?>admin_panel/Masters/subjects_edit/add" class="btn btn-primary">Add</a>
                            <table class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Sr. #</th>
                                    <th>Subject code </th>
                                    <th>Subject name</th>
                                    <th>Combination class</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $iter=1;
                                foreach($all_subjects as $op){
                                    ?>
                                    <tr>
                                        <td><?=$iter++?></td>
                                        <td><?=$op->sub_s_name?></td>
                                        <td><?= $op->sub_name ?></td>
                                        <td><?=($op->comb == 0) ? 'No' : 'Yes' ?></td>
                                        <td nowrap>
                                            <a href="<?=base_url()?>admin_panel/Masters/subjects_edit/edit/<?=$op->sub_id?>" class="btn btn-warning">Edit</a>
                                            <!--<a href="" class="btn btn-danger">Delete</a>-->
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                            <?php
                        }
                        else if($menu_name == 'Classes-Sections'){
                            ?>
                            <a style="display: table;margin-bottom: 10px;width: 100px;" href="<?=base_url()?>admin_panel/Masters/cls_sub_setup_edit/add" class="btn btn-primary">Add</a>
                            <table class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Sr. #</th>
                                    <th>Class &amp; Section </th>
                                    <th>Subject</th>
                                    <th>Sorting</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $iter=1;
                                foreach($all_class_sections as $op){
                                    ?>
                                    <tr>
                                        <td><?=$iter++?></td>
                                        <td><?=$op->class_sec?></td>
                                        <td><?= $op->sub_name ?></td>
                                        <td><?= $op->Sorting ?></td>
                                        <td nowrap>
                                            <a href="<?=base_url()?>admin_panel/Masters/cls_sub_setup_edit/edit/<?=$op->CS_SUB_LINK_SEQ?>" class="btn btn-warning">Edit</a>
                                            <!--<a href="" class="btn btn-danger">Delete</a>-->
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                            <?php
                        }
                        else if($menu_name == 'Examinations'){
                            ?>
                            <a style="display: table;margin-bottom: 10px;width: 100px;" href="<?=base_url()?>admin_panel/Masters/exam_master_edit/add" class="btn btn-primary">Add</a>
                            <table class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Sr. #</th>
                                    <th>Exam name </th>
                                    <th>Exam year</th>
                                    <th>Full marks</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $iter=1;
                                foreach($all_exams as $op){
                                    ?>
                                    <tr>
                                        <td><?=$iter++?></td>
                                        <td><?=$op->Exam_Name?></td>
                                        <td><?= $op->Exam_Year ?></td>
                                        <td><?= $op->Full_Marks ?></td>
                                        <td nowrap>
                                            <a href="<?=base_url()?>admin_panel/Masters/exam_master_edit/edit/<?=$op->EXAM_SEQ?>" class="btn btn-warning">Edit</a>
                                            <!--<a href="" class="btn btn-danger">Delete</a>-->
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                            <?php
                        }
                        else if($menu_name == 'Books'){
                            ?>
                            <a style="display: table;margin-bottom: 10px;width: 100px;" href="<?=base_url()?>admin_panel/Masters/books_edit/add" class="btn btn-primary">Add</a>
                            <table class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Sr. #</th>
                                    <th>Accession No.</th>
                                    <th>Subject name </th>
                                    <th>Book year</th>
                                    <th>Author</th>
                                    <th>Total Copies</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $iter=1;
                                foreach($all_books as $op){
                                    ?>
                                    <tr>
                                        <td><?=$iter++?></td>
                                        <td><?= $op->Accession_No ?></td>
                                        <td><?= $op->sub_name ?></td>
                                        <td><?= $op->Book_Name ?></td>
                                        <td><?= $op->Author ?></td>
                                        <td><?= $op->Total_Copies ?></td>
                                        <td nowrap>
                                            <a href="<?=base_url()?>admin_panel/Masters/books_edit/edit/<?=$op->BOOK_SEQ?>" class="btn btn-warning">Edit</a>
                                            <!--<a href="" class="btn btn-danger">Delete</a>-->
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                            <?php
                        }
                        else if($menu_name == 'Grades'){
                            ?>
                            <a style="display: table;margin-bottom: 10px;width: 100px;" href="<?=base_url()?>admin_panel/Masters/grade_setup_edit/add" class="btn btn-primary">Add</a>
                            <table class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Sr. #</th>
                                    <th>Marks From</th>
                                    <th>Marks To</th>
                                    <th>Grade</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $iter=1;
                                foreach($all_grades as $op){
                                    ?>
                                    <tr>
                                        <td><?=$iter++?></td>
                                        <td><?= $op->marks_from ?></td>
                                        <td><?= $op->marks_to ?></td>
                                        <td><?= $op->grade ?></td>
                                        <td nowrap>
                                            <a href="<?=base_url()?>admin_panel/Masters/grade_setup_edit/edit/<?=$op->grd_id?>" class="btn btn-warning">Edit</a>
                                            <!--<a href="" class="btn btn-danger">Delete</a>-->
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                            <?php
                        }
                        else if($menu_name == 'Employees'){
                            ?>
                            <a style="display: table;margin-bottom: 10px;width: 100px;" href="<?=base_url()?>admin_panel/Masters/employees_edit/add" class="btn btn-primary">Add</a>
                            <table class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Sr. #</th>
                                    <th>Picture</th>
                                    <th>Department</th>
                                    <th>Emp. name</th>
                                    <th>Emp. phone</th>
                                    <th>Emp. gender</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $iter=1;
                                foreach($all_employees as $op){
                                    ?>
                                    <tr>
                                        <td><?=$iter++?></td>
                                        <td><img src="<?= base_url('assets/img/employees') . '/' . $op->EMP_PICTURE ?>" style="width:75px" /></td>
                                        <td><?= $op->DEPT_NAME ?></td>
                                        <td><?= $op->EMP_NAME ?></td>
                                        <td><?= $op->EMP_PHONE ?></td>
                                        <td><?= ($op->EMP_SEX == 0) ? 'Male' : 'Female' ?></td>
                                        <td nowrap>
                                            <a href="<?=base_url()?>admin_panel/Masters/employees_edit/edit/<?=$op->EMP_SEQ?>" class="btn btn-warning">Edit</a>
                                            <a href="" class="btn btn-danger">Delete</a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                            <?php
                        }else if($menu_name == 'Signatures'){
                            ?>
                            <a style="display: table;margin-bottom: 10px;width: 100px;" href="<?=base_url()?>admin_panel/Masters/signatures_edit/add" class="btn btn-primary">Add</a>
                            <table class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Sr. #</th>
                                    <th>Signature</th>
                                    <th>Effective From</th>

                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $iter=1;
                                foreach($all_signatures as $op){
                                    ?>
                                    <tr>
                                        <td><?=$iter++?></td>
                                        <td><img src="<?= base_url('assets/img') . '/' . $op->signature ?>" style="width:75px" /></td>
                                        <td><?= $op->effective_from ?></td>

                                        <td nowrap>
                                            <a href="<?=base_url()?>admin_panel/Masters/signatures_edit/edit/<?=$op->id?>" class="btn btn-warning">Edit</a>
                                            <a href="" class="btn btn-danger">Delete</a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                            <?php
                        }
                        else if($menu_name == 'Routine'){
                            ?>
                            <!--<a style="display: table;margin-bottom: 10px;width: 100px;" href="< ?=base_url()?>admin_panel/Students/routine_edit/add" class="btn btn-primary">Add</a>-->
                            <table class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Sr. #</th>
                                    <th>Class name</th>
                                    <th>Section name</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $iter=1;
                                foreach($all_routines as $op){
                                    ?>
                                    <tr>
                                        <td><?=$iter++?></td>
                                        <td><?= $op->Class_Name ?></td>
                                        <td><?= $op->Sec_Name ?></td>
                                        <td nowrap>
                                            <?php
                                            if($op->class_id != ''){
                                                echo '<a href="'.base_url('admin/edit_routine/'.$op->CS_SEQ).'" class="btn btn-primary" role="button"><span class="ui-button-text">&nbsp;Edit</span></a>';
                                            }else{
                                                echo '<a href="'.base_url('admin/add_routine/'.$op->CS_SEQ).'" class="btn btn-info" role="button"><span class="ui-button-text">&nbsp;Add</span></a>';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                            <?php
                        }
                        else if($menu_name == 'Library'){
                            ?>
                            <a style="display: table;margin-bottom: 10px;width: 100px;" href="<?=base_url()?>admin/add_library_tran" class="btn btn-primary">Add</a>
                            <table class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Sr. #</th>
                                    <th>Class-section </th>
                                    <th>Issue date</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $iter=1;
                                foreach($all_library as $op){
                                    ?>
                                    <tr>
                                        <td><?=$iter++?></td>
                                        <td><?= ($op->Class_Name . '-' . $op->Sec_Name) ?></td>
                                        <td><?= $op->date_issue ?></td>
                                        <td nowrap>
                                            <a href="<?=base_url('admin/edit_library_tran/'.$op->lb_hdr_id)?>" class="btn btn-info" role="button"><span class="ui-button-text">&nbsp;Edit</span></a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                            <?php
                        }
                        else if($menu_name == 'Homework'){
                            ?>
                            <a style="display: table;margin-bottom: 10px;width: 100px;" href="<?=base_url()?>admin/homework/add" class="btn btn-primary">Add</a>
                            <table class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                <tr>
                                    <th>Sr. #</th>
                                    <th>Class-section </th>
                                    <th>Release date</th>
                                    <th>Subject Name</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $iter=1;
                                foreach($all_homework as $op){
                                    ?>
                                    <tr>
                                        <td><?=$iter++?></td>
                                        <td><?= ($op->class_sec) ?></td>
                                        <td><?= date('d-m-Y', strtotime($op->release_date)) ?></td>
                                        <td><?= ($op->sub_name) ?></td>
                                        <td nowrap>
                                            <!--admin_panel/Teachers/homework/edit-->
                                            <a href="<?=base_url('admin_panel/Teachers/homework_edit/edit/'.$op->hw_id)?>" class="btn btn-info" role="button"><span class="ui-button-text">&nbsp;Edit</span></a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                            <?php
                        }
                        else if($menu_name == 'PoT & PoW Update'){
                            ?>
                            <form class="cmxform form-horizontal tasi-form" id="form_student_class_update" style="padding:10px;" method="post" action="<?= base_url('admin/update_pot_pow_form'); ?>">
                                    <div class="form-group">
                                        <label for="from_class" class="control-label col-lg-2 text-danger">Class *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" id="from_class" name="from_class" required onchange="getStudentList()" >
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
                            <?php
                        }
                        else if($menu_name == '2nd Language Update'){
                            ?>
                            <form class="cmxform form-horizontal tasi-form" id="form_student_class_update" style="padding:10px;" method="post" action="<?= base_url('admin/update_second_language_form'); ?>">
                                    <div class="form-group">
                                        <label for="from_class" class="control-label col-lg-2 text-danger">Class *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" id="from_class" name="from_class" required onchange="getSecondLangStudentList()" >
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
                            <?php
                        }
                        else if($menu_name == '3rd Language Update'){
                            ?>
                            <form class="cmxform form-horizontal tasi-form" id="form_student_class_update" style="padding:10px;" method="post" action="<?= base_url('admin/update_third_language_form'); ?>">
                                    <div class="form-group">
                                        <label for="from_class" class="control-label col-lg-2 text-danger">Class *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" id="from_class" name="from_class" required onchange="getThirdLangStudentList()" >
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
                            <?php
                        }
                         else if($menu_name == 'Mobile Number Update'){
                            ?>
                            <form class="cmxform form-horizontal tasi-form" id="form_student_class_update" style="padding:10px;" method="post" action="<?= base_url('admin/update_mobileno_form'); ?>">
                                    <div class="form-group">
                                        <label for="to_class" class="control-label col-lg-2 "> Registration No</label>
                                        <div class="col-lg-10 iconic-input">
                                          <input type="text" name="reg_no" id="reg_no" class="form-control" onchange="getMobileNoStudentList()" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="from_class" class="control-label col-lg-2 text-danger">Class *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" id="from_class" name="from_class" required onchange="getMobileNoStudentList()" >
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
                                        <label for="to_class" class="control-label col-lg-2 text-danger"> Student *</label>
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
                            <?php
                        }
                         else if($menu_name == 'Concession Fees Update'){ 
                            ?>
                            <form class="cmxform form-horizontal tasi-form" id="form_student_class_update" style="padding:10px;" method="post" action="<?= base_url('admin/update_concession_fees_form'); ?>"> 
                                    <div class="form-group">
                                        <label for="to_class" class="control-label col-lg-2 "> Registration No</label>
                                        <div class="col-lg-10 iconic-input">
                                          <input type="text" name="reg_no" id="reg_no" class="form-control" onchange="getConcessionFeesStudentList()" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="from_class" class="control-label col-lg-2 text-danger">Class *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" id="from_class" name="from_class" required onchange="getConcessionFeesStudentList()" >
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
                                        <label for="to_class" class="control-label col-lg-2 text-danger"> Student *</label>
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
                            <?php
                        }
                        else if($menu_name == 'House Update'){
                            ?>
                            <form class="cmxform form-horizontal tasi-form" id="form_student_class_update" style="padding:10px;" method="post" action="<?= base_url('admin/update_house_update_form'); ?>">
                                    <div class="form-group">
                                        <label for="from_class" class="control-label col-lg-2 text-danger">Class *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" id="from_class" name="from_class" required onchange="getHouseUpdateStudentList()" >
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
                            <?php
                        }
                        else if($menu_name == 'Aadhar ID Update'){
                            ?>
                            <form class="cmxform form-horizontal tasi-form" id="form_student_class_update" style="padding:10px;" method="post" action="<?= base_url('admin/update_aadharno_form'); ?>">
                                    <div class="form-group">
                                        <label for="to_class" class="control-label col-lg-2 "> Registration No</label>
                                        <div class="col-lg-10 iconic-input">
                                          <input type="text" name="reg_no" id="reg_no" class="form-control" onchange="getAadharNoStudentList()" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="from_class" class="control-label col-lg-2 text-danger">Class *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" id="from_class" name="from_class" required onchange="getAadharNoStudentList()" >
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
                                        <label for="to_class" class="control-label col-lg-2 text-danger"> Student *</label>
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
                            <?php
                        }
                         else if($menu_name == 'Bangla Shiksha ID Update'){
                            ?>
                            <form class="cmxform form-horizontal tasi-form" id="form_student_class_update" style="padding:10px;" method="post" action="<?= base_url('admin/update_shiksha_form'); ?>">
                                    <div class="form-group">
                                        <label for="to_class" class="control-label col-lg-2 "> Registration No</label>
                                        <div class="col-lg-10 iconic-input">
                                          <input type="text" name="reg_no" id="reg_no" class="form-control" onchange="getShikshaNoStudentList()" />  
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="from_class" class="control-label col-lg-2 text-danger">Class *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" id="from_class" name="from_class" required onchange="getShikshaNoStudentList()" >
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
                                        <label for="to_class" class="control-label col-lg-2 text-danger"> Student *</label>
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
                            <?php
                        }
                         else if($menu_name == 'Copy Student'){
                            ?>
                            <form class="cmxform form-horizontal tasi-form" id="form_student_class_update" style="padding:10px;" method="post" action="<?= base_url('admin/update_pot_pow_form'); ?>">
                                     <div class="form-group">
                                        <label for="from_class" class="control-label col-lg-2 text-danger">From Session *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" id="cpy_std_session" name="cpy_std_session" required  >
                                                <option value="">Select Session</option>
                                                <option value="2025">2025</option>
                                                <option value="2024">2024</option>
                                                <option value="2023">2023</option>
                                                <option value="2023">2023</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="from_class" class="control-label col-lg-2 text-danger">From Class *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" id="cpy_std_class" name="cpy_std_class" required onchange="getSessionStudentList()" >
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
                                        <label for="from_class" class="control-label col-lg-2 text-danger">To Session *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" id="cpy_std_to_session" name="cpy_std_to_session" required  >
                                                <option value="">Select Session</option>
                                                <option value="2025">2025</option>
                                                <option value="2024">2024</option>
                                                <option value="2023">2023</option>
                                                <option value="2023">2023</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="from_class" class="control-label col-lg-2 text-danger">From Class *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" id="cpy_std_to_class" name="cpy_std_to_class" required  >
                                                <option value="">Select To Class</option>
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
                                        <label for="to_class" class="control-label col-lg-2 text-danger">Select Student *</label>
                                        <div class="col-lg-10 iconic-input" id="cpy_std_update">
                                          
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
                            <?php
                        }
                        else if($menu_name == 'Staff Leave Record'){ 
                            ?>
                            <form class="cmxform form-horizontal tasi-form" id="form_student_class_update" style="padding:10px;" method="post"> 
                                 
                                    <div class="form-group">
                                        <label for="from_class" class="control-label col-lg-2 text-danger">Staff *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" id="staff" name="staff"   >
                                                <option value="">Select Staff</option>
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
                                                <option value="">Select</option>
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
                                            <button class="btn btn-success" type="button" name="submit" onclick="searchLeave()"
                                                    > Search
                                            </button>
                                            <a href="<?php echo base_url()?>admin/add_staff_leave_record" class="btn btn-primary" type="button" 
                                                    value="class_update"> Add New Leave
                                            </a>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">  
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Sl No</th>
                                                    <th>Staff</th>
                                                    <th>Leave Category</th>
                                                    <th>From Date</th>
                                                    <th>To Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="table_data">
                                               
                                            </tbody>
                                        </table>
                                    </div>
                            </form>
                            <?php
                        }
                          else if($menu_name == 'Add Staff Leave Record'){ 
                            ?>
                            <form class="cmxform form-horizontal tasi-form" id="form_student_class_update" style="padding:10px;" method="post" action="<?= base_url('admin/submit_staff_leave'); ?>"> 
                                 
                                    <div class="form-group">
                                        <label for="from_class" class="control-label col-lg-2 text-danger">Staff *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <select class="form-control round-input select2" id="staff" name="staff" required  >
                                                <option value="">Select Staff</option>
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
                                            <select class="form-control round-input select2" id="leave_category" name="leave_category" required  >
                                                <option value="">Select</option>
                                                <option value="Casual Leave">Casual Leave</option>
                                                <option value="Annual Leave">Annual Leave</option>
                                                <option value="Medical Leave">Medical Leave</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="from_class" class="control-label col-lg-2 text-danger">From date *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <input type="date" name="from_date" class="form-control" required />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="from_class" class="control-label col-lg-2 text-danger">To Date *</label>
                                        <div class="col-lg-10 iconic-input">
                                            <input type="date" name="to_date" class="form-control" required />
                                        </div>
                                    </div>
                                    
                                   

                                   

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            <button class="btn btn-success" type="submit" name="submit"
                                                    value="class_update"> Submit
                                            </button>
                                           
                                        </div>
                                    </div>
                            </form>
                            <?php
                        }
                        ?>
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

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<!--common scripts for all pages-->
<script src="<?=base_url();?>assets/admin_panel/js/bootstrap.min.js"></script>
<script src="<?=base_url();?>assets/admin_panel/js/modernizr.min.js"></script>

<script src="<?=base_url();?>assets/admin_panel/js/scripts.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
<script src="<?=base_url();?>assets/admin_panel/js/toastr-master/toastr.js"></script>
<script>

    $(document).ready(function () {
        $('table').DataTable();
    });
</script>

<?php
//notification
if($this->session->flashdata('msg')) {
    $notification_type = $this->session->flashdata('type') ? $this->session->flashdata('type') : "warning";
    ?>
    <script>
        toastr["<?=$notification_type;?>"]("<?=$this->session->flashdata('msg');?>", "<?=$this->session->flashdata('title');?>", {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "15000",
            "extendedTimeOut": "10000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        })
    </script>
    <?php
}
?>
<script>
    function getStudentList(){
        var class_id = $('#from_class').val();
        $.ajax({
            url: "<?=base_url();?>admin/ajax_get_potpow_students",
            type: "post",
            data: {class_id:class_id},
            dataType:'json',
            success: function(response) {
                $('#std_class_update').html(response.html);
            }
        });
    }
    function getSessionStudentList(){
        var class_id = $('#cpy_std_class').val();
        var session = $('#cpy_std_session').val();
        $.ajax({
            url: "<?=base_url();?>admin/ajax_get_cpysession_students",
            type: "post",
            data: {class_id:class_id,session:session},
            dataType:'json',
            success: function(response) {
                //$('#cpy_std_update').html(response.html);
            }
        });
    }
    function getSecondLangStudentList(){
        var class_id = $('#from_class').val();
        $.ajax({
            url: "<?=base_url();?>admin/ajax_second_language_students",
            type: "post",
            data: {class_id:class_id},
            dataType:'json',
            success: function(response) {
                $('#std_class_update').html(response.html);
            }
        });
    }
    function getHouseUpdateStudentList(){
        var class_id = $('#from_class').val();
        $.ajax({
            url: "<?=base_url();?>admin/ajax_house_update_students",
            type: "post",
            data: {class_id:class_id},
            dataType:'json',
            success: function(response) {
                $('#std_class_update').html(response.html);
            }
        });
    }
    function getThirdLangStudentList(){
        var class_id = $('#from_class').val();
        $.ajax({
            url: "<?=base_url();?>admin/ajax_third_language_students",
            type: "post",
            data: {class_id:class_id},
            dataType:'json',
            success: function(response) {
                $('#std_class_update').html(response.html);
            }
        });
    }
    function getMobileNoStudentList(){
        var class_id = $('#from_class').val();
        var reg_no = $('#reg_no').val();
        $.ajax({
            url: "<?=base_url();?>admin/ajax_mobileno_students",  
            type: "post",
            data: {class_id:class_id,reg_no:reg_no},
            dataType:'json',
            success: function(response) {
                $('#std_class_update').html(response.html);
                 $('#from_class').val(response.class);
            }
        });
    }
    function getAadharNoStudentList(){
        var class_id = $('#from_class').val();
        var reg_no = $('#reg_no').val();
        $.ajax({
            url: "<?=base_url();?>admin/ajax_aadharno_students",
            type: "post",
            data: {class_id:class_id,reg_no:reg_no},
            dataType:'json',
            success: function(response) {
                $('#std_class_update').html(response.html);
                $('#from_class').val(response.class);
            }
        });
    }
    function getShikshaNoStudentList(){
        var class_id = $('#from_class').val();
        var reg_no = $('#reg_no').val();
        $.ajax({
            url: "<?=base_url();?>admin/ajax_shiksha_students",
            type: "post",
            data: {class_id:class_id,reg_no:reg_no},
            dataType:'json',
            success: function(response) {
                $('#std_class_update').html(response.html);
                $('#from_class').val(response.class);
            }
        });
    }
    function getConcessionFeesStudentList(){ 
        var class_id = $('#from_class').val();
        var reg_no = $('#reg_no').val();
        $.ajax({
            url: "<?=base_url();?>admin/ajax_concession_fees_students",  
            type: "post",
            data: {class_id:class_id,reg_no:reg_no},
            dataType:'json',
            success: function(response) {
                $('#std_class_update').html(response.html);
                $('#from_class').val(response.class);
            }
        });
    }
    function searchLeave(){
        var staff = $('#staff').val();
        var leave_category = $('#leave_category').val();
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        $.ajax({
            url: "<?=base_url();?>admin/ajax_search_leave_record",  
            type: "post",
            data: {staff:staff,leave_category:leave_category,from_date:from_date,to_date:to_date},
            dataType:'json',
            success: function(response) {
                $('#table_data').html(response.html);
            }
        });
    }
</script>
</body>
</html>
 
