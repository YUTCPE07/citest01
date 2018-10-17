<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['api-facebook-login']['POST'] = 'Services/loginfb';

$route['default_controller'] = 'Dashboard';

$route['product'] = 'Product';
$route['product/:num'] = 'Product_lookup';
$route['brand'] = 'Brand';
$route['brand/:num'] = 'Brand_lookup';
$route['about'] = 'About';


$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
