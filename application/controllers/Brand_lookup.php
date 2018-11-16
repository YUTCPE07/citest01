<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

class Brand_lookup extends CI_Controller {

	public function __construct() {

		parent::__construct();

		// load base_url
		$this->load->helper('url');

		// Load Model
		$this->load->model('Main_model');
	}

	public function index() {
		$brand_Id = $this->uri->segment(2);

		$query = $this->Main_model->getLookupBrand($brand_Id);
		if (count($query) == 1) {
			$output['db'] = $query[0];
		} else {
			$output['db'] = null;
			redirect('/brand');
		}

		$this->load->view('template/header');
		$this->load->view('template/navbar');
		$this->load->view('template/login');
		$this->load->view('brand/brand_lookup', $output);
		$this->load->view('template/footer');
		// echo 'Thsi is barnd lookup id' . $brand_Id;
		// print_r($query[0]);
	}

	// public function getdata($brand_Id) {
	// echo "ss" . $coupon_Id;
	// get data
	// $query = $this->Main_model->getLookupBrand($brand_Id);
	// $result = $query->result_array();
	// echo $query;
	// echo json_encode($data);
	// }
}
