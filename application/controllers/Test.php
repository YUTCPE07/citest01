<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

	public function __construct() {

		parent::__construct();
		$this->load->helper('url');
		$this->load->model('Main_model');
	}

	public function index() {

		$this->load->view('template/header');
		$this->load->view('template/test');
		$this->load->view('template/footer');
	}

	public function isMyUser() {
		$postdata = file_get_contents("php://input");
		$user = json_decode($postdata);
		$data = $this->Main_model->isMyUser($user);
		echo json_encode($data);
	}
}
