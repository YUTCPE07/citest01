<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Pay extends CI_Controller {

	public function __construct() {

		parent::__construct();
		// load base_url
		$this->load->helper('url');
		$this->load->model('Main_model');
	}

	public function index() {
		$userAction = $this->input->get();
		// print_r($product_id);
		// exit;
		$output['userAction'] = $userAction;
		$this->load->view('template/header');
		$this->load->view('pay/pay', $output);
		$this->load->view('template/footer');

	}

	public function postDataUserActionBuy() {

	}
	// public function getBrandRecommand() {
	// 	$data = $this->Main_model->getBrandRecommand();
	// 	echo json_encode($data);
	// }

}
