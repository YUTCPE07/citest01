<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Main_model extends CI_Model {

	function echo_data() {
		$sql = 'SELECT *
				FROM mi_branch';
		$q = $this->db->query($sql);
		$results = $q->result_array();
		$resultsKey = array_keys($results[0]);
		foreach ($resultsKey as $value) {
			echo "mi_branch.$value, <br />";
		}
		// echo '<pre>';
		// print_r($results[0]);
		// echo '</pre>';
		// foreach ($resultsKey as $value) {
		// 	echo 'hilight_coupon.' . $value . ',<br />';
		// }

		exit;
	} /*end function echo_data *watch varible mysql */

	/*user_store_________________________________________________________________*/

	function getStoreMyRight($user_id) {
		$sql = "SELECT
				productAll.date_expire,
				productAll.date_create,
				productAll.count,
				productAll.product_id,
				productAll.product_name,
				productAll.product_image,
				productAll.product_imgPath,
				productAll.brand_id,
			    brandAll.name As brand_name
			from	(

					SELECT mr.date_expire AS date_expire,
							mr.date_create AS date_create,
							COUNT(mr.member_register_id) AS count,
							mc.card_id AS product_id,
							mc.name AS product_name,
							mc.image AS product_image,
							mc.path_image AS product_imgPath,
							mc.brand_id
					FROM mb_member_register AS mr
					LEFT JOIN mi_card AS mc
					ON mr.card_id = mc.card_id
					WHERE mr.flag_del!='T'
					AND mr.member_id=$user_id
					AND (mr.date_expire >= date('Y-m-d H:i:s')
					OR mr.period_type = '4')
					GROUP BY mr.card_id
					UNION
					SELECT
						hb.hcbu_ExpiredDate AS date_expire,
						hb.hcbu_CreatedDate AS date_create,
						COUNT(hb.hcbu_HilightCouponBuyID) AS count,
						hc.coup_CouponID AS product_id,
						hc.coup_Name AS product_name,
						hc.coup_Image AS product_image,
						hc.coup_ImagePath AS product_path,
						hc.bran_BrandID
					FROM hilight_coupon_buy AS hb
					LEFT JOIN hilight_coupon AS hc
					ON hc.coup_CouponID = hb.hico_HilightCouponID
					WHERE hb.hcbu_Deleted!='T'
					AND hb.memb_MemberID=$user_id
					AND hb.hcbu_ExpiredDate >= date('Y-m-d H:i:s')
					AND hb.hcbu_UseStatus='Wait'
					GROUP BY hb.hico_HilightCouponID
			) as productAll
			left join mi_brand as brandAll on brandAll.brand_id = productAll.brand_id
			ORDER BY productAll.date_create DESC";
		$q = $this->db->query($sql);
		$results = $q->result_array();
		return $results;
	}

	function getStoreMyRightHistory($user_id) {
		$sql = "SELECT c.shhi_CreatedDate AS date_use,
					c.hcbu_HilightCouponBuyID AS code_use,
					a.hcbu_CreatedDate AS date_create,
					b.coup_Name AS product_name,
					b.coup_Image AS product_image,
					b.coup_ImagePath AS peoduct_path,
					b.bran_BrandID As brand_id,
					c.shhi_Comment AS review,
					c.shhi_Rating AS rating,
					c.shhi_Image AS review_img,
					c.shhi_ImagePath AS review_path,
					'Shop' AS type,
					d.name AS brand_name
					FROM mb_member
					LEFT JOIN hilight_coupon_buy AS a
					ON a.memb_MemberID = mb_member.member_id
					LEFT JOIN hilight_coupon AS b
					ON a.hico_HilightCouponID = b.coup_CouponID
					LEFT JOIN shop_history AS c
					ON c.hcbu_HilightCouponBuyID = a.hcbu_HilightCouponBuyID
					LEFT JOIN mi_brand AS d
					ON d.brand_id = b.bran_BrandID
					WHERE a.hcbu_Deleted=''
					AND a.hcbu_UseStatus='Use'
					AND a.memb_MemberID=$user_id

					UNION

					SELECT a.mepe_CreatedDate AS date_use,
					a.mepe_MemberPrivlegeID AS code_use,
					e.date_create AS date_create,
					b.priv_Name AS use_name,
					b.priv_Image AS use_image,
					b.priv_ImagePath AS use_path,
					b.bran_BrandID As brand_id,
					a.mepe_Comment AS review,
					a.mepe_Rating AS rating,
					a.mepe_Image AS review_img,
					a.mepe_ImagePath AS review_path,
					'Member Card' AS type,
					d.name AS brand_name
					FROM mb_member
					LEFT JOIN member_privilege_trans AS a
					ON a.memb_MemberID = mb_member.member_id
					LEFT JOIN privilege AS b
					ON b.priv_PrivilegeID = a.priv_PrivilegeID
					LEFT JOIN mi_brand AS d
					ON d.brand_id = b.bran_BrandID
					LEFT JOIN mb_member_register AS e
					ON e.member_register_id = a.mere_MemberRegisterID
					WHERE a.mepe_Deleted=''
					AND a.memb_MemberID=$user_id

					UNION

					SELECT a.meco_CreatedDate AS date_use,
					a.meco_MemberCouponID AS code_use,
					e.date_create AS date_create,
					b.coup_Name AS use_name,
					b.coup_Image AS use_image,
					b.coup_ImagePath AS use_path,
					b.bran_BrandID As brand_id,
					a.meco_Comment AS review,
					a.meco_Rating AS rating,
					a.meco_Image AS review_img,
					a.meco_ImagePath AS review_path,
					'Member Card' AS type,
					d.name AS brand_name
					FROM mb_member
					LEFT JOIN member_coupon_trans AS a
					ON a.memb_MemberID = mb_member.member_id
					LEFT JOIN coupon AS b
					ON b.coup_CouponID = a.coup_CouponID
					LEFT JOIN mi_brand AS d
					ON d.brand_id = b.bran_BrandID
					LEFT JOIN mb_member_register AS e
					ON e.member_register_id = a.mere_MemberRegisterID
					WHERE a.meco_Deleted=''
					AND a.memb_MemberID=$user_id

					UNION

					SELECT a.meac_CreatedDate AS date_use,
					a.meac_MemberActivityID AS code_use,
					e.date_create AS date_create,
					b.acti_Name AS use_name,
					b.acti_Image AS use_image,
					b.acti_ImagePath AS use_path,
					b.bran_BrandID As brand_id,
					a.meac_Comment AS review,
					a.meac_Rating AS rating,
					a.meac_Image AS review_img,
					a.meac_ImagePath AS review_path,
					'Member Card' AS type,
					d.name AS brand_name
					FROM mb_member
					LEFT JOIN member_activity_trans AS a
					ON a.memb_MemberID = mb_member.member_id
					LEFT JOIN activity AS b
					ON b.acti_ActivityID = a.acti_ActivityID
					LEFT JOIN mi_brand AS d
					ON d.brand_id = b.bran_BrandID
					LEFT JOIN mb_member_register AS e
					ON e.member_register_id = a.mere_MemberRegisterID
					WHERE a.meac_Deleted=''
					AND a.memb_MemberID=$user_id

					ORDER BY date_use DESC";
		$q = $this->db->query($sql);
		$results = $q->result_array();
		return $results;
	}

	function getStoreMyRightExp($user_id) {
		// '2018-11-29 11:44:46'
		date_default_timezone_set("Asia/Magadan");
		$dateTimeNow = date("Y-m-d h:i:s");
		$sql = "SELECT
				 productAll.date_expire AS date_expire,
				 productAll.date_create AS date_create,
				 productAll.bran_BrandID AS brand_id,
				 productAll.card_id AS product_id,
				 productAll.name AS product_name,
				 productAll.image AS product_image,
				 productAll.path_image AS product_path,
				 brandAll.name AS brand_name
				 from (

				 SELECT mr.date_expire,
				 mr.date_create ,
				 mr.bran_BrandID,
				 mc.card_id ,
				 mc.name,
				 mc.image,
				 mc.path_image


				 FROM mb_member_register AS mr
				 LEFT JOIN mi_card AS mc
				 ON mr.card_id = mc.card_id
				 WHERE mr.flag_del='T'
				 AND mr.member_id={$user_id}
				 AND (mr.date_expire < '{$dateTimeNow}'
				 AND mr.period_type != '4')


				UNION

				SELECT hb.hcbu_ExpiredDate AS date_expire,
				 hb.hcbu_CreatedDate AS date_create,
				 hb.bran_BrandID AS brand_id,
				 hc.coup_CouponID AS id,
				 hc.coup_Name AS use_name,
				 hc.coup_Image AS use_image,
				 hc.coup_ImagePath AS use_path

				 FROM hilight_coupon_buy AS hb
				 LEFT JOIN hilight_coupon AS hc
				 ON hc.coup_CouponID = hb.hico_HilightCouponID
				 WHERE hb.hcbu_Deleted='T'
				 AND hb.memb_MemberID = {$user_id}
				 AND hb.hcbu_ExpiredDate < '{$dateTimeNow}'
				 AND hb.hcbu_UseStatus='Expire'

				 ) as productAll

				 left join mi_brand as brandAll
				 on brandAll.brand_id = productAll.bran_BrandID

				 ORDER BY date_create DESC";
		// echo $sql;
		// exit;
		$q = $this->db->query($sql);
		$results = $q->result_array();
		return $results;
	}

	// ____________________________________________________________________________

	function getRecordsLimit($limit) {
		// Select user records
		// $this->db->select('*');
		$sql = 'SELECT * FROM (
					SELECT 	hilight_coupon.coup_CouponID,
							hilight_coupon.coup_Name,
							hilight_coupon.coup_ImagePath,
							hilight_coupon.coup_Image,
							hilight_coupon.coup_Price,
							hilight_coupon.coup_Description,
							hilight_coupon.coup_UpdatedDate,
							hilight_coupon.coup_Cost,
							hilight_coupon.coup_Type,
							mi_brand.path_logo,
							mi_brand.logo_image,
							mi_brand.category_brand,
							mi_brand.flag_status,
							mi_brand.flag_del,
							mi_brand.flag_hidden
					FROM hilight_coupon LEFT JOIN mi_brand ON hilight_coupon.bran_BrandID=mi_brand.brand_Id
					UNION ALL
					SELECT 	mi_card.card_id,
							mi_card.name,
							mi_card.path_image,
							mi_card.image,
							mi_card.member_price,
							mi_card.description,
							mi_card.greeting_updateddate,
							mi_card.original_fee,
							"Member" AS coup_Type,
							mi_brand.path_logo,
							mi_brand.logo_image,
							mi_brand.category_brand,
							mi_brand.flag_status,
							mi_brand.flag_del,
							mi_brand.flag_hidden
					FROM mi_card LEFT JOIN mi_brand ON mi_card.brand_id=mi_brand.brand_Id
				) AS U
				WHERE 	U.flag_status = 1 AND
						U.flag_del = 0 AND
						U.flag_hidden = "No"
				ORDER BY coup_UpdatedDate DESC LIMIT ' . $limit;
		$q = $this->db->query($sql);
		$results = $q->result_array();
		// echo $results;
		// return $results;
		return $results;
	} /*dashboard controller*/

	function getAllDataBrand() {
		$sql = "SELECT
				    b.brand_id, b.name, b.path_logo, b.logo_image, b.date_update
				FROM
				    mi_brand b,
				    mi_branch bch
				WHERE
				    b.flag_status = 1
					AND b.flag_del = 0
					AND b.flag_hidden = 'No'
				    AND b.brand_id = bch.brand_id
					AND bch.default_status = 1
				ORDER BY b.date_update DESC";
		$q = $this->db->query($sql);
		$results = $q->result_array();
		return $results;
	} /*dashboard controller*/

	function getLookupBrand($brand_id) {
		// echo $brand_id;
		// exit;
		// test brand_id 16,18,27,28,30,39
		$sql = "SELECT
					mi_brand.brand_id,
				    mi_brand.name,
				    mi_brand.name_en,
				    mi_brand.company_type,
				    mi_brand.company_name,
				    mi_brand.slogan,
				    mi_brand.category_brand,
				    mi_brand.type_brand,
				    mi_brand.phone,
				    mi_brand.mobile,
				    mi_brand.fax,
				    mi_brand.email,
				    mi_brand.website,
				    mi_brand.facebook_url,
				    mi_brand.line_type,
				    mi_brand.line_id,
				    mi_brand.instragram,
				    mi_brand.tweeter,
				    mi_brand.logo_image,
				    mi_brand.cover,
				    mi_brand.path_logo,
				    mi_brand.path_cover,
				    mi_brand.signature_info,
				    mi_brand.date_create,
				    mi_brand.date_update,
				    mi_branch.name As mi_branch_name,
				    mi_branch.map_latitude,
				    mi_branch.map_longitude,
				    mi_branch.address_no,
				    mi_branch.moo,
				    mi_branch.junction,
				    mi_branch.soi,
				    mi_branch.road,
				    mi_branch.sub_district,
				    mi_branch.district,
				    mi_branch.sub_district_id,
				    mi_branch.district_id,
				    mi_branch.province_id,
				    mi_branch.region_id,
				    mi_branch.country_id,
				    mi_branch.postcode
				FROM
				    mi_brand ,
				    mi_branch
				WHERE
					mi_brand.flag_status = 1
					AND mi_brand.flag_del = 0
					AND mi_brand.flag_hidden = 'No'
					AND mi_brand.brand_id = $brand_id
				    AND mi_brand.brand_id = mi_branch.brand_id
					AND mi_branch.default_status = 1";
		$q = $this->db->query($sql);
		$results = $q->result_array();
		return $results;

	}

	function getBrandRecommand() {
		// Select user records
		// $this->db->select('*');
		$sql = 'SELECT `brand_id`,`name`,`name_en`,`type_brand`,`logo_image`,`path_logo` FROM `mi_brand` ORDER BY `date_update`DESC  LIMIT 9';
		$q = $this->db->query($sql);
		$results = $q->result_array();
		// echo $results;
		// return $results;
		return $results;
	}

	function getProductsByValue($searchStr) {
		/*test id 36*/
		$sql = "SELECT
				    i.coup_CouponID,
				    i.bran_BrandID,
				    i.brand_name,
				    i.coup_Name,
				    i.coup_ImagePath,
				    i.coup_Image,
				    i.coup_Price,
				    i.coup_Description,
				    i.coup_UpdatedDate,
				    i.coup_Cost,
				    i.coup_Type,
				    i.coup_numUse,
				    i.path_logo,
				    i.logo_image,
				    i.category_brand,
				    mi_category_brand.name AS category_brand_name
				FROM
				    (SELECT
				        z.coup_CouponID,
				            z.bran_BrandID,
				            z.coup_Name,
				            z.coup_ImagePath,
				            z.coup_Image,
				            z.coup_Price,
				            z.coup_Description,
				            z.coup_UpdatedDate,
				            z.coup_Cost,
				            z.coup_Type,
				            IFNULL(z.coup_numUse, 0) AS coup_numUse,
				            mi_brand.name AS brand_name,
				            mi_brand.path_logo,
				            mi_brand.logo_image,
				            mi_brand.category_brand
					    FROM
					        (SELECT
						        hilight_coupon.coup_CouponID,
						            hilight_coupon.bran_BrandID,
						            hilight_coupon.coup_Name,
						            hilight_coupon.coup_ImagePath,
						            hilight_coupon.coup_Image,
						            hilight_coupon.coup_Price,
						            hilight_coupon.coup_Description,
						            hilight_coupon.coup_UpdatedDate,
						            hilight_coupon.coup_Cost,
						            hilight_coupon.coup_Type,
						            x.coup_numUse
							    FROM
							        hilight_coupon
						    	LEFT JOIN (SELECT
										        hilight_coupon_trans.coup_CouponID AS coup_id,
										            COUNT(*) AS coup_numUse
											    FROM
											        hilight_coupon_trans
											    WHERE
											        hilight_coupon_trans.hico_Deleted != 'T'
									    		GROUP BY hilight_coupon_trans.coup_CouponID) AS x ON hilight_coupon.coup_CouponID = x.coup_id UNION ALL SELECT
											        mi_card.card_id,
											            mi_card.brand_id,
											            mi_card.name,
											            mi_card.path_image,
											            mi_card.image,
											            mi_card.member_price,
											            mi_card.description,
											            mi_card.date_update,
											            mi_card.original_fee,
											            'Member' AS coup_Type,
											            y.coup_numUse
											    FROM
								        mi_card
								    LEFT JOIN (SELECT
								        hilight_coupon_buy.hico_HilightCouponID AS coup_id,
								            COUNT(*) AS coup_numUse
								    FROM
								        hilight_coupon_buy
								    WHERE
								        hilight_coupon_buy.hcbu_Deleted != 'T'
								    GROUP BY hilight_coupon_buy.hico_HilightCouponID) AS y ON mi_card.card_id = y.coup_id) AS z
				    	LEFT JOIN
				    		mi_brand ON z.bran_BrandID = mi_brand.brand_id
				    	WHERE
				        	mi_brand.flag_status = 1
				            AND mi_brand.flag_del = 0
				            AND mi_brand.flag_hidden = 'No') AS i
		        LEFT JOIN
			    	mi_category_brand ON mi_category_brand.category_brand_id = i.category_brand
				WHERE
					i.brand_name LIKE '%{$searchStr}%' OR
			    	i.coup_Name LIKE '%{$searchStr}%' OR
			    	mi_category_brand.name LIKE '%{$searchStr}%'
			    	ORDER BY coup_UpdatedDate DESC";
		$q = $this->db->query($sql);
		$results = $q->result_array();
		return $results;
	} /*end function shop_lookup*/

	function getRecommentCouponOther($brand_id) {
		$sql = "SELECT
				    i.coup_CouponID,
				    i.bran_BrandID,
				    i.brand_name,
				    i.coup_Name,
				    i.coup_ImagePath,
				    i.coup_Image,
				    i.coup_Price,
				    i.coup_Description,
				    i.coup_UpdatedDate,
				    i.coup_Cost,
				    i.coup_Type,
				    i.coup_numUse,
				    i.path_logo,
				    i.logo_image,
				    i.category_brand,
				    mi_category_brand.name AS category_brand_name
				FROM
				    (SELECT
				        z.coup_CouponID,
				            z.bran_BrandID,
				            z.coup_Name,
				            z.coup_ImagePath,
				            z.coup_Image,
				            z.coup_Price,
				            z.coup_Description,
				            z.coup_UpdatedDate,
				            z.coup_Cost,
				            z.coup_Type,
				            IFNULL(z.coup_numUse, 0) AS coup_numUse,
				            mi_brand.name AS brand_name,
				            mi_brand.path_logo,
				            mi_brand.logo_image,
				            mi_brand.category_brand
				    FROM
				        (SELECT
				        hilight_coupon.coup_CouponID,
				            hilight_coupon.bran_BrandID,
				            hilight_coupon.coup_Name,
				            hilight_coupon.coup_ImagePath,
				            hilight_coupon.coup_Image,
				            hilight_coupon.coup_Price,
				            hilight_coupon.coup_Description,
				            hilight_coupon.coup_UpdatedDate,
				            hilight_coupon.coup_Cost,
				            hilight_coupon.coup_Type,
				            x.coup_numUse
				    FROM
				        hilight_coupon
				    LEFT JOIN (SELECT
				        hilight_coupon_trans.coup_CouponID AS coup_id,
				            COUNT(*) AS coup_numUse
				    FROM
				        hilight_coupon_trans
				    WHERE
				        hilight_coupon_trans.hico_Deleted != 'T'
				    GROUP BY hilight_coupon_trans.coup_CouponID) AS x ON hilight_coupon.coup_CouponID = x.coup_id UNION ALL SELECT
				        mi_card.card_id,
				            mi_card.brand_id,
				            mi_card.name,
				            mi_card.path_image,
				            mi_card.image,
				            mi_card.member_price,
				            mi_card.description,
				            mi_card.date_update,
				            mi_card.original_fee,
				            'Member' AS coup_Type,
				            y.coup_numUse
				    FROM
				        mi_card
				    LEFT JOIN (SELECT
				        hilight_coupon_buy.hico_HilightCouponID AS coup_id,
				            COUNT(*) AS coup_numUse
				    FROM
				        hilight_coupon_buy
				    WHERE
				        hilight_coupon_buy.hcbu_Deleted != 'T'
				    GROUP BY hilight_coupon_buy.hico_HilightCouponID) AS y ON mi_card.card_id = y.coup_id) AS z
				    LEFT JOIN mi_brand ON z.bran_BrandID = mi_brand.brand_id
				    WHERE
				        mi_brand.flag_status = 1
				            AND mi_brand.flag_del = 0
				            AND mi_brand.flag_hidden = 'No') AS i
				        LEFT JOIN
				    mi_category_brand ON mi_category_brand.category_brand_id = i.category_brand
				WHERE bran_BrandID = $brand_id
				ORDER BY coup_UpdatedDate DESC";
		$q = $this->db->query($sql);
		$results = $q->result_array();
		return $results;
	} /*end function getRecommentCouponOther*/

	function isMyUser($user) {
		// test@test.com
		// test
		$username = $user->username;
		// $username = 'test@test.com';
		$password = $user->password;
		// $password = 'test';
		$sql = "SELECT
				    mb_member.email,
				    mb_member.home_phone,
				    mb_member.firstname,
				    mb_member.lastname
				FROM
				    mb_member
				WHERE
					( email =  '$username'  OR home_phone =  '$username' )
					AND password = '$password' ";
		$q = $this->db->query($sql);
		$results = $q->result_array();
		return $results;
	} /*end function isMyUser*/

	function isUsernameMyHave($username) {
		// test@test.com
		$sql = "SELECT
				    count(*) as value
				FROM
				    mb_member
				WHERE email =  '$username'  OR home_phone =  '$username' ";
		$q = $this->db->query($sql);
		$results = $q->result_array();
		return $results;
	} /*end function isUsernameMyHave*/

	function shop_lookup($id) {
		/*test id 36*/
		$sql = "SELECT
				    hilight_coupon.coup_CouponID,
				    hilight_coupon.coup_Name,
				    hilight_coupon.coup_ImagePath,
				    hilight_coupon.coup_Image,
				    hilight_coupon.coup_Price,
				    hilight_coupon.coup_Description,
				    hilight_coupon.coup_UpdatedDate,
				    hilight_coupon.coup_Cost,
				    hilight_coupon.coup_Type,
				    hilight_coupon.coup_CreatedDate,
				    hilight_coupon.coup_StartDate,
				    hilight_coupon.coup_EndDate,
				    hilight_coupon.coup_StartTime,
				    hilight_coupon.coup_EndTime,
				    hilight_coupon.coup_Participation,
				    hilight_coupon.coup_RepetitionMember,
				    hilight_coupon.coup_QtyMember,
				    hilight_coupon.coup_QtyPerMember,
				    hilight_coupon.coup_Method,
				    hilight_coupon.coup_EndDateUse,
				    hilight_coupon.coup_MethodUseOther,
				    hilight_coupon.coup_HowToUse,
				    hilight_coupon.coup_Condition,
				    hilight_coupon.coup_Exception,
				    hilight_coupon.coup_Contact,
				    hilight_coupon.coup_ActivityDuration,
				    hilight_coupon.coup_QtyPerMemberData,
				    mi_brand.brand_id,
				    mi_brand.name AS brand_name,
				    mi_brand.shop_reservation_brief,
				    mi_brand.signature_info,
				    mi_brand.open_brief,
				    mi_brand.shop_howtouse_brief,
				    mi_brand.open_description,
				    mi_brand.shop_cancellation_description,
				    mi_brand.shop_q1,
				    mi_brand.shop_a1,
				    mi_brand.shop_q2,
				    mi_brand.shop_a2,
				    mi_brand.shop_q3,
				    mi_brand.shop_a3,
				    mi_brand.shop_q4,
				    mi_brand.shop_a4,
				    mi_brand.shop_q5,
				    mi_brand.shop_a5,
				    mi_brand.website,
				    mi_brand.facebook_url,
				    mi_brand.line_id,
				    mi_brand.instragram,
				    mi_brand.tweeter,
				    mi_brand.path_logo,
				    mi_brand.logo_image,
				    mi_brand.category_brand,
				    mi_brand.flag_status,
				    mi_brand.flag_del,
				    mi_brand.flag_hidden,
				    mi_branch.map_latitude,
				    mi_branch.map_longitude,
				    mi_branch.address_no,
				    mi_branch.moo,
				    mi_branch.junction,
				    mi_branch.soi,
				    mi_branch.road,
				    mi_branch.sub_district,
				    mi_branch.district,
				    mi_branch.sub_district_id,
				    mi_branch.district_id,
				    mi_branch.province_id,
				    mi_branch.region_id,
				    mi_branch.country_id,
				    mi_branch.postcode
				FROM
				    hilight_coupon,
				    mi_brand,
				    mi_branch
				WHERE
		    		hilight_coupon.bran_BrandID = mi_brand.brand_Id
		        AND mi_brand.flag_status = 1
		        AND mi_brand.flag_del = 0
		        AND mi_brand.flag_hidden = 'No'
		        AND mi_brand.brand_id = mi_branch.brand_id
		        AND mi_branch.default_status = 1
		        AND hilight_coupon.coup_Type = 'Buy'
		        AND hilight_coupon.coup_CouponID = $id ";
		$q = $this->db->query($sql);
		$results = $q->result_array();
		return $results;
	} /*end function shop_lookup*/

	function promotion_lookup($id) {
		/*test id 36*/
		$sql = 'SELECT
					hilight_coupon.coup_CouponID,
					hilight_coupon.coup_Name,
					hilight_coupon.coup_ImagePath,
					hilight_coupon.coup_Image,
					hilight_coupon.coup_Price,
					hilight_coupon.coup_Description,
					hilight_coupon.coup_UpdatedDate,
					hilight_coupon.coup_Cost,
					hilight_coupon.coup_Type,
					hilight_coupon.coup_CreatedDate,
					hilight_coupon.coup_StartDate,
					hilight_coupon.coup_EndDate,
					hilight_coupon.coup_StartTime,
					hilight_coupon.coup_EndTime,
					hilight_coupon.coup_Participation,

					hilight_coupon.coup_RepetitionMember,
					hilight_coupon.coup_QtyMember,
					hilight_coupon.coup_QtyPerMember,
					hilight_coupon.coup_Method,
					hilight_coupon.coup_EndDateUse,
					hilight_coupon.coup_MethodUseOther,
					hilight_coupon.coup_HowToUse,
					hilight_coupon.coup_Condition,
					hilight_coupon.coup_Exception,
					hilight_coupon.coup_Contact,
					hilight_coupon.coup_ActivityDuration,

                    mi_brand.shop_reservation_brief,
					mi_brand.signature_info,
					mi_brand.open_brief,
					mi_brand.shop_howtouse_brief,
					mi_brand.open_description,
					mi_brand.shop_cancellation_description,
					mi_brand.shop_q1,
					mi_brand.shop_a1,
					mi_brand.shop_q2,
					mi_brand.shop_a2,
					mi_brand.shop_q3,
					mi_brand.shop_a3,
					mi_brand.shop_q4,
					mi_brand.shop_a4,
					mi_brand.shop_q5,
					mi_brand.shop_a5,
					mi_brand.website,
					mi_brand.facebook_url,
					mi_brand.line_id,
					mi_brand.instragram,
					mi_brand.tweeter,
					mi_brand.path_logo,
					mi_brand.logo_image,
					mi_brand.category_brand,
					mi_brand.flag_status,
					mi_brand.flag_del,
					mi_brand.flag_hidden
				FROM hilight_coupon LEFT JOIN mi_brand ON hilight_coupon.bran_BrandID=mi_brand.brand_Id

				WHERE 	mi_brand.flag_status = 1 AND
						mi_brand.flag_del = 0 AND
						mi_brand.flag_hidden = "No" AND
						hilight_coupon.coup_Type = "Use" AND
						hilight_coupon.coup_CouponID =' . $id;
		$q = $this->db->query($sql);
		$results = $q->result_array();
		return $results;
	} /*end function promotion_lookup*/

	function membercard_lookup($id) {
		/*test id 36*/
		$sql = 'SELECT
					mi_card.card_id,
					mi_card.brand_id,
					mi_card.branch_id,
					mi_card.card_type_id,
					mi_card.name,
					mi_card.description,
					mi_card.image,
					mi_card.image_newupload,
					mi_card.qr_code_text,
					mi_card.qr_code_image,
					mi_card.path_image,
					mi_card.path_qr,
					mi_card.class,
					mi_card.purpose,
					mi_card.price_type,
					mi_card.original_fee,
					mi_card.member_fee,
					mi_card.member_amount,
					mi_card.member_vat,
					mi_card.member_price,
					mi_card.charge_percent,
					mi_card.expense_fee,
					mi_card.charge_status,
					mi_card.card_percent,
					mi_card.vat_type,
					mi_card.limit_member,
					mi_card.limit_privilege,
					mi_card.note,
					mi_card.target_member_type,
					mi_card.period_type,
					mi_card.period_type_other,
					mi_card.date_last_register,
					mi_card.date_expired,
					mi_card.condition_card,
					mi_card.exception,
					mi_card.greeting_messages,
					mi_card.greeting_messages_ckedit,
					mi_card.greeting_updateddate,
					mi_card.greeting_updatedby,
					mi_card.greeting_type,
					mi_card.greeting_accept,
					mi_card.special_code,
					mi_card.flag_collection,
					mi_card.flag_coupon,
					mi_card.flag_status,
					mi_card.flag_hidden,
					mi_card.flag_multiple,
					mi_card.flag_autorenew,
					mi_card.flag_approve,
					mi_card.flag_existing,
					mi_card.variety_id,
					mi_card.display_data,
					mi_card.register_condition,
					mi_card.how_to_activate,
					mi_card.birthday_privileges,
					mi_card.how_to_use,
					mi_card.collection_data,
					mi_card.re_new,
					mi_card.upgrade_data,
					mi_card.where_to_use,
					mi_card.source_information,
					mi_card.date_status,
					mi_card.flag_del,
					mi_card.date_create,
					mi_card.date_update,

					mi_brand.cover,
					mi_brand.path_cover,
					mi_brand.slogan,
					mi_brand.path_logo,
					mi_brand.logo_image,
                    mi_brand.shop_reservation_brief,
					mi_brand.signature_info,
					mi_brand.open_brief,
					mi_brand.shop_howtouse_brief,
					mi_brand.open_description,
					mi_brand.shop_cancellation_description,
					mi_brand.shop_q1,
					mi_brand.shop_a1,
					mi_brand.shop_q2,
					mi_brand.shop_a2,
					mi_brand.shop_q3,
					mi_brand.shop_a3,
					mi_brand.shop_q4,
					mi_brand.shop_a4,
					mi_brand.shop_q5,
					mi_brand.shop_a5,
					mi_brand.website,
					mi_brand.facebook_url,
					mi_brand.line_id,
					mi_brand.instragram,
					mi_brand.tweeter,
					mi_brand.category_brand,
					mi_brand.flag_status,
					mi_brand.flag_del,
					mi_brand.flag_hidden
				FROM mi_card LEFT JOIN mi_brand ON mi_card.brand_id=mi_brand.brand_Id

				WHERE 	mi_brand.flag_status = 1 AND
						mi_brand.flag_del = 0 AND
						mi_brand.flag_hidden = "No" AND
						mi_card.card_id =' . $id;
		$q = $this->db->query($sql);
		$results = $q->result_array();
		return $results;
	} /*end function membercard_lookup*/

	function membercard_lookup_privilege($id) {
		$sql = 'SELECT
					mi_card_register.card_id,
					mi_card_register.brand_id,
					mi_card_register.branch_id,
					mi_card_register.privilege_id,
					mi_card_register.activity_id,
					mi_card_register.coupon_id,
					mi_card_register.date_create,
					mi_card_register.date_update,
					mi_card_register.flag_del,
					mi_card_register.qrcode_privileges_text,
					mi_card_register.status,
					mi_card_register.qrcode_privileges_image,
					mi_card_register.path_qr,

					privilege.priv_PrivilegeID,
					privilege.priv_Name,
					privilege.priv_Image,
					privilege.priv_ImagePath
				FROM mi_card_register
				LEFT JOIN privilege
				ON mi_card_register.privilege_id = privilege.priv_PrivilegeID
				WHERE
					mi_card_register.status = "0" AND
					mi_card_register.privilege_id != "" AND
					mi_card_register.card_id = ' . $id;

		$q = $this->db->query($sql);
		$results = $q->result_array();
		return $results;
	} /*end function membercard_lookup_privilege */

	function membercard_lookup_coupon($id) {
		$sql = 'SELECT
					mi_card_register.card_id,
					mi_card_register.brand_id,
					mi_card_register.branch_id,
					mi_card_register.privilege_id,
					mi_card_register.activity_id,
					mi_card_register.coupon_id,
					mi_card_register.date_create,
					mi_card_register.date_update,
					mi_card_register.flag_del,
					mi_card_register.qrcode_privileges_text,
					mi_card_register.status,
					mi_card_register.qrcode_privileges_image,
					mi_card_register.path_qr,

					coupon.coup_CouponID,
					coupon.coup_Name,
					coupon.coup_ImagePath,
					coupon.coup_Image
				FROM mi_card_register
				LEFT JOIN coupon
				ON 	mi_card_register.coupon_id = coupon.coup_CouponID
				WHERE
					coupon.coup_Birthday != "T" AND
					mi_card_register.status = "0"  AND
					mi_card_register.card_id = ' . $id;

		$q = $this->db->query($sql);
		$results = $q->result_array();
		return $results;
	} /*end function membercard_lookup_coupon */

	function membercard_lookup_coupon_birthday($id) {
		$sql = 'SELECT
					mi_card_register.card_id,
					mi_card_register.brand_id,
					mi_card_register.branch_id,
					mi_card_register.privilege_id,
					mi_card_register.activity_id,
					mi_card_register.coupon_id,
					mi_card_register.date_create,
					mi_card_register.date_update,
					mi_card_register.flag_del,
					mi_card_register.qrcode_privileges_text,
					mi_card_register.status,
					mi_card_register.qrcode_privileges_image,
					mi_card_register.path_qr,

					coupon.coup_CouponID,
					coupon.coup_Name,
					coupon.coup_ImagePath,
					coupon.coup_Image
				FROM mi_card_register
				LEFT JOIN coupon
				ON 	mi_card_register.coupon_id = coupon.coup_CouponID
				WHERE
					coupon.coup_Birthday = "T" AND
					mi_card_register.status = "0"  AND
					mi_card_register.card_id = ' . $id;

		$q = $this->db->query($sql);
		$results = $q->result_array();
		return $results;
	} /*end function membercard_lookup_coupon_birthday */

	function membercard_lookup_activity($id) {
		$sql = 'SELECT
					mi_card_register.card_id,
					mi_card_register.brand_id,
					mi_card_register.branch_id,
					mi_card_register.privilege_id,
					mi_card_register.activity_id,
					mi_card_register.coupon_id,
					mi_card_register.date_create,
					mi_card_register.date_update,
					mi_card_register.flag_del,
					mi_card_register.qrcode_privileges_text,
					mi_card_register.status,
					mi_card_register.qrcode_privileges_image,
					mi_card_register.path_qr,

					activity.acti_ActivityID,
					activity.acti_Name,
					activity.acti_ImagePath,
					activity.acti_Image
				FROM mi_card_register
				LEFT JOIN activity
				ON 	mi_card_register.activity_id = activity.acti_ActivityID
				WHERE
					mi_card_register.status = "0"  AND
					mi_card_register.activity_id !="" AND
					mi_card_register.card_id = ' . $id;

		$q = $this->db->query($sql);
		$results = $q->result_array();
		return $results;
	} /*end function membercard_lookup_activity */

	function membercard_lookup_reward($bran_BrandID) {
		$sql = 'SELECT
					reward_redeem.rede_Name,
					reward_redeem.rewa_RewardID,
					reward_redeem.bran_BrandID,

					reward.rewa_Name,
					reward.rewa_Image,
					reward.rewa_ImagePath
				FROM reward_redeem
				LEFT JOIN reward
				ON reward_redeem.rewa_RewardID = reward.rewa_RewardID
				WHERE reward_redeem.bran_BrandID = ' . $bran_BrandID;

		$q = $this->db->query($sql);
		$results = $q->result_array();
		return $results;
	} /*end function membercard_lookup_reward */

	function getAlldataProduct() {
		// Select user records
		// $this->db->select('*');
		$sql = "SELECT
					i.coup_CouponID,
				    i.bran_BrandID,
				    i.coup_Name,
				    i.coup_ImagePath,
				    i.coup_Image,
				    i.coup_Price,
				    i.coup_Description,
				    i.coup_UpdatedDate,
				    i.coup_Cost,
				    i.coup_Type,
				    i.coup_numUse,
				    i.path_logo,
					i.logo_image,
					i.category_brand,
				    mi_category_brand.name As category_brand_name
				from

				(


				select
				    z.coup_CouponID,
				    z.bran_BrandID,
				    z.coup_Name,
				    z.coup_ImagePath,
				    z.coup_Image,
				    z.coup_Price,
				    z.coup_Description,
				    z.coup_UpdatedDate,
				    z.coup_Cost,
				    z.coup_Type,
				    IFNULL(z.coup_numUse, 0) as coup_numUse,
				    mi_brand.path_logo,
					mi_brand.logo_image,
					mi_brand.category_brand
				from
				    (
						select
							hilight_coupon.coup_CouponID,
				            hilight_coupon.bran_BrandID,
							hilight_coupon.coup_Name,
							hilight_coupon.coup_ImagePath,
							hilight_coupon.coup_Image,
							hilight_coupon.coup_Price,
							hilight_coupon.coup_Description,
							hilight_coupon.coup_UpdatedDate,
							hilight_coupon.coup_Cost,
				            hilight_coupon.coup_Type,
							x.coup_numUse
						from
							hilight_coupon
						left join
							(
								SELECT
									hilight_coupon_trans.coup_CouponID AS coup_id,
								COUNT(*) AS coup_numUse
								FROM
									hilight_coupon_trans
								WHERE
									hilight_coupon_trans.hico_Deleted != 'T'
								GROUP BY hilight_coupon_trans.coup_CouponID
							) AS x
						ON hilight_coupon.coup_CouponID = x.coup_id
				    union all
						select
							mi_card.card_id,
				            mi_card.brand_id,
							mi_card.name,
							mi_card.path_image,
							mi_card.image,
							mi_card.member_price,
							mi_card.description,
							mi_card.date_update,
							mi_card.original_fee,
							'Member' AS coup_Type,
							y.coup_numUse
						from
							mi_card
							left join
								(select
									hilight_coupon_buy.hico_HilightCouponID AS coup_id,
									COUNT(*) AS coup_numUse
								FROM
									hilight_coupon_buy
								WHERE
									hilight_coupon_buy.hcbu_Deleted != 'T'
								GROUP BY hilight_coupon_buy.hico_HilightCouponID
								) AS y
							on mi_card.card_id = y.coup_id
				    ) as z

				left join mi_brand on z.bran_BrandID = mi_brand.brand_id
				where mi_brand.flag_status = 1 and mi_brand.flag_del = 0 and mi_brand.flag_hidden = 'No'

				) as i


				LEFT JOIN
					mi_category_brand ON mi_category_brand.category_brand_id = i.category_brand

				order by coup_UpdatedDate desc";
		$q = $this->db->query($sql);
		$results = $q->result_array();

		// echo $results;
		return $results;
	}

	function get_product_limit($limit) {
		/*test coupon_Id = 137 Ans 14*/
		// echo $limit;exit;
		$sql = 'SELECT
				    z.coup_CouponID,
				    z.bran_BrandID,
				    z.coup_Name,
				    z.coup_ImagePath,
				    z.coup_Image,
				    z.coup_Price,
				    z.coup_Description,
				    z.coup_UpdatedDate,
				    z.coup_Cost,
				    z.coup_Type,
				    IFNULL(z.coup_numUse, 0) AS coup_numUse,
				    mi_brand.path_logo,
					mi_brand.logo_image,
					mi_brand.category_brand
				FROM
				    (
						SELECT
							hilight_coupon.coup_CouponID,
				            hilight_coupon.bran_BrandID,
							hilight_coupon.coup_Name,
							hilight_coupon.coup_ImagePath,
							hilight_coupon.coup_Image,
							hilight_coupon.coup_Price,
							hilight_coupon.coup_Description,
							hilight_coupon.coup_UpdatedDate,
							hilight_coupon.coup_Cost,
				            hilight_coupon.coup_Type,
							x.coup_numUse
						FROM
							hilight_coupon
						LEFT JOIN
							(
								SELECT
									hilight_coupon_trans.coup_CouponID AS coup_id,
								COUNT(*) AS coup_numUse
								FROM
									hilight_coupon_trans
								WHERE
									hilight_coupon_trans.hico_Deleted != "T"
								GROUP BY hilight_coupon_trans.coup_CouponID
							) AS x
						ON hilight_coupon.coup_CouponID = x.coup_id
				    UNION ALL
						SELECT
							mi_card.card_id,
				            mi_card.brand_id,
							mi_card.name,
							mi_card.path_image,
							mi_card.image,
							mi_card.member_price,
							mi_card.description,
							mi_card.date_update,
							mi_card.original_fee,
							"Member" AS coup_Type,
							y.coup_numUse
						FROM
							mi_card
							LEFT JOIN
								(select
									hilight_coupon_buy.hico_HilightCouponID AS coup_id,
									COUNT(*) AS coup_numUse
								FROM
									hilight_coupon_buy
								WHERE
									hilight_coupon_buy.hcbu_Deleted != "T"
								GROUP BY hilight_coupon_buy.hico_HilightCouponID
								) AS y
							ON mi_card.card_id = y.coup_id
				    ) AS z

				LEFT JOIN mi_brand ON z.bran_BrandID = mi_brand.brand_id
				WHERE mi_brand.flag_status = 1 AND mi_brand.flag_del = 0 AND mi_brand.flag_hidden = "No"
				ORDER BY coup_UpdatedDate DESC LIMIT ' . $limit;
		$q = $this->db->query($sql);
		$results = $q->result_array();
		return $results;
	}

	function getdata_Catrogy_barnd() {
		$sql = "SELECT b.category_brand ,b.product_category_length, c.name as category_name from
					(
						SELECT
					    	a.category_brand, COUNT(*) AS product_category_length
						FROM (
								SELECT
					        	z.coup_CouponID,
					            z.bran_BrandID,
					            z.coup_Name,
					            z.coup_ImagePath,
					            z.coup_Image,
					            z.coup_Price,
					            z.coup_Description,
					            z.coup_UpdatedDate,
					            z.coup_Cost,
					            z.coup_Type,
					            IFNULL(z.coup_numUse, 0) AS coup_numUse,
					            mi_brand.path_logo,
					            mi_brand.logo_image,
					            mi_brand.category_brand
							    FROM
							        (	SELECT
								        	hilight_coupon.coup_CouponID,
								            hilight_coupon.bran_BrandID,
								            hilight_coupon.coup_Name,
								            hilight_coupon.coup_ImagePath,
								            hilight_coupon.coup_Image,
								            hilight_coupon.coup_Price,
								            hilight_coupon.coup_Description,
								            hilight_coupon.coup_UpdatedDate,
								            hilight_coupon.coup_Cost,
								            hilight_coupon.coup_Type,
								            x.coup_numUse
									    FROM
									        hilight_coupon
									    LEFT JOIN (SELECT
									        hilight_coupon_trans.coup_CouponID AS coup_id,
									            COUNT(*) AS coup_numUse
									    FROM
					        			hilight_coupon_trans
					    				WHERE
					        				hilight_coupon_trans.hico_Deleted != 'T'
				    					GROUP BY hilight_coupon_trans.coup_CouponID) AS x ON hilight_coupon.coup_CouponID = x.coup_id
				    					UNION ALL SELECT
								       	 	mi_card.card_id,
								            mi_card.brand_id,
								            mi_card.name,
								            mi_card.path_image,
								            mi_card.image,
								            mi_card.member_price,
								            mi_card.description,
								            mi_card.date_update,
								            mi_card.original_fee,
								            'Member' AS coup_Type,
								            y.coup_numUse
									    FROM
									        mi_card
									    LEFT JOIN (SELECT
									        hilight_coupon_buy.hico_HilightCouponID AS coup_id,
									            COUNT(*) AS coup_numUse
									    FROM
									        hilight_coupon_buy
									    WHERE
									        hilight_coupon_buy.hcbu_Deleted != 'T'
									    GROUP BY hilight_coupon_buy.hico_HilightCouponID) AS y ON mi_card.card_id = y.coup_id) AS z
									    LEFT JOIN mi_brand ON z.bran_BrandID = mi_brand.brand_id
									    WHERE
									        mi_brand.flag_status = 1
									            AND mi_brand.flag_del = 0
									            AND mi_brand.flag_hidden = 'No') AS a
										GROUP BY category_brand
					) as b
				left join (SELECT * FROM memberin_v1.mi_category_brand) as c
				on b.category_brand = c.category_brand_id";
		$q = $this->db->query($sql);
		$results = $q->result_array();
		return $results;
	}

	function countColumnType() {

		$sql = 'SELECT type, COUNT(*) AS typeCount FROM hilight_coupon GROUP BY type';
		$q = $this->db->query($sql);
		$results = $q->result_array();
		return $results;
	}

	function getLookupCoupon($id) {
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
            ON bran_BrandID=brand_Id WHERE coup_CouponID =' . $id;
		$q = $this->db->query($sql);
		$results = $q->result_array();

		// echo $results;
		return $results;
	}

	// function get_rating() {
	// 	$sql = 'SELECT coup_CouponID, COUNT(hico_Rating) as coup_count, SUM(hico_Rating) as coup_sum FROM hilight_coupon_trans WHERE hico_Rating != "0" GROUP BY coup_CouponID';
	// 	$q = $this->db->query($sql);
	// 	$results = $q->result_array();
	// 	return $results;
	// }

	// function get_hilight_coupon_trans() {
	// 	/*test coupon_Id = 137 Ans 14*/
	// 	$sql = 'SELECT hico_HilightCouponID,coup_CouponID,hico_Rating FROM hilight_coupon_trans WHERE hico_Deleted != "T" ';
	// 	$q = $this->db->query($sql);
	// 	$results = $q->result_array();
	// 	return $results;
	// }
}