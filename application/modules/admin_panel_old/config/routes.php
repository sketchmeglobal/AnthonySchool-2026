<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 09-07-17
 * Time: xx:xx
 */

$route['admin_panel'] = 'admin_panel/Dashboard/dashboard';
$route['admin/dashboard'] = 'admin_panel/Dashboard/dashboard';
$route['404'] = 'user_panel/Dashboard/error_404';
$route['js_disabled'] = 'admin_panel/Dashboard/js_disabled';

// $route['admin/test'] = 'admin_panel/Dashboard/data_update';a


$route['admin/profile'] = 'admin_panel/Profile/profile';
$route['admin/form_basic_info'] = 'admin_panel/Profile/form_basic_info';
$route['admin/form_change_pass'] = 'admin_panel/Profile/form_change_pass';
$route['admin/form_change_email'] = 'admin_panel/Profile/form_change_email';
$route['admin/change_email/(:any)'] = 'admin_panel/Profile/change_email/$1';
$route['admin/ajax_username_check'] = 'admin_panel/Profile/ajax_username_check';
$route['admin/form_change_username'] = 'admin_panel/Profile/form_change_username';

$route['admin/create_account'] = 'admin_panel/Administrations/create_account';
$route['admin/add_create_account'] = 'admin_panel/Administrations/add_create_account';
$route['admin/add_create_account_operator'] = 'admin_panel/Administrations/add_create_account_operator';
$route['admin/manage_users'] = 'admin_panel/Administrations/manage_users';
$route['admin/set_user_permissions/(:any)'] = 'admin_panel/Administrations/set_user_permissions/$1';
$route['admin/form_set_user_permissions'] = 'admin_panel/Administrations/form_set_user_permissions';
$route['admin/student_control'] = 'admin_panel/Administrations/student_control';
$route['admin/ajax_fetch_students_by_class'] = 'admin_panel/Administrations/ajax_fetch_students_by_class';
$route['admin/form_student_control'] = 'admin_panel/Administrations/form_student_control';
$route['admin/update_fees_month'] = 'admin_panel/Administrations/update_fees_month';
$route['admin/permission_student_for_result'] = 'admin_panel/Administrations/permission_student_for_result';

$route['admin/database_backup'] = 'admin_panel/Masters/database_backup';
$route['admin/account_group'] = 'admin_panel/Masters/account_group';
$route['admin/account_group/add'] = 'admin_panel/Masters/account_group_edit/add';
$route['admin_panel/Masters/account_group_edit'] = 'admin_panel/Masters/account_group';
$route['admin_panel/Masters/account_group_edit/success/(:num)'] = 'admin_panel/Masters/account_group';
$route['admin/account_group_edit/(:num)'] = 'admin_panel/Masters/account_group_edit';

$route['admin/account_master'] = 'admin_panel/Masters/account_master';
$route['admin/account_master/add'] = 'admin_panel/Masters/account_master_edit/add';
$route['admin_panel/Masters/account_master_edit'] = 'admin_panel/Masters/account_master';
$route['admin_panel/Masters/account_master_edit/success/(:num)'] = 'admin_panel/Masters/account_master';

$route['admin/class_section'] = 'admin_panel/Masters/class_section';
$route['admin/class_section/add'] = 'admin_panel/Masters/class_section_edit/add';

$route['admin/edit_class_fess/(:any)'] = 'admin_panel/Masters/edit_class_fess/$1';
$route['admin/class_sec_fees_edit'] = 'admin_panel/Masters/class_sec_fees_edit';

$route['admin/subjects'] = 'admin_panel/Masters/subjects';
$route['admin/subjects_edit/add'] = 'admin_panel/Masters/subjects_edit/add';
$route['admin_panel/Masters/subjects_edit'] = 'admin_panel/Masters/subjects';
$route['admin_panel/Masters/subjects_edit/success/(:num)'] = 'admin_panel/Masters/subjects';

// admin_panel/Masters/subjects/insert_validation

