<?php

header('Content-Type:text/html; charset=UTF-8');

//========================================//

ini_set("display_errors",1);
error_reporting(1);

//========================================//

include('../../include/common.php');
include('../../include/common_check.php');
include('../../lib/function_normal.php');
require_once('../../include/connect.php');

//========================================//

$oTmp = new TemplateEngine();
$oDB = new DBI();

if ($bDebug) {

	$oErr = new Tracker();
	$oDB->SetTracker($oErr);
}

//========================================//

if ($_SESSION['role_action']['kpi']['view'] != 1) {

	echo "<script> history.back(); </script>";
	exit();
}

//========================================//

$time_insert = date("Y-m-d H:i:s");

$Act = $_REQUEST['act'];

$brand_id = $_REQUEST['brand'];


if($_SESSION['user_type_id_ses']>1){ $brand_id = $_SESSION['user_brand_id']; }


if ($brand_id && $Act) {

	$card_sql = "SELECT card_id FROM mi_card WHERE flag_del=0";

	$oRes = $oDB->Query($card_sql);

	$i = 1;

	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		$card_data .= "function get".$axRow_script['card_id']."() {

    					if (check".$axRow_script['card_id'].".checked) {

        					document.getElementById('img".$axRow_script['card_id']."').style.border = '2px outset #0d93c7';
        					document.getElementById('img".$axRow_script['card_id']."').style.borderRadius = '5px';

    					} else {

        					$('#img".$axRow_script['card_id']."').removeAttr('style');
        					document.getElementById('img".$axRow_script['card_id']."').style.borderRadius = '5px';
    					}
					}";
	}


	$card_id = "";

	for($i=0;$i<count($_POST["card"]);$i++) {

		if(trim($_POST["card"][$i]) != ""){

			if ($i==count($_POST["card"])-1) {

				$card_id .= $_POST["card"][$i];

			} else {

				$card_id .= $_POST["card"][$i].",";
			}
		}
	}

	#######################################################

	if ($card_id) {

		$sql_data = "SELECT 
					card.card_id, 			-- 0
					card.image, 			-- 1
					card.image_newupload,	-- 2
					card.name,				-- 3
					card.member_fee,		-- 4
					card.path_image			-- 5

					FROM mi_card AS card

					WHERE card.card_id IN (".$card_id.")";

		$card = mysql_query($sql_data);

		$CardArray = array();

		while($axRow_card = mysql_fetch_array($card)) {

			array_push($CardArray,$axRow_card);
		}

		$data_table = '<table id="kpi_data" class="table table-striped table-bordered" cellspacing="0" width="100%">';

		$data_table .= '<tr><td id="kpi_td">KPI</td>';

		for ($i=0; $i<count($CardArray) ; $i++) { 

			$data_table .= '<td id="kpi_head">'.$CardArray[$i][3].'</td>';

			$oTmp->assign('card_id_'.$CardArray[$i][0], $CardArray[$i][0]);
		}

		$data_table .= '</tr>';

	// ============== //
	//      IMAGE     //
	// ============== //

	$data_table .= '<tr><td id="kpi_td">Image</td>';

		for ($i=0; $i<count($CardArray) ; $i++) { 

			if($CardArray[$i][2]){

				$image = '<img src="../../upload/'.$CardArray[$i][5].$CardArray[$i][2].'" width="100" class="img-rounded image_border"/>';

			} else if($CardArray[$i][1]){

				$image = '<img src="../../upload/'.$CardArray[$i][5].$CardArray[$i][1].'" width="100" class="img-rounded image_border"/>';

			} else {

				$image = '<img src="../../images/card_privilege.jpg" width="100" class="img-rounded image_border"/>';
			}

			$data_table .= '<td style="text-align:center">'.$image.'</td>';
		}

	$data_table .= '</tr>';


	// ============== //
	//   MEMBER FEE   //
	// ============== //

	$data_table .= '<tr><td id="kpi_td">Member Fee</td>';

		for ($i=0; $i<count($CardArray) ; $i++) { 

			if ($CardArray[$i][4] == '0.00') { $amout = 'Free Card'; } 

			else { $amout = $CardArray[$i][4].' Baht.'; }

			$data_table .= '<td style="text-align:center">'.$amout.'</td>';
		}

	$data_table .= '</tr>';


	// ============== //
	//     REGESTER   //
	// ============== //

	$data_table .= '<tr><td bgcolor="#CCCCCC"><b>Member Register</b></td>';

		$i = count($CardArray);

		$data_table .= '<td colspan="'.($i).'" style="text-align:center"></td>';

	$data_table .= '</tr>';


	// ============== //
	//      M / F     //
	// ============== //

	$data_table .= '<tr><td id="kpi_td">Male / Female</td>';

		for ($i=0; $i<count($CardArray) ; $i++) { 

			#######################################################

			$sql_male = "SELECT count(member.flag_gender) AS male
							FROM mb_member_register AS regis

							LEFT JOIN mb_member AS member
							ON regis.member_id = member.member_id

							WHERE regis.card_id = ".$CardArray[$i][0]." AND member.flag_gender=1";

			$male = $oDB->QueryOne($sql_male);

			$sql_female = "SELECT count(member.flag_gender) AS female
							FROM mb_member_register AS regis

							LEFT JOIN mb_member AS member
							ON regis.member_id = member.member_id

							WHERE regis.card_id = ".$CardArray[$i][0]." AND member.flag_gender=2";

			$female = $oDB->QueryOne($sql_female);

			$sql_gender = "SELECT count(member.flag_gender) AS gender
							FROM mb_member_register AS regis

							LEFT JOIN mb_member AS member
							ON regis.member_id = member.member_id

							WHERE regis.card_id = ".$CardArray[$i][0]." AND member.flag_gender=0";

			$gender = $oDB->QueryOne($sql_gender);


			#######################################################	

			// $data_table .= '<td>Male : '.$male.'<br>Female : '.$female.'<br>Unknow : '.$gender.'</td>';	

			$data_table .= '<td style="text-align:center"><center>
							<table id="kpi_gender">
								<tr>
									<td width="20px" style="text-align:right">Male</td>
									<td width="10px" style="text-align:center">:</td>
									<td style="text-align:left"> &nbsp; '.$male.'</td>
								</tr>
								<tr>
									<td style="text-align:right">Female</td>
									<td style="text-align:center">:</td>
									<td style="text-align:left"> &nbsp; '.$female.'</td>
								</tr>
								<tr>
									<td style="text-align:right">Unknow</td>
									<td style="text-align:center">:</td>
									<td style="text-align:left"> &nbsp; '.$gender.'</td>
								</tr>
							</table></center>
							</td>';
		}

	$data_table .= '</tr>';


	// ============== //
	//     ACTIVE     //
	// ============== //

	$data_table .= '<tr><td id="kpi_td">Active No.</td>';

		for ($i=0; $i<count($CardArray) ; $i++) { 

			#######################################################

			$sql_use = "SELECT count(member_id)

						FROM mi_member_use_privilege AS use_priv

						WHERE use_priv.card_id = ".$CardArray[$i][0]." GROUP BY use_priv.member_id";

			$use_priv = $oDB->QueryOne($sql_use);

			#######################################################	

			if (!$use_priv) { $use_priv = "0"; }

			$data_table .= '<td style="text-align:center">'.$use_priv.'</td>';	
		}

	$data_table .= '</tr>';


	// ============== //
	//      TOTAL     //
	// ============== //

	$data_table .= '<tr><td id="kpi_td">Total No.</td>';

		for ($i=0; $i<count($CardArray) ; $i++) { 

			#######################################################



			$sql_total = "SELECT COUNT( member.flag_gender ) AS gender
							FROM mb_member_register AS regis

							LEFT JOIN mb_member AS member 
							ON regis.member_id = member.member_id

							WHERE regis.card_id =".$CardArray[$i][0];

			$total = $oDB->QueryOne($sql_total);

			#######################################################	

			$data_table .= '<td style="text-align:center">'.$total.'</td>';	
		}

	$data_table .= '</tr>';


	// ============== //
	//   REGIS RATE   //
	// ============== //

	$data_table .= '<tr><td id="kpi_td">Registration Rate</td>';

		for ($i=0; $i<count($CardArray) ; $i++) { 

			#######################################################

			$sql_regis_rate = "";

			$regis_rate = $oDB->QueryOne($sql_regis_rate);

			#######################################################	

			$data_table .= '<td style="text-align:center"></td>';	
		}

	$data_table .= '</tr>';


	// ============== //
	//   CONVE RATE   //
	// ============== //

	$data_table .= '<tr><td id="kpi_td">Conversion Rate</td>';

			#######################################################

			$sql_count_card = "SELECT count(card_id)
								FROM mi_card
								WHERE mi_card.brand_id=".$brand_id." AND member_fee=0";

			$count_card = $oDB->QueryOne($sql_count_card);

			$sql_card_free = "SELECT card_id
								FROM mi_card
								WHERE mi_card.brand_id=".$brand_id." AND member_fee=0";

			$card_free = $oDB->Query($sql_card_free);

			$i = 1;

			while($axRow_count = $card_free->FetchRow(DBI_ASSOC)) {

				if ($i == $count_card) {

					$data_card_free .= $axRow_count['card_id'];

				} else {

					$data_card_free .= $axRow_count['card_id'].",";
				}

				$i++;
			}

			#######################################################	

		for ($i=0; $i<count($CardArray) ; $i++) { 

			if ($data_card_free) {

				$sql_member = "SELECT DISTINCT member_id
								FROM mb_member_register
								WHERE card_id IN (".$data_card_free.")";

				$member_id = $oDB->Query($sql_member);

				$count_member = 0;

				while($axRow_member = $member_id->FetchRow(DBI_ASSOC)) {

					$sql_member = "SELECT member_id
									FROM mb_member_register
									WHERE card_id=".$CardArray[$i][0]." AND member_id=".$axRow_member['member_id'];

					$member_data = $oDB->QueryOne($sql_member);

					if ($member_data) { $count_member++; }
				}
			}

			if ($CardArray[$i][4]==0) {

				$conve_rate = "-";

			} else {

				$conve_rate = $count_member;
			}

			$data_table .= '<td style="text-align:center">'.$conve_rate.'</td>';	
		}

		$data_table .= '</tr>';


		// =================== //
		//    DATA PRIVILEGE   //
		// =================== //

		$num_colum = 0;

		for ($i=0; $i<count($CardArray) ; $i++) { 

			$count_privilege = "SELECT count(DISTINCT privilege_id)
				 				FROM mi_card_register 
				 				WHERE card_id=".$CardArray[$i][0]." 
				 				AND status='0'
				 				AND privilege_id!='0'";

			$privilege_count = $oDB->QueryOne($count_privilege);

			$sql_privilege = "SELECT DISTINCT privilege_id
				 				FROM mi_card_register 
				 				WHERE card_id=".$CardArray[$i][0]." 
				 				AND status='0' 
				 				AND privilege_id!='0'
				 				ORDER BY privilege_id";

			$privilege_data = $oDB->Query($sql_privilege);

			$priv_id = "";

			$k = 1;

			while($axRow_privilege = $privilege_data->FetchRow(DBI_ASSOC)) {

				 if ($k==$privilege_count) { $priv_id .= $axRow_privilege['privilege_id']; } 

				 else { $priv_id .= $axRow_privilege['privilege_id'].','; }

				 $k++;
			}

			$sql_priv_data = "SELECT 

				 				priv.priv_PrivilegeID, 			-- 0
				 				priv.priv_Image, 				-- 1
				 				priv.priv_ImageNew,				-- 2
				 				priv.priv_Name,					-- 3
				 				priv.priv_ImagePath				-- 4

				 				FROM privilege AS priv
				 				WHERE priv.priv_PrivilegeID IN (".$priv_id.")";

			$privilege = mysql_query($sql_priv_data);

			$PrivArray = array();

			while($axRow_privilege = mysql_fetch_array($privilege)) {

				array_push($PrivArray,$axRow_privilege);
			}

			$CardArray[$i][6] = $PrivArray;

			if ($num_colum < $privilege_count) {
				
				$num_colum = $privilege_count;
			}
		}

		for ($j=0; $j<$num_colum ; $j++) { 

			// ============== //
			//    PRIVILEGE   //
			// ============== //

			$data_table .= '<tr><td bgcolor="#CCCCCC"><b>Privilege</b></td>';

			$i = count($CardArray);

			$data_table .= '<td colspan="'.($i).'" style="text-align:center"></td>';

			$data_table .= '</tr>';

			// ============== //
			//      IMAGE     //
			// ============== //

			$data_table .= '<tr><td id="kpi_td">Image</td>';

			for ($i=0; $i<count($CardArray) ; $i++) { 

				if ($CardArray[$i][6][$j][1]) {

					$data_table .= '<td style="text-align:center"><img src="../../upload/'.$CardArray[$i][6][$j][4].$CardArray[$i][6][$j][1].'" width="120px"></td>';

				} else {

					$data_table .= '<td style="text-align:center"></td>';
				}
			}

			$data_table .= '</tr>';

			// ============== //
			//      NAME      //
			// ============== //

			$data_table .= '<tr><td id="kpi_td">Name</td>';

			for ($i=0; $i<count($CardArray) ; $i++) { 

				if ($CardArray[$i][6][$j][3]) {

					$data_table .= '<td style="text-align:center">'.$CardArray[$i][6][$j][3].'</td>';

				} else {

					$data_table .= '<td></td>';
				}
			}

			$data_table .= '</tr>';


			// ============== //
			//      M / F     //
			// ============== //

			$data_table .= '<tr><td id="kpi_td">Male / Female</td>';

			for ($i=0; $i<count($CardArray) ; $i++) { 

				#######################################################

				if ($CardArray[$i][6][$j]) {

					$sql_male = "SELECT count(member.flag_gender) AS male
								FROM member_privilege_trans AS use_priv

								LEFT JOIN mb_member AS member
								ON use_priv.memb_MemberID = member.member_id

								LEFT JOIN mi_card AS card
								ON card.card_id = use_priv.card_CardID

								WHERE use_priv.priv_PrivilegeID = ".$CardArray[$i][6][$j][0]." 
								AND use_priv.card_CardID = ".$CardArray[$i][0]."
								AND member.flag_gender=1";

					$male = $oDB->QueryOne($sql_male);

					$sql_female = "SELECT count(member.flag_gender) AS female
									FROM member_privilege_trans AS use_priv

									LEFT JOIN mb_member AS member
									ON use_priv.memb_MemberID = member.member_id

									LEFT JOIN mi_card AS card
									ON card.card_id = use_priv.card_CardID

									WHERE use_priv.priv_PrivilegeID = ".$CardArray[$i][6][$j][0]." 
									AND use_priv.card_CardID = ".$CardArray[$i][0]."
									AND member.flag_gender=2";

					$female = $oDB->QueryOne($sql_female);

					$sql_gender = "SELECT count(member.flag_gender) AS gender
									FROM member_privilege_trans AS use_priv

									LEFT JOIN mb_member AS member
									ON use_priv.memb_MemberID = member.member_id

									LEFT JOIN mi_card AS card
									ON card.card_id = use_priv.card_CardID

									WHERE use_priv.priv_PrivilegeID = ".$CardArray[$i][6][$j][0]." 
									AND use_priv.card_CardID = ".$CardArray[$i][0]."
									AND member.flag_gender=0";

					$gender = $oDB->QueryOne($sql_gender);

				#######################################################	

					if (!$male) { $male = 0; }

					if (!$female) { $female = 0; }

					if (!$gender) { $gender = 0; }

					$data_table .= '<td style="text-align:center"><center>
									<table id="kpi_gender">
										<tr>
											<td width="20px" style="text-align:right">Male</td>
											<td width="10px" style="text-align:center">:</td>
											<td style="text-align:left"> &nbsp; '.$male.'</td>
										</tr>
										<tr>
											<td style="text-align:right">Female</td>
											<td style="text-align:center">:</td>
											<td style="text-align:left"> &nbsp; '.$female.'</td>
										</tr>
										<tr>
											<td style="text-align:right">Unknow</td>
											<td style="text-align:center">:</td>
											<td style="text-align:left"> &nbsp; '.$gender.'</td>
										</tr>
									</table></center>
									</td>';
				} else {

					$data_table .= '<td></td>';
				}
			}

		$data_table .= '</tr>';


		// ============== //
		//   SATIS RATE   //
		// ============== //

			$data_table .= '<tr><td id="kpi_td">Satisfaction Rate</td>';

			for ($i=0; $i<count($CardArray) ; $i++) { 

				$data_table .= '<td></td>';
			}

			$data_table .= '</tr>';


		// ============== //
		//   REPUR RATE   //
		// ============== //

			$data_table .= '<tr><td id="kpi_td">Repurchase Rate</td>';

			for ($i=0; $i<count($CardArray) ; $i++) { 

				$limit_member=0;

				if ($CardArray[$i][6][$j]) {

					$sql_multi = "SELECT DISTINCT memb_MemberID 
									FROM member_privilege_trans 
									WHERE memb_MemberID IN (

    									SELECT memb_MemberID
										FROM member_privilege_trans
										GROUP BY memb_MemberID
                                		HAVING COUNT(memb_MemberID)>1

									) AND priv_PrivilegeID = ".$CardArray[$i][6][$j][0]." 
									AND card_CardID = ".$CardArray[$i][0];

					$multi_data = $oDB->Query($sql_multi);

					while($member_count = $multi_data->FetchRow(DBI_ASSOC)) {

						$limit_member++;
					}

					$data_table .= '<td style="text-align:center">'.$limit_member.'</td>';

				} else {

					$data_table .= '<td></td>';
				}
			}

			$data_table .= '</tr>';
		}


		// =================== //
		//      DATA COUPON    //
		// =================== //

		$num_colum = 0;

		for ($i=0; $i<count($CardArray) ; $i++) { 

			$count_coupon = "SELECT count(DISTINCT coupon_id)
				 				FROM mi_card_register 
				 				WHERE card_id=".$CardArray[$i][0]." 
				 				AND status='0'
				 				AND coupon_id!='0'";

			$coupon_count = $oDB->QueryOne($count_coupon);

			$sql_coupon = "SELECT DISTINCT coupon_id
				 				FROM mi_card_register 
				 				WHERE card_id=".$CardArray[$i][0]." 
				 				AND status='0' 
				 				AND coupon_id!='0'
				 				ORDER BY coupon_id";

			$coupon_data = $oDB->Query($sql_coupon);

			$coup_id = "";

			$k = 1;

			while($axRow_coupon = $coupon_data->FetchRow(DBI_ASSOC)) {

				 if ($k==$coupon_count) { $coup_id .= $axRow_coupon['coupon_id']; } 
				 else { $coup_id .= $axRow_coupon['coupon_id'].','; }

				 $k++;
			}

			$sql_coup_data = "SELECT 

				 				coup.coup_CouponID, 			-- 0
				 				coup.coup_Image, 				-- 1
				 				coup.coup_ImageNew,				-- 2
				 				coup.coup_Name,					-- 3
				 				coup.coup_ImagePath				-- 4

				 				FROM coupon AS coup

				 				WHERE coup.coup_CouponID IN (".$coup_id.")";

			$coupon = mysql_query($sql_coup_data);

			$CoupArray = array();

			while($axRow_coupon = mysql_fetch_array($coupon)) {

				array_push($CoupArray,$axRow_coupon);
			}

			$CardArray[$i][7] = $CoupArray;

			if ($num_colum < $coupon_count) {

				$num_colum = $coupon_count;
			}
		}

		for ($j=0; $j<$num_colum ; $j++) { 

			// ============== //
			//     COUPON     //
			// ============== //

			$data_table .= '<tr><td bgcolor="#CCCCCC"><b>Coupon</b></td>';

				$i = count($CardArray);

				$data_table .= '<td colspan="'.($i).'" style="text-align:center"></td>';

			$data_table .= '</tr>';


			// ============== //
			//      IMAGE     //
			// ============== //

			$data_table .= '<tr><td id="kpi_td">Image</td>';

			for ($i=0; $i<count($CardArray) ; $i++) { 

				if ($CardArray[$i][7][$j][1]) {

					$data_table .= '<td style="text-align:center"><img src="../../upload/'.$CardArray[$i][7][$j][4].$CardArray[$i][7][$j][1].'" width="120px"></td>';

				} else {

					$data_table .= '<td style="text-align:center"></td>';
				}
			}

			$data_table .= '</tr>';


			// ============== //
			//      NAME      //
			// ============== //

			$data_table .= '<tr><td id="kpi_td">Name</td>';

				for ($i=0; $i<count($CardArray) ; $i++) { 

					if ($CardArray[$i][7][$j][3]) {

						$data_table .= '<td style="text-align:center">'.$CardArray[$i][7][$j][3].'</td>';

					} else {

						$data_table .= '<td></td>';
					}
				}

			$data_table .= '</tr>';


			// ============== //
			//      M / F     //
			// ============== //

			$data_table .= '<tr><td id="kpi_td">Male / Female</td>';

			for ($i=0; $i<count($CardArray) ; $i++) { 

				#######################################################

				if ($CardArray[$i][7][$j]) {

					$sql_male = "SELECT count(member.flag_gender) AS male

								FROM member_coupon_trans AS use_coup

								LEFT JOIN mb_member AS member
								ON use_coup.memb_MemberID = member.member_id

								LEFT JOIN mi_card AS card
								ON card.card_id = use_coup.card_CardID

								WHERE use_coup.coup_CouponID = ".$CardArray[$i][7][$j][0]." 
								AND use_coup.card_CardID = ".$CardArray[$i][0]."
								AND member.flag_gender=1";

					$male = $oDB->QueryOne($sql_male);

					$sql_female = "SELECT count(member.flag_gender) AS female
									FROM member_coupon_trans AS use_coup

									LEFT JOIN mb_member AS member
									ON use_coup.memb_MemberID = member.member_id

									LEFT JOIN mi_card AS card
									ON card.card_id = use_coup.card_CardID

									WHERE use_coup.coup_CouponID = ".$CardArray[$i][7][$j][0]." 
									AND use_coup.card_CardID = ".$CardArray[$i][0]."
									AND member.flag_gender=2";

					$female = $oDB->QueryOne($sql_female);

					$sql_gender = "SELECT count(member.flag_gender) AS gender
									FROM member_coupon_trans AS use_coup

									LEFT JOIN mb_member AS member
									ON use_coup.memb_MemberID = member.member_id

									LEFT JOIN mi_card AS card
									ON card.card_id = use_coup.card_CardID

									WHERE use_coup.coup_CouponID = ".$CardArray[$i][7][$j][0]." 
									AND use_coup.card_CardID = ".$CardArray[$i][0]."
									AND member.flag_gender=0";

					$gender = $oDB->QueryOne($sql_gender);

				#######################################################	

					if (!$male) { $male = 0; }

					if (!$female) { $female = 0; }

					if (!$gender) { $gender = 0; }

					$data_table .= '<td style="text-align:center"><center>
									<table id="kpi_gender">
										<tr>
											<td width="20px" style="text-align:right">Male</td>
											<td width="10px" style="text-align:center">:</td>
											<td style="text-align:left"> &nbsp; '.$male.'</td>
										</tr>
										<tr>
											<td style="text-align:right">Female</td>
											<td style="text-align:center">:</td>
											<td style="text-align:left"> &nbsp; '.$female.'</td>
										</tr>
										<tr>
											<td style="text-align:right">Unknow</td>
											<td style="text-align:center">:</td>
											<td style="text-align:left"> &nbsp; '.$gender.'</td>
										</tr>
									</table></center>
									</td>';
				} else {

					$data_table .= '<td></td>';
				}
			}

		$data_table .= '</tr>';


		// ============== //
		//   SATIS RATE   //
		// ============== //

			$data_table .= '<tr><td id="kpi_td">Satisfaction Rate</td>';

			for ($i=0; $i<count($CardArray); $i++) { 

				$data_table .= '<td></td>';
			}

			$data_table .= '</tr>';

		// ============== //
		//   REPUR RATE   //
		// ============== //

			$data_table .= '<tr><td id="kpi_td">Repurchase Rate</td>';

			for ($i=0; $i<count($CardArray); $i++) { 

				$limit_member=0;

				if ($CardArray[$i][7][$j]) {

					$sql_multi = "SELECT DISTINCT memb_MemberID 
									FROM member_coupon_trans 
									WHERE memb_MemberID IN (
    									SELECT memb_MemberID
											FROM member_coupon_trans
											GROUP BY memb_MemberID
                                			HAVING COUNT(memb_MemberID)>1
										) AND coup_CouponID = ".$CardArray[$i][7][$j][0]." 
										AND card_CardID = ".$CardArray[$i][0];

						$multi_data = $oDB->Query($sql_multi);

						while($member_count = $multi_data->FetchRow(DBI_ASSOC)) {

							$limit_member++;
						}

						$data_table .= '<td style="text-align:center">'.$limit_member.'</td>';

					} else {

						$data_table .= '<td></td>';
					}
				}

			$data_table .= '</tr>';
		}


		// =================== //
		//     DATA ACTIVITY   //
		// =================== //

		$num_colum = 0;

		for ($i=0; $i<count($CardArray) ; $i++) { 

			$count_coupon = "SELECT count(DISTINCT activity_id)
				 				FROM mi_card_register 
				 				WHERE card_id=".$CardArray[$i][0]." 
				 				AND status='0'
				 				AND activity_id!='0'";

			$activity_count = $oDB->QueryOne($count_activity);

			$sql_activity = "SELECT DISTINCT activity_id
				 				FROM mi_card_register 
				 				WHERE card_id=".$CardArray[$i][0]." 
				 				AND status='0' 
				 				AND activity_id!='0'
				 				ORDER BY activity_id";

			$activity_data = $oDB->Query($sql_activity);

			$acti_id = "";

			$k = 1;

			while($axRow_activity = $activity_data->FetchRow(DBI_ASSOC)) {

				 if ($k==$activity_count) { $acti_id .= $axRow_activity['activity_id']; } 

				 else { $acti_id .= $axRow_activity['activity_id'].','; }

				 $k++;
			}

			$sql_acti_data = "SELECT 

				 				acti.acti_ActivityID, 			-- 0
				 				acti.acti_Image, 				-- 1
				 				acti.acti_ImageNew,				-- 2
				 				acti.acti_Name,					-- 3
				 				acti.acti_ImagePath				-- 4

				 				FROM activity AS acti

				 				WHERE acti.acti_ActivityID IN (".$acti_id.")";

			$activity = mysql_query($sql_acti_data);

			$ActiArray = array();

			while($axRow_activity = mysql_fetch_array($activity)) {

				array_push($actiArray,$axRow_activity);
			}

			$CardArray[$i][8] = $ActiArray;

			if ($num_colum < $activity_count) {

				$num_colum = $activity_count;
			}
		}

		for ($j=0; $j<$num_colum ; $j++) { 

			// ============== //
			//    ACTIVITY    //
			// ============== //

			$data_table .= '<tr><td bgcolor="#CCCCCC"><b>Activity</b></td>';

				$i = count($CardArray);

				$data_table .= '<td colspan="'.($i).'" style="text-align:center"></td>';

			$data_table .= '</tr>';


			// ============== //
			//      IMAGE     //
			// ============== //

			$data_table .= '<tr><td id="kpi_td">Image</td>';

				for ($i=0; $i<count($CardArray) ; $i++) { 

					if ($CardArray[$i][8][$j][1]) {

						$data_table .= '<td style="text-align:center"><img src="../../upload/'.$CardArray[$i][8][$j][4].$CardArray[$i][8][$j][1].'" width="120px"></td>';

					} else {

						$data_table .= '<td style="text-align:center"></td>';
					}
				}

			$data_table .= '</tr>';


			// ============== //
			//      NAME      //
			// ============== //

			$data_table .= '<tr><td id="kpi_td">Name</td>';

				for ($i=0; $i<count($CardArray) ; $i++) { 

					if ($CardArray[$i][8][$j][3]) {

						$data_table .= '<td style="text-align:center">'.$CardArray[$i][8][$j][3].'</td>';

					} else {

						$data_table .= '<td></td>';
					}
				}

			$data_table .= '</tr>';


			// ============== //
			//      M / F     //
			// ============== //

			$data_table .= '<tr><td id="kpi_td">Male / Female</td>';

			for ($i=0; $i<count($CardArray) ; $i++) { 

				#######################################################

				if ($CardArray[$i][7][$j]) {

					$sql_male = "SELECT count(member.flag_gender) AS male
									FROM member_activity_trans AS use_acti

									LEFT JOIN mb_member AS member
									ON use_acti.memb_MemberID = member.member_id

									LEFT JOIN mi_card AS card
									ON card.card_id = use_acti.card_CardID

									WHERE use_acti.acti_actionID = ".$CardArray[$i][7][$j][0]." 
									AND use_acti.card_CardID = ".$CardArray[$i][0]."
									AND member.flag_gender=1";

					$male = $oDB->QueryOne($sql_male);

					$sql_female = "SELECT count(member.flag_gender) AS female
									FROM member_activity_trans AS use_acti

									LEFT JOIN mb_member AS member
									ON use_acti.memb_MemberID = member.member_id

									LEFT JOIN mi_card AS card
									ON card.card_id = use_acti.card_CardID

									WHERE use_acti.acti_actionID = ".$CardArray[$i][7][$j][0]." 
									AND use_acti.card_CardID = ".$CardArray[$i][0]."
									AND member.flag_gender=2";

					$female = $oDB->QueryOne($sql_female);

					$sql_gender = "SELECT count(member.flag_gender) AS gender
									FROM member_activity_trans AS use_acti

									LEFT JOIN mb_member AS member
									ON use_acti.memb_MemberID = member.member_id

									LEFT JOIN mi_card AS card
									ON card.card_id = use_acti.card_CardID

									WHERE use_acti.acti_actionID = ".$CardArray[$i][7][$j][0]." 
									AND use_acti.card_CardID = ".$CardArray[$i][0]."
									AND member.flag_gender=0";

					$gender = $oDB->QueryOne($sql_gender);

				#######################################################	

					if (!$male) { $male = 0; }

					if (!$female) { $female = 0; }

					if (!$gender) { $gender = 0; }

					$data_table .= '<td style="text-align:center"><center>
									<table id="kpi_gender">
										<tr>
											<td width="20px" style="text-align:right">Male</td>
											<td width="10px" style="text-align:center">:</td>
											<td style="text-align:left"> &nbsp; '.$male.'</td>
										</tr>
										<tr>
											<td style="text-align:right">Female</td>
											<td style="text-align:center">:</td>
											<td style="text-align:left"> &nbsp; '.$female.'</td>
										</tr>
										<tr>
											<td style="text-align:right">Unknow</td>
											<td style="text-align:center">:</td>
											<td style="text-align:left"> &nbsp; '.$gender.'</td>
										</tr>
									</table></center>
									</td>';
				} else {

					$data_table .= '<td></td>';
				}
			}

		$data_table .= '</tr>';


		// ============== //
		//   SATIS RATE   //
		// ============== //

			$data_table .= '<tr><td id="kpi_td">Satisfaction Rate</td>';

				for ($i=0; $i<count($CardArray); $i++) { 

					$data_table .= '<td></td>';
				}

			$data_table .= '</tr>';

		// ============== //
		//   REPUR RATE   //
		// ============== //

			$data_table .= '<tr><td id="kpi_td">Repurchase Rate</td>';

				for ($i=0; $i<count($CardArray); $i++) { 

					$limit_member=0;

					if ($CardArray[$i][8][$j]) {

						$sql_multi = "SELECT DISTINCT memb_MemberID 
										FROM member_activity_trans 
										WHERE memb_MemberID IN (
    										SELECT memb_MemberID
											FROM member_coupon_trans
											GROUP BY memb_MemberID
                                			HAVING COUNT(memb_MemberID)>1
										) AND acti_ActivityID = ".$CardArray[$i][7][$j][0]." 
										AND card_CardID = ".$CardArray[$i][0];

						$multi_data = $oDB->Query($sql_multi);

						while($member_count = $multi_data->FetchRow(DBI_ASSOC)) {

							$limit_member++;
						}

						$data_table .= '<td style="text-align:center">'.$limit_member.'</td>';

					} else {

						$data_table .= '<td></td>';
					}
				}

			$data_table .= '</tr>';
		}

		$data_table .= '</table>';
	}

} else {

	$data_table = '<br><center><b>Please Select Card and Click Submit.</b></center>';
}


