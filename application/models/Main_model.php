<?php if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Main_model extends CI_Model {

	function echo_data() {
		$sql = 'SELECT *
				FROM mi_brand';
		$q = $this->db->query($sql);
		$results = $q->result_array();
		$resultsKey = array_keys($results[0]);
		echo '<pre>';
		print_r($results[0]);
		echo '</pre>';
		foreach ($resultsKey as $value) {
			echo 'mi_brand.' . $value . ',<br />';
		}
		exit;
	} /*end function echo_data *watch varible mysql */

	function getLookupBrand($id) {

		$sql = 'SELECT
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
					mi_brand.create_by,
					mi_brand.update_by,
					mi_brand.flag_status,
					mi_brand.date_status,
					mi_brand.flag_del,
					mi_brand.flag_hidden,
					mi_brand.flag_approve,
					mi_brand.shop_q1,
					mi_brand.shop_a1,
					mi_brand.shop_q2,
					mi_brand.shop_a2,
					mi_brand.shop_q3,
					mi_brand.shop_a3,
					mi_brand.shop_q4,
					mi_brand.shop_a4,
					mi_brand.shop_q5,
					mi_brand.shop_a5
				FROM
				    mi_brand
				WHERE
				    mi_brand.flag_status = 1 AND
				    mi_brand.flag_del = 0 AND
				    mi_brand.flag_hidden = "No" AND
				    mi_brand.brand_id = ' . $id;
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
	}

	function getAllDataBrand() {
		$sql = 'SELECT
				    b.brand_id, b.name, b.path_logo, b.logo_image, b.date_update
				FROM
				    mi_brand b
				WHERE
				    b.flag_status = 1 AND
				    b.flag_del = 0 AND
				    b.flag_hidden = "No"
				ORDER BY b.date_update DESC';
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

	function shop_lookup($id) {
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
						hilight_coupon.coup_Type = "Buy" AND
						hilight_coupon.coup_CouponID =' . $id;
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
								hilight_coupon_trans.hico_Deleted != "T"
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
						"Member" AS coup_Type,
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
								hilight_coupon_buy.hcbu_Deleted != "T"
							GROUP BY hilight_coupon_buy.hico_HilightCouponID
							) AS y
						on mi_card.card_id = y.coup_id
			    ) as z

			left join mi_brand on z.bran_BrandID = mi_brand.brand_id
			where mi_brand.flag_status = 1 and mi_brand.flag_del = 0 and mi_brand.flag_hidden = "No"
			order by coup_UpdatedDate desc';
		$q = $this->db->query($sql);
		$results = $q->result_array();

		// echo $results;
		return $results;
	}

	function get_hilight_coupon_trans() {
		/*test coupon_Id = 137 Ans 14*/
		$sql = 'SELECT hico_HilightCouponID,coup_CouponID,hico_Rating FROM hilight_coupon_trans WHERE hico_Deleted != "T" ';
		$q = $this->db->query($sql);
		$results = $q->result_array();
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

	function get_rating() {
		$sql = 'SELECT coup_CouponID, COUNT(hico_Rating) as coup_count, SUM(hico_Rating) as coup_sum FROM hilight_coupon_trans WHERE hico_Rating != "0" GROUP BY coup_CouponID';
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

}