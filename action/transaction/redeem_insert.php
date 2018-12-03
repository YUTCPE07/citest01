<?php

header('Content-Type:text/html; charset=UTF-8');

//========================================//

ini_set("display_errors",1);
error_reporting(1);

//========================================//

include('../../include/common.php');
include('../../lib/function_normal.php');
include('../../include/common_check.php');
require_once('../../include/connect.php');

//========================================//

$oTmp = new TemplateEngine();
$oDB = new DBI();

if ($bDebug) {

	$oErr = new Tracker();
	$oDB->SetTracker($oErr);
}

//========================================//

if (($_SESSION['role_action']['register_trans']['add'] != 1) || ($_SESSION['role_action']['register_trans']['edit'] != 1)) {
	echo "<script> history.back(); </script>";
	exit();
}

//========================================//


$Act = $_REQUEST['act'];

$time_insert = date("Y-m-d H:i:s");


if ($Act == 'save') {

	$redeem_id = trim_txt($_REQUEST['redeem_id']);
	$member_id = trim_txt($_REQUEST['member_id']);
	$brnc_BranchID = trim_txt($_REQUEST['brnc_BranchID']);
	$bran_BrandID = trim_txt($_REQUEST['bran_BrandID']);
	$redeem_date = trim_txt($_REQUEST['redeem_date']);
	$redeem_month = trim_txt($_REQUEST['redeem_month']);
	$redeem_year = trim_txt($_REQUEST['redeem_year']);

	$date_redeem = $redeem_year.'-'.$redeem_month.'-'.$redeem_date;


	# RANDOM CODE

	$sql_code = 'SELECT mi_brand.pre_code,
						mi_brand.flag_numberic
					FROM mi_brand
					WHERE brand_id = "'.$bran_BrandID.'"';
	$oRes_code = $oDB->Query($sql_code);
	$axRow_code = $oRes_code->FetchRow(DBI_ASSOC);
	$flag_numberic = $axRow_code['flag_numberic'];
	$pre_code = $axRow_code['pre_code'];

	$sql_redeem = 'SELECT 
						rr.coty_CollectionTypeID, 
					    rr.rera_RewardQty,
					    rr.rera_CardID,
					    rr.rera_RewardQty_Point,
					    rr.rera_RewardQty_Stamp,
					    IF(rr.coty_CollectionTypeID = "0", rr.rera_RewardQty_Point,rr.rera_RewardQty_Stamp) as TotalUse,
					    rw.*,
					    rw.rewa_RewardID reward_id,
					    rw.card_CardID reward_card_id,
					    rd.*

				    FROM reward_redeem rd 

				    LEFT JOIN reward rw 
				    ON rd.rewa_RewardID = rw.rewa_RewardID

				    LEFT JOIN reward_ratio rr 
				    ON rd.rede_RewardRedeemID = rr.rede_RewardRedeemID

				    WHERE rd.rede_RewardRedeemID = '.$redeem_id.'';

	$oRes_redeem = $oDB->Query($sql_redeem);
	$axRow = $oRes_redeem->FetchRow(DBI_ASSOC);

	$status_redeem = 'True';

	$TotalHave = CheckPointType($bran_BrandID,$member_id,$axRow['coty_CollectionTypeID']);

	# CHECK START - END DATE

	if ($axRow['rede_Time'] == 'T') {

		if ($axRow['rede_StartDate'] <= $date_redeem && $date_redeem <= $axRow['rede_EndDate']) { } 
		else { $status_redeem = 'False'; $reason_false = 'ไม่สามารถแลกของรางวัลได้ เนื่องจากไม่อยู่ในช่วงวันที่แลกของรางวัลได้'; }
	}

	# REPEPTITION ALL

	if ($axRow['rede_RedeemLimit'] == 'Limit') { 

		$sql_value = 'SELECT COUNT(retr_RewardRedeemTransID) 
						FROM reward_redeem_trans 
						WHERE rede_RewardRedeemID="'.$redeem_id.'"
						AND retr_Deleted=""';
		$count_redeem = $oDB->QueryOne($sql_value);

		if ($count_redeem >= $axRow['rede_NumberTime']) {

			$status_redeem = 'False';
			$reason_false = 'ไม่สามารถแลกของรางวัลได้ เนื่องจากสิทธิ์การแลกของรางวัลครบแล้ว';
		}
	}

	# REPEPTITION MEMBER

	if ($axRow['rede_Repetition'] == 'T') { 

		switch ($axRow['rede_QtyPer']) {
									
			case 'Daily' :
				$total = RepetitionDaily($member_id, $redeem_id, $axRow['rede_Qty'], $date_redeem);
				if ($total == 0) { $status_redeem = "False"; }
				break;

			case 'Weekly' :
				$total = RepetitionWeekly($member_id, $redeem_id, $axRow['rede_Qty'], $axRow['rede_QtyPerData'], $date_redeem);
				if ($total == 0) { $status_redeem = "False"; }
				break;

			case 'Monthly' :
				$total = RepetitionMonth($member_id, $redeem_id, $axRow['rede_Qty'], $axRow['rede_QtyPerData'], $date_redeem);
				if ($total == 0) { $status_redeem = "False"; }
				break;

			case 'Not Specific' :
				$total = RepetitionNotSpecific($member_id, $redeem_id, $axRow['rede_Qty']);
				if ($total == 0) { $status_redeem = "False"; }
				break;

			default :
				break;
		}	
	}

	# REWARD QTY

	if ($axRow['rewa_Limit'] == 'T' && $axRow['rewa_Qty'] == 0) {

		$status_redeem = 'False';
		$reason_false = 'ไม่สามารถแลกของรางวัลได้ เนื่องจากของรางวัลหมด';
	}

	# CARD REGISTER REDEEM

	if ($axRow['rera_CardID'] == '') {

		# CHECK POINT STAMP

		if ($TotalHave < $axRow['TotalUse']) { 

			$status_redeem = 'False';
			$reason_false = 'ไม่สามารถแลกของรางวัลได้ เนื่องจากแต้มไม่เพียงพอ';
		}

	} else {

		$sql_register = 'SELECT member_register_id 
							FROM mb_member_register 
							WHERE card_id IN ('.$axRow['rera_CardID'].')
							AND member_id="'.$member_id.'"
							AND flag_del=""';
		$register_id = $oDB->QueryOne($sql_register);

		if (!$register_id) {

			$status_redeem = 'False';
			$reason_false = 'ไม่สามารถแลกของรางวัลได้ เนื่องจากไม่ตรงตามเงื่อนไขการสมัครบัตร';
		}
	}

	# CARD REWARD

	if ($axRow['rewa_Type'] == 'Card') {

		$sql_card = 'SELECT flag_multiple 
						FROM mi_card 
						WHERE card_id="'.$axRow['reward_card_id'].'"';
		$card_multiple = $oDB->QueryOne($sql_card);

		if ($card_multiple!='Yes') {

			$sql_register = 'SELECT member_register_id 
								FROM mb_member_register 
								WHERE card_id="'.$axRow['reward_card_id'].'"
								AND member_id="'.$member_id.'"
								AND flag_del=""';
			$register_id = $oDB->QueryOne($sql_register);

			if ($register_id) {

				$status_redeem = 'False';
				$reason_false = 'ไม่สามารถแลกของรางวัลได้ เนื่องจากคุณมีบัตรสมาชิกนี้แล้ว';
			}
		}
	}

	# INSERT REDEEM TRANS

	if ($status_redeem == 'True') {

		$date = date("Y-m-d");
		$rewa_RewardID = $axRow["rewa_RewardID"];

		$balance = $TotalHave - $axRow["TotoUse"];

		if ($axRow["rewa_Type"] == "Card") { # REWARD IS CARD

			# CHECK MEMBER REGISTER

			$sql_card = 'SELECT flag_multiple FROM mi_card WHERE card_id="'.$axRow['reward_card_id'].'"';
			$card_multiple = $oDB->QueryOne($sql_card);

			if ($card_multiple!='Yes') {

				$sql_register = 'SELECT member_register_id 
									FROM mb_member_register 
									WHERE card_id="'.$axRow['reward_card_id'].'"
									AND member_id="'.$member_id.'"
									AND flag_del=""';
				$register_id = $oDB->QueryOne($sql_register);

				if ($register_id) {

					$status_redeem = 'False';
					$reason_false = 'ไม่สามารถแลกของรางวัลได้ เนื่องจากคุณมีบัตรสมาชิกนี้แล้ว';
					echo '<script type="text/javascript">alert("'.$reason_false.'")</script>';
					echo '<script>window.location.href="redeem_insert.php";</script>';
					exit();

				} else { $status_redeem = 'True'; }
			
			} else { $status_redeem = 'True'; }

			if ($status_redeem == 'True') {

				if ($axRow['rera_CardID'] != "") { # HAVE CARD REGISTER CAN REDEEM

					$sql_register = 'SELECT member_register_id 
										FROM mb_member_register 
										WHERE card_id IN ('.$axRow['rera_CardID'].')
										AND member_id="'.$member_id.'"
										AND flag_del=""';
					$register_id = $oDB->QueryOne($sql_register);

					if (!$register_id) { # NO CARD REGISTER

						$reason_redeem = 'ไม่สามารถแลกของรางวัลได้ เนื่องจากไม่ตรงตามเงื่อนไขการรับของรางวัล';
						echo '<script type="text/javascript">alert("'.$reason_false.'")</script>';
						echo '<script>window.location.href="redeem_insert.php";</script>';
						exit();
					
					} else { # HAVE CARD REGISTER

						$member_register_id = 0;
						$check_trans_id = "";

						$oRes = $oDB->Query($sql_register);
						while ($record = $oRes->FetchRow(DBI_ASSOC)) {

							$sql_trans = 'SELECT retr_RewardRedeemTransID
											FROM reward_redeem_trans
											WHERE rede_RewardRedeemID = "'.$redeem_id.'"
											AND mere_MemberRegisterID = "'.$record['member_register_id'].'"
											AND retr_Deleted = ""';
							$trans_id = $oDB->QueryOne($sql_trans);

							if ($trans_id) { $check_trans_id = $trans_id; } 
							else { $member_register_id = $record['member_register_id']; }
						}

						if ($member_register_id != 0) {

							if ($axRow['rewa_Limit']=="T") { # CHECK LIMIT REWARD

								$rewa_Qty_last = $axRow["rewa_Qty"] - $axRow["rera_RewardQty"];

								if ($rewa_Qty_last < 0) {

									$reason_false = 'ไม่สามารถแลกของรางวัลได้ เนื่องจากของรางวัลหมด';
									echo '<script type="text/javascript">alert("'.$reason_false.'")</script>';
									echo '<script>window.location.href="redeem_insert.php";</script>';
									exit();
								}
							}

							if ($axRow['rede_AutoRedeem']!='T') {

								$cut_status = CutPoint($bran_BrandID, $member_id, $axRow['TotalUse'], $axRow['coty_CollectionTypeID']);

							} else { $cut_status = "True"; }

							if ($cut_status == "True") {

								CutReward($rewa_Qty_last, $axRow["reward_id"]);
								RegisterCard($axRow['reward_card_id'], $member_id);
								InsertUseRedeem($pre_code,$flag_numberic,$member_register_id, $redeem_id, $brnc_BranchID, $member_id, $date_redeem, $axRow['rede_AutoRedeem'], $axRow['coty_CollectionTypeID'], $axRow['rera_RewardQty_Point'], $axRow['rera_RewardQty_Stamp'], $axRow['rera_RewardQty'], $date_redeem);

							} else {

								$reason_false = 'ไม่สามารถแลกของรางวัลได้ เนื่องจากแต้มไม่เพียงพอ';
								echo '<script type="text/javascript">alert("'.$reason_false.'")</script>';
								echo '<script>window.location.href="redeem_insert.php";</script>';
								exit();
							}

						} else {

							$reason_redeem = 'ไม่สามารถแลกของรางวัลได้ เนื่องจากคุณได้ใช้สิทธิ์ในการแลกของรางวัลหมดแล้ว';
							echo '<script type="text/javascript">alert("'.$reason_false.'")</script>';
							echo '<script>window.location.href="redeem_insert.php";</script>';
							exit();
						}
					}

				} else { # USE POINT OR STAMP TO REDEEM

					if ($axRow['rewa_Limit']=="T") { # CHECK LIMIT REWARD

						$rewa_Qty_last = $axRow["rewa_Qty"] - $axRow["rera_RewardQty"];

						if ($rewa_Qty_last < 0) {

							$reason_false = 'ไม่สามารถแลกของรางวัลได้ เนื่องจากของรางวัลหมด';
							echo '<script type="text/javascript">alert("'.$reason_false.'")</script>';
							echo '<script>window.location.href="redeem_insert.php";</script>';
							exit();

						}  else {

							CutReward($rewa_Qty_last,$axRow["reward_id"]);
						}
					}

					if ($axRow['rede_AutoRedeem']!='T') {

						$cut_status = CutPoint($bran_BrandID, $member_id, $axRow['TotalUse'], $axRow['coty_CollectionTypeID']);
						
					} else { $cut_status = "True"; }

					if ($cut_status == "True") {

						CutReward($rewa_Qty_last, $axRow["reward_id"]);
						RegisterCard($axRow['reward_card_id'], $member_id);
						InsertUseRedeem($pre_code,$flag_numberic,"0", $redeem_id, $brnc_BranchID, $member_id, $date_redeem, $axRow['rede_AutoRedeem'], $axRow['coty_CollectionTypeID'], $axRow['rera_RewardQty_Point'], $axRow['rera_RewardQty_Stamp'], $axRow['rera_RewardQty'], $date_redeem);

					} else {

						$reason_false = 'ไม่สามารถแลกของรางวัลได้ เนื่องจากแต้มไม่เพียงพอ';
						echo '<script type="text/javascript">alert("'.$reason_false.'")</script>';
						echo '<script>window.location.href="redeem_insert.php";</script>';
						exit();
					}
				}
			}
		
		} else { # REWARD IS NOT CARD

			if ($axRow['rera_CardID'] != "") { # CHECK CARD REGISTER

				$sql_register = 'SELECT member_register_id 
									FROM mb_member_register 
									WHERE card_id IN ('.$axRow['rera_CardID'].')
									AND member_id="'.$member_id.'"
									AND flag_del=""';
				$register_id = $oDB->QueryOne($sql_register);

				if (!$register_id) { # NO CARD REGISTER

					$reason_redeem = 'ไม่สามารถแลกของรางวัลได้ เนื่องจากไม่ตรงตามเงื่อนไขการรับของรางวัล';
					echo '<script type="text/javascript">alert("'.$reason_false.'")</script>';
					echo '<script>window.location.href="redeem_insert.php";</script>';
					exit();
					
				} else { # HAVE CARD REGISTER

					$member_register_id = 0;
					$check_trans_id = "";

					$oRes = $oDB->Query($sql_register);
					while ($record = $oRes->FetchRow(DBI_ASSOC)) {

						$sql_trans = 'SELECT retr_RewardRedeemTransID
										FROM reward_redeem_trans
										WHERE rede_RewardRedeemID = "'.$redeem_id.'"
										AND mere_MemberRegisterID = "'.$record['member_register_id'].'"
										AND retr_Deleted = ""';
						$trans_id = $oDB->QueryOne($sql_trans);

						if ($trans_id) { $check_trans_id = $trans_id; } 
						else { $member_register_id = $record['member_register_id']; }
					}

					if ($member_register_id != 0) {

						if ($axRow['rewa_Limit']=="T") { # CHECK LIMIT REWARD

							$rewa_Qty_last = $axRow["rewa_Qty"] - $axRow["rera_RewardQty"];

							if ($rewa_Qty_last < 0) {

								$reason_false = 'ไม่สามารถแลกของรางวัลได้ เนื่องจากของรางวัลหมด';
								echo '<script type="text/javascript">alert("'.$reason_false.'")</script>';
								echo '<script>window.location.href="redeem_insert.php";</script>';
								exit();
							}
						}

						CutReward($rewa_Qty_last, $axRow["reward_id"]);
						InsertUseRedeem($pre_code,$flag_numberic,$member_register_id, $redeem_id, $brnc_BranchID, $member_id, $date_redeem, $axRow['rede_AutoRedeem'], $axRow['coty_CollectionTypeID'], $axRow['rera_RewardQty_Point'], $axRow['rera_RewardQty_Stamp'], $axRow['rera_RewardQty'], $date_redeem);
					}
				}

			} else { # USE POINT OR STAMP TO REDEEM

				if ($axRow['rewa_Limit']=="T") { # CHECK LIMIT REWARD

					$rewa_Qty_last = $axRow["rewa_Qty"] - $axRow["rera_RewardQty"];

					if ($rewa_Qty_last < 0) {

						$reason_false = 'ไม่สามารถแลกของรางวัลได้ เนื่องจากของรางวัลหมด';
						echo '<script type="text/javascript">alert("'.$reason_false.'")</script>';
						echo '<script>window.location.href="redeem_insert.php";</script>';
						exit();
					}
				}

				if ($axRow['rede_AutoRedeem']!='T') {

					$cut_status = CutPoint($bran_BrandID, $member_id, $axRow['TotalUse'], $axRow['coty_CollectionTypeID']);

				} else { $cut_status = "True"; }

				if ($cut_status == "True") {

					CutReward($rewa_Qty_last, $axRow["reward_id"]);
					$id_use = InsertUseRedeem($pre_code,$flag_numberic,"0", $redeem_id, $brnc_BranchID, $member_id, $date_redeem, $axRow['rede_AutoRedeem'], $axRow['coty_CollectionTypeID'], $axRow['rera_RewardQty_Point'], $axRow['rera_RewardQty_Stamp'], $axRow['rera_RewardQty'], $date_redeem);

				} else {

					$reason_false = 'ไม่สามารถแลกของรางวัลได้ เนื่องจากแต้มไม่เพียงพอ';
					echo '<script type="text/javascript">alert("'.$reason_false.'")</script>';
					echo '<script>window.location.href="redeem_insert.php";</script>';
					exit();
				}
			}
		}

		echo '<script type="text/javascript">alert("แลกของรางวัลเรียบร้อย")</script>';

		echo '<script>window.location.href="redeem.php";</script>';
		exit();

	} else {

		echo '<script type="text/javascript">alert("'.$reason_false.'")</script>';

		echo '<script>window.location.href="redeem_insert.php";</script>';
		exit();
	}
}


