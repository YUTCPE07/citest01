<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Aboutus extends CI_Controller {

	public function index() {
		$urlSegment = $this->uri->segment(0);
		// echo 'Aboutus page';
		$this->load->view('template/header');

		$this->load->view('other/aboutus');
		$this->load->view('template/footer');
	}

}