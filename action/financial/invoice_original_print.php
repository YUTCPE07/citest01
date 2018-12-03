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

    error_reporting(1);

    include('../../lib/function_normal.php');

    // require '../../service/PDOConnect.class.php';

    require realpath(__DIR__ . '/../..') . '/include/service/PDOConnect.class.php';

    $db = PDOConnect::init();

    $id = $_GET["id"];

    $sql = "SELECT invoice_h.*,
            mi_brand.company_name AS brand_name,
            mi_brand.mobile AS brand_mobile,
            mi_brand.tax_id AS brand_tax,
            mi_branch.*
            FROM invoice_h
            LEFT JOIN mi_brand
            ON mi_brand.brand_id = invoice_h.bran_BrandID
            LEFT JOIN mi_branch
            ON mi_branch.brand_id = mi_brand.brand_id
            WHERE invh_InvoiceHID=".$id." AND mi_branch.default_status=1";





    $sql_company = "SELECT * FROM company WHERE comp_ComapnyID = 1";

    $stm_company = $db->prepare($sql_company);

    $stm = $db->prepare($sql);

    $stm->bindParam(":id",$id);

    if ($stm->execute() && $stm_company->execute()) {

      $result = $stm->fetch(PDO::FETCH_ASSOC);
      $result_company = $stm_company->fetch(PDO::FETCH_ASSOC);

      // COMPANY

      $comp_Name        =  $result_company["comp_Name"];
      $comp_Address1    =  $result_company["comp_Address1"];
      $comp_Address2    =  $result_company["comp_Address2"];
      $comp_Tel         =  $result_company["comp_Tel"];
      $comp_TaxID       =  $result_company["comp_TaxID"];

      $company_address = $comp_Address1."<br>".$comp_Address2;



      # INVOICE

      $sql_invoice = "SELECT invb_ServiceCharge, invb_Vat, invb_TotalAmount  
                      FROM invoice_b 
                      WHERE invh_InvoiceHID=".$result["invh_InvoiceHID"];

      $oRes = $db->prepare($sql_invoice);
      $oRes->execute();

      $service_charge = 0;
      $vat = 0;
      $total_amount = 0;

      while ($axRow_invoice = $oRes->fetch(PDO::FETCH_ASSOC)){

          $service_charge += $axRow_invoice['invb_ServiceCharge'];
          $vat += $axRow_invoice['invb_Vat'];
          $total_amount += $axRow_invoice['invb_TotalAmount'];

      }



      $brand_name               = $result["brand_name"];
      $brand_mobile             = $result["brand_mobile"];
      $date_create              = date("Y-m-d", strtotime("-1 day", strtotime($result["invh_CreatedDate"])));
      $receipt_no               = $result["invh_InvoiceReceipt"];

      if (!$brand_mobile) {  $brand_mobile = "-";  }
      if (!$brand_tax) {  $brand_tax = "-";  }


      # BRANCH ADDRESS

      if ($result["moo"]) {       $result["moo"] = "หมู่ ".$result["moo"];  }
      if ($result["junction"]) {  $result["junction"] = "แยก ".$result["junction"];  }
      if ($result["soi"]) {       $result["soi"] = "ซอย ".$result["soi"];  }
      if ($result["road"]) {      $result["road"] = "ถนน".$result["road"];  }
      if ($result["sub_district"]) { $result["sub_district"] = "แขวง".$result["sub_district"];  }
      if ($result["district"]) {  $result["district"] = "เขต".$result["district"];  }
      if ($result["postcode"]) {  $result["postcode"] = "จังหวัด".$result["postcode"];  }

      if ($result["province_id"]!=0) { 

        $sql_province = "SELECT prov_Name FROM province WHERE prov_ProvinceID = ".$result["province_id"];

        $province = $db->prepare($sql_province);
        $province->execute();
        $province = $province->fetch(PDO::FETCH_ASSOC);

      } else {  $result["province_id"] = ""; }

      $brand_address1 = $result["address_no"]." ".$result["moo"]." ".$result["junction"]." ".$result["soi"]." ".$result["road"];

      $brand_address2 = $result["sub_district"]." ".$result["district"]." ".$province["prov_Name"]." ".$result["postcode"];


    }





