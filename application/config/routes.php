<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['api-facebook-login']['POST'] = 'Services/loginfb';

$route['default_controller'] = 'Dashboard';

$route['product'] = 'Product';
$route['product/:num'] = 'Product_lookup';
$route['brand'] = 'Brand';
$route['brand/:num'] = 'Brand_lookup';

// $route['other/[a-z]+'] = 'Other';
$route['aboutus'] = 'Other/Aboutus';
$route['policy'] = 'Other/Policy';
$route['termsofuse'] = 'Other/Termsofuse';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