$route['admin/cls_sub_setup'] = 'admin_panel/Masters/cls_sub_setup';
$route['admin/cls_sub_setup/add'] = 'admin_panel/Masters/cls_sub_setup_edit/add';
$route['admin_panel/Masters/cls_sub_setup_edit'] = 'admin_panel/Masters/cls_sub_setup';
$route['admin_panel/Masters/cls_sub_setup_edit/success/(:num)'] = 'admin_panel/Masters/cls_sub_setup';

$route['admin/copy_subjects'] = 'admin_panel/Masters/copy_subjects';
$route['admin/form_copy_subjects'] = 'admin_panel/Masters/form_copy_subjects';

$route['admin/books'] = 'admin_panel/Masters/books';
$route['admin/books/add'] = 'admin_panel/Masters/books_edit/add';
$route['admin_panel/Masters/books_edit'] = 'admin_panel/Masters/books';
$route['admin_panel/Masters/books_edit/success/(:num)'] = 'admin_panel/Masters/books';

$route['admin/exam_master'] = 'admin_panel/Masters/exam_master';
$route['admin/exam_master/add'] = 'admin_panel/Masters/exam_master_edit/add';
$route['admin_panel/Masters/exam_master_edit'] = 'admin_panel/Masters/exam_master';
$route['admin_panel/Masters/exam_master_edit/success/(:num)'] = 'admin_panel/Masters/exam_master';

$route['admin/exam_terms'] = 'admin_panel/Masters/exam_terms';

$route['admin/grade_setup'] = 'admin_panel/Masters/grade_setup';
$route['admin/grade_setup/add'] = 'admin_panel/Masters/grade_setup_edit/add';
$route['admin_panel/Masters/grade_setup_edit'] = 'admin_panel/Masters/grade_setup';
$route['admin_panel/Masters/grade_setup_edit/success/(:num)'] = 'admin_panel/Masters/grade_setup';

$route['admin/teachers'] = 'admin_panel/Masters/teachers';
$route['admin_panel/Masters/teachers/add'] = 'admin_panel/Masters/teachers_edit/add';
$route['admin_panel/Masters/teachers_edit'] = 'admin_panel/Masters/teachers';
$route['admin_panel/Masters/teachers_edit/success/(:num)'] = 'admin_panel/Masters/teachers';
$route['admin_panel/Masters/teachers/delete/(:num)'] = 'admin_panel/Masters/teachers_delete/$1';

$route['admin/employees'] = 'admin_panel/Masters/employees';
$route['admin/employees/add'] = 'admin_panel/Masters/employees_edit/add';
$route['admin_panel/Masters/employees_edit'] = 'admin_panel/Masters/employees';
$route['admin_panel/Masters/employees_edit/success/(:num)'] = 'admin_panel/Masters/employees';

$route['admin/signatures'] = 'admin_panel/Masters/signatures';
$route['admin/signatures/add'] = 'admin_panel/Masters/signatures_edit/add';
$route['admin_panel/Masters/signatures_edit'] = 'admin_panel/Masters/signatures';
$route['admin_panel/Masters/signatures_edit/success/(:num)'] = 'admin_panel/Masters/signatures';

$route['admin/class_fees'] = 'admin_panel/Masters/class_fees';
$route['admin/copy_fees'] = 'admin_panel/Masters/copy_fees';

$route['admin/student_details'] = 'admin_panel/Masters/student_details';
$route['admin/student_details/add'] = 'admin_panel/Masters/student_details_edit/add';
$route['admin_panel/Masters/student_details_edit'] = 'admin_panel/Masters/student_details';
$route['admin_panel/Masters/student_details_edit/success/(:num)'] = 'admin_panel/Masters/student_details';
// $route['admin_panel/Masters/student_details_edit/edit/(:num)'] = 'admin_panel/Masters/student_details_edit'; 

$route['admin/pot_pow_update'] = 'admin_panel/Masters/pot_pow_update';
$route['admin/ajax_get_potpow_students'] = 'admin_panel/Masters/ajax_get_potpow_students';
$route['admin/update_pot_pow_form'] = 'admin_panel/Masters/update_pot_pow_form';