##################################################


if ($_SESSION['user_brand_id']) {

	$script_sql = "SELECT card_id FROM mi_card WHERE brand_id=".$_SESSION['user_brand_id']." AND flag_del=0";

} else {

	$script_sql = "SELECT card_id FROM mi_card WHERE flag_del=0";
}


	$oRes = $oDB->Query($script_sql);

	$script_data = "<script>";

	$script_data = "<script>";

	while ($axRow_script = $oRes->FetchRow(DBI_ASSOC)){

		$script_data .= "function get".$axRow_script['card_id']."() {

	    					if (check".$axRow_script['card_id'].".checked) {

	        					document.getElementById('img".$axRow_script['card_id']."').style.border = '2px outset #0d93c7';

	        					document.getElementById('img".$axRow_script['card_id']."').style.borderRadius = '5px';

	    					}

	    					else {

	        					$('#img".$axRow_script['card_id']."').removeAttr('style');

	        					document.getElementById('img".$axRow_script['card_id']."').style.borderRadius = '5px';

	    					}
						}";
	}

	$script_data .= "</script>";



##################################################



#  brand dropdownlist

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' brand_id="'.$_SESSION['user_brand_id'].'" ';
}

$as_brand = dropdownlist_from_table($oDB,'mi_brand','brand_id','name',$where_brand,' ORDER BY name');

$oTmp->assign('brand_opt', $as_brand);


$oTmp->assign('data_table', $data_table);

$oTmp->assign('brand_id', $brand_id);

$oTmp->assign('script_data', $script_data);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_analytics');

$oTmp->assign('content_file', 'analytics/kpi.htm');

$oTmp->display('layout/template.html');


//========================================//


$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());
}


//========================================//

?>