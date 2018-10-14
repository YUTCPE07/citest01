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
		$header = array(
			'title' => 'CodeIgniter By YUT', 
			'description' => 'webbord, forum', 
			'author' => 'MI Team', 

		);

		$footer = array(
			'location' => '2215 John Daniel Drive<br>Clark, MO 65243'
		);
		$lookup = array(
			'getIdUrl' => $this->uri->segment(2)
		);

		$this->load->view('template/header',$header);
		$this->load->view('template/navbar');
    	$this->load->view('template/login');
		$this->load->view('product/lookup',$lookup);
		$this->load->view('template/footer',$footer);

		// echo base_url(uri_string());
		
		 // $this->counttype();
	}

	// Call this method from AngularJS $http request
	// public function getdata(){
	// 	// echo '222';
	//     // get data
	//     $data = $this->Main_model->getRecords();
	//     echo json_encode($data);
	// }

	// public function counttype(){
	// 	$data = $this->Main_model->countColumnType();
	// 	echo json_encode($data);
	// }
	
}
