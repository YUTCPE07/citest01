<?php

class Member_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function login($data)
	{
		$result = false;
		$hasQuery = $this->db->insert('db_member',$data);
		if($hasQuery){
			$result = true;
		}
		return $result;
	}

	public function getId($id)
	{
		$result = false;
		$this->db->where('id_fb', $id);
		$hasQuery = $this->db->get('db_member');
		if($hasQuery->num_rows() > 0){
			$result = true;
		}
		return $result;
	}
}








