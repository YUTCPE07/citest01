<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

class Shop_lookup extends CI_Controller {

	public function __construct() {

		parent::__construct();
		$this->load->helper('url');
		$this->load->model('Main_model');
	}

	public function index() {
		$coupon_Id = $this->uri->segment(2);
		$query = $this->Main_model->shop_lookup($coupon_Id);
		$output['db'] = $query[0];
		// echo "<pre>";
		// print_r($query);
		// exit;
		if (count($query) == 1) {
			$output['db'] = $query[0];
		} else {
			echo '<pre>';
			echo $query;
			// $output['db'] = null;
			// redirect('/product');
		}
		// $query->free_result();

		$this->load->view('template/header');

		$this->load->view('product/shop_lookup', $output);
		$this->load->view('template/footer');
	}

	public function shop_lookup() {
		$postdata = file_get_contents("php://input");
		$p_id = json_decode($postdata);
		$data = $this->Main_model->shop_lookup($p_id);
		echo json_encode($data);
	}

	public function getRecommentCouponOther() {
		$postdata = file_get_contents("php://input");
		$b_id = json_decode($postdata);
		$data = $this->Main_model->getRecommentCouponOther($b_id);
		echo json_encode($data);
	}

}