$route['admin/second_language_update'] = 'admin_panel/Masters/second_language_update';
$route['admin/ajax_second_language_students'] = 'admin_panel/Masters/ajax_second_language_students';
$route['admin/update_second_language_form'] = 'admin_panel/Masters/update_second_language_form';

$route['admin/third_language_update'] = 'admin_panel/Masters/third_language_update';
$route['admin/ajax_third_language_students'] = 'admin_panel/Masters/ajax_third_language_students';
$route['admin/update_third_language_form'] = 'admin_panel/Masters/update_third_language_form';

$route['admin/mobile_no_update'] = 'admin_panel/Masters/mobile_no_update'; 
$route['admin/ajax_mobileno_students'] = 'admin_panel/Masters/ajax_mobileno_students';
$route['admin/update_mobileno_form'] = 'admin_panel/Masters/update_mobileno_form';

$route['admin/concession_fees_update'] = 'admin_panel/Masters/concession_fees_update';  
$route['admin/ajax_concession_fees_students'] = 'admin_panel/Masters/ajax_concession_fees_students'; 
$route['admin/update_concession_fees_form'] = 'admin_panel/Masters/update_concession_fees_form';

$route['admin/aadhar_id_update'] = 'admin_panel/Masters/aadhar_id_update'; 
$route['admin/ajax_aadharno_students'] = 'admin_panel/Masters/ajax_aadharno_students';
$route['admin/update_aadharno_form'] = 'admin_panel/Masters/update_aadharno_form';

$route['admin/shiksha_id_update'] = 'admin_panel/Masters/shiksha_id_update';  
$route['admin/ajax_shiksha_students'] = 'admin_panel/Masters/ajax_shiksha_students';
$route['admin/update_shiksha_form'] = 'admin_panel/Masters/update_shiksha_form';

$route['admin/house_update'] = 'admin_panel/Masters/house_update';
$route['admin/ajax_house_update_students'] = 'admin_panel/Masters/ajax_house_update_students';
$route['admin/update_house_update_form'] = 'admin_panel/Masters/update_house_update_form';

$route['admin/staff_leave_record'] = 'admin_panel/Masters/staff_leave_record'; 
$route['admin/add_staff_leave_record'] = 'admin_panel/Masters/add_staff_leave_record'; 
$route['admin/submit_staff_leave'] = 'admin_panel/Masters/submit_staff_leave'; 
$route['admin/ajax_search_leave_record'] = 'admin_panel/Masters/ajax_search_leave_record';
$route['admin/delete_staff_leave_record/(:any)'] = 'admin_panel/Masters/delete_staff_leave_record/$1';   

$route['admin/copy_student'] = 'admin_panel/Masters/copy_student';
$route['admin/ajax_get_cpysession_students'] = 'admin_panel/Masters/ajax_get_cpysession_students';

$route['admin/form_copy_fees'] = 'admin_panel/Masters/form_copy_fees';
$route['admin/concession_fees'] = 'admin_panel/Masters/concession_fees';
$route['admin/add_concession_fees/(:any)/(:any)'] = 'admin_panel/Masters/add_concession_fees/$1/$2';
$route['admin/form_add_concession_fees'] = 'admin_panel/Masters/form_add_concession_fees';
$route['admin/edit_concession_fees/(:any)/(:any)'] = 'admin_panel/Masters/edit_concession_fees/$1/$2';
$route['admin/delete_concession_fees/(:any)'] = 'admin_panel/Masters/delete_concession_fees/$1';
$route['admin/form_edit_concession_fees'] = 'admin_panel/Masters/form_edit_concession_fees';

$route['admin/voucher_entry'] = 'admin_panel/Transactions/voucher_entry';
$route['admin/add_voucher_entry'] = 'admin_panel/Transactions/add_voucher_entry';
$route['admin/form_add_voucher_entry'] = 'admin_panel/Transactions/form_add_voucher_entry';
$route['admin/edit_voucher_entry/(:any)'] = 'admin_panel/Transactions/edit_voucher_entry/$1';
$route['admin/form_edit_voucher_entry'] = 'admin_panel/Transactions/form_edit_voucher_entry';

