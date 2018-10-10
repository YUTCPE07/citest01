<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main_model extends CI_Model {

  function getRecords(){
    // Select user records
    // $this->db->select('*');
    $sql = 'SELECT hilight_coupon.coup_CouponID, hilight_coupon.coup_Name, hilight_coupon.coup_ImagePath, 
            hilight_coupon.coup_Image, hilight_coupon.coup_Price, 
            mi_brand.path_logo, mi_brand.logo_image, mi_brand.category_brand 
            FROM hilight_coupon INNER JOIN mi_brand 
            ON hilight_coupon.coup_CouponID=mi_brand.brand_Id';
    $q = $this->db->query($sql);
    $results = $q->result_array();

    // echo $results;
    return $results;
  }

  function countColumnType(){

  	$sql = 'SELECT type, COUNT(*) AS typeCount FROM hilight_coupon GROUP BY type';
  	$q = $this->db->query($sql);
  	$results = $q->result_array();
   	return $results;
  }

}