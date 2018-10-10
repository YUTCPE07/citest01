<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class About extends CI_Controller {

	public function index()
	{	


		$header = array(
			'title' => 'CodeIgniter By YUT', 
			'description' => 'webbord, forum', 
			'author' => 'MI Team', 

		);


		$footer = array(
			'location' => '2215 John Daniel Drive<br>Clark, MO 65243'
		);


		$this->load->view('template/header',$header);
		$this->load->view('template/navbar');
    	$this->load->view('template/login');
		$this->load->view('template/about');
		$this->load->view('template/footer',$footer);
	}

	
}