function CheckPointType($bran_BrandID,$member_id,$coty_CollectionTypeID="0") {

	$oDB = new DBI();

	if ($coty_CollectionTypeID == "0") {

		$strSQL  = "SELECT IF (SUM(memp_PointQty) >= SUM(memp_LastQty),
							(SUM(memp_PointQty)-SUM(memp_LastQty)),
							(SUM(memp_LastQty)-SUM(memp_PointQty))) AS Total
					FROM member_motivation_point_trans mmt
					INNER JOIN mb_member_register mmr 
					ON mmr.member_register_id = mmt.mere_MemberRegisterID
					INNER JOIN mi_brand mb 
					ON mb.brand_id = mmr.bran_BrandID
					WHERE mmr.member_id = ".$member_id."
					AND mmr.bran_BrandID = ".$bran_BrandID."
					AND mmt.memp_StatusExp ='F'";

	} else {

		$strSQL  = "SELECT IF (SUM(mems_StampQty) >= SUM(mems_LastQty),
							(SUM(mems_StampQty)-SUM(mems_LastQty)),
							(SUM(mems_LastQty)-SUM(mems_StampQty))) AS Total
					FROM member_motivation_stamp_trans mmt
					INNER JOIN mb_member_register mmr 
					ON mmr.member_register_id = mmt.mere_MemberRegisterID
					INNER JOIN mi_brand mb 
					ON mb.brand_id = mmr.bran_BrandID
					WHERE mmr.member_id = ".$member_id."
					AND coty_CollectionTypeID = ".$coty_CollectionTypeID."
					AND mmr.bran_BrandID = ".$bran_BrandID." 
					AND mmt.mems_StatusExp ='F'";
	}

	$total_have = $oDB->QueryOne($strSQL);

 	if ($total_have == "") { $total_have = 0; }

	return $total_have;
}

