<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Brand extends CI_Controller {

	public function __construct() {

		parent::__construct();

		// load base_url
		$this->load->helper('url');

		// Load Model
		$this->load->model('Main_model');
	}

	public function index() {
		// $data['brands'] = $this->Main_model->getAllDataBrand();

		//// echo "<pre>";
		//// print_r($data);
		//// exit;
		//// $data['brands'] = array('Clean House', 'Call Mom', 'Run Errands');

		$this->load->view('template/header');
		$this->load->view('brand/brand_index');
		// $this->load->view('brand/brand_index', $data);
		$this->load->view('template/footer');
	}

	public function getAllDataBrand() {
		$data = $this->Main_model->getAllDataBrand();
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}

}
