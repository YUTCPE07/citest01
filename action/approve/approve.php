<?php

header('Content-Type:text/html; charset=UTF-8');

//===============================================//

ini_set("display_errors", 1);
error_reporting(1);

//===============IMPORT FILE=====================//

include('../../include/common.php');
include('../../include/common_check.php');
include('../../lib/function_normal.php');
require_once '../../lib/phpmailer/class.phpmailer.php';
require_once '../../lib/phpmailer/PHPMailerAutoload.php';

//============FUNCTION SEND EMAIL================//

function sendAccountEmail($email, $title, $subject, $alt, $body) {
    try {
        $mail = new PHPMailer();

        $mail->CharSet = 'utf-8';
        $mail->Debugoutput = 'html';
        $mail->IsSMTP();
        $mail->Host = 'mail.memberin.com';
        $mail->SMTPAuth = true;
        $mail->SMTPDebug = 0;
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];
        $mail->Username = 'noreply@memberin.com';
        $mail->Password = 'm3mb3rIN@2016';
        $mail->SMTPSecure = '25';

        $mail->SetFrom('noreply@memberin.com', 'MemberIn');
        $mail->Subject = $subject;
        $mail->AltBody = $alt;

        $mail->isHTML(true);
        $mail->MsgHTML($body);

        $address = $email;

        $mail->AddAddress($address, $title);
        $mail->Send();

        return json_encode(['isSuccess' => true]);
    } catch (phpmailerException $e) {
        return json_encode(['isSuccess' => false, 'message' => $e->errorMessage()]);
    } catch (Exception $e) {
        return json_encode(['isSuccess' => false, 'message' => $e->errorMessage()]);
    }
}

//==========END FUNCTION SEND EMAIL======================//

$oTmp = new TemplateEngine();
$oDB = new DBI();

if ($bDebug) {
    $oErr = new Tracker();
    $oDB->SetTracker($oErr);
}

//=========================================================//

if ($_SESSION['role_action']['top']['view'] != 1) {
    echo "<script> history.back(); </script>";
    exit();
}

//===========================================================//

$time_insert = date("Y-m-d H:i:s");

$data = $_POST['approve_status'];

