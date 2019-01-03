<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Blog extends CI_Controller {

	public function __construct() {

		parent::__construct();

		// load base_url
		$this->load->helper('url');

		// Load Model
		$this->load->model('Main_model');
	}

	public function index() {
		// $data['brands'] = $this->Main_model->getAllDataBrand();
		// echo "<pre>";
		// print_r($data);
		// exit;
		// $data['brands'] = array('Clean House', 'Call Mom', 'Run Errands');
		// echo "Blog Controller CI";
		$this->load->view('template/header');
		// $this->load->view('other/brand_index', $data);
		$this->load->view('blog/blog');
		$this->load->view('template/footer');
	}

	// public function counttype(){
	// 	$data = $this->Main_model->countColumnType();
	// 	echo json_encode($data);
	// }

}