$HTML = '<table width="800" style="border: 1px solid #000000;padding: 30px 30px 10px 30px;line-height: 30px;">
      <tr>
        <td width="50%" valign="top">
          <div style="width:350px;display: block;position: relative;">
            <div style="float: left;position: relative;background-color: #FFFFFF;padding-right: 5px;padding-top:70px">
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
            เลขที่ใบเสร็จรับเงิน / ใบกำกับภาษี<br>Receipt / Tax Invoice No.    <span>'.$receipt_no.'</span>
            </div>
            <!-- <div class="dotwidth"></div> -->
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="2" align="center" style="padding-top:20px">
          <table width="100%">
            <tr><td width="20%"></td>
                <td align="center">
                  <span style="font-size: 25px;" >RECEIPT / TAX INVOICE</span><br>
                  <span style="font-size: 15px;">ใบเสร็จรับเงิน / ใบกำกับภาษี</span>
                </td>
                <td width="20%" align="center" style="border: 4px double black;">
                  <span style="font-size:18px;">Original<br>ต้นฉบับ</span>
                </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>

          <div style="width:350px;display: block;position: relative;">
            <div style="float: left;position: relative;background-color: #FFFFFF;padding-right: 5px;">
                <span class="" >'.$brand_name.'</span>
            </div>
          </div>
          <div style="clear: both;"></div>
          <div style="width:350px;display: block;position: relative;">
            <div style="float: left;position: relative;background-color: #FFFFFF;padding-right: 5px;">
              <span>'.$brand_address1.'<br>'.$brand_address2.'</span>
            </div>
              <div style="clear: both;"></div>
          </div><br>
          <div style="width:350px;display: block;position: relative;">
            <div style="float: left;position: relative;background-color: #FFFFFF;padding-right: 5px;">
                เลขประจำตัวผู้เสียภาษี : <span> &nbsp;'.$brand_tax.'</span>
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
              <td style="border: 1px solid #D2D2D2;"  >รายการ / List</td><td style="border: 1px solid #D2D2D2;"  >จำนวน / Amount</td>
            </tr>
            <tr>
              <td style="border-left: 1px solid #D2D2D2;" > &nbsp;Service Charge</td><td style="border-left: 1px solid #D2D2D2;"  align="right" >'.number_format($service_charge,2).' &nbsp;</td>
            </tr>
            <tr>
              <td style="border-left: 1px solid #D2D2D2;" > </td><td style="border-left: 1px solid #D2D2D2;"  align="right" > &nbsp;</td>
            </tr>
            <tr>
              <td style="border-left: 1px solid #D2D2D2;" > </td><td style="border-left: 1px solid #D2D2D2;"  align="right" > &nbsp;</td>
            </tr>
            <tr>
              <td style="border-left: 1px solid #D2D2D2;" > </td><td style="border-left: 1px solid #D2D2D2;"  align="right" > &nbsp;</td>
            </tr>
            <tr>
              <td style="border: 1px solid #D2D2D2;" align="right"> &nbsp;ภาษีมูลค่าเพิ่ม / VAT 7% &nbsp;</td><td style="border: 1px solid #D2D2D2;" align="right">'.number_format($vat,2).' &nbsp;</td>
            </tr>
            <tr>
              <td style="border: 1px solid #D2D2D2;" align="right"> &nbsp;รายการรวมทั้งสิ้น / Total &nbsp;</td><td style="border: 1px solid #D2D2D2;" align="right">'.number_format($total_amount,2).' &nbsp;</td>
            </tr>
            <tr>
              <td style="border: 1px solid #D2D2D2;" align="right"> &nbsp;จำนวนเงินเป็นตัวอักษร &nbsp;</td><td style="border: 1px solid #D2D2D2;" align="right">'.convert(number_format($total_amount,2)).' &nbsp;</td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
        </td>
        <td align="center"><br>ผู้รับเงิน / Collector<br>
            .....................................................................<br>
            (....................................................................)<br>
            วันที่ / Date &nbsp; ............../............../..............
        </td>
      </tr>
      <tr><td align="center" colspan="2"><br><b>Thank You For Your Business!</b></td></tr>

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

<script type="text/javascript">

window.print();
  
</script>