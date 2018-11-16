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
		$data['brands'] = $this->Main_model->getAllDataBrand();
		// echo "<pre>";
		// print_r($data);
		// exit;
		// $data['brands'] = array('Clean House', 'Call Mom', 'Run Errands');

		$this->load->view('template/header');
		$this->load->view('template/navbar');
		$this->load->view('template/login');
		$this->load->view('brand/brand_index', $data);
		$this->load->view('template/footer');
		echo 'this is brand controller';
	}

	// public function counttype(){
	// 	$data = $this->Main_model->countColumnType();
	// 	echo json_encode($data);
	// }

}