function RepetitionDaily($member_id,$redeem_id,$rede_Qty,$date_redeem) {

	$oDB = new DBI();

	$date = $date_redeem;
	$date .= '%';

	$sql = "SELECT COUNT(retr_RewardRedeemTransID) AS ToTalUse
			FROM reward_redeem_trans
			WHERE memb_MemberID = ".$member_id."
			AND rede_RewardRedeemID = ".$redeem_id."
			AND retr_RedeemDate LIKE '".$date."'
			AND retr_Deleted=''";
	
	$total_use = $oDB->QueryOne($sql);	

	if ($total_use < $rede_Qty) { return $rede_Qty-$total_use; } 
	else { return 0; }
}

function RepetitionWeekly($member_id,$redeem_id,$rede_Qty,$rede_QtyPerData,$date_redeem) {

	$oDB = new DBI();
	
	$status = "false";
	$date = date("Y-m-d", $date_redeem);
	$date_redeem = strtotime($date_redeem);

	$date_week = explode(',', $rede_QtyPerData);

	if (count($date_week) > 0) {
		
		foreach ($date_week as $date_select) {

			switch ($date_select) {

				case 'Sun' :
					$date_check = date("Y-m-d", strtotime("monday this week - 1 day",$date_redeem));
					break;
				case 'Mon' :
					$date_check = date("Y-m-d", strtotime("monday this week",$date_redeem));
					break;
				case 'Tue' :
					$date_check = date('Y-m-d', strtotime('tuesday this week',$date_redeem));
					break;
				case 'Wed' :
					$date_check = date('Y-m-d', strtotime('wednesday this week',$date_redeem));
					break;
				case 'Thu' :
					$date_check = date('Y-m-d', strtotime('thursday this week',$date_redeem));
					break;
				case 'Fri' :
					$date_check = date('Y-m-d', strtotime('friday this week',$date_redeem));
					break;
				case 'Sat' :
					$date_check = date('Y-m-d', strtotime('saturday this week',$date_redeem));
					break;
				default :
					$status = "false";
					$date_check = $date;
					break;
			}

			if ($date_check == $date) $status = "true";
		}
	}

	$monday = date("Y-m-d", strtotime("monday this week - 1 day",$date));
	$monday .='%'; 
	$sunday = date("Y-m-d", strtotime("sunday this week",$date));
	$sunday .='%'; 

	if ($status == 'true') {

		$sql = "SELECT COUNT(retr_RewardRedeemTransID) AS ToTalUse
				FROM reward_redeem_trans
				WHERE memb_MemberID = ".$member_id."
				AND rede_RewardRedeemID = ".$redeem_id."
				AND retr_RedeemDate BETWEEN '".$monday."' AND '".$sunday."'
				AND retr_Deleted=''";

		$total_use = $oDB->QueryOne($sql);

		if ($total_use < $rede_Qty) { $total = $rede_Qty-$total_use; } 
		else { $total = 0; }

	} else { $total = 0; }

	return $total;
}


