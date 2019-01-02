<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class JoinJobUs extends CI_Controller {

	public function index() {
		// $urlSegment = $this->uri->segment(0);
		// echo 'JoinJobUs page';
		$this->load->view('template/header');
		$this->load->view('other/JoinJobUs');
		$this->load->view('template/footer');
	}

}