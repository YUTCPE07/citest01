<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=750">
    <link rel="shortcut icon" href="/images/icon/favicon.ico">
    <link rel="stylesheet" href="payment.css" media="screen" title="no title" charset="utf-8">
    <title>.:: MemberIn ::.</title>
  </head>
  <body>
  <center>

<?php

    // ini_set('display_errors', 1);
    // error_reporting(-1);

    include('../../lib/function_normal.php');

    // require '../../service/PDOConnect.class.php';

    require realpath(__DIR__ . '/../..') . '/include/service/PDOConnect.class.php';

    $db = PDOConnect::init();

    $receipt_no = $_GET["token"];
    $type = $_GET["type"];

    if ($type == 'Card') {

      $sql = "SELECT
              mb_member.firstname,
              mb_member.lastname,
              mb_member.home_address,
              mb_member.home_room_no,
              mb_member.home_moo,
              mb_member.home_junction,
              mb_member.home_soi,
              mb_member.home_road,
              mb_member.home_district,
              mb_member.home_province,
              mb_member.home_postcode,
              mb_member.home_sub_district,
              mb_member.mobile,
              mi_brand.name AS brand_name,
              mi_card.name AS card_name,
              mi_card.original_fee AS original_fee,
              mi_card.member_fee AS member_fee,
              COUNT(mb_member_register.member_register_id) AS qty,
              SUM(mb_member_register.total_amt) AS sum_amount,
              mb_member_register.date_create AS date_create,
              mb_member_register.total_amt AS Total,
              mb_member_register.sale_amt AS Price,
              mb_member_register.vat_amt AS Vat,
              mb_member_register.vat_rate AS vat_rate,
              mb_member_register.receipt_no AS receipt_no,
              mb_member_register.payr_PaymentType AS payment_type,
              mb_member_register.status AS payment_status,
              mb_member_register.email AS email
              FROM mb_member_register
              INNER JOIN mi_card
              ON  mi_card.card_id = mb_member_register.card_id
              INNER JOIN mb_member
              ON mb_member.member_id = mb_member_register.member_id
              INNER JOIN mi_brand
              ON mi_brand.brand_id = mi_card.brand_id
              WHERE mb_member_register.receipt_no = '".$receipt_no."'
              GROUP BY mb_member_register.receipt_no";

    } else {

      $sql = "SELECT
              mb_member.firstname,
              mb_member.lastname,
              mb_member.home_address,
              mb_member.home_room_no,
              mb_member.home_moo,
              mb_member.home_junction,
              mb_member.home_soi,
              mb_member.home_road,
              mb_member.home_district,
              mb_member.home_province,
              mb_member.home_postcode,
              mb_member.home_sub_district,
              mb_member.mobile,
              mi_brand.name AS brand_name,
              hilight_coupon.coup_Name AS card_name,
              hilight_coupon.coup_Price AS member_fee,
              hilight_coupon.coup_Cost AS original_fee,
              COUNT(hilight_coupon_buy.hcbu_HilightCouponBuyID) AS qty,
              SUM(hilight_coupon_buy.hcbu_TotalAmount) AS sum_amount,
              hilight_coupon_buy.hcbu_CreatedDate AS date_create,
              hilight_coupon_buy.hcbu_TotalAmount AS Total,
              hilight_coupon_buy.hcbu_SaleAmount AS Price,
              hilight_coupon_buy.hcbu_VatAmount AS Vat,
              hilight_coupon_buy.hcbu_VatRate AS vat_rate,
              hilight_coupon_buy.hcbu_ReceiptNo AS receipt_no,
              hilight_coupon_buy.hcbu_PaymentType AS payment_type,
              hilight_coupon_buy.hcbu_Status AS payment_status,
              hilight_coupon_buy.hcbu_Email AS email
              FROM hilight_coupon_buy
              INNER JOIN hilight_coupon
              ON  hilight_coupon.coup_CouponID = hilight_coupon_buy.hico_HilightCouponID
              INNER JOIN mb_member
              ON mb_member.member_id = hilight_coupon_buy.memb_MemberID
              INNER JOIN mi_brand
              ON mi_brand.brand_id = hilight_coupon.bran_BrandID
              WHERE hilight_coupon_buy.hcbu_ReceiptNo = '".$receipt_no."'
              GROUP BY hilight_coupon_buy.hcbu_ReceiptNo";
    }

    $sql_company = "SELECT * FROM company WHERE comp_ComapnyID = 1";

    $stm_company = $db->prepare($sql_company);

    $stm = $db->prepare($sql);

    $stm->bindParam(":code_use_omise",$code_use_omise);

    if ($stm->execute() && $stm_company->execute()) {

      $result = $stm->fetch(PDO::FETCH_ASSOC);
      $result_company = $stm_company->fetch(PDO::FETCH_ASSOC);
      // print_r($result);

      //  company

      $comp_Name        =  $result_company["comp_Name"];
      $comp_Address1    =  $result_company["comp_Address1"];
      $comp_Address2    =  $result_company["comp_Address2"];
      $comp_Tel         =  $result_company["comp_Tel"];
      $comp_TaxID       =  $result_company["comp_TaxID"];

      $company_address = $comp_Address1."<br>".$comp_Address2;

      //  company

      $Total                 = $result["Total"];
      $date_create           = $result["date_create"];
      $Price                 = $result["Price"];
      $email                 = $result["email"];
      $Vat                   = $result["Vat"];
      $member_name           = $result["firstname"]." ".$result["lastname"];
      $mobile                = $result["mobile"];
      $brand_name            = $result["brand_name"];
      $card_name             = $result["card_name"];
      $date_create           = $result["date_create"];
      $toPercent_vat         = $result["vat_rate"];
      $receipt_no            = $result["receipt_no"];
      $payment_type          = $result["payment_type"];
      $qty                   = $result["qty"];
      $original_fee          = $result["original_fee"];
      $member_fee            = $result["member_fee"]-$Vat;
      $sum_amount            = $result["sum_amount"];

      if ($original_fee == 0) { 

        $original_fee = $member_fee; 
        $discount = 0;
      
      } else { $discount = $result["original_fee"]-$Total; }

      

      if ($mobile=="") { $mobile = "-";  }

      $home_address1 = "";

      if ($result["home_moo"]) { $result["home_moo"] = "หมู่ ".$result["home_moo"];  }
      if ($result["home_junction"]) { $result["home_junction"] = "แยก ".$result["home_junction"];  }
      if ($result["home_soi"]) { $result["home_soi"] = "ซอย ".$result["home_soi"];  }
      if ($result["home_road"]) { $result["home_road"] = "ถนน".$result["home_road"];  }
      if ($result["home_sub_district"]) { $result["home_sub_district"] = "แขวง".$result["home_sub_district"];  }
      if ($result["home_district"]) { $result["home_district"] = "เขต".$result["home_district"];  }
      if ($result["home_postcode"]) { $result["home_postcode"] = "จังหวัด".$result["home_postcode"];  }

      if ($result["home_province"]!=0) { 

        $sql_province = "SELECT prov_Name FROM province WHERE prov_ProvinceID = ".$result["home_province"];

        $stmt = $db->prepare($sql_province);
        $stmt->execute();

        $province = $stmt->fetch(PDO::FETCH_ASSOC);

        $result["home_province"] = "จังหวัด".$province['prov_Name'];  

      } else {  $result["home_province"] = ""; }

      if ($result["home_moo"] || $result["home_junction"] || $result["home_soi"] || $result["home_soi"] || $result["home_road"]) {

        $home_address1 = $result["home_address"]." ".$result["home_room_no"]." ".$result["home_moo"]." ".$result["home_junction"]." ".$result["home_soi"]." ".$result["home_road"]." ".$result["home_sub_district"]." ".$result["home_district"]." ".$result["home_province"]." ".$result["home_postcode"];
     
      }
    }


