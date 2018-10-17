<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Brand extends CI_Controller {

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


		// $this->load->view('template/header',$header);
		// $this->load->view('template/navbar');
  //   	$this->load->view('template/login');
		// $this->load->view('product/index');
		// $this->load->view('template/footer',$footer);
		echo 'this is brand controller';
	}

	// public function getdata(){
	//     $data = $this->Main_model->getRecords();
	//     echo json_encode($data);
	// }

	// public function counttype(){
	// 	$data = $this->Main_model->countColumnType();
	// 	echo json_encode($data);
	// }
	
}
