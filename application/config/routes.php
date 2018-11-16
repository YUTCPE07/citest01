<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['api-facebook-login']['POST'] = 'Services/loginfb';

$route['default_controller'] = 'Dashboard';

$route['product'] = 'Product/Product';
// $route['product/:num'] = 'Product/Product/Shop_lookup';
$route['membercard/:num'] = 'Product/Membercard_lookup'; /* test : membercard/14*/
$route['shop/:num'] = 'Product/Shop_lookup'; /*test : /shop/158*/
$route['promotion/:num'] = 'Product/Promotion_lookup'; /* test : /promotion/1*/

$route['brand'] = 'Brand';
$route['brand/:num'] = 'Brand_lookup';

$route['db'] = 'Echo_db';

// $route['service_ci/CountNumberSell/:num'] = 'service_ci/CountNumberSell';

// $route['other/[a-z]+'] = 'Other';
$route['aboutus'] = 'Other/Aboutus';
$route['policy'] = 'Other/Policy';
$route['termsofuse'] = 'Other/Termsofuse';

$route['404_override'] = 'Error';
$route['translate_uri_dashes'] = FALSE;
