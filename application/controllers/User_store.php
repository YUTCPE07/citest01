<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User_store extends CI_Controller {

	public function __construct() {

		parent::__construct();
		$this->load->helper('url');
		$this->load->model('Main_model');
	}

	public function index() {
		// echo "User_store";
		// exit;
		$this->load->view('template/header');
		$this->load->view('user/store');
		$this->load->view('template/footer');
	}

	public function getStoreMyRight() {
		$postdata = file_get_contents("php://input");
		$user_id = json_decode($postdata);
		$data = $this->Main_model->getStoreMyRight($user_id);
		echo json_encode($data);
	}

	public function getStoreMyRightHistory() {
		$postdata = file_get_contents("php://input");
		$user_id = json_decode($postdata);
		$data = $this->Main_model->getStoreMyRightHistory($user_id);
		echo json_encode($data);
	}
}