$route['admin/student_parent_details'] = 'admin_panel/Students/student_parent_details_datatables';
$route['admin/student_parent_details/success/(:num)'] = 'admin_panel/Students/student_parent_details_datatables';

$route['admin/student_parent_details/add'] = 'admin_panel/Students/student_parent_details_edit';
$route['admin/student_parent_details/(:any)/(:num)'] = 'admin_panel/Students/student_parent_details_edit';

$route['admin/student_auto_roll'] = 'admin_panel/Students/student_auto_roll';
$route['admin/form_student_auto_roll'] = 'admin_panel/Students/form_student_auto_roll';
$route['admin/admit_card'] = 'admin_panel/Students/admit_card';
$route['admin/ajax_update_std_admit_card'] = 'admin_panel/Students/ajax_update_std_admit_card';
$route['admin/ajax_update_std_admit_card1'] = 'admin_panel/Students/ajax_update_std_admit_card1';
$route['admin/ajax-std-reg-no-on-admit-card'] = 'admin_panel/Students/ajax_std_reg_no_on_admit_card';
$route['admin/print_admit_card'] = 'admin_panel/Students/print_admit_card';
$route['admin/identity_card'] = 'admin_panel/Students/identity_card';
$route['admin/print_identity_card'] = 'admin_panel/Students/print_identity_card';

$route['admin/routine'] = 'admin_panel/Students/routine';
$route['admin/routine/add'] = 'admin_panel/Students/routine_edit/add';
$route['admin_panel/Students/routine_edit'] = 'admin_panel/Students/routine';
$route['admin_panel/Students/routine_edit/success/(:num)'] = 'admin_panel/Students/routine';

$route['admin/generate_routine'] = 'admin_panel/Students/generate_routine';
$route['admin/add_routine/(:any)'] = 'admin_panel/Students/add_routine/$1';
$route['admin/form_add_routine'] = 'admin_panel/Students/form_add_routine';
$route['admin/edit_routine/(:any)'] = 'admin_panel/Students/edit_routine/$1';
$route['admin/form_edit_routine'] = 'admin_panel/Students/form_edit_routine';
$route['admin/ajax_teacher_availability'] = 'admin_panel/Students/ajax_teacher_availability';

$route['admin/library'] = 'admin_panel/Students/library';
$route['admin/library/add'] = 'admin_panel/Students/library_edit/add';
$route['admin_panel/Students/library_edit'] = 'admin_panel/Students/library';
$route['admin_panel/Students/library_edit/success/(:num)'] = 'admin_panel/Students/library';


$route['admin/add_library_tran'] = 'admin_panel/Students/add_library_tran';
$route['admin/ajax_update_std_table_data'] = 'admin_panel/Students/ajax_update_std_table_data';
$route['admin/form_add_library_tran'] = 'admin_panel/Students/form_add_library_tran';
$route['admin/edit_library_tran/(:any)'] = 'admin_panel/Students/edit_library_tran/$1';
$route['admin/form_edit_library_tran'] = 'admin_panel/Students/form_edit_library_tran';

/*Custom Form For Student*/
$route['admin/add_student'] = 'admin_panel/Students/add_student';

$route['admin/check_reg_no'] = 'admin_panel/Students/check_reg_no';

$route['admin/delete_student/(:num)'] = 'admin_panel/Students/delete_student/$1';


$route['admin/form_add_student'] = 'admin_panel/Students/form_add_student';
$route['admin/insert_data_for_new_users'] = 'admin_panel/Students/insert_data_for_new_users';

$route['admin/edit_student/(:num)'] = 'admin_panel/Students/edit_student/$1';

$route['admin/form_edit_student'] = 'admin_panel/Students/form_edit_student';

