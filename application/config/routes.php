<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['save_user'] = 'users/save';
$route['show_login_form'] = 'users/login_form';
$route['login_user'] = 'users/login';
$route['users/logout'] = 'users/logout';
$route['dashboard/(:num)'] = 'dashboard/index';
$route['api/dashboard'] = 'dashboard/postman_dashbaord';


$route['expenses'] = 'ExpenseController/index';
$route['api/expenses'] = 'ExpenseController/expenses_get';     
$route['api/expenses/add'] = 'ExpenseController/add_expense';
$route['api/expenses/(:num)'] = 'ExpenseController/get_expense_by_id/$1';
$route['api/expenses/update/(:num)'] = 'ExpenseController/update_expense/$1';
$route['api/expenses/delete/(:num)'] = 'ExpenseController/delete_expense/$1';
$route['api/notifications'] = 'ExpenseController/get_notifications';
$route['api/notifications/mark_read'] = 'ExpenseController/mark_read';


$route['categories'] = 'CategoryController/index';
$route['api/categories'] = 'CategoryController/get_categories';
$route['api/categories/add'] = 'CategoryController/add_category';
$route['api/categories/update/(:num)'] = 'CategoryController/update_category/$1';
$route['api/categories/delete/(:num)'] = 'CategoryController/delete_category/$1';
$route['api/categories/(:num)'] = 'CategoryController/get_category/$1';


$route['budget'] = 'BudgetController/index';
$route['api/budget'] = 'BudgetController/get_budget';
$route['api/budget/add'] = 'BudgetController/add_budget';
$route['api/budget/update/(:num)'] = 'BudgetController/update_budget/$1';
$route['api/budget/delete/(:num)'] = 'BudgetController/delete_budget/$1';
$route['api/budget/(:num)'] = 'BudgetController/get_budget_by_id/$1';

$route['api/export/csv'] = 'ExpenseController/export_csv';
$route['api/export/pdf'] = 'ExpenseController/export_pdf';

$route['api/import/csv'] = 'ExpenseController/import_csv';



$route['monthly_report'] = 'ReportController/monthly_report';
$route['api/reports/monthly'] = 'ReportController/get_monthly_report';


$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