if ($data == "app_card") {

    foreach ($_REQUEST['card_id'] as $id) {

        $do_sql_upload = "UPDATE mi_card SET flag_approve='T', date_update='" . $time_insert . "' WHERE card_id='" . $id . "'";
        $oDB->QueryOne($do_sql_upload);
    }

    echo '<script>window.location.href="approve.php";</script>';

} else if ($data == "un_card") {

    foreach ($_REQUEST['card_id'] as $id) {

        $do_sql_upload = "UPDATE mi_card SET image='', flag_status='2', flag_approve='', date_update='" . $time_insert . "' WHERE card_id='" . $id . "' ";

        $oDB->QueryOne($do_sql_upload);
    }

    echo '<script>window.location.href="approve.php";</script>';

} else if ($data == "app_priv") {

    foreach ($_REQUEST['priv_id'] as $id) {

        $do_sql_upload = "UPDATE privilege SET priv_Approve='T', priv_UpdatedDate='" . $time_insert . "', priv_UpdatedBy='" . $_SESSION['UID'] . "' WHERE priv_PrivilegeID='" . $id . "'";

        $oDB->QueryOne($do_sql_upload);
    }

    echo '<script>window.location.href="approve.php";</script>';

} else if ($data == "un_priv") {

    foreach ($_REQUEST['priv_id'] as $id) {

        unlink_file($oDB, 'privilege', 'priv_Image', 'priv_PrivilegeID', $id, '../../upload/' . $axRow['priv_ImagePath'], $axRow['priv_Image']);

        $do_sql_upload = "UPDATE privilege SET priv_Approve='', priv_Image='', priv_Status='Pending', priv_UpdatedDate='" . $time_insert . "', priv_UpdatedBy='" . $_SESSION['UID'] . "' WHERE priv_PrivilegeID='" . $id . "'";

        $oDB->QueryOne($do_sql_upload);
    }

    echo '<script>window.location.href="approve.php";</script>';

} else if ($data == "app_coup") {

    foreach ($_REQUEST['coup_id'] as $id) {

        $do_sql_upload = "UPDATE coupon SET coup_Approve='T', coup_UpdatedDate='" . $time_insert . "', coup_UpdatedBy='" . $_SESSION['UID'] . "' WHERE coup_CouponID='" . $id . "'";

        $oDB->QueryOne($do_sql_upload);
    }

    echo '<script>window.location.href="approve.php";</script>';

} else if ($data == "un_coup") {

    foreach ($_REQUEST['coup_id'] as $id) {

        unlink_file($oDB, 'coupon', 'coup_Image', 'coup_CouponID', $id, '../../upload/' . $axRow['coup_ImagePath'], $axRow['coup_Image']);

        $do_sql_upload = "UPDATE coupon SET coup_Approve='', coup_Status='Pending', coup_UpdatedDate='" . $time_insert . "', coup_UpdatedBy='" . $_SESSION['UID'] . "' WHERE coup_CouponID='" . $id . "' ";

        $oDB->QueryOne($do_sql_upload);
    }

    echo '<script>window.location.href="approve.php";</script>';

} else if ($data == "app_acti") {

    foreach ($_REQUEST['acti_id'] as $id) {

        $do_sql_upload = "UPDATE activity SET acti_Approve='T',acti_UpdatedDate='" . $time_insert . "', acti_UpdatedBy='" . $_SESSION['UID'] . "' WHERE acti_ActivityID='" . $id . "'";

        $oDB->QueryOne($do_sql_upload);
    }

    echo '<script>window.location.href="approve.php";</script>';

} else if ($data == "un_acti") {

    foreach ($_REQUEST['acti_id'] as $id) {

        unlink_file($oDB, 'activity', 'acti_Image', 'acti_ActivityID', $id, '../../upload/' . $axRow['acti_ImagePath'], $axRow['acti_Image']);

        $do_sql_upload = "UPDATE activity SET acti_Image='',acti_Status='Pending',acti_Approve='',acti_UpdatedDate='" . $time_insert . "', acti_UpdatedBy='" . $_SESSION['UID'] . "'  WHERE acti_ActivityID='" . $id . "' ";

        $oDB->QueryOne($do_sql_upload);
    }

    echo '<script>window.location.href="approve.php";</script>';

} else if ($data == "app_hbd") {

    foreach ($_REQUEST['hbd_id'] as $id) {

        $do_sql_upload = "UPDATE coupon SET coup_Approve='T',coup_UpdatedDate='" . $time_insert . "', coup_UpdatedBy='" . $_SESSION['UID'] . "'WHERE coup_CouponID='" . $id . "'";

        $oDB->QueryOne($do_sql_upload);
    }

    echo '<script>window.location.href="approve.php";</script>';

} else if ($data == "un_hbd") {

    foreach ($_REQUEST['hbd_id'] as $id) {

        unlink_file($oDB, 'coupon', 'coup_Image', 'coup_CouponID', $id, '../../upload/' . $axRow['coup_ImagePath'], $axRow['coup_Image']);

        $do_sql_upload = "UPDATE coupon SET coup_Image='',coup_Status='Pending',coup_Approve='',coup_UpdatedDate='" . $time_insert . "', coup_UpdatedBy='" . $_SESSION['UID'] . "'WHERE coup_CouponID='" . $id . "' ";

        $oDB->QueryOne($do_sql_upload);
    }

    echo '<script>window.location.href="approve.php";</script>';

} else if ($data == "app_brand") {

    foreach ($_REQUEST['brand_id'] as $id) {

        $do_sql_upload = "UPDATE mi_brand SET flag_approve='T',date_update='" . $time_insert . "' WHERE brand_id='" . $id . "'";

        $companyEmail = "SELECT email FROM mi_brand WHERE mi_brand.brand_id = '" . $id . "'";
        $company = "SELECT company_name FROM mi_brand WHERE mi_brand.brand_id = '" . $id . "'";
        $username = "SELECT username FROM mi_user WHERE mi_user.brand_id = '" . $id . "'";
//        $passeord = "SELECT password FROM mi_user WHERE mi_user.brand_id = '" . $id . "'";
        $oDB->QueryOne($do_sql_upload);
        $email = $oDB->QueryOne($companyEmail);
        $companyName = $oDB->QueryOne($company);
        $userDefault = $oDB->QueryOne($username);
        $passDefault = $oDB->QueryOne($username);
    }

    $title = 'ได้รับการอนุมัติการลงทะเบียน';
    $subject = 'คำขอลงทะเบียนเพื่อขอใช้ระบบของท่านได้รับการอนุมัติแล้ว';
    $body = '<h3>เรียน' . '  ' . $companyName . '</h3>';
    $body .= '<p>ตามที่ท่านได้ลงทะเบียนทางเรา ได้พิจารณาอนุมัติให้ท่านเข้าใช้ระบบได้ด้วย</p>';
    $body .= '<p><h4>Username:' . ' ' . $userDefault . '</h4></p>';
    $body .= '<p><h4>Password:' . ' ' . $passDefault . '</h4></p>';
    $body .= '<center><a href="https://www.memberin.com/demo/action" target="_blank" rel="noopener noreferrer" style="display:block; text-align:center; height:30px; max-width:380px; line-height:31px; font-size:22px; color:#ffffff; background:#130f99; padding:10px 0; border-radius:5px; text-decoration:none; font-weight:bold">เข้าสู่ระบบได้ที่นี้</a></center>';
    $body .= '<p>หวังอย่างยิ่งว่าท่านจะได้รับความประทับใจ ทุกครั้งที่ได้เข้ามาใช้บริการ  หากติดปัญหา หรือมีข้อสงสัยกรุณาติดต่อเราได้ที่</p>';
    $body .= '<p><b>โทร. 02-061-1169</b></p>';
    $body .= '<p><b>ขอแสดงความนับถือ</b></p>';
    $body .= '<p><b>MemberIn Application Team</b></p>';

    if (sendAccountEmail($email, $title, $subject, '', $body)) {
//        echo "Email Send Success.";
    } else {
//        echo "Email Can Not Send.";
    }

    echo '<script>window.location.href="approve.php";</script>';

} else if ($data == "un_brand") {

    foreach ($_REQUEST['brand_id'] as $id) {

        $do_sql_upload = "UPDATE mi_brand, mi_user SET mi_user.flag_del = '1', mi_brand.logo_image = '',  mi_brand.flag_status = '2', mi_brand.flag_approve = '', mi_brand.date_update = '" . $time_insert . "' WHERE mi_brand.brand_id = '" . $id . "' AND mi_user.brand_id = '" . $id . "' ";

        $oDB->QueryOne($do_sql_upload);
    }

    echo '<script>window.location.href="approve.php";</script>';
}


