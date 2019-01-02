<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Trick extends CI_Controller {

	public function index() {
		// $urlSegment = $this->uri->segment(0);
		// echo 'Trick page';
		$this->load->view('template/header');
		$this->load->view('other/trick');
		$this->load->view('template/footer');
	}

}