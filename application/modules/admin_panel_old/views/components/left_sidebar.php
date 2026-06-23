<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 09-07-17
 * Time: xx:xx
 */
?>

<?php
$class_name = $this->router->fetch_class();
$method_name = $this->router->fetch_method();
$user_type = $this->session->usertype;

//fetch user access permission
$this->db->where('user_id', $this->session->user_id);
$rs_user_prm = $this->db->get('user_permissions')->result_array();
$prm_arr = array_column($rs_user_prm, 'permission', 'menu_id');
// echo '<pre>', print_r($prm_arr), '</pre>'; die();
?>

<div class="sidebar-left">
    <!--responsive view logo start-->
    <div class="logo theme-logo-bg visible-xs-* visible-sm-*">
        <a href="<?=base_url();?>" target="_blank">
            <!--            <img src="--><?//=base_url();?><!--assets/admin_panel/img/logo-icon.png" alt="">-->
            <i class="fa fa-home"></i>
            <span class="brand-name"><strong><?=WEBSITE_NAME_SHORT;?></strong></span>
        </a>
    </div>
    <!--responsive view logo end-->

    <div class="sidebar-left-info">
        <!-- visible small devices start-->
        <div class=" search-field">  </div>
        <!-- visible small devices end-->

        <!--sidebar nav start-->
        <ul class="nav nav-pills nav-stacked side-navigation">
            <li><h3 class="navigation-title">Menu</h3></li>
            <li class="<?=(($class_name == 'Dashboard') && ($method_name == 'dashboard')) ? 'active' : ''; ?>">
                <a href="<?=base_url();?>admin/dashboard"><i class="fa fa-tachometer"></i> <span>Dashboard</span></a>
            </li>

            <li class="<?= (($class_name == 'Profile') && ($method_name == 'profile')) ? 'active' : ''; ?>">
                <a href="<?= base_url(); ?>admin/profile"><i class="fa fa-vcard-o"></i> <span>Profile</span></a>
            </li>

            <?php
            # Menus for Student Panel
            if($user_type == 4) {
                ?>
                <!--                <li class="--><?//=(($class_name == 'Student_Single') && ($method_name == 'student_exam')) ? 'active' : ''; ?><!--">-->
                <!--                    <a href="--><?//=base_url();?><!--admin/student_exam"><i class="fa fa-pencil"></i> Examination</a>-->
                <!--                </li>-->

                <li class="<?=(($class_name == 'Student_Single') && ($method_name == 'student_homework')) ? 'active' : ''; ?>">
                    <a href="<?=base_url();?>admin/student_homework"><i class="fa fa-file-word-o"></i> Home Works / Notes</a>
                </li>

                <!--                <li class="--><?//=(($class_name == 'Student_Single') && ($method_name == 'student_library')) ? 'active' : ''; ?><!--">-->
                <!--                    <a href="--><?//=base_url();?><!--admin/student_library"><i class="fa fa-book"></i> Library</a>-->
                <!--                </li>-->

                <li class="<?=(($class_name == 'Student_Single') && ($method_name == 'my_routine')) ? 'active' : ''; ?>">
                    <a target="_blank" href="<?=base_url();?>admin/my_routine"><i class="fa fa-clock-o"></i> <span>Routine</span></a>
                </li>

                <!--                <li class="--><?//=(($class_name == 'Student_Single') && ($method_name == 'my_details')) ? 'active' : ''; ?><!--">-->
                <!--                    <a href="--><?//=base_url();?><!--admin/my_details"><i class="fa fa-vcard"></i> <span>My Details</span></a>-->
                <!--                </li>-->

                <!--                <li class="--><?//=(($class_name == 'Student_Single') && ($method_name == 'parent_details')) ? 'active' : ''; ?><!--">-->
                <!--                    <a href="--><?//=base_url();?><!--admin/parent_details"><i class="fa fa-vcard-o"></i> <span>Parent Details</span></a>-->
                <!--                </li>-->

                <!--                <li class="--><?//=(($class_name == 'Student_Single') && ($method_name == 'my_dues')) ? 'active' : ''; ?><!--">-->
                <!--                    <a target="_blank" href="--><?//=base_url();?><!--admin/my_dues"><i class="fa fa-exclamation-triangle"></i> <span>All Dues</span></a>-->
                <!--                </li>-->

                <!--<li class="< ?=(($class_name == 'Student_Single') && ($method_name == 'my_dues')) ? 'active' : ''; ?>">
                    <a target="_blank" href="< ?=base_url('admin/add_monthly_fees/'.$this->session->tbl_id);?>"><i class="fa fa-money"></i> <span> Monthly Fees</span></a>
                </li>-->

                <li class="menu-list <?=($method_name == 'monthly_pay_hist' || $method_name == 'yearly_pay_hist' || $method_name == 'admission_pay_hist') ? 'active' : ''; ?>"><a href=""><i class="fa fa-money"></i> <span>Payment History</span></a>
                    <ul class="child-list">
                        <li class="<?=(($class_name == 'Student_Single') && ($method_name == 'monthly_pay_hist')) ? 'active' : ''; ?>">
                            <a href="<?=base_url();?>admin/monthly_pay_hist"><i class="fa fa-circle"></i> <span>Monthly Fee</span></a>
                        </li>

                        <li class="<?=(($class_name == 'Student_Single') && ($method_name == 'yearly_pay_hist')) ? 'active' : ''; ?>">
                            <a href="<?=base_url();?>admin/yearly_pay_hist"><i class="fa fa-circle"></i> <span>Yearly Fee</span></a>
                        </li>

                        <!--                        <li class="--><?//=(($class_name == 'Student_Single') && ($method_name == 'admission_pay_hist')) ? 'active' : ''; ?><!--">-->
                        <!--                            <a href="--><?//=base_url();?><!--admin/admission_pay_hist"><i class="fa fa-circle"></i> <span>New Admission Fee</span></a>-->
                        <!--                        </li>-->
                    </ul>
                </li>

                <!--                <li class="--><?//=(($class_name == 'Student_Single') && ($method_name == 'my_progress')) ? 'active' : ''; ?><!--">-->
                <!--                    <a href="--><?//=base_url();?><!--admin/my_progress"><i class="fa fa-file-pdf-o"></i> <span>Academic Progress</span></a>-->
                <!--                </li>-->

                <li class="<?=(($class_name == 'Student_Single') && ($method_name == 'my_progress_report')) ? 'active' : ''; ?>">
                    <a href="<?=base_url();?>admin/my_progress_report" target="_blank"><i class="fa fa-graduation-cap"></i> <span>Progress Report</span></a>
                </li>
                <?php
            }

            # Administrative
            if($user_type == 1) {
                ?>
                <li class="menu-list <?=($class_name == 'Administrations') ? 'active' : ''; ?>"><a href=""><i class="fa fa-street-view"></i> <span>Administrative</span></a>
                    <ul class="child-list">
                        <li class="<?=(($class_name == 'Administrations') && ($method_name == 'create_account')) ? 'active' : ''; ?>">
                            <a href="<?=base_url();?>admin/create_account"><i class="fa fa-circle"></i> Create Account</a>
                        </li>

                        <li class="<?=(($class_name == 'Administrations') && ($method_name == 'manage_users')) ? 'active' : ''; ?>">
                            <a href="<?=base_url();?>admin/manage_users"><i class="fa fa-circle"></i> Manage Users</a>
                        </li>

                        <li class="<?=(($class_name == 'Administrations') && ($method_name == 'student_control')) ? 'active' : ''; ?>">
                            <a href="<?=base_url();?>admin/student_control"><i class="fa fa-circle"></i> Student Control</a>
                        </li>
                    </ul>
                </li>
                <?php
            }

            #-----------------------------------------------------------------------------------------------------------------------

            # Menus for Operator Panel
            if($user_type == 1 or $user_type == 6) {
                # Masters
                if ($user_type == 1 || $prm_arr[22] == 1 || $prm_arr[23] == 1 || $prm_arr[24] == 1 || $prm_arr[25] == 1 || $prm_arr[26] == 1 ||
                    $prm_arr[27] == 1 || $prm_arr[28] == 1 || $prm_arr[29] == 1 || $prm_arr[30] == 1 || $prm_arr[31] == 1 ||
                    $prm_arr[32] == 1 || $prm_arr[33] == 1 || $prm_arr[34] == 1 || $prm_arr[35] == 1) { ?>
                    <li class="menu-list <?= ($class_name == 'Masters') || ($class_name == 'Accounts') ? 'active' : ''; ?>">
                        <a href=""><i class="fa fa-universal-access"></i> <span>Masters</span></a>
                        <ul class="child-list">
                            <?php if ($user_type == 1 || $prm_arr[22] == 1) { ?>
                                <li class="<?= (($class_name == 'Masters') && ($method_name == 'account_group')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/account_group"><i class="fa fa-circle"></i> Account
                                        Group</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[23] == 1) { ?>
                                <li class="<?= (($class_name == 'Masters') && ($method_name == 'account_master')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/account_master"><i class="fa fa-circle"></i>
                                        Account Master</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[25] == 1) { ?>
                                <li class="<?= (($class_name == 'Masters') && ($method_name == 'class_fees')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/class_fees"><i class="fa fa-circle"></i> Fees
                                        Master</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[26] == 1) { ?>
                                <li class="<?= (($class_name == 'Accounts') && ($method_name == 'copy_fees')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/copy_fees"><i class="fa fa-circle"></i> Copy Fees (Class - Sec Wise)</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[24] == 1) { ?>
                                <li class="<?= (($class_name == 'Masters') && ($method_name == 'class_section')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/class_section"><i class="fa fa-circle"></i> Class - Section Master</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[31] == 1) { ?>
                                <li class="<?= (($class_name == 'Masters') && ($method_name == 'subjects')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/subjects"><i class="fa fa-circle"></i> Subjects Details</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[27] == 1) { ?>
                                <li class="<?= (($class_name == 'Masters') && ($method_name == 'cls_sub_setup')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/cls_sub_setup"><i class="fa fa-circle"></i> Class Wise Subject Setup</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[28] == 1) { ?>
                                <li class="<?= (($class_name == 'Masters') && ($method_name == 'student_details')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/student_details"><i class="fa fa-circle"></i>
                                        Student Registration</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[29] == 1) { ?>
                                <li class="<?= (($class_name == 'Masters') && ($method_name == 'concession_fees')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/concession_fees"><i class="fa fa-circle"></i>
                                        Student Concession Setting</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[32] == 1) { ?>
                                <li class="<?= (($class_name == 'Masters') && ($method_name == 'exam_terms')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/exam_terms"><i class="fa fa-circle"></i> Exam Terms</a>
                                </li>
                                <li class="<?= (($class_name == 'Masters') && ($method_name == 'exam_master')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/exam_master"><i class="fa fa-circle"></i> Exam & Test Details</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[34] == 1) { ?>
                                <li class="<?= (($class_name == 'Masters') && ($method_name == 'grade_setup')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/grade_setup"><i class="fa fa-circle"></i> Exam Grade Setup</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[33] == 1) { ?>
                                <li class="<?= (($class_name == 'Masters') && ($method_name == 'books')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/books"><i class="fa fa-circle"></i> Books</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[30] == 1) { ?>
                                <li class="<?= (($class_name == 'Masters') && ($method_name == 'teachers')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/teachers"><i class="fa fa-circle"></i> Teachers & Staff</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[35] == 1) { ?>
                                <li class="<?= (($class_name == 'Masters') && ($method_name == 'employees')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/employees"><i class="fa fa-circle"></i>
                                        Employees</a>
                                </li>
                            <?php } ?>
                            <?php if ($user_type == 1 || $prm_arr[35] == 1) { ?>
                                <li class="<?= (($class_name == 'Masters') && ($method_name == 'signatures')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/signatures"><i class="fa fa-circle"></i>
                                        Signatures</a>
                                </li>
                            <?php } ?>
                            
                             <?php if ($user_type == 1 || $prm_arr[28] == 1) { ?>
                                <li class="<?= (($class_name == 'Masters') && ($method_name == 'pot_pow_update')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/pot_pow_update"><i class="fa fa-circle"></i>
                                       PoT & PoW Update</a>
                                </li>
                            <?php } ?>
                            <?php if ($user_type == 1 || $prm_arr[28] == 1) { ?>
                                <li class="<?= (($class_name == 'Masters') && ($method_name == 'second_language_update')) ? 'active' : ''; ?>">  
                                    <a href="<?= base_url(); ?>admin/second_language_update"><i class="fa fa-circle"></i>
                                       2nd Language Update</a>
                                </li>
                            <?php } ?>
                             <?php if ($user_type == 1 || $prm_arr[28] == 1) { ?>
                                <li class="<?= (($class_name == 'Masters') && ($method_name == 'third_language_update')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/third_language_update"><i class="fa fa-circle"></i>
                                       3rd Language Update</a>
                                </li>
                            <?php } ?>
                            <?php if ($user_type == 1 || $prm_arr[28] == 1) { ?> 
                                <li class="<?= (($class_name == 'Masters') && ($method_name == 'mobile_no_update')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/mobile_no_update"><i class="fa fa-circle"></i>
                                       Mobile No Update</a>
                                </li>
                            <?php } ?>
                            <?php if ($user_type == 1 || $prm_arr[28] == 1) { ?>
                                <li class="<?= (($class_name == 'Masters') && ($method_name == 'house_update')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/house_update"><i class="fa fa-circle"></i>
                                       House Update</a>
                                </li>
                            <?php } ?>
                             <?php if ($user_type == 1 || $prm_arr[28] == 1) { ?> 
                                <li class="<?= (($class_name == 'Masters') && ($method_name == 'aadhar_id_update')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/aadhar_id_update"><i class="fa fa-circle"></i>
                                       Aadhar ID Update</a>
                                </li>
                            <?php } ?>
                             <?php if ($user_type == 1 || $prm_arr[28] == 1) { ?> 
                                <li class="<?= (($class_name == 'Masters') && ($method_name == 'bangla_shiksha_update')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/shiksha_id_update"><i class="fa fa-circle"></i>
                                       Bangla Shiksha ID Update</a>
                                </li>
                            <?php } ?>
                             <?php if ($user_type == 1 || $prm_arr[28] == 1) { ?> 
                                <li class="<?= (($class_name == 'Masters') && ($method_name == 'concession_fees_update')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/concession_fees_update"><i class="fa fa-circle"></i>
                                       Concession Fees Update</a>
                                </li>
                            <?php } ?> 
                             <?php if ($user_type == 1 || $prm_arr[28] == 1) { ?> 
                                <li class="<?= (($class_name == 'Masters') && ($method_name == 'staff_leave_record')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/staff_leave_record"><i class="fa fa-circle"></i>
                                       Staff Leave Record</a> 
                                </li>
                            <?php } ?> 
                            
                        </ul>
                    </li>
                    <?php
                }

                # Accounts
                if ($user_type == 1 || $prm_arr[52] == 1) { ?>
                    <li class="menu-list <?= ($class_name == 'Accounts') ? 'active' : ''; ?>"><a href=""><i
                                    class="fa fa-money"></i> <span>Accounts</span></a>
                        <ul class="child-list">
                            <?php if ($user_type == 1 || $prm_arr[52] == 1) { ?>
                                <li class="<?= (($class_name == 'Accounts') && ($method_name == 'voucher_entry')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/voucher_entry"><i class="fa fa-circle"></i> Voucher
                                        Entry</a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php
                }

                # Students
                if ($user_type == 1 || $prm_arr[36] == 1 || $prm_arr[37] == 1 || $prm_arr[38] == 1 || $prm_arr[39] == 1 || $prm_arr[40] == 1 ||
                    $prm_arr[41] == 1 || $prm_arr[42] == 1 || $prm_arr[43] == 1 || $prm_arr[44] == 1) { ?>
                    <li class="menu-list <?= ($class_name == 'Students') ? 'active' : ''; ?>"><a href=""><i
                                    class="fa fa-graduation-cap"></i> <span>Students (Reg & Others)</span></a>
                        <ul class="child-list">
                            <?php if ($user_type == 1 || $prm_arr[36] == 1) { ?>
                                <li class="<?= (($class_name == 'Students') && ($method_name == 'student_parent_details')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/student_parent_details"><i
                                                class="fa fa-circle"></i> Student Parent Reg</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[37] == 1) { ?>
                                <li class="<?= (($class_name == 'Students') && ($method_name == 'student_auto_roll')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/student_auto_roll"><i class="fa fa-circle"></i>
                                        Auto Allot Student Roll No.</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[38] == 1) { ?>
                                <li class="<?= (($class_name == 'Students') && ($method_name == 'admit_card')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/admit_card"><i class="fa fa-circle"></i> Print
                                        Admit Card</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[39] == 1) { ?>
                                <li class="<?= (($class_name == 'Students') && ($method_name == 'identity_card')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/identity_card"><i class="fa fa-circle"></i> Print
                                        Identity Card</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[40] == 1) { ?>
                                <li class="<?= (($class_name == 'Students') && ($method_name == 'routine')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/routine"><i class="fa fa-circle"></i> Routine</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[41] == 1) { ?>
                                <li class="<?= (($class_name == 'Students') && ($method_name == 'library')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/library"><i class="fa fa-circle"></i> Library</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[42] == 1) { ?>
                                <li class="<?= (($class_name == 'Students') && ($method_name == 'leaving_certificate')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/leaving_certificate"><i class="fa fa-circle"></i>
                                        Leaving certificate</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[43] == 1) { ?>
                                <li class="<?= (($class_name == 'Students') && ($method_name == 'character_certificate')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/character_certificate"><i class="fa fa-circle"></i>
                                        Character Certificate</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[44] == 1) { ?>
                                <li class="<?= (($class_name == 'Students') && ($method_name == 'general_letter')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/general_letter"><i class="fa fa-circle"></i>
                                        General Letter</a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php
                }

                # Teachers
                if ($user_type == 1 || $prm_arr[46] == 1 || $prm_arr[47] == 1 || $prm_arr[48] == 1 || $prm_arr[49] == 1 ||
                    $prm_arr[50] == 1 || $prm_arr[51] == 1) { ?>
                    <li class="menu-list <?= ($class_name == 'Teachers') ? 'active' : ''; ?>"><a href=""><i
                                    class="fa fa-podcast"></i> <span>Teachers</span></a>
                        <ul class="child-list">
                            <?php if ($user_type == 1 || $prm_arr[46] == 1) { ?>
                                <li class="<?= (($class_name == 'Teachers') && ($method_name == 'attendance')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/attendance"><i class="fa fa-circle"></i> Class
                                        Attendance</a>
                                </li>
                            <?php } ?>
                            <?php if ($user_type == 1 || $prm_arr[46] == 1) { ?>
                                <li class="<?= (($class_name == 'Teachers') && ($method_name == 'view_attendance')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/view_attendance"><i class="fa fa-circle"></i> View Class
                                        Attendance</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[47] == 1) { ?>
                                <li class="<?= (($class_name == 'Teachers') && ($method_name == 'student_class_update')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/student_class_update"><i class="fa fa-circle"></i>
                                        Class Promotion</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[48] == 1) { ?>
                                <li class="<?= (($class_name == 'Teachers') && ($method_name == 'marks_entry')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/marks_entry"><i class="fa fa-circle"></i> Marks
                                        Entry</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[49] == 1) { ?>

                                <!-- ##########ST. ANTHONY FORMAT IS DIFFERENT########## -->

                                <!-- <li class="< ?= (($class_name == 'Teachers') && ($method_name == 'progress_report')) ? 'active' : ''; ?>">
                                    <a href="< ?= base_url(); ?>admin/progress_report"><i class="fa fa-circle"></i>
                                        Progress Report</a>
                                </li> -->
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[50] == 1) { ?>
                                <li class="<?= (($class_name == 'Teachers') && ($method_name == 'progress_report_entry')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/progress-report-entry"><i class="fa fa-circle"></i> Progress Report Entry</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[50] == 1) { ?>
                                <li class="<?= (($class_name == 'Teachers') && ($method_name == 'progress_report')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/progress_report"><i class="fa fa-circle"></i> Progress Report</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[51] == 1) { ?>
                                <li class="<?= (($class_name == 'Teachers') && ($method_name == 'homework')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/homework"><i class="fa fa-circle"></i> Home Works &
                                        Notes</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[51] == 1) { ?>
                                <li class="<?= (($class_name == 'Teachers') && ($method_name == 'notices')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/notices"><i class="fa fa-circle"></i>Notice Board</a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php
                }

                # Examinations
                if ($user_type == 1 || $prm_arr[53] == 1 || $prm_arr[54] == 1 || $prm_arr[55] == 1 || $prm_arr[56] == 1 || $prm_arr[57] == 1) { ?>
                    <li class="menu-list <?= ($class_name == 'Exams') ? 'active' : ''; ?>"><a href=""><i
                                    class="fa fa-pencil"></i> <span>Examinations</span></a>
                        <ul class="child-list">
                            <?php if ($user_type == 1 || $prm_arr[53] == 1) { ?>
                                <li class="<?= (($class_name == 'Exams') && ($method_name == 'exam_time_setup')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/exam_time_setup"><i class="fa fa-circle"></i> Exam
                                        Details Setup</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[54] == 1) { ?>
                                <li class="<?= (($class_name == 'Exams') && ($method_name == 'ques_setup')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/ques_setup"><i class="fa fa-circle"></i> Question
                                        Setup</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[55] == 1) { ?>
                                <li class="<?= (($class_name == 'Exams') && ($method_name == 'exam_answers')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/exam_answers"><i class="fa fa-circle"></i> Answers
                                        & Marks</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[56] == 1) { ?>
                                <li class="<?= (($class_name == 'Exams') && ($method_name == 'mcq_ques_setup')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/mcq_ques_setup"><i class="fa fa-circle"></i> MCQ
                                        Question Setup</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[57] == 1) { ?>
                                <li class="<?= (($class_name == 'Exams') && ($method_name == 'mcq_exam_answers')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/mcq_exam_answers"><i class="fa fa-circle"></i> MCQ
                                        Answers & Marks</a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php
                }

                # Fees Collection (REMOVED/HIDDEN)
                if (false && ($user_type == 1 || $prm_arr[58] == 1 || $prm_arr[59] == 1 || $prm_arr[60] == 1)) { ?>
                    <li class="menu-list <?= ($class_name == 'Fees') ? 'active' : ''; ?>"><a href=""><i
                                    class="fa fa-credit-card"></i> <span>Fees Collection</span></a>
                        <ul class="child-list">
                            <?php if (false && ($user_type == 1 || $prm_arr[58] == 1)) { ?>
                                <li class="<?= (($class_name == 'Fees') && ($method_name == 'monthly_fees')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/monthly_fees"><i class="fa fa-circle"></i> Monthly
                                        Fees</a>
                                </li>
                            <?php } ?>

                            <?php if (false && ($user_type == 1 || $prm_arr[59] == 1)) { ?>
                                <li class="<?= (($class_name == 'Fees') && ($method_name == 'yearly_fees')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/yearly_fees"><i class="fa fa-circle"></i> Yearly
                                        Fees</a>
                                </li>
                            <?php } ?>

                            <?php if (false && ($user_type == 1 || $prm_arr[60] == 1)) { ?>
                                <li class="<?= (($class_name == 'Fees') && ($method_name == 'new_admission_fees')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/new_admission_fees"><i class="fa fa-circle"></i>
                                        New Admission Fees</a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php
                }

                # Transactions
                if ($user_type == 1 || $prm_arr[61] == 1 || $prm_arr[62] == 1 || $prm_arr[63] == 1) { ?>
                    <li class="menu-list <?= ($class_name == 'Transactions') ? 'active' : ''; ?>"><a href=""><i
                                    class="fa fa-print"></i> <span>Transactions</span></a>
                        <ul class="child-list">
                            <?php if ($user_type == 1 || $prm_arr[61] == 1 || $prm_arr[62] == 1 || $prm_arr[63] == 1) { ?>
                                <li class="<?= (($class_name == 'Transactions')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/fees_collection"><i class="fa fa-circle"></i> Fees
                                        Collection</a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php
                }

                # Utilities
                if ($user_type == 1 || $prm_arr[64] == 1) { ?>
                    <li class="menu-list <?= ($class_name == 'Utilities') ? 'active' : ''; ?>"><a href=""><i
                                    class="fa fa-wrench"></i> <span>Utilities</span></a>
                        <ul class="child-list">
                            <?php if ($user_type == 1 || $prm_arr[64] == 1) { ?>
                                <li class="<?= (($class_name == 'Utilities') && ($method_name == 'fees_related_transfer')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/fees_related_transfer"><i class="fa fa-circle"></i>
                                        Transfer</a>
                                </li>
                                <li class="<?= (($class_name == 'Utilities') && ($method_name == 'activity_locks')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/activity-locks"><i class="fa fa-circle"></i>
                                        Lock activity</a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php
                }

                # Reports
                if ($user_type == 1 || $prm_arr[6] == 1 || $prm_arr[7] == 1 || $prm_arr[8] == 1 || $prm_arr[9] == 1 || $prm_arr[10] == 1 ||
                    $prm_arr[11] == 1 || $prm_arr[12] == 1 || $prm_arr[13] == 1 || $prm_arr[14] == 1 || $prm_arr[15] == 1 || $prm_arr[16] == 1 ||
                    $prm_arr[17] == 1 || $prm_arr[18] == 1 || $prm_arr[19] == 1 || $prm_arr[20] == 1 || $prm_arr[21] == 1 || $prm_arr[22] == 1) { ?>
                    <li class="menu-list <?= ($class_name == 'Reports') ? 'active' : ''; ?>"><a href=""><i
                                    class="fa fa-file-pdf-o"></i> <span>Reports</span></a>
                        <ul class="child-list">
                            <?php if ($user_type == 1 || $prm_arr[6] == 1 || $prm_arr[7] == 1 || $prm_arr[8] == 1 || $prm_arr[9] == 1 || $prm_arr[10] == 1) { ?>
                                <li class="<?= (($class_name == 'Reports') && ($method_name == 'student_related_report')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/student_related_report"><i
                                                class="fa fa-circle"></i> Student Related Report</a>
                                </li>
                            <?php } ?>
                            <?php if ($user_type == 1 || $prm_arr[6] == 1 || $prm_arr[7] == 1 || $prm_arr[8] == 1 || $prm_arr[9] == 1 || $prm_arr[10] == 1) { ?>
                                <li class="<?= (($class_name == 'Reports') && ($method_name == 'teacher_related_report')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/teacher_related_report"><i
                                                class="fa fa-circle"></i> Teacher Related Report</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[11] == 1) { ?>
                                <li class="<?= (($class_name == 'Reports') && ($method_name == 'all_tran_report')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/all_tran_report"><i class="fa fa-circle"></i> All
                                        Transaction Report</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[12] == 1) { ?>
                                <li class="<?= (($class_name == 'Reports') && ($method_name == 'all_fees_type1_report')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/all_fees_type1_report"><i class="fa fa-circle"></i>
                                        All Fees Report (Date Wise)</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[13] == 1) { ?>
                                <li class="<?= (($class_name == 'Reports') && ($method_name == 'all_fees_type2_report')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/all_fees_type2_report"><i class="fa fa-circle"></i>
                                        All Fees Report (Month Wise)</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[14] == 1) { ?>
                                <li class="<?= (($class_name == 'Reports') && ($method_name == 'single_month_dues_report')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/single_month_dues_report"><i
                                                class="fa fa-circle"></i> Single Month Dues Report</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[15] == 1) { ?>
                                <li class="<?= (($class_name == 'Reports') && ($method_name == 'all_dues_report')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/all_dues_report"><i class="fa fa-circle"></i> Fees
                                        Due Statement BOTH (Date Wise)</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[22] == 1) { ?>
                                <li class="<?= (($class_name == 'Reports') && ($method_name == 'all_dues_report')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/outstanding_total_report"><i class="fa fa-circle"></i> Fees
                                        Outstanding Total Report</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[16] == 1) { ?>
                                <li class="<?= (($class_name == 'Reports') && ($method_name == 'payment_type_report')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/payment_type_report"><i class="fa fa-circle"></i>
                                        Payment Type Report</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[17] == 1) { ?>
                                <li class="<?= (($class_name == 'Reports') && ($method_name == 'library_register')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/library_register"><i class="fa fa-circle"></i>
                                        Library Register</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[18] == 1) { ?>
                                <li class="<?= (($class_name == 'Reports') && ($method_name == 'books_register')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/books_register"><i class="fa fa-circle"></i> Books
                                        Register</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[19] == 1) { ?>
                                <li class="<?= (($class_name == 'Reports') && ($method_name == 'class_routine')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/class_routine"><i class="fa fa-circle"></i> Class
                                        Routine</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[20] == 1) { ?>
                                <li class="<?= (($class_name == 'Reports') && ($method_name == 'teacher_routine')) ? 'active' : ''; ?>">
                                    <a href="<?= base_url(); ?>admin/teacher_routine"><i class="fa fa-circle"></i>
                                        Teacher Routine</a>
                                </li>
                            <?php } ?>

                            <?php if ($user_type == 1 || $prm_arr[21] == 1) { ?>
                                <li class="<?= (($class_name == 'Reports') && ($method_name == 'master_routine')) ? 'active' : ''; ?>">
                                    <a  href="<?= base_url(); ?>admin/master_routine"><i
                                                class="fa fa-circle"></i> Master Routine</a> 
                                </li>
                            <?php } ?>
                             <?php if ($user_type == 1 || $prm_arr[21] == 1) { ?>
                                <li class="<?= (($class_name == 'Reports') && ($method_name == 'staff_leave_report')) ? 'active' : ''; ?>">
                                    <a  href="<?= base_url(); ?>admin/staff_leave_report"><i
                                                class="fa fa-circle"></i> Staff Leave Report</a> 
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php
                }

                # Database Backup
                if ($user_type == 1) { ?>
                    <li class="<?= (($class_name == 'Masters') && ($method_name == 'database_backup')) ? 'active' : ''; ?>">
                        <a href="<?= base_url(); ?>admin/database_backup"><i class="fa fa-database"></i> <span>Database Backup</span></a>
                    </li>
                    <?php
                }
            }

            #-----------------------------------------------------------------------------------------------------------------------

            # Menus for Library Panel
            if($user_type == 1 or $user_type == 5) {
                ?>
                <li style="background-color: gold"><h3 class="navigation-title">Library Menu</h3></li>

                <?php
                # Masters
                if($user_type == 1 || $prm_arr[1] == 1 || $prm_arr[2] == 1) { ?>
                    <li class="menu-list <?=($class_name == 'Masters') ? 'active' : ''; ?>"><a href=""><i class="fa fa-wrench"></i> <span>Masters</span></a>
                        <ul class="child-list">
                            <?php if($user_type == 1 || $prm_arr[1] == 1) { ?>
                                <li class="<?=(($class_name == 'Masters') && ($method_name == 'books')) ? 'active' : ''; ?>">
                                    <a href="<?=base_url();?>library/books"><i class="fa fa-database"></i> <span>Books</span></a>
                                </li>
                            <?php } ?>

                            <?php if($user_type == 1 || $prm_arr[2] == 1) { ?>
                                <li class="<?=(($class_name == 'Masters') && ($method_name == 'fine')) ? 'active' : ''; ?>">
                                    <a href="<?=base_url();?>library/fine"><i class="fa fa-database"></i> <span>Fines</span></a>
                                </li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php }

                # Book Issue
                if($user_type == 1 || $prm_arr[3] == 1) { ?>
                    <li class="<?= (($class_name == 'Utilities') && ($method_name == 'issue_books')) ? 'active' : ''; ?>">
                        <a href="<?= base_url(); ?>library/issue_books"><i class="fa fa-check"></i> <span>Book Issue</span></a>
                    </li>
                <?php }

                # Book Return
                if($user_type == 1 || $prm_arr[4] == 1) { ?>
                    <li class="<?= (($class_name == 'Utilities') && ($method_name == 'return_books')) ? 'active' : ''; ?>">
                        <a href="<?= base_url(); ?>library/return_books"><i class="fa fa-exchange"></i> <span>Book Return</span></a>
                    </li>
                <?php }

                # Reports
                if($user_type == 1 || $prm_arr[5] == 1) { ?>
                    <li class="<?=(($class_name == 'Reports') && ($method_name == 'details_report')) ? 'active' : ''; ?>">
                        <a href="<?=base_url();?>library/details_report"><i class="fa fa-print"></i> <span>Reports</span></a>
                    </li>
                <?php }
            }
            ?>
        </ul>
        <!--sidebar nav end-->

        <!--sidebar widget start-->
        <div class="sidebar-widget">
            <h4>Account Information</h4>
            <ul class="list-group">
                <li>
                    <p>
                        <strong><i class="fa fa-user-circle-o"></i> <span class="username"><?=$this->session->username;?></span></strong>
                        <br/>
                        <strong><i class="fa fa-envelope"></i> <?=$this->session->email;?></strong>
                    </p>
                </li>
                <li>
                    <a href="<?=base_url();?>admin/profile" class="btn btn-info btn-sm addon-btn">Edit Info. <i class="fa fa-vcard pull-left"></i></a>
                </li>
            </ul>
        </div>
        <!--sidebar widget end-->

    </div>
</div>