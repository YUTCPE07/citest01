<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Echo_db extends CI_Controller {

	public function __construct() {

		parent::__construct();

		// load base_url
		$this->load->helper('url');

		// Load Model
		$this->load->model('Main_model');
	}

	public function index() {

		$data = $this->Main_model->echo_data();
		echo json_encode($data);

	}

}
