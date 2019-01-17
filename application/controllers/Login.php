<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct() {

		parent::__construct();
		$this->load->helper('url');
		$this->load->model('Main_model');
	}

	public function index() {
		// $this->load->view('template/facebook');

		// echo "string";
		//
		//
	}

	public function getUserByFacebookId() {
		$postdata = file_get_contents('php://input');
		// $id = json_decode($postdata);
		$id = $postdata;
		$data = $this->Main_model->getUserByFacebookId($id);
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}

	public function insertUserFormFacebook() {
		date_default_timezone_set("Asia/Bangkok");
		$postdata = file_get_contents('php://input');
		$user = json_decode($postdata);
		$fb_id = $user->{'id'};

		//copy imageFacebook to myServer
		$locationFileTarget = "http://graph.facebook.com/$fb_id/picture?type=large";
		$serverUploadPath = "upload/member_upload/";
		$imageNewName = "member_" . date("Ymd_His") . ".jpg";
		$locationFilePathUpload = $serverUploadPath . $imageNewName;
		if (!copy($locationFileTarget, $locationFilePathUpload)) {
			//failed to copy file
			$isInsertDB = false;
		} else {
			//copy success

			//set formath
			// ["birthday"]=>
			// string(10) "06/15/1994"
			// ["gender"]=>
			// int(1)
			// ["first_name"]=>
			// string(3) "Yut"
			// ["id"]=>
			// string(16) "193303481063317"
			// ["last_name"]=>
			// string(8) "Teerapat"
			// ["name"]=>
			// string(12) "Teerapat Yut"
			// ["email"]=>
			// string(24) "taset@hotmail.com"
			// ["member_image"]=>
			// string(26) "member_203457_132846.jpg"
			// ["date_create"]=>
			// string(16) "2019-01-17 13:28"
			// ["date_update"]=>
			// string(16) "2019-01-17 13:28"
			// ["date_login"]=>
			// string(16) "2019-01-17 13:28"
			// ["platform"]=>
			// string(7) "website"
			$dateTimeNow = date("Y-m-d H:i");
			$user->{'email'} = $user->{'email'} ?? '';
			$user->{'mobile'} = $user->{'mobile'} ?? '';
			$user->{'password'} = '';
			$user->{'gender'} = $user->{'gender'} ?? 0; //check gender null = 0
			($user->{'gender'} == 'male') ? $user->{'gender'} = 1 : $user->{'gender'} = 2;
			$user->{'facebook_id'} = $user->{'id'};
			$user->{'facebook_name'} = $user->{'name'};
			$user->{'firstname'} = $user->{'first_name'};
			$user->{'lastname'} = $user->{'last_name'};
			$user->{'date_birth'} = $user->{'birthday'};
			$user->{'member_image'} = $imageNewName;
			$user->{'date_create'} = $dateTimeNow;
			$user->{'date_update'} = $dateTimeNow;
			$user->{'date_login'} = $dateTimeNow;
			$user->{'platform'} = 'website';
			$isInsertDB = $this->Main_model->insertUserMember($user);
		}
		// echo $user;
		// // $id = $postdata;
		//
		// echo json_encode($data, JSON_NUMERIC_CHECK);
		echo $isInsertDB;
		// echo $fileTarget;
		// echo var_dump($user);
	}

	public function insertUserFormNormal() {
		date_default_timezone_set("Asia/Bangkok");
		$postdata = file_get_contents('php://input');
		$user = json_decode($postdata);
		$imageNewName = "user.png";
		$dateTimeNow = date("Y-m-d H:i");
		$user->{'facebook_id'} = '';
		$user->{'facebook_name'} = '';
		$user->{'password'} = md5($user->{'password'});
		$user->{'date_birth'} = $user->{'birthday'};
		$user->{'member_image'} = $imageNewName;
		$user->{'date_create'} = $dateTimeNow;
		$user->{'date_update'} = $dateTimeNow;
		$user->{'date_login'} = $dateTimeNow;
		$user->{'platform'} = 'website';
		$isInsertDB = $this->Main_model->insertUserMember($user);
		if ($isInsertDB) {
			$newDataUser = new stdClass();
			$newDataUser->username = $user->{'email'};
			$newDataUser->password = $user->{'password'};
			$data = $this->Main_model->isMyUser($newDataUser);
			$data[0]['mesage'] = 'success';
		} else {
			$data = array();
			$data[0]['mesage'] = 'error';
		}
		echo json_encode($data[0]);
		// echo var_dump($data[0]);
	}

	public function isMyUser() {
		$postdata = file_get_contents('php://input');
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
		$postdata = file_get_contents('php://input');
		// $username = json_decode($postdata);
		$username = $postdata; //becaus $postdata is string not obj
		$data = $this->Main_model->isUsernameMyHave($username);
		echo json_encode($data, JSON_NUMERIC_CHECK);
	}

}