$route['admin/attendance'] = 'admin_panel/Teachers/attendance';
$route['admin/view_attendance'] = 'admin_panel/Teachers/view_attendance';
$route['admin/ajax_search_attendance'] = 'admin_panel/Teachers/ajax_search_attendance';
$route['admin/ajax_update_std_attendance'] = 'admin_panel/Teachers/ajax_update_std_attendance';
$route['admin/edit_attendance/(:any)'] = 'admin_panel/Teachers/edit_attendance/$1';
$route['admin/attendance_update'] = 'admin_panel/Teachers/attendance_update'; 
$route['admin/form_attendance'] = 'admin_panel/Teachers/form_attendance';

$route['admin/marks_entry'] = 'admin_panel/Teachers/marks_entry';
$route['admin/add_marks'] = 'admin_panel/Teachers/add_marks';
$route['admin/form_add_marks'] = 'admin_panel/Teachers/form_add_marks';
$route['admin/edit_marks/(:any)'] = 'admin_panel/Teachers/edit_marks/$1';
$route['admin/form_edit_marks'] = 'admin_panel/Teachers/form_edit_marks';
$route['admin/ajax_update_std_marks_table'] = 'admin_panel/Teachers/ajax_update_std_marks_table';
$route['admin/ajax_fetch_std_on_marks_entry'] = 'admin_panel/Teachers/ajax_fetch_std_on_marks_entry';
$route['admin/ajax_mark_type_on_subject'] = 'admin_panel/Teachers/ajax_mark_type_on_subject';

$route['admin/progress-report-entry'] = 'admin_panel/Teachers/progress_report_entry';
$route['admin/fetch_student_on_class_sec'] = 'admin_panel/Teachers/fetch_student_on_class_sec';

$route['admin/progress_report'] = 'admin_panel/Teachers/progress_report';
$route['admin/print_progress_report'] = 'admin_panel/Teachers/print_progress_report';
$route['admin/marksheet'] = 'admin_panel/Teachers/marksheet';
$route['admin/print_marksheet'] = 'admin_panel/Teachers/print_marksheet';

$route['admin/homework'] = 'admin_panel/Teachers/homework';
$route['admin_panel/Teachers/homework_edit'] = 'admin_panel/Teachers/homework';
$route['admin/homework/add'] = 'admin_panel/Teachers/homework_edit/add';
$route['admin/edit_homework/(:num)'] = 'admin_panel/Teachers/homework_edit/edit';
$route['admin_panel/Teachers/homework_edit/success/(:num)'] = 'admin_panel/Teachers/homework';
$route['admin/notices'] = 'admin_panel/Teachers/notices';

$route['admin/student_class_update'] = 'admin_panel/Teachers/student_class_update';

$route['admin/ajax_update_std_class_table'] = 'admin_panel/Teachers/ajax_update_std_class_table';
$route['admin/form_update_student_class'] = 'admin_panel/Teachers/form_update_student_class';


$route['admin/exam_time_setup'] = 'admin_panel/Exams/exam_time_setup';
$route['admin/ques_setup'] = 'admin_panel/Exams/ques_setup';
$route['admin/exam_answers'] = 'admin_panel/Exams/exam_answers';
$route['admin/mcq_ques_setup'] = 'admin_panel/Exams/mcq_ques_setup';
$route['admin/mcq_questions/(:any)'] = 'admin_panel/Exams/mcq_questions/$1';
$route['admin/mcq_exam_answers'] = 'admin_panel/Exams/mcq_exam_answers';


$route['admin/monthly_fees'] = 'admin_panel/Fees/monthly_fees';
$route['admin/monthly_fees/(:any)'] = 'admin_panel/Fees/monthly_fees/$1';
$route['admin/add_monthly_fees/(:any)'] = 'admin_panel/Fees/add_monthly_fees/$1';
$route['admin/form_add_monthly_fees'] = 'admin_panel/Fees/form_add_monthly_fees';
$route['admin/add_monthly_fees'] = 'admin_panel/Fees/add_monthly_fees';

