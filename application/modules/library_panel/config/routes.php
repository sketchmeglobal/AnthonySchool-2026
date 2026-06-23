<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 09-07-17
 * Time: xx:xx
 */

$route['library_panel'] = 'library_panel/Dashboard/dashboard';
$route['library/dashboard'] = 'library_panel/Dashboard/dashboard';
$route['404'] = 'user_panel/Dashboard/error_404';
$route['js_disabled'] = 'library_panel/Dashboard/js_disabled';

// $route['library/test'] = 'library_panel/Dashboard/data_update';


$route['library/profile'] = 'library_panel/Profile/profile';
$route['library/form_basic_info'] = 'library_panel/Profile/form_basic_info';
$route['library/form_change_pass'] = 'library_panel/Profile/form_change_pass';
$route['library/form_change_email'] = 'library_panel/Profile/form_change_email';
$route['library/change_email/(:any)'] = 'library_panel/Profile/change_email/$1';
$route['library/ajax_username_check'] = 'library_panel/Profile/ajax_username_check';
$route['library/form_change_username'] = 'library_panel/Profile/form_change_username';

$route['library/class_subject'] = 'library_panel/Masters/class_subject';
$route['library/class_subject/add'] = 'library_panel/Masters/class_subject_edit/add';
$route['library_panel/Masters/class_subject_edit'] = 'library_panel/Masters/class_subject';
$route['library_panel/Masters/class_subject_edit/success/(:num)'] = 'library_panel/Masters/class_subject';
$route['library/fine'] = 'library_panel/Masters/fine';

$route['library/books'] = 'library_panel/Masters/books';
$route['library/books/add'] = 'library_panel/Masters/books_edit/add';
$route['library_panel/Masters/books_edit'] = 'library_panel/Masters/books';
$route['library_panel/Masters/books_edit/success/(:num)'] = 'library_panel/Masters/books';


$route['library/issue_books'] = 'library_panel/Utilities/issue_books';
$route['library/issue_books/(:any)'] = 'library_panel/Utilities/issue_books/$1';
$route['library/add_issue_books/(:any)'] = 'library_panel/Utilities/add_issue_books/$1';
$route['library/add_issue_books/(:any)/(:any)'] = 'library_panel/Utilities/add_issue_books/$1';
$route['library/form_add_issue_books'] = 'library_panel/Utilities/form_add_issue_books';
$route['library/add_issue_books'] = 'library_panel/Utilities/add_issue_books';
$route['ajax_book_issue_table_data'] = 'library_panel/Utilities/ajax_book_issue_table_data';
$route['ajax_delete_issued_book'] = 'library_panel/Utilities/ajax_delete_issued_book';


$route['library/form_issue_book_filter'] = 'library_panel/Utilities/form_issue_book_filter';


$route['library/form_book_add_filter'] = 'library_panel/Utilities/form_book_add_filter';
$route['library/add_book_issue_detail'] = 'library_panel/Utilities/add_book_issue_detail';
$route['library/add_book_issue_detail_for_multiple_student_lists'] = 'library_panel/Utilities/add_book_issue_detail_for_multiple_student_lists';


$route['library/return_books'] = 'library_panel/Utilities/return_books';
$route['library/return_books/(:any)'] = 'library_panel/Utilities/return_books/$1';


$route['ajax_book_return_table_data'] = 'library_panel/Utilities/ajax_book_return_table_data';


$route['library/form_return_book_filter'] = 'library_panel/Utilities/form_return_book_filter';


$route['library/form_book_return'] = 'library_panel/Utilities/form_book_return';


$route['library/details_report'] = 'library_panel/Reports/details_report';


$route['library/generate_details_print_format'] = 'library_panel/Reports/generate_details_print_format';