function RepetitionMonth($member_id,$redeem_id,$rede_Qty,$rede_QtyPerData,$date_redeem) {

	$oDB = new DBI();

	$status = "false";

	$date = date('d', $date_redeem);

	$date_week = explode(',', $rede_QtyPerData);

	if (count($date_week) > 0) {
		
		foreach ($date_week as $date_select) {

			if ($date_select == $date || $date_select == "") { $status = "true"; }
		}
	}

	$firstday = date('Y-m-d', strtotime('first day of this month',$date_redeem));
	$firstday .='%'; 
	$lastday = date('Y-m-d', strtotime('last day of this month',$date_redeem));
	$lastday .='%'; 

	if ($status == 'true') {

		$sql = "SELECT COUNT(retr_RewardRedeemTransID) AS ToTalUse
				FROM reward_redeem_trans
				WHERE memb_MemberID = ".$member_id."
				AND rede_RewardRedeemID = ".$redeem_id."
				AND retr_RedeemDate BETWEEN '".$firstday."' AND '".$lastday."'
				AND retr_Deleted=''";

		$total_use = $oDB->QueryOne($sql);	
		
		if ($total_use < $rede_Qty) { $total = $rede_Qty-$total_use; } 
		else { $total = 0; }

	} else { $total = 0; }

	return $total;
}


