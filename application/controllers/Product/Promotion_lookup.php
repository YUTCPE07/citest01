<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

class Promotion_lookup extends CI_Controller {

	public function __construct() {

		parent::__construct();

		// load base_url
		$this->load->helper('url');

		// Load Model
		$this->load->model('Main_model');

	}

	public function index() {
		$coupon_Id = $this->uri->segment(2);
		// echo $coupon_Id;
		$query = $this->Main_model->promotion_lookup($coupon_Id);
		// echo '<pre>';
		// print_r($query);
		// echo '</pre>';
		// $output['db'] = $query[0];

		if (count($query) == 1) {
			$output['db'] = $query[0];
		} else {
			echo '<pre>';
			echo $query;
			// $output['db'] = null;
			// redirect('/product');
		}

		$this->load->view('template/header');

		$this->load->view('product/promotion_lookup', $output);
		$this->load->view('template/footer');
	}

}
