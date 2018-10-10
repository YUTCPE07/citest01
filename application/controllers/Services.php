<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Services extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Member_model', 'Member'); //load Member_model and cheng name for call
	}

	public function loginfb()
	{
		// echo 'ss';
		$_POST = json_decode(file_get_contents('php://input'),TRUE);
		$resp['status'] = 'error';
		$data = array(
			'id_fb' => $this->input->post('id',TRUE),
			'name_fb' => $this->input->post('name',TRUE),
			'created_at' => date('Y-m-d H:i:s')
		);

		// $hasLogin = $this->Member->login($data);

		// if($hasLogin == true){
		// 	$resp['status'] = 'success';
		// }

		// echo $this->input->post('id',TRUE);
		// exit;
		$isMember = $this->Member->getId($this->input->post('id',TRUE));

		if($isMember == true){
			$resp['stutus'] = 'success';
		}else{
			$hasLogin = $this->Member->login($data);
			if($hasLogin == true){
				$resp['status'] = 'success';
			}
		}
		
		$result = json_encode($resp);
		echo $result;
	}

}
