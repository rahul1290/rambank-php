<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$route = array();

$route['default_controller'] = 'Auth';
$route['404_override'] = 'Notfound';
$route['translate_uri_dashes'] = FALSE;

$route['images'] = 'Admin_ctrl/app_images';
$route['users'] = 'Admin_ctrl/users';
$route['user-detail/(:any)'] = 'Admin_ctrl/user_detail/$1';
$route['users/logout'] = 'Admin_ctrl/logout';
$route['sheetdata'] = 'Admin_ctrl/getsheet_data';
$route['fetch-sheet-data'] = 'Admin_ctrl/fetchsheet_data';



