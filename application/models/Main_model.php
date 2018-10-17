<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main_model extends CI_Model {

  function getRecordsLimit($limit){
    // Select user records
    // $this->db->select('*');
    $sql = 'SELECT hilight_coupon.coup_CouponID, hilight_coupon.coup_Name, hilight_coupon.coup_ImagePath, 
            hilight_coupon.coup_Image, hilight_coupon.coup_Price,hilight_coupon.coup_Description,
            hilight_coupon.coup_CreatedDate, 
            mi_brand.path_logo, mi_brand.logo_image, mi_brand.category_brand 
            FROM hilight_coupon INNER JOIN mi_brand 
            ON hilight_coupon.coup_CouponID=mi_brand.brand_Id 
            ORDER BY hilight_coupon.coup_CreatedDate DESC LIMIT '.$limit;
    $q = $this->db->query($sql);
    $results = $q->result_array();
    // echo $results;
    // return $results;
    return $results;
  }

   function getBrandRecommand(){
    // Select user records
    // $this->db->select('*');
    $sql = 'SELECT `brand_id`,`name`,`name_en`,`type_brand`,`logo_image`,`path_logo` FROM `mi_brand` ORDER BY `date_update`DESC  LIMIT 9';
    $q = $this->db->query($sql);
    $results = $q->result_array();
    // echo $results;
    // return $results;
    return $results;
  }

  function getRecords(){
    // Select user records
    // $this->db->select('*');
    $sql = 'SELECT hilight_coupon.coup_CouponID, hilight_coupon.coup_Name, hilight_coupon.coup_ImagePath, 
            hilight_coupon.coup_Image, hilight_coupon.coup_Price,hilight_coupon.coup_Description,
            hilight_coupon.coup_CreatedDate, 
            mi_brand.path_logo, mi_brand.logo_image, mi_brand.category_brand 
            FROM hilight_coupon INNER JOIN mi_brand 
            ON hilight_coupon.coup_CouponID=mi_brand.brand_Id ORDER BY hilight_coupon.coup_CreatedDate DESC';
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

  function getLookupCoupon($id){
    // Select user records
    // $this->db->select('*');
    $sql = 'SELECT hilight_coupon.coup_CouponID, hilight_coupon.coup_Name, hilight_coupon.coup_ImagePath, 
            hilight_coupon.coup_Image, hilight_coupon.coup_Price,hilight_coupon.coup_Description,
            hilight_coupon.coup_CreatedDate, 
            mi_brand.path_logo, mi_brand.logo_image, mi_brand.category_brand 
            FROM hilight_coupon INNER JOIN mi_brand 
            ON hilight_coupon.coup_CouponID=mi_brand.brand_Id WHERE hilight_coupon.coup_CouponID ='.$id;
    $q = $this->db->query($sql);
    $results = $q->result_array();

    // echo $results;
    return $results;
  }

  function getLookupBrand($id){
    $sql = 'SELECT  brand_id,path_logo,logo_image,name,company_type,company_name,slogan,category_brand,
              phone,mobile,fax,email,website,facebook_url,line_id,instragram,tweeter,date_create,date_update
            FROM mi_brand WHERE brand_id ='.$id;
    $q = $this->db->query($sql);
    $results = $q->result_array();
    return $results;
  }

}