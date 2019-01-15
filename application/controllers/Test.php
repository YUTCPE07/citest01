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
		$user->password = md5($user->password);
		// echo $user->password;
		$data = $this->Main_model->isMyUser($user);
		$arrobj = $data[0];
		$arrobj['isUser'] = false;
		// echo $arrobj['firstname'];
		if ($data) {
			$arrobj['isUser'] = true;
			// $arrobj['loginBy'] = 'normal';
			// $session_data = array(
			// 	'username' => $user->username,
			// );
			// echo "t";
			//$this->session->set_userdata($arrobj); //set session By ci
			// isset($_SESSION['some_name'])
		}
		echo json_encode($arrobj);

	}

	public function isUsernameMyHave() {
		$postdata = file_get_contents("php://input");
		// $username = json_decode($postdata);
		$username = $postdata; //becaus $postdata is string not obj
		$data = $this->Main_model->isUsernameMyHave($username);
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}

	public function test_md5() {
		//test
		//4c172282115c7410291640853a8fbe62

		$str = "test";
		echo md5($str);
	}

}