$route['admin/add_yearly_fees'] = 'admin_panel/Fees/add_yearly_fees';

$route['admin/add_new_admission_fees'] = 'admin_panel/Fees/add_new_admission_fees';


$route['admin/monthly_fees_payment_insert'] = 'admin_panel/Fees/monthly_fees_payment_insert';

$route['admin/monthly_fees_payment_complete'] = 'admin_panel/Fees/monthly_fees_payment_complete';
$route['admin/ajax_net_fee'] = 'admin_panel/Fees/ajax_net_fee';
$route['admin/yearly_fees'] = 'admin_panel/Fees/yearly_fees';
$route['admin/yearly_fees/(:any)'] = 'admin_panel/Fees/yearly_fees/$1';
$route['admin/add_yearly_fees/(:any)'] = 'admin_panel/Fees/add_yearly_fees/$1';
$route['admin/form_add_yearly_fees'] = 'admin_panel/Fees/form_add_yearly_fees';
$route['admin/form_add_yearly_fees_due'] = 'admin_panel/Fees/form_add_yearly_fees_due';
$route['admin/form_add_consc_fees'] = 'admin_panel/Fees/form_add_consc_fees';
$route['admin/ajax_net_fee_yearly'] = 'admin_panel/Fees/ajax_net_fee_yearly';
$route['admin/ajax_net_fee_monthly'] = 'admin_panel/Fees/ajax_net_fee_monthly';
$route['admin/ajax_net_fee_adm'] = 'admin_panel/Fees/ajax_net_fee_adm';
$route['admin/new_admission_fees'] = 'admin_panel/Fees/new_admission_fees';
$route['admin/new_admission_fees/(:any)'] = 'admin_panel/Fees/new_admission_fees/$1';
$route['admin/add_new_admission_fees/(:any)'] = 'admin_panel/Fees/add_new_admission_fees/$1';
$route['admin/form_add_new_admission_fees'] = 'admin_panel/Fees/form_add_new_admission_fees';


$route['admin/fees_collection'] = 'admin_panel/Transactions/fees_collection';

$route['admin/monthly_fees_report'] = 'admin_panel/Transactions/monthly_fees_report';
$route['admin/yearly_fees_report'] = 'admin_panel/Transactions/yearly_fees_report';
$route['admin/new_admission_fees_report'] = 'admin_panel/Transactions/new_admission_fees_report';
$route['admin/print_monthly_fess/(:any)'] = 'admin_panel/Transactions/print_monthly_fess/$1';
$route['admin/print_yearly_fess/(:any)'] = 'admin_panel/Transactions/print_yearly_fess/$1';
$route['admin/print_new_admission_fess/(:any)'] = 'admin_panel/Transactions/print_new_admission_fess/$1';

$route['admin/print_fees/(:num)'] = 'admin_panel/Transactions/print_fees/$1';
$route['admin/transaction'] = 'admin_panel/Transactions/transaction';

$route['admin/print_all_students_fee/(:any)'] = 'admin_panel/Transactions/print_all_students_fee/$1';


$route['admin/std_reg_report'] = 'admin_panel/Reports/std_reg_report';
$route['admin/print_std_reg_report'] = 'admin_panel/Reports/print_std_reg_report';
$route['admin/std_consc_report'] = 'admin_panel/Reports/std_consc_report';
$route['admin/ajax_fetch_classes_by_class_type'] = 'admin_panel/Reports/ajax_fetch_classes_by_class_type';

$route['admin/print_std_consc_report'] = 'admin_panel/Reports/print_std_consc_report';
$route['admin/print_std_consc_report_2'] = 'admin_panel/Reports/print_std_consc_report_2';
$route['admin/print_std_consc_report_3'] = 'admin_panel/Reports/print_std_consc_report_3';
$route['admin/print_session_consc_report'] = 'admin_panel/Reports/print_session_consc_report';