$HTML = '<table width="800" style="border: 1px solid #000000;padding: 30px 30px 10px 30px;line-height: 30px;">
          <tr>
            <td width="50%" valign="top">
              <div style="width:350px;display: block;position: relative;">
                <div style="float: left;position: relative;background-color: #FFFFFF;padding-right: 5px;padding-top:40px">
                    <span class="" >'.$comp_Name.'</span>
                </div>
              </div>
              <div style="clear: both;"></div>
              <div style="width:350px;display: block;position: relative;">
                <div style="float: left;position: relative;background-color: #FFFFFF;padding-right: 5px;">
                  <span>'.$company_address.'</span>
                </div>
              </div>
              <div style="width:350px;display: block;position: relative;">
                <div style="float: left;position: relative;background-color: #FFFFFF;padding-right: 5px;">
                  เบอร์โทรศัพท์ / Contact :
                </div>
                <div style="float: left;position: relative;background-color: #FFFFFF;padding-right: 5px;">
                    <span>'.$comp_Tel.'</span>
                </div>
              </div>
              <div style="width:350px;display: block;position: relative;">
                <div style="float: left;position: relative;background-color: #FFFFFF;padding-right: 5px;">
                  เลขประจำตัวผู้เสียภาษี :
                </div>
                <div style="float: left;position: relative;background-color: #FFFFFF;padding-right: 5px;">
                    <span>'.$comp_TaxID.'</span>
                </div>
              </div>
            </td>
            <td width="50%" align="right" valign="top">
              <img src="img/logo.png" width="200" alt="" /><br>
              <div style="width:300px;display: block;position: relative;">
                <div style="float: right;position: relative;background-color: #FFFFFF;padding-right: 5px;">
                วันที่ / Date :   <span>'.DateOnly($date_create).'</span>
                </div>
                <!-- <div class="dotwidth"></div> -->
              </div>
              <div style="clear: both;"></div>
              <div style="width:350px;display: block;position: relative;">
                <div style="float: right;position: relative;background-color: #FFFFFF;padding-right: 5px;">
                เลขที่ใบเสร็จรับเงิน / Receipt No. :    <span>'.$receipt_no.'</span>
                </div>
                <!-- <div class="dotwidth"></div> -->
              </div>
            </td>
          </tr>
          <tr>
            <td colspan="2" align="center" style="padding-top:20px">
              <span style="font-size: 25px;" >ใบเสร็จรับเงิน / RECEIPT</span>
            </td>
          </tr>
          <tr>
            <td>

              <div style="width:350px;display: block;position: relative;">
                <div style="float: left;position: relative;background-color: #FFFFFF;padding-right: 5px;">
                    <span class="" >'.$member_name.'</span>
                </div>
              </div>
              <div style="clear: both;"></div>
              <div style="width:350px;display: block;position: relative;">
                <div style="float: left;position: relative;background-color: #FFFFFF;padding-right: 5px;">
                  <span>'.$home_address1.'</span>
                </div>
                  <div style="clear: both;"></div>
              </div>
              <div style="width:350px;display: block;position: relative;">
                <div style="float: left;position: relative;background-color: #FFFFFF;padding-right: 5px;">
                    เบอร์โทรศัพท์ <span>'.$mobile.'</span>
                </div>
              </div>

            </td>
          </tr>
          <tr>
            <td colspan="2" align="center">
            </td>
          </tr>
          <tr>
            <td colspan="2" >
              <table width="100%"  style="border: 1px solid #D2D2D2;border-collapse:collapse;line-height: 25px;">

                <tr align = "center">
                  <td style="border: 1px solid #D2D2D2;">รายการ<br>Detail</td>
                  <td style="border: 1px solid #D2D2D2;">จำนวน<br>Quantity</td>
                  <td style="border: 1px solid #D2D2D2;">หน่วยละ<br>Unit Price</td>
                  <td style="border: 1px solid #D2D2D2;">ส่วนลดต่อหน่วย<br>Discount per Unit</td>
                  <td style="border: 1px solid #D2D2D2;">จำนวนเงิน<br>Amount</td>
                </tr>
                <tr>
                  <td style="border-left: 1px solid #D2D2D2;"> &nbsp;'.$card_name.' ('.$brand_name.')</td>
                  <td style="border-left: 1px solid #D2D2D2;" align="right"> &nbsp;'.$qty.' &nbsp;</td>
                  <td style="border-left: 1px solid #D2D2D2;" align="right"> &nbsp;'.number_format($original_fee,2).' &nbsp;</td>
                  <td style="border-left: 1px solid #D2D2D2;" align="right"> &nbsp;'.number_format($discount,2).' &nbsp;</td>
                  <td style="border-left: 1px solid #D2D2D2;" align="right"> &nbsp;'.number_format($sum_amount,2).' &nbsp;</td>
                </tr>
                <tr>
                  <td style="border-left: 1px solid #D2D2D2;"> </td>
                  <td style="border-left: 1px solid #D2D2D2;"> &nbsp;</td>
                  <td style="border-left: 1px solid #D2D2D2;"> &nbsp;</td>
                  <td style="border-left: 1px solid #D2D2D2;"> &nbsp;</td>
                  <td style="border-left: 1px solid #D2D2D2;"> &nbsp;</td>
                </tr>
                <tr>
                  <td style="border-left: 1px solid #D2D2D2;"> </td>
                  <td style="border-left: 1px solid #D2D2D2;"> &nbsp;</td>
                  <td style="border-left: 1px solid #D2D2D2;"> &nbsp;</td>
                  <td style="border-left: 1px solid #D2D2D2;"> &nbsp;</td>
                  <td style="border-left: 1px solid #D2D2D2;"> &nbsp;</td>
                </tr>
                <tr>
                  <td style="border-left: 1px solid #D2D2D2;"> </td>
                  <td style="border-left: 1px solid #D2D2D2;"> &nbsp;</td>
                  <td style="border-left: 1px solid #D2D2D2;"> &nbsp;</td>
                  <td style="border-left: 1px solid #D2D2D2;"> &nbsp;</td>
                  <td style="border-left: 1px solid #D2D2D2;"> &nbsp;</td>
                </tr>';

