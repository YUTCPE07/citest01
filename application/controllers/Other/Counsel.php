<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Counsel extends CI_Controller {

	public function index() {
		// $urlSegment = $this->uri->segment(0);
		// echo 'Counsel page';
		$this->load->view('template/header');

		$this->load->view('other/counsel');
		$this->load->view('template/footer');
	}

}