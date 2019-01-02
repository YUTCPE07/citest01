<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Address extends CI_Controller {

	public function index() {
		// $urlSegment = $this->uri->segment(0);
		// echo 'Address page';

		// $query = $this->Main_model->getLookupBrand();
		// print_r($query);
		// exit;

		// $output['db'] = $query[0];

		// $this->load->view('other/address', $output);
		$this->load->view('template/header');
		$this->load->view('other/address');
		$this->load->view('template/footer');
	}

}