// $HTML .= '      <tr>
//                   <td style="border: 1px solid #D2D2D2;" align="right" colspan="4"> &nbsp;ภาษีมูลค่าเพิ่ม / VAT '.$toPercent_vat.'% &nbsp;</td><td style="border: 1px solid #D2D2D2;" align="right">'.number_format($Vat*$qty,2).' &nbsp;</td>
//                 </tr>';

$HTML .= '      <tr>
                  <td style="border: 1px solid #D2D2D2;" align="right" colspan="4"> &nbsp;รายการรวมทั้งสิ้น / Total &nbsp;</td><td style="border: 1px solid #D2D2D2;" align="right">'.number_format($sum_amount,2).' &nbsp;</td>
                </tr>
                <tr>
                  <td style="border: 1px solid #D2D2D2;" align="right" colspan="4"> &nbsp;จำนวนเงินเป็นตัวอักษร &nbsp;</td><td style="border: 1px solid #D2D2D2;" align="right">'.convert($sum_amount).' &nbsp;</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
        <td>
          <div style="display: block;width: 100%;border: 1px solid #D2D2D2;margin-top: 5px;">
               &nbsp;ประเภทการชำระเงิน / Receipt Type : '.$payment_type.'
          </div>
        </td>
        <td align="center" >

        </td>
      </tr>

    </table>';