$data_card = "";
$data_privilege = "";
$data_coupon = "";
$data_hbd = "";
$data_activity = "";
$data_brand = "";

$card_n = "1";
$priv_n = "1";
$coup_n = "1";
$hbd_n = "1";
$acti_n = "1";
$brand_n = "1";


// =============== CARD =============== //

$card = "SELECT mi_card.*, 
                mi_brand.name AS brand_name, 
                mi_brand.path_logo, 
                mi_brand.logo_image

            FROM mi_card

            LEFT JOIN mi_brand 
            ON mi_card.brand_id = mi_brand.brand_id

            WHERE mi_card.flag_approve='' 
            AND mi_card.flag_del!='1' 
            AND mi_card.image!=''

            ORDER BY mi_card.date_update DESC";

$rs_card = $oDB->Query($card);

if (!$rs_card) {

    echo "An error occurred: " . mysql_error();

} else {

    while ($axRow = $rs_card->FetchRow(DBI_ASSOC)) {

        # LOGO BRAND

        if ($axRow['logo_image'] != '') {

            $logo_brand = '<img src="../../upload/'.$axRow['path_logo'].$axRow['logo_image'].'" class="image_border" width="60" height="60"/>';

        } else {

            $logo_brand = '<img src="../../images/400x400.png" class="image_border" width="60" height="60"/>';
        }


        # CARD IMAGE

        if ($axRow['image'] != '') {

            $card_image = '<img src="../../upload/'.$axRow['path_image'].$axRow['image'].'" class="img-rounded image_border" width="128" height="80"/>';
        }

        # STATUS

        $status = '';

        if ($axRow['flag_del'] == '1') {

            $status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';
        } else {

            if ($axRow['flag_status'] == '1') {

                $status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';

            } else {

                $status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';
            }
        }

        $data_card .= '<tr>
                            <td>'.$card_n++.'</td>
                            <td style="text-align:center">
                                <input type="checkbox" class="card" name="card_id[]" value="'.$axRow['card_id'].'"></td>
                            <td style="text-align:center"><a href="../brand/brand.php">'.$logo_brand.'</a><br>
                                <span style="font-size:11px;">'.$axRow['brand_name'].'</span></td>
                            <td style="text-align:center"><a href="../card/card.php">'.$card_image.'</a></td>
                            <td>'.$axRow['name'].'</td>
                            <td style="text-align:center">'.$status.'</td>
                            <td>'.DateTime($axRow['date_update']).'</td>
		  	           </tr>';
    }
}


// =============== PRIVILEGE =============== //

$privilege = "SELECT privilege.*,
                    mi_brand.name AS brand_name,
                    mi_brand.path_logo,
                    mi_brand.logo_image

                FROM privilege

                LEFT JOIN mi_brand 
                ON privilege.bran_BrandID = mi_brand.brand_id

                WHERE privilege.priv_Approve='' 
                AND privilege.priv_Deleted!='T' 
                AND privilege.priv_Image!=''

                ORDER BY privilege.priv_UpdatedDate DESC";

$rs_privilege = $oDB->Query($privilege);

