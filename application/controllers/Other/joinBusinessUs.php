<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class joinBusinessUs extends CI_Controller {

	public function index() {
		// $urlSegment = $this->uri->segment(0);
		// echo 'joinBusinessUs page';
		$this->load->view('template/header');
		$this->load->view('other/joinBusinessUs');
		$this->load->view('template/footer');
	}

}