echo $HTML;

function convert($number){
    
    $txtnum1 = array('ศูนย์', 'หนึ่ง', 'สอง', 'สาม', 'สี่', 'ห้า', 'หก', 'เจ็ด', 'แปด', 'เก้า', 'สิบ');
    $txtnum2 = array('', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน', 'สิบ', 'ร้อย', 'พัน', 'หมื่น', 'แสน', 'ล้าน');
    $number = str_replace(',', '', $number);
    $number = str_replace(' ', '', $number);
    $number = str_replace('บาท', '', $number);
    $number = explode(".", $number);

    $strlen = strlen($number[0]);
    $convert = '';

    for($i = 0; $i < $strlen; $i++){
      $n = substr($number[0], $i,1);
      if($n != 0){
        if($i == ($strlen - 1) && $n == 1)
          $convert .= 'เอ็ด';
        else if($i == ($strlen - 2) && $n == 2)
          $convert .= 'ยี่';
        else if($i == ($strlen - 2) && $n == 1)
          $convert .= '';
        else
          $convert .= $txtnum1[$n];

          $convert .= $txtnum2[$strlen - $i - 1];
       }
    }
    
    $convert .= 'บาท';

    if(sizeof($number) == 1)
      $convert .= 'ถ้วน';
    else {
      if ($number[1] == '0' || $number[1] == '00' || $number[1] == '')
        $convert .= 'ถ้วน';
      else {
        $strlen = strlen($number[1]);

      for ($i = 0; $i < $strlen; $i++) {
        $n = substr($number[1], $i, 1);

        if ($n != 0) {
          if ($i == ($strlen - 1) && $n == 1)
            $convert .= 'เอ็ด';
          else if ($i == ($strlen - 2) && $n == 2)
            $convert .= 'ยี่';
          else if ($i == ($strlen - 2) && $n == 1)
            $convert .= '';
          else
            $convert .= $txtnum1[$n];

            $convert .= $txtnum2[$strlen - $i - 1];
        }
      }
      $convert .= 'สตางค์';
    }
  }

  return $convert;
}

?>

    </center>
  </body>
</html>