function RepetitionNotSpecific($member_id,$redeem_id,$rede_Qty) {

	$oDB = new DBI();

	$sql = "SELECT COUNT(retr_RewardRedeemTransID) AS ToTalUse
			FROM reward_redeem_trans
			WHERE memb_MemberID = ".$member_id."
			AND rede_RewardRedeemID = ".$redeem_id."
			AND retr_Deleted=''";

	$total_use = $oDB->QueryOne($sql);

	if ($total_use < $rede_Qty) { $total = $rede_Qty - $total_use; } 
	else { $total = 0; }

	return $total;
}


function RegisterCard($card_id,$member_id,$date_redeem) {

	$oDB = new DBI();

	$strSQLMember = "SELECT email, mobile FROM mb_member WHERE member_id = '".$member_id."'";

  	$oRes = $oDB->Query($strSQLMember);
    $recordMember = $oRes->FetchRow(DBI_ASSOC);

	$moblie = $recordMember["mobile"];
	$email = $recordMember["email"];

	$strSQL  = "SELECT period_type, 
						period_type_other, 
						date_expired,
						member_price,
						member_vat,
						member_amount,
						brand_id,
						flag_autorenew
				FROM mi_card
				WHERE card_id = '".$card_id."'";

  	$oRes = $oDB->Query($strSQL);
    $record = $oRes->FetchRow(DBI_ASSOC);

  	$period_type 		= $record["period_type"];
  	$brand_id 			= $record["brand_id"];
  	$member_price 		= $record["member_price"];
  	$member_amount 		= $record["member_amount"];
  	$member_vat 		= $record["member_vat"];
  	$period_type_other  = $record["period_type_other"];
  	$date_expired 		= $record["date_expired"];
  	$flag_autorenew 	= $record["flag_autorenew"];

  	switch ($period_type) {
    	case '1':
      		$date_expire = DateTime::createFromFormat('Y-m-d', $date_expired)->format('Y-m-d');
      		break;
    	case '2':
      		$date_expire = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime($date_redeem)) . " + ".$period_type_other." Month"));
      		break;
    	case '3':
      		$date_expire = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime($date_redeem)) . " + 1 Year"));
      		break;
    	case '4':
      		$date_expire = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime($date_redeem))));
     		break;
    	default:
        	return false;
  	}

  	$date = date("Y-m-d H:i:s");

  	$SQLInserCard = "INSERT INTO mb_member_register(
  								member_id, 
  								card_id,
  								date_create,
  								date_expire,
  								email,
  								tel,
  								platform,
  								payr_TransferStatus,
  								status,
  								bran_BrandID,
  								payr_PaymentType,
  								flag_autorenew,
  								period_type,
  								period_type_other,
  								payr_CreatedBy)
  						VALUES ('".$member_id."',
  								'".$card_id."',
  								'".$date."',
  								'".$date_expire."',
  								'".$email."',
  								'".$moblie."',
  								'Reward',
  								'Wait',
  								'Complete',
  								'".$brand_id."',
  								'Reward',
  								'".$flag_autorenew."',
  								'".$period_type."',
  								'".$period_type_other."',
  								'".$_SESSION['UID']."')";

  	$oDB->QueryOne($SQLInserCard);
}