$route['admin/all_tran_report'] = 'admin_panel/Reports/all_tran_report';
$route['admin/print_all_tran_report'] = 'admin_panel/Reports/print_all_tran_report';
$route['admin/all_fees_type1_report'] = 'admin_panel/Reports/all_fees_type1_report';
$route['admin/print_all_fees_type1_report'] = 'admin_panel/Reports/print_all_fees_type1_report';
$route['admin/all_fees_type2_report'] = 'admin_panel/Reports/all_fees_type2_report';
$route['admin/print_all_fees_type2_report'] = 'admin_panel/Reports/print_all_fees_type2_report';
$route['admin/std_fees_ledger_report'] = 'admin_panel/Reports/std_fees_ledger_report';
$route['admin/print_std_fees_ledger_report'] = 'admin_panel/Reports/print_std_fees_ledger_report';
$route['admin/single_month_dues_report'] = 'admin_panel/Reports/single_month_dues_report';
$route['admin/print_single_month_dues_report'] = 'admin_panel/Reports/print_single_month_dues_report';
$route['admin/all_dues_report'] = 'admin_panel/Reports/all_dues_report';
$route['admin/print_all_dues_report'] = 'admin_panel/Reports/print_all_dues_report';

$route['admin/outstanding_total_report'] = 'admin_panel/Reports/outstanding_total_report';
$route['admin/print_outstanding_total_report'] = 'admin_panel/Reports/print_outstanding_total_report';

$route['admin/payment_type_report'] = 'admin_panel/Reports/payment_type_report';
$route['admin/print_payment_type_report'] = 'admin_panel/Reports/print_payment_type_report';
$route['admin/library_register'] = 'admin_panel/Reports/library_register';
$route['admin/print_library_register'] = 'admin_panel/Reports/print_library_register';
$route['admin/books_register'] = 'admin_panel/Reports/books_register';


$route['admin/print_books_register'] = 'admin_panel/Reports/print_books_register';
$route['admin/class_routine'] = 'admin_panel/Reports/class_routine';
$route['admin/print_class_routine'] = 'admin_panel/Reports/print_class_routine';
$route['admin/teacher_routine'] = 'admin_panel/Reports/teacher_routine';
$route['admin/master_routine'] = 'admin_panel/Reports/master_routine'; 
$route['admin/print_teacher_routine'] = 'admin_panel/Reports/print_teacher_routine';
$route['admin/print_master_routine'] = 'admin_panel/Reports/print_master_routine';
$route['admin/student_strength'] = 'admin_panel/Reports/student_strength';
$route['admin/print_student_strength_report'] = 'admin_panel/Reports/print_student_strength_report';

$route['admin/student_related_report'] = 'admin_panel/Reports/student_related_report';
$route['admin/teacher_related_report'] = 'admin_panel/Reports/teacher_related_report';
$route['admin/print_teacher_related_report'] = 'admin_panel/Reports/print_teacher_related_report'; 

$route['admin/staff_leave_report'] = 'admin_panel/Reports/staff_leave_report';
$route['admin/print_staff_leave_report'] = 'admin_panel/Reports/print_staff_leave_report'; 

$route['admin/student_list'] = 'admin_panel/Reports/student_list';
$route['admin/student_rank_list'] = 'admin_panel/Reports/student_rank_list';
$route['admin/class_subject_topper'] = 'admin_panel/Reports/class_subject_topper';
$route['admin/due_undertaking_report'] = 'admin_panel/Reports/due_undertaking_report';
$route['admin/print_due_undertaking_report'] = 'admin_panel/Reports/print_due_undertaking_report';
$route['admin/get_studentlist_ajax'] = 'admin_panel/Reports/get_studentlist_ajax';
$route['admin/get_subjectlist_ajax'] = 'admin_panel/Reports/get_subjectlist_ajax';
$route['admin/print_ranklist_report'] = 'admin_panel/Reports/print_ranklist_report';
$route['admin/print_class_subject_topper_report'] = 'admin_panel/Reports/print_class_subject_topper_report';

$route['admin/print_student_list_report'] = 'admin_panel/Reports/print_student_list_report';

