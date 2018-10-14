<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

class Product_lookup extends CI_Controller {

	public function __construct(){

	    parent::__construct();

	    // load base_url
	    $this->load->helper('url');

	    // Load Model
	    $this->load->model('Main_model');

	    
  	}


	public function index()
	{	
		$coupon_Id = $this->uri->segment(2);
		$header = array(
			'title' => 'CodeIgniter By YUT', 
			'description' => 'webbord, forum', 
			'author' => 'MI Team', 

		);

		$footer = array(
			'location' => '2215 John Daniel Drive<br>Clark, MO 65243'
		);

		

		$query = $this->Main_model->getLookupCoupon($coupon_Id);

	    // $result = $query->result_array();
		$lookup = array(
			'getIdUrl' => $coupon_Id
		);
		echo count($query);
		// print_r($query);
		// echo $query[0]['coup_CouponID'];
		if ( count($query) == 1 ) {
		    $output['db'] = $query[0];
		} else {
		    $output['db'] = null;
		    redirect('/product');
		}
		// $query->free_result();

		$this->load->view('template/header',$header);
		$this->load->view('template/navbar');
    	$this->load->view('template/login');
		$this->load->view('product/lookup',$output);
		$this->load->view('template/footer',$footer);

		// echo base_url(uri_string());
		
		 // $this->getdata($coupon_Id);
	}

	// Call this method from AngularJS $http request
	public function getdata($coupon_Id){
		// echo "ss" . $coupon_Id;
	    // get data
	    $query = $this->Main_model->getLookupCoupon($coupon_Id);
	    // $result = $query->result_array();
		// echo $query;
	    // echo json_encode($data);
	}

	// public function counttype(){
	// 	$data = $this->Main_model->countColumnType();
	// 	echo json_encode($data);
	// }
	
}
