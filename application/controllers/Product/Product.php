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
		// $ptype_is_empty = empty($this->input->get('ptype'));
		// if($ptype_is_empty){

		// }

		$this->load->view('template/header');
		$this->load->view('product/index');
		$this->load->view('template/footer');

		// $coupon_Id = $this->uri->segment(2);
		// http://localhost/citest01/product?ptype=5&page=2
		// echo $this->input->get('page') === 1;

		// กลับมาเเก้ต่อ
		// https://stackoverflow.com/questions/3673514/current-uri-segment-in-codeigniter
		//https://stackoverflow.com/questions/9666433/codeigniter-get-value-from-url
		// echo ($this->input->get('page') === '') ? "null" : "Yes";

	}

	// Call this method from AngularJS $http request
	public function getProductsByValue() {
		$postdata = file_get_contents('php://input');
		// $searchStr = json_decode($postdata);
		// $data = $this->Main_model->shop_lookup($p_id);
		// echo json_encode($data);
		$data = $this->Main_model->getProductsByValue($postdata);
		// echo json_encode($data);
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}

	public function getAlldataProduct() {
		// echo '222';
		// get data
		$data = $this->Main_model->getAlldataProduct();
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}

	public function getdata_Catrogy_barnd() {
		$data = $this->Main_model->getdata_Catrogy_barnd();
		echo json_encode($data);
	}

	// public function get_hilight_coupon_trans() {
	// 	$data = $this->Main_model->get_hilight_coupon_trans();
	// 	echo json_encode($data);
	// }

	// public function get_rating() {
	// 	$data = $this->Main_model->get_rating();
	// 	echo json_encode($data);
	// }

	public function counttype() {
		$data = $this->Main_model->countColumnType();
		echo json_encode($data);
	}

}