if (!$rs_privilege) {

    echo "An error occurred: " . mysql_error();

} else {

    while ($axRow = $rs_privilege->FetchRow(DBI_ASSOC)) {

        # LOGO BRAND

        if ($axRow['logo_image'] != '') {

            $logo_brand = '<img src="../../upload/' . $axRow['path_logo'] . $axRow['logo_image'] . '" class="image_border" width="60" height="60"/>';

        } else {

            $logo_brand = '<img src="../../images/400x400.png" class="image_border" width="60" height="60"/>';
        }

        # PRIVILEGE IMAGE

        if ($axRow['priv_Image'] != '') {

            $privilege_image = '<img src="../../upload/' . $axRow['priv_ImagePath'] . $axRow['priv_Image'] . '" class="image_border" width="128" height="80"/>';
        }

        # STATUS

        $status = '';

        if ($axRow['priv_Deleted'] == 'T') {

            $status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';
        } else {

            if ($axRow['priv_Status'] == 'Active') {

                $status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';

            } else {

                $status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';
            }
        }

        $data_privilege .= '<tr>
                				<td>'.$priv_n++.'</td>
                				<td style="text-align:center">
                                    <input type="checkbox" class="priv" name="priv_id[]" value="'.$axRow['priv_PrivilegeID'].'"></td>
                				<td style="text-align:center"><a href="../brand/brand.php">'.$logo_brand.'</a><br>
                				    <span style="font-size:11px;">'.$axRow['brand_name'].'</span>
                				</td>
                				<td style="text-align:center"><a href="../privilege/privilege.php">'.$privilege_image.'</a></td>
                				<td>'.$axRow['priv_Name'].'</td>
                				<td style="text-align:center">'.$status.'</td>
                				<td>'.DateTime($axRow['priv_UpdatedDate']).'</td>
                            </tr>';
    }
}


// =============== COUPON =============== //

$coupon = "SELECT coupon.*,
                mi_brand.name AS brand_name,
                mi_brand.path_logo,
                mi_brand.logo_image

		  FROM coupon

		  LEFT JOIN mi_brand 
          ON coupon.bran_BrandID = mi_brand.brand_id

		  WHERE coupon.coup_Approve='' 
          AND coupon.coup_Birthday!='T' 
          AND coupon.coup_Deleted!='T' 
          AND coupon.coup_Image!=''

		  ORDER BY coupon.coup_UpdatedDate DESC";

$rs_coupon = $oDB->Query($coupon);

if (!$rs_coupon) {

    echo "An error occurred: " . mysql_error();

} else {

    while ($axRow = $rs_coupon->FetchRow(DBI_ASSOC)) {

        # LOGO BRAND

        if ($axRow['logo_image'] != '') {

            $logo_brand = '<img src="../../upload/' . $axRow['path_logo'] . $axRow['logo_image'] . '" class="image_border" width="60" height="60"/>';

        } else {

            $logo_brand = '<img src="../../images/400x400.png" class="image_border" width="60" height="60"/>';
        }

        # COUPON IMAGE

        if ($axRow['coup_Image'] != '') {

            $coupon_image = '<img src="../../upload/' . $axRow['coup_ImagePath'] . $axRow['coup_Image'] . '" class="image_border" width="128" height="80"/>';
        }

        # STATUS

        $status = '';

        if ($axRow['coup_Deleted'] == 'T') {

            $status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

        } else {

            if ($axRow['coup_Status'] == 'Active') {

                $status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';

            } else {

                $status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';
            }
        }

        $data_coupon .= '<tr>
            				<td>'.$coup_n++.'</td>
            				<td style="text-align:center">
                                <input type="checkbox" class="coup" name="coup_id[]" value="'.$axRow['coup_CouponID'].'"></td>
            				<td style="text-align:center"><a href="../brand/brand.php">'.$logo_brand.'</a><br>
            				    <span style="font-size:11px;">'.$axRow['brand_name'].'</span>
            				</td>
            				<td style="text-align:center"><a href="../coupon/coupon.php">'.$coupon_image.'</a></td>
            				<td>'.$axRow['coup_Name'].'</td>
            				<td style="text-align:center">'.$status.'</td>
            				<td>'.DateTime($axRow['coup_UpdatedDate']).'</td>
            		  	</tr>';
    }
}



// =============== HBD =============== //

$hbd = "SELECT coupon.*,
                mi_brand.name AS brand_name,
                mi_brand.path_logo,
                mi_brand.logo_image

            FROM coupon

            LEFT JOIN mi_brand 
            ON coupon.bran_BrandID = mi_brand.brand_id

            WHERE coupon.coup_Approve='' 
            AND coupon.coup_Birthday='T' 
            AND coupon.coup_Deleted!='T' 
            AND coupon.coup_Image!=''
            ORDER BY coupon.coup_UpdatedDate DESC";

$rs_hbd = $oDB->Query($hbd);