function InsertUseRedeem($pre_code,$flag_numberic,$mere_MemberRegisterID='0',$rede_RewardRedeemID,$branch_id,$member_id,$date,$rede_AutoRedeem,$coty_CollectionTypeID,$rera_RewardQty_Point,$rera_RewardQty_Stamp,$rera_RewardQty,$date_redeem) {

	$oDB = new DBI();

	if ($pre_code) {

		$length = 6;

		if ($flag_numberic=='T') {

			$randomString = substr(str_shuffle("0123456789"), 0, $length);
			$id_use = $pre_code.$randomString;

		} else {

			$randomString = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
			$id_use = $pre_code.$randomString;
		}

	} else {

		$length = 7;
		$randomString = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
		$id_use = 'RE'.$randomString;
	}

    $flag = true;         
        
    while ($flag) {
            
        $sql = 'SELECT retr_RewardRedeemTransID 
        		FROM reward_redeem_trans 
        		WHERE retr_RewardRedeemTransID = "'.$id_use.'" 
        		LIMIT 1';
        
        $id = $oDB->QueryOne($sql);
        
        if (!$id) {

            $flag = false;

        } else {

			if ($pre_code) {

				$length = 6;

				if ($flag_numberic=='T') {

					$randomString = substr(str_shuffle("0123456789"), 0, $length);
					$id_use = $pre_code.$randomString;

				} else {

					$randomString = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
					$id_use = $pre_code.$randomString;
				}

			} else {

				$length = 7;
				$randomString = substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
				$id_use = 'RE'.$randomString;
			}
        }
    }

    $sql = "INSERT INTO reward_redeem_trans 
        					(retr_RewardRedeemTransID, 
        					rede_RewardRedeemID,
        					mere_MemberRegisterID,
        					brnc_BranchID,
        					retr_RedeemQty,
        					retr_Status,
        					memb_MemberID,
        					retr_CreatedDate,
        					retr_UpdatedDate,
        					retr_CreatedBy,
        					retr_UpdatedBy,
        					retr_RedeemDate,
        					rede_AutoRedeem,
        					coty_CollectionTypeID,
        					rera_RewardQty_Point,
        					rera_RewardQty_Stamp,
        					retr_Platform)
        			VALUES ('".$id_use."', 
        					'".$rede_RewardRedeemID."',
        					'".$mere_MemberRegisterID."',
        					'".$branch_id."',
        					'".$rera_RewardQty."',
        					'Complete',
        					'".$member_id."',
        					'".date("Y-m-d H:i:s")."',
        					'".date("Y-m-d H:i:s")."',
        					'".$_SESSION['UID']."',
        					'".$_SESSION['UID']."',
        					'".$date_redeem."',
        					'".$rede_AutoRedeem."',
        					'".$coty_CollectionTypeID."',
        					'".$rera_RewardQty_Point."',
        					'".$rera_RewardQty_Stamp."',
        					'Insert')";

    $oDB->QueryOne($sql);

	return $id_use;
}


