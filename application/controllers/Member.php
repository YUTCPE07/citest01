<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Member extends CI_Controller {

	public function index()
	{	
		$header = array(
			'title' => 'CodeIgniter By YUT', 
			'description' => 'webbord, forum', 
			'author' => 'MI Team', 

		);

		$index = array(
			'topSite' => 'Hello word'
		);

		$footer = array(
			'location' => '2215 John Daniel Drive<br>Clark, MO 65243'
		);


		$this->load->view('template/header',$header);
		$this->load->view('home/home',$index);
		$this->load->view('template/footer',$footer);
	}

	public function product()
	{
		echo "product";
	}

	public function save()
	{
		echo "save";
	}

	public function update()
	{
		echo "update";
	}


	public function delete()
	{
		echo "delete";
	}
}