if (!$rs_hbd) {

    echo "An error occurred: " . mysql_error();

} else {

    while ($axRow = $rs_hbd->FetchRow(DBI_ASSOC)) {

        # LOGO BRAND

        if ($axRow['logo_image'] != '') {

            $logo_brand = '<img src="../../upload/' . $axRow['path_logo'] . $axRow['logo_image'] . '" class="image_border" width="60" height="60"/>';

        } else {

            $logo_brand = '<img src="../../images/400x400.png" class="image_border" width="60" height="60"/>';
        }

        # HBD IMAGE

        if ($axRow['coup_Image'] != '') {

            $coupon_image = '<img src="../../upload/' . $axRow['coup_ImagePath'] . $axRow['coup_Image'] . '" class="image_border" width="128" height="80"/>';
        }

        # STATUS

        $status = '';

        if ($axRow['coup_Deleted'] == 'T') {

            $status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

        } else {

            if ($axRow['coup_Status'] == 'Active') {

                $status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';

            } else {

                $status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';
            }
        }

        $data_hbd .= '<tr>
            			<td>'.$hbd_n++.'</td>
            			<td style="text-align:center"><input type="checkbox" class="hbd" name="hbd_id[]" value="'.$axRow['coup_CouponID'].'"></td>
            			<td style="text-align:center"><a href="../brand/brand.php">'.$logo_brand.'</a><br>
            			     <span style="font-size:11px;">'.$axRow['brand_name'].'</span>
            			</td>
            			<td style="text-align:center"><a href="../coupon/birthday.php">'.$coupon_image.'</a></td>
            			<td>'.$axRow['coup_Name'].'</td>
            			<td style="text-align:center">'.$status.'</td>
            			<td>'.DateTime($axRow['coup_UpdatedDate']).'</td>
                    </tr>';
    }
}


// =============== ACTIVITY =============== //

$activity = "SELECT activity.*,
                mi_brand.name AS brand_name,
                mi_brand.path_logo,
                mi_brand.logo_image

                FROM activity

                LEFT JOIN mi_brand 
                ON activity.bran_BrandID = mi_brand.brand_id

                WHERE activity.acti_Approve='' 
                AND activity.acti_Deleted!='T' 
                AND activity.acti_Image!=''
                ORDER BY activity.acti_UpdatedDate DESC";

$rs_activity = $oDB->Query($activity);

if (!$rs_activity) {

    echo "An error occurred: " . mysql_error();

} else {

    while ($axRow = $rs_activity->FetchRow(DBI_ASSOC)) {

        # LOGO BRAND

        if ($axRow['logo_image'] != '') {

            $logo_brand = '<img src="../../upload/' . $axRow['path_logo'] . $axRow['logo_image'] . '" class="image_border" width="60" height="60"/>';

        } else {

            $logo_brand = '<img src="../../images/400x400.png" class="image_border" width="60" height="60"/>';
        }

        # ACTIVITY IMAGE

        if ($axRow['acti_Image'] != '') {

            $activity_image = '<img src="../../upload/' . $axRow['acti_ImagePath'] . $axRow['acti_Image'] . '" class="image_border" width="128" height="80"/>';
        }

        # STATUS

        $status = '';

        if ($axRow['acti_Deleted'] == 'T') {

            $status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

        } else {

            if ($axRow['acti_Status'] == 'Active') {

                $status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';

            } else {

                $status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';
            }
        }

        $data_activity .= '<tr>
                				<td>'.$acti_n++.'</td>
                				<td style="text-align:center">
                                    <input type="checkbox" class="acti" name="acti_id[]" value="'.$axRow['acti_ActivityID'].'"></td>
                				<td style="text-align:center"><a href="../brand/brand.php">'.$logo_brand.'</a><br>
                				<span style="font-size:11px;">'.$axRow['brand_name'].'</span>
                				</td>
                				<td style="text-align:center"><a href="../activity/activity.php">'.$activity_image.'</a></td>
                				<td>'.$axRow['acti_Name'].'</td>
                				<td style="text-align:center">'.$status.'</td>
                				<td>'.DateTime($axRow['acti_UpdatedDate']).'</td>
                            </tr>';
    }
}



// =============== BRAND =============== //

$brand = "SELECT mi_brand.*, 
                mi_brand.brand_id, 
                mi_brand.name AS brand_name, 
                mi_brand.path_logo, 
                mi_brand.logo_image, 
                mi_brand.date_update,
                categoryBrand.name AS category_brand, 
                brandType.name_en AS type_brand,
                 master.name AS company_type, 
                 userType.name AS user_type

        FROM mi_brand

        LEFT JOIN mi_category_brand AS categoryBrand 
        ON categoryBrand.category_brand_id = mi_brand.category_brand

        LEFT JOIN mi_brand_type AS brandType 
        ON brandType.brand_type_id = mi_brand.type_brand 

        LEFT JOIN mi_master AS master 
        ON master.value = mi_brand.company_type

        LEFT JOIN mi_user AS user 
        ON user.user_id = mi_brand.update_by 

        LEFT JOIN mi_user_type AS userType 
        ON user.user_type_id = userType.user_type_id 

        WHERE mi_brand.flag_approve=''
        GROUP BY mi_brand.brand_id
        ORDER BY mi_brand.date_update DESC";

