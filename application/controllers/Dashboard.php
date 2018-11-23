<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public function __construct() {

		parent::__construct();

		// load base_url
		$this->load->helper('url');

		// Load Model
		$this->load->model('Main_model');
	}

	public function index() {

		$header = array(
			'title' => 'CodeIgniter By YUT',
			'description' => 'webbord, forum',
			'author' => 'MI Team',
		);

		$index = array(
			// 'pd_Recommend' => $this->getdataLimit()

		);

		$footer = array(
			'location' => '2215 John Daniel Drive<br>Clark, MO 65243',
		);

		// print_r($this->getdataLimit());
		$this->load->view('template/header', $header);

		$this->load->view('home/home');
		$this->load->view('template/footer', $footer);

	}

	public function get_product_limit() {
		$limit = 9;
		// $data = $this->Main_model->getRecordsLimit($limit);
		$data = $this->Main_model->get_product_limit(9);
		echo json_encode($data);
		// echo ('<pre>');
		// print_r($data);
		// return $data;
	}

	public function getBrandRecommand() {
		$data = $this->Main_model->getBrandRecommand();
		echo json_encode($data);
	}

}