function CutPoint($brand_id,$member_id,$rera_RewardQty,$coty_CollectionTypeID) {

	$oDB = new DBI();
	
	if ($coty_CollectionTypeID == "0") { # POINT
        
        $strSQL  = "SELECT IF(mmt.memp_ExpiredDate = '0000-00-00', CONCAT('9999-12-31'),mmt.memp_ExpiredDate) as Date_order,
        					mmt.memp_PointQty Qty,
        					mmt.memp_LastQty LastQty,
        					memp_MemberMotivationPointID,
        					mmr.member_register_id,
        					mmt.memp_ExpiredDate
        			FROM member_motivation_point_trans mmt
        			INNER JOIN mb_member_register mmr 
        			ON mmr.member_register_id = mmt.mere_MemberRegisterID
        			WHERE mmr.member_id = ".$member_id." 
        			AND mmr.bran_BrandID = ".$brand_id."
        			AND mmt.memp_StatusExp = 'F' 
        			ORDER BY Date_order ASC";

    } else { # STAMP

        $strSQL = "SELECT IF(mms.mems_ExpiredDate = '0000-00-00',CONCAT('9999-12-31'),mms.mems_ExpiredDate) as Date_order,
        				mms.mems_StampQty Qty,
        				mms.mems_LastQty LastQty,
        				mems_MemberMotivationStampTransID memp_MemberMotivationPointID,
        				mmr.member_register_id,
        				mms.mems_ExpiredDate ExpiredDate
        			FROM member_motivation_stamp_trans mms
        			INNER JOIN mb_member_register mmr 
        			ON mmr.member_register_id = mms.mere_MemberRegisterID
        			WHERE mmr.member_id = ".$member_id." 
        			AND mmr.bran_BrandID = ".$brand_id."
        			AND mms.coty_CollectionTypeID = ".$coty_CollectionTypeID."
        			AND mms.mems_StatusExp = 'F' 
        			ORDER BY Date_order ASC";
    }

  	$oRes = $oDB->Query($strSQL);

    while ($record = $oRes->FetchRow(DBI_ASSOC)) {

    	$LastQty = $record["LastQty"];
		$Qty = $record["Qty"];

        $memp_MemberMotivationPointID = $record["memp_MemberMotivationPointID"];

        if ($rera_RewardQty != 0) {

            $balance = balance($Qty,$LastQty,$rera_RewardQty);
            $rera_RewardQty = $balance["rera_RewardQty"];

          	Update_Motivation($member_id,$memp_MemberMotivationPointID,$balance["last_qty"],$coty_CollectionTypeID);
        }
    }

    if ($rera_RewardQty == 0) {

        return "True";

    } else {

        return "False";
    }
}


