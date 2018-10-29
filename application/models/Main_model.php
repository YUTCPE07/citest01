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
    $sql = 'SELECT coup_CouponID, coup_Name, coup_ImagePath, 
            coup_Image, coup_Price,coup_Description,
            coup_CreatedDate, 
            path_logo, logo_image, category_brand 
            FROM hilight_coupon INNER JOIN mi_brand 
            ON bran_BrandID=brand_Id ORDER BY coup_CreatedDate DESC';
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
    $sql = 'SELECT coup_CouponID, coup_Name, coup_ImagePath, coup_Image, coup_Price,coup_Description,
            coup_CreatedDate,coup_StartDate ,coup_EndDate, coup_StartTime,coup_EndTime,coup_Participation,
            shop_reservation_brief,coup_RepetitionMember,coup_QtyMember,coup_QtyPerMember,coup_Method,
            coup_EndDateUse, coup_MethodUseOther, coup_HowToUse, coup_Condition, coup_Exception, coup_Contact,
            coup_ActivityDuration,coup_Participation,
            
            path_logo, logo_image, category_brand,signature_info,open_brief,shop_howtouse_brief,open_description,
            shop_cancellation_description, shop_q1, shop_a1, shop_q2, shop_a2, shop_q3, shop_a3,
            shop_q4, shop_a4, shop_q5, shop_a5, website, facebook_url, line_id, instragram, tweeter
            FROM hilight_coupon INNER JOIN mi_brand 
            ON bran_BrandID=brand_Id WHERE coup_CouponID ='.$id;
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