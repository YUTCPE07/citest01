<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

class Shop_lookup extends CI_Controller {

	public function __construct() {

		parent::__construct();
		$this->load->helper('url');
		$this->load->model('Main_model');
	}

	public function index() {
		$coupon_Id = $this->uri->segment(2);
		$query = $this->Main_model->shop_lookup($coupon_Id);
		// echo "<pre>";
		// print_r($query);
		// exit;
		if (count($query) == 1) {
			$output['db'] = $query[0];
		} else {
			$output['db'] = null;
			redirect('/product');
		}
		// $query->free_result();

		$this->load->view('template/header');
		$this->load->view('template/navbar');
		$this->load->view('template/login');
		$this->load->view('product/shop_lookup', $output);
		$this->load->view('template/footer');
	}

}
