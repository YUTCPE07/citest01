<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

class Membercard_lookup extends CI_Controller {

	public function __construct() {

		parent::__construct();

		// load base_url
		$this->load->helper('url');

		// Load Model
		$this->load->model('Main_model');

	}

	public function index() {
		$coupon_Id = $this->uri->segment(2);
		// echo $coupon_Id;
		// exit;
		$query = $this->Main_model->membercard_lookup($coupon_Id);

		if (count($query) == 1) {
			$output['db'] = $query[0];
		} else {
			$output['db'] = null;
			redirect('/product');
		}
		// echo $query[0]['brand_id'] . 'This is brand_id';
		// exit;
		$output['privileges'] = $this->Main_model->membercard_lookup_privilege($coupon_Id);
		$output['coupon'] = $this->Main_model->membercard_lookup_coupon($coupon_Id);
		$output['coupon_birthday'] = $this->Main_model->membercard_lookup_coupon_birthday($coupon_Id);
		$output['activity'] = $this->Main_model->membercard_lookup_activity($coupon_Id);
		$output['reward'] = $this->Main_model->membercard_lookup_reward($output['db']['brand_id']);

		$this->load->view('template/header');
		$this->load->view('template/navbar');
		$this->load->view('template/login');
		$this->load->view('product/membercard_lookup', $output);
		$this->load->view('template/footer');
	}

}