function Update_Motivation($member_id,$memp_MemberMotivationPointID,$balance,$coty_CollectionTypeID) {

	$oDB = new DBI();
  	$date = date("Y-m-d");

  	if ($coty_CollectionTypeID == "0") {

	  	$strSQLUpdate = "UPDATE member_motivation_point_trans 
	  						SET memp_LastQty = ".$balance."+memp_LastQty ,
	  							memp_UpdatedDate = '".$date."'
	  						WHERE memp_MemberMotivationPointID = '".$memp_MemberMotivationPointID."'";

	} else {

	    $strSQLUpdate = "UPDATE member_motivation_stamp_trans 
	    					SET mems_LastQty = ".$balance."+mems_LastQty,
	    						mems_UpdatedDate = '".$date."'
	    					WHERE mems_MemberMotivationStampTransID = '".$memp_MemberMotivationPointID."'";
	}

	$oDB->QueryOne($strSQLUpdate);
}


function balance($Qty,$LastQty,$rera_RewardQty) {

	$balance = $Qty - $LastQty;
	$balance_cut = $balance - $rera_RewardQty;

	if ($balance_cut > 0) {

		$balance = $balance - $balance_cut;
		$rera_RewardQty = 0;

	} else {

		$rera_RewardQty = str_replace("-","",$balance_cut);
	}

	return array("last_qty" => $balance,"rera_RewardQty" => $rera_RewardQty);
}


function CutReward($rewa_Qty_last,$rewa_RewardID) {

	$oDB = new DBI();

  	$date = date("Y-m-d");

	$strSQL  = "UPDATE reward
				SET rewa_Qty = ".$rewa_Qty_last.", 
					rewa_UpdatedDate = '".$date."'
				WHERE rewa_RewardID = '".$rewa_RewardID."'";

	$oDB->QueryOne($strSQL);
}




#  branch dropdownlist

$where_brand = '';

if($_SESSION['user_type_id_ses']>1){

	if ($_SESSION['user_type_id_ses']==3) {

		$where_brand = ' branch_id="'.$_SESSION['user_branch_id'].'"';

	} else {

		$where_brand = ' brand_id="'.$_SESSION['user_brand_id'].'" AND flag_status="1"';
	}
}

$as_branch_id = dropdownlist_from_table($oDB,'mi_branch','branch_id','name',$where_brand,' ORDER BY name ASC');

$oTmp->assign('branch', $as_branch_id);


#  brand dropdownlist

if($_SESSION['user_type_id_ses']>1){

	$where_brand = ' AND brand_id="'.$_SESSION['user_brand_id'].'" ';
}

$as_brand_id = dropdownlist_from_table($oDB,'mi_brand','brand_id','name','flag_del=0 '.$where_brand,' ORDER BY name ASC');

$oTmp->assign('brand', $as_brand_id);




$oTmp->assign('data', $asData);

$oTmp->assign('act', 'save');

$oTmp->assign('is_menu', 'is_transaction');

$oTmp->assign('content_file', 'transaction/redeem_insert.htm');

$oTmp->display('layout/template.html');



//========================================//

$oDB->Close();

if ($bDebug) {

	echo($oErr->GetAll());

}

//========================================//

?>
