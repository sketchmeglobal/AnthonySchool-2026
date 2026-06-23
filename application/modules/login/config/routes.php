<?php
/**
 * Coded by: Pran Krishna Das
 * Social: www.fb.com/pran93
 * CI: 3.0.6
 * Date: 09-07-17
 * Time: xx:xx
 */

$route['index'] = 'login/Login/admin_login';
$route['home'] = 'login/Login/admin_login';
$route['admin'] = 'login/Login/admin_login';
$route['login'] = 'login/Login/admin_login';

$route['admin_logout'] = 'login/Login/admin_logout';
$route['logout'] = 'login/Login/admin_logout';

$route['change_password/(:any)'] = 'login/Login/change_password/$1';
$route['update_password'] = 'login/Login/update_password';

$route['library/login'] = 'login/Login/library_login';
$route['library'] = 'login/Login/library_login';

$route['library/logout'] = 'login/Login/library_logout';