$rs_brand = $oDB->Query($brand);

if (!$rs_brand) {

    echo "An error occurred: " . mysql_error();

} else {

    while ($axRow = $rs_brand->FetchRow(DBI_ASSOC)) {

        # LOGO BRAND

        if ($axRow['logo_image'] != '') {

            $logo_brand = '<img src="../../upload/' . $axRow['path_logo'] . $axRow['logo_image'] . '" class="image_border" width="128" height="80"/>';

            $logo_view = '<img src="../../upload/' . $axRow['path_logo'] . $axRow['logo_image'] . '" class="image_border" width="150" height="150"/>';

        } else {

            $logo_brand = '<img src="../../images/400x400.png" width="128" height="80"/>';

            $logo_view = '<img src="../../images/400x400.png" width="150" height="150"/>';
        }

        if ($axRow['cover'] != '') {

            $cover_brand = '<img src="../../upload/' . $axRow['path_cover'] . $axRow['cover'] . '" class="image_border" height="150"/>';

        } else {

            if ($axRow['earn_attention'] == 'Yes') {

                $cover_brand = '';

            } else {

                $cover_brand = '<img src="../../images/img_size.jpg" height="150"/>';
            }
        }

        # STATUS

        $status = '';

        if ($axRow['flag_del'] == '1') {

            $status = '<button style="width:100px;" class="form-control text-md status_inactive" disabled>Inactive</button>';

        } else {

            if ($axRow['flag_status'] == '1') {

                $status = '<button style="width:60px;" class="form-control text-md status_active" name="active_status">On</button>';

            } else {
                
                $status = '<button style="width:60px;" class="form-control text-md status_pending" name="pending_status">Off</button>';
            }
        }

        //=========== DATA ===========//

        if ($axRow['tax_type'] == 1) { $axRow['tax_type'] = 'VAT. Registration';
        } else if ($axRow['tax_type'] == 2) { $axRow['tax_type'] = 'VAT. Exemption';
        } else { $axRow['tax_type'] = '-'; }

        if ($axRow['tax_id'] == '') {  $axRow['tax_id'] = '-'; }

        if ($axRow['tax_issue_by'] == '') { $axRow['tax_issue_by'] = '-'; }

        if ($axRow['tax_issue_date'] == '0000-00-00 00:00:00') { $axRow['tax_issue_date'] = '-';
        } else { $axRow['tax_issue_date'] = DateOnly($axRow['tax_issue_date']); }

        if ($axRow['phone'] == '') { $axRow['phone'] = '-'; }

        if ($axRow['mobile'] == '') { $axRow['mobile'] = '-'; }

        if ($axRow['fax'] == '') { $axRow['fax'] = '-'; }

        if ($axRow['website'] == '') { $axRow['website'] = '-'; }

        if ($axRow['facebook_url'] == '') { $axRow['facebook_url'] = '-'; }

        if ($axRow['line_id'] == '') { $axRow['line_id'] = '-'; }

        if ($axRow['instragram'] == '') { $axRow['instragram'] = '-'; }

        if ($axRow['tweeter'] == '') { $axRow['tweeter'] = '-'; }

        if ($axRow['slogan'] == '') { $axRow['slogan'] = '-'; }

        if ($axRow['signature_info'] == '') { $axRow['signature_info'] = '-'; }

        if ($axRow['greeting_message'] == '') { $axRow['greeting_message'] = '-'; }

        $sql_price = 'SELECT name FROM mi_master WHERE type="price_lange_type" AND value="' . $axRow['price_range_type'] . '"';

        $price_range_type = $oDB->QueryOne($sql_price);

        if ($price_range_type == '') { $price_range_type = '-'; }

        if ($axRow['special_for_group'] == '') { $axRow['special_for_group'] = '<span class="glyphicon glyphicon-unchecked"></span>';
        } else { $axRow['special_for_group'] = '<span class="glyphicon glyphicon-check"></span>'; }

        if ($axRow['special_for_children'] == '') { $axRow['special_for_children'] = '<span class="glyphicon glyphicon-unchecked"></span>';
        } else { $axRow['special_for_children'] = '<span class="glyphicon glyphicon-check"></span>';  }

        if ($axRow['other'] == '') { $axRow['other'] = '';
        } else { $axRow['other'] = '<span class="glyphicon glyphicon-check"></span> ' . $axRow['other']; }

        $special_type = $axRow['special_for_group'] . ' Group<br>';
        $special_type .= $axRow['special_for_children'] . ' Children<br>';
        $special_type .= $axRow['other'];

        if ($axRow['text_color'] == 'white') { $axRow['text_color'] = 'F2F2F2';
        } else { $axRow['text_color'] = '111111'; }

        if ($axRow['code_color'] == 'FFFFFF') { $axRow['code_color'] = 'F2F2F2'; }

        if ($axRow['flag_hidden'] == 'No') { $axRow['flag_hidden'] = '<span class="glyphicon glyphicon-eye-open"></span>';
        } else { $axRow['flag_hidden'] = '<span class="glyphicon glyphicon-eye-close"></span>'; }

        $view = '<button type="button" class="btn btn-default btn-sm" data-toggle="modal" data-target="#View'.$axRow['brand_id'].'"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></button>
			         <div class="modal fade" id="View'.$axRow['brand_id'].'" tabindex="-1" role="dialog" aria-labelledby="ViewDataLabel">
                        <div class="modal-dialog" role="document">
				            <div class="modal-content">
                                <div class="modal-body" align="left">
					                <span style="font-size:16px">'.$axRow['flag_hidden'].' &nbsp; <b>'.$axRow['name'].'</b></span>
					                <div style="float:right">
                                        <span class="glyphicon glyphicon-stop" style="font-size:25px;color:#'.$axRow['code_color'].'"></span>
                                        <span class="glyphicon glyphicon-stop" style="font-size:25px;color:#'.$axRow['text_color'].'"></span>
					                </div>
					                <hr>
					                <center>
                                    '.$logo_view.' '.$cover_brand.'<br><br>
                                    <span style="font-size:12px">
					                   <ul id="tapMenu" class="nav nav-tabs">
					                        <li class="active" style="width:20%">
                                            <a data-toggle="tab" href="#basic' . $axRow['brand_id'] . '">
                                                <center><b>Basic</b></center></a>
					                        </li>
                					        <li style="width:20%">
                					            <a data-toggle="tab" href="#profile' . $axRow['brand_id'] . '">
                					            <center><b>Profile</b></center></a>
                					        </li>
                					        <li style="width:20%">
                					            <a data-toggle="tab" href="#social' . $axRow['brand_id'] . '">
                					            <center><b>Social</b></center></a>
                					        </li>
                					        <li style="width:20%">
                					            <a data-toggle="tab" href="#note' . $axRow['brand_id'] . '">
                                                                    <center><b>Note</b></center></a>
                					        </li>
                					        <li style="width:20%">
                					            <a data-toggle="tab" href="#special' . $axRow['brand_id'] . '">
                					            <center><b>Special Info.</b></center></a>
                					        </li>
                					    </ul>
                					</span>
                					<div class="tab-content">
					                   <div id="basic'.$axRow['brand_id'].'" class="tab-pane active"><br>
                                            <table width="80%" class="myPopup">
							                     <tr>
                                                    <td style="text-align:right" width="45%">Brand Name</td>
                                                    <td style="text-align:center" width="5%">:</td>
                                                    <td>' . $axRow['name'] . '</td>
                    							</tr>
                    							<tr>
                                                    <td style="text-align:right">Type</td>
                                                    <td style="text-align:center">:</td>
                                                    <td>' . $axRow['type_brand'] . '</td>
                    							</tr>
                    							<tr>
                                                    <td style="text-align:right">Category</td>
                                                    <td style="text-align:center">:</td>
                                                    <td>' . $axRow['category_brand'] . '</td>
							                     </tr>';

        if ($_SESSION['user_type_id_ses'] == 1) {

                                    $view .= '  <tr>
                                                    <td style="text-align:right">Earn Attention</td>
                                                    <td style="text-align:center">:</td>
                                                    <td>' . $axRow['earn_attention'] . '</td>
                                                </tr>';
        }

        $view .= '                          </table>
                                        </div>
                                        <div id="profile' . $axRow['brand_id'] . '" class="tab-pane"><br>
                            				<table width="100%" class="myPopup">
                                                <tr>
                                					<td style="text-align:right" width="45%">Company / Organization / Shop Name</td>
                                					<td style="text-align:center" width="5%">:</td>
                                					<td>' . $axRow['company_name'] . '</td>
                                                                    </tr>
                                                                    <tr>
                                					<td style="text-align:right">Company Type</td>
                                					<td style="text-align:center">:</td>
                                					<td>' . $axRow['company_type'] . '</td>
                                                                    </tr>
                                                                    <tr>
                                					<td style="text-align:right">Tax Type</td>
                                					<td style="text-align:center">:</td>
                                					<td>' . $axRow['tax_type'] . '</td>
                                                                    </tr>
                                                                    <tr>
                                					<td style="text-align:right">Vat</td>
                                					<td style="text-align:center">:</td>
                                					<td>' . $axRow['tax_vat'] . ' %</td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right">Tax Identification No.</td>
                                                    <td style="text-align:center">:</td>
                                                    <td>' . $axRow['tax_id'] . '</td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right">Issue By</td>
                                                    <td style="text-align:center">:</td>
                                                    <td>' . $axRow['tax_issue_by'] . '</td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right">Issue Date</td>
                                                    <td style="text-align:center">:</td>
                                                    <td>' . $axRow['tax_issue_date'] . '</td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right">Email</td>
                                                    <td style="text-align:center">:</td>
                                                    <td>' . $axRow['email'] . '</td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right">Phone</td>
                                                    <td style="text-align:center">:</td>
                                                    <td>' . $axRow['phone'] . '</td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right">Mobile</td>
                                                    <td style="text-align:center">:</td>
                                                    <td>' . $axRow['mobile'] . '</td>
                                                </tr>
                                                <tr>
                                                    <td style="text-align:right">Fax</td>
                                                    <td style="text-align:center">:</td>
                                                    <td>' . $axRow['fax'] . '</td>
                                                </tr>
                                            </table>
			                             </div>
			                             <div id="social' . $axRow['brand_id'] . '" class="tab-pane"><br>
                                            <table width="80%" class="myPopup">
                                				<tr height="35px">
                                                    <td style="text-align:right" width="45%"><img src="../../images/icon/web.png" width="25" height="25" alt="Website"></td>
                                                    <td style="text-align:center" width="5%">:</td>
                                                    <td>' . $axRow['website'] . '</td>
                                				</tr>
                                				<tr height="35px">
                                                    <td style="text-align:right"><img src="../../images/icon/facebook.png" width="25" height="25" alt="Website"></td>
                                                    <td style="text-align:center">:</td>
                                                    <td>' . $axRow['facebook_url'] . '</td>
                                				</tr>
                                				<tr height="35px">
                                                    <td style="text-align:right"><img src="../../images/icon/line.png" width="25" height="25" alt="Website"></td>
                                                    <td style="text-align:center">:</td>
                                                    <td>' . $axRow['line_id'] . '</td>
                                				</tr>
                                				<tr height="35px">
                                                    <td style="text-align:right"><img src="../../images/icon/instagram.png" width="25" height="25" alt="Website"></td>
                                                    <td style="text-align:center">:</td>
                                                    <td>' . $axRow['instragram'] . '</td>
                                				</tr>
                                				<tr height="35px">
                                                    <td style="text-align:right"><img src="../../images/icon/twiter.png" width="25" height="25" alt="Website"></td>
                                                    <td style="text-align:center">:</td>
                                                    <td>' . $axRow['tweeter'] . '</td>
                                				</tr>
                                			</table>
                                		</div>
                                		<div id="note'.$axRow['brand_id'].'" class="tab-pane"><br>
                                            <table width="80%" class="myPopup">
                                                <tr>
                                                    <td style="text-align:right" valign="top" width="45%">Slogan</td>
                                                    <td style="text-align:center" width="5%" valign="top">:</td>
                                                    <td>' . nl2br($axRow['slogan']) . '</td>
                                			    </tr>
                                			    <tr>
                                                    <td style="text-align:right" valign="top">Signature Info.</td>
                                                    <td style="text-align:center" valign="top">:</td>
                                                    <td>' . nl2br($axRow['signature_info']) . '</td>
                                			    </tr>
                                            </table>
                                		</div>
                                		<div id="special'.$axRow['brand_id'].'" class="tab-pane"><br>
                                            <table width="80%" class="myPopup">
                                                <tr>
                                                    <td style="text-align:right" width="45%">Price Range Type</td>
                                                    <td style="text-align:center" width="5%" valign="top">:</td>
                                                    <td>' . $price_range_type . '</td>
                                			    </tr>
                                			    <tr>
                                                    <td style="text-align:right" valign="top">Special For Type</td>
                                                    <td style="text-align:center" valign="top">:</td>
                                                    <td>' . $special_type . '</td>
                                			    </tr>
                                            </table>
                                		</div>
                                    </div>
                                </center>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>';

        # DATA TABLE

        $data_brand .= '<tr>
                            <td>'.$brand_n++.'</td>
                            <td style="text-align:center"><input type="checkbox" class="brand" name="brand_id[]" value="'.$axRow['brand_id'].'"></td>
                            <td style="text-align:center"><a href="../brand/brand.php">'.$logo_brand.'</a></td>
                            <td>'.$axRow['brand_name'].'</td>
                            <td style="text-align:center">'.$status.'</td>
                            <td style="text-align:center"s>'.$view.'</td>
                            <td>' . DateTime($axRow['date_update']).'</td>
		  	</tr>';
    }
}


/* ================================= */

$oTmp->assign('data_card', $data_card);
$oTmp->assign('data_privilege', $data_privilege);
$oTmp->assign('data_coupon', $data_coupon);
$oTmp->assign('data_hbd', $data_hbd);
$oTmp->assign('data_activity', $data_activity);
$oTmp->assign('data_brand', $data_brand);

$oTmp->assign('is_menu', 'is_approve');
$oTmp->assign('content_file', 'approve/approve.htm');
$oTmp->display('layout/template.html');

/* ================================= */

?>