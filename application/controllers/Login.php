<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct() {

		parent::__construct();
		$this->load->helper('url');
	}

	public function index() {
		$this->load->view('template/facebook');

		// echo "string";
		//
		//
	}

	// public function getBrandRecommand() {
	// 	$data = $this->Main_model->getBrandRecommand();
	// 	echo json_encode($data);
	// }

}
