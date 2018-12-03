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
		$this->load->view('template/header');
		$this->load->view('pay/pay');
		$this->load->view('template/footer');
	}

	// public function getBrandRecommand() {
	// 	$data = $this->Main_model->getBrandRecommand();
	// 	echo json_encode($data);
	// }

}
