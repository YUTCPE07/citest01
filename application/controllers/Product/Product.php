<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product extends CI_Controller {

	public function __construct() {

		parent::__construct();

		// load base_url
		$this->load->helper('url');

		// Load Model
		$this->load->model('Main_model');
	}

	public function index() {
		// echo "222222222222222";
		$this->load->view('template/header');
		$this->load->view('template/navbar');
		$this->load->view('template/login');
		$this->load->view('product/index');
		$this->load->view('template/footer');

		// $this->counttype();
	}

	// Call this method from AngularJS $http request
	public function getdata() {
		// echo '222';
		// get data
		$data = $this->Main_model->getAlldataProduct();
		echo json_encode($data);
	}

	public function getdata_Catrogy_barnd() {
		$data = $this->Main_model->getCatrogy_barnd();
		echo json_encode($data);
	}

	public function get_hilight_coupon_trans() {
		$data = $this->Main_model->get_hilight_coupon_trans();
		echo json_encode($data);
	}

	public function get_rating() {
		$data = $this->Main_model->get_rating();
		echo json_encode($data);
	}

	public function counttype() {
		$data = $this->Main_model->countColumnType();
		echo json_encode($data);
	}

}
