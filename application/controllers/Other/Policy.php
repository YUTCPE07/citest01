<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Policy extends CI_Controller {

	public function index() {
		$urlSegment = $this->uri->segment(1);
		// echo 'Policy page';
		$this->load->view('template/header');
		$this->load->view('template/navbar');
		$this->load->view('template/login');
		$this->load->view('other/policy');
		$this->load->view('template/footer');
	}

}
