<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User_store extends CI_Controller {

	public function __construct() {

		parent::__construct();
		$this->load->helper('url');
	}

	public function index() {
		// echo "User_store";
		// exit;
		$this->load->view('template/header');
		$this->load->view('user/store');
		$this->load->view('template/footer');
	}

	// public function getBrandRecommand() {
	// 	$data = $this->Main_model->getBrandRecommand();
	// 	echo json_encode($data);
	// }

}