$route['admin/print_student_category_list_report'] = 'admin_panel/Reports/print_student_category_list_report';
$route['admin/notice_report'] = 'admin_panel/Reports/notice_report';
$route['admin/notice_report/add'] = 'admin_panel/Reports/add_notice_report';
$route['admin/notice_report/edit/(:num)'] = 'admin_panel/Reports/edit_notice_report/$1';
$route['admin/notice_report/delete/(:num)'] = 'admin_panel/Reports/delete_notice_report/$1'; 
$route['admin/submit_notice_report'] = 'admin_panel/Reports/submit_notice_report';
$route['admin/update_notice_report'] = 'admin_panel/Reports/update_notice_report';
$route['admin/notice_report/print/(:num)'] = 'admin_panel/Reports/print_notice_report/$1'; 

//Utilities
$route['admin/fees_related_transfer'] = 'admin_panel/Utilities/fees_related_transfer';
$route['admin/get_fees_data'] = 'admin_panel/Utilities/get_fees_data';
$route['admin/form_fees_related_transfer'] = 'admin_panel/Utilities/form_fees_related_transfer';
$route['admin/activity-locks'] = 'admin_panel/Utilities/activity_locks';
$route['admin/activity-exception-users/(:num)'] = 'admin_panel/Utilities/activity_exception_users/$1';

//$route['admin/student_exam'] = 'admin_panel/Student_Single/student_exam';
//$route['admin/exam_answer_save'] = 'admin_panel/Student_Single/exam_answer_save';
//$route['admin/mcq_exam_answer_save'] = 'admin_panel/Student_Single/mcq_exam_answer_save';
$route['admin/student_homework'] = 'admin_panel/Student_Single/student_homework';
$route['admin/student_homework_details/(:any)'] = 'admin_panel/Student_Single/student_homework_details/$1';
//$route['admin/student_library'] = 'admin_panel/Student_Single/student_library';
$route['admin/my_routine'] = 'admin_panel/Student_Single/my_routine';
//$route['admin/my_details'] = 'admin_panel/Student_Single/my_details';
//$route['admin/parent_details'] = 'admin_panel/Student_Single/parent_details';
//$route['admin/my_dues'] = 'admin_panel/Student_Single/my_dues';
$route['admin/monthly_pay_hist'] = 'admin_panel/Student_Single/monthly_pay_hist';
$route['admin/yearly_pay_hist'] = 'admin_panel/Student_Single/yearly_pay_hist';
//$route['admin/admission_pay_hist'] = 'admin_panel/Student_Single/admission_pay_hist';
//$route['admin/my_progress'] = 'admin_panel/Student_Single/my_progress';
$route['admin/my_progress_report'] = 'admin_panel/Student_Single/my_progress_report';

// Certificates starts

$route['admin/print_certificate/(:any)'] = 'admin_panel/Students/print_certificate/$1';

$route['admin/leaving_certificate'] = 'admin_panel/Students/leaving_certificate';
$route['admin/add_leaving_certificate'] = 'admin_panel/Students/add_leaving_certificate';
$route['admin/print_leaving_certificate'] = 'admin_panel/Students/print_leaving_certificate';
$route['admin/print_leaving_certificate/(:num)'] = 'admin_panel/Students/print_leaving_certificate/$1';

$route['admin/character_certificate'] = 'admin_panel/Students/character_certificate';
$route['admin/add_character_certificate'] = 'admin_panel/Students/add_character_certificate';
$route['admin/print_character_certificate'] = 'admin_panel/Students/print_character_certificate';
$route['admin/print_character_certificate/(:num)'] = 'admin_panel/Students/print_character_certificate/$1';

$route['admin/general_letter'] = 'admin_panel/Students/general_letter';
$route['admin/add_general_letter'] = 'admin_panel/Students/add_general_letter';
$route['admin/print_general_letter'] = 'admin_panel/Students/print_general_letter';
$route['admin/print_general_letter/(:num)'] = 'admin_panel/Students/print_general_letter/$1';

// Certificates ends