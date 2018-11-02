<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Termsofuse extends CI_Controller {

	public function index() {
		$urlSegment = $this->uri->segment(1);
		// echo 'Termsofuse page';

		$this->load->view('template/header');
		$this->load->view('template/navbar');
		$this->load->view('template/login');
		$this->load->view('other/termsofuse');
		$this->load->view('template/footer');
	}

}
