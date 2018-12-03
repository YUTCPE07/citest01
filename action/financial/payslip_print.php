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

    include('../../lib/function_normal.php');

    include('../../omise/lib/Omise.php');

    // require '../../service/PDOConnect.class.php';

    require realpath(__DIR__ . '/../..') . '/include/service/PDOConnect.class.php';

    $db = PDOConnect::init();

    $id = $_GET["payslip"];

    $sql = "SELECT payment_payable_trans.*,
            mi_brand.name AS brand_name,
            mi_brand.mobile AS brand_mobile,
            invoice_h.invh_InvoiceHID,
            invoice_h.invh_InvoiceReceipt,
            outstanding_balance.ouba_OutstandingReceipt,
            mi_branch.*
            FROM payment_payable_trans
            LEFT JOIN payslip
            ON payslip.pays_PayslipNo = payment_payable_trans.pays_PayslipNo
            LEFT JOIN invoice_h
            ON invoice_h.invh_InvoiceHID = payslip.invh_InvoiceHID
            LEFT JOIN outstanding_balance
            ON invoice_h.ouba_OutstandingBalanceID = outstanding_balance.ouba_OutstandingBalanceID
            LEFT JOIN mi_brand
            ON mi_brand.brand_id = invoice_h.bran_BrandID
            LEFT JOIN mi_branch
            ON mi_branch.brand_id = mi_brand.brand_id
            WHERE payslip.pays_PayslipNo='".$id."' AND mi_branch.default_status=1";



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



      # DATA BANK

      $brand_bank = $result["payp_TokenID"];

      $transfers = OmiseTransfer::retrieve($brand_bank);

      $recipient = OmiseRecipient::retrieve('recp_test_5283rp3dkbg9omr3y4q');



      # DATA PAYSLIP

      $data_payslip = "";
      $total_amount = 0;
      $i = 0;

      $sql_payslip = "SELECT payslip.invh_InvoiceHID
                      FROM payment_payable_trans
                      LEFT JOIN payslip
                      ON payslip.pays_PayslipNo = payment_payable_trans.pays_PayslipNo
                      WHERE payslip.pays_PayslipNo='".$id."'";

      $payslip = $db->prepare($sql_payslip);
      $payslip->execute();

      while ($axRow_payslip = $payslip->fetch(PDO::FETCH_ASSOC)){

        $sql_invoice = "SELECT 
                        invoice_h.*, 
                        outstanding_balance.ouba_OutstandingReceipt,
                        outstanding_balance.ouba_OutstandingBalance
                        FROM invoice_h
                        LEFT JOIN outstanding_balance
                        ON invoice_h.ouba_OutstandingBalanceID = outstanding_balance.ouba_OutstandingBalanceID
                        WHERE invoice_h.invh_InvoiceHID=".$axRow_payslip["invh_InvoiceHID"];

        $oRes = $db->prepare($sql_invoice);
        $oRes->execute();

        while ($axRow_invoice = $oRes->fetch(PDO::FETCH_ASSOC)){

          $i++;

          $brand_amt = $axRow_invoice['ouba_OutstandingBalance'];

          $sql_invoiceb = "SELECT 
                          invoice_b.invb_TotalAmount
                          FROM invoice_b
                          WHERE invoice_b.invh_InvoiceHID=".$axRow_invoice["invh_InvoiceHID"];

          $oRes_b = $db->prepare($sql_invoiceb);
          $oRes_b->execute();

          while ($axRow_b = $oRes_b->fetch(PDO::FETCH_ASSOC)){

            $brand_amt -= $axRow_b['invb_TotalAmount'];

          }

          $data_payslip .= '<tr>
                            <td style="border:1px solid #D2D2D2;" align="center">'.$i.'.</td>
                            <td style="border:1px solid #D2D2D2;">&nbsp;&nbsp;
                            '.$axRow_invoice['ouba_OutstandingReceipt'].' / '.$axRow_invoice['invh_InvoiceReceipt'].'</td>
                            <td style="border:1px solid #D2D2D2;" align="right">'.$brand_amt.'&nbsp;&nbsp;</td>
                          </tr>';

          $total_amount += $brand_amt;

        }
      }



      $brand_name               = $result["brand_name"];
      $brand_mobile             = $result["brand_mobile"];
      $date_create              = $result["payp_CreatedDate"];
      $receipt_no               = $result["pays_PayslipNo"];
      $payment_type             = $result["payp_PaymentType"];

      if (!$brand_mobile) {  $brand_mobile = "-";  }


      # BRANCH ADDRESS

      if ($result["moo"]) {       $result["moo"] = "หมู่ ".$result["moo"];  }
      if ($result["junction"]) {  $result["junction"] = "แยก ".$result["junction"];  }
      if ($result["soi"]) {       $result["soi"] = "ซอย ".$result["soi"];  }
      if ($result["road"]) {      $result["road"] = "ถนน".$result["road"];  }
      if ($result["sub_district"]) { $result["sub_district"] = "แขวง".$result["sub_district"];  }
      if ($result["district"]) {  $result["district"] = "เขต".$result["district"];  }

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
            เอกสารเลขที่การชำระเงิน / Pay Slip No.    <br><span>'.$receipt_no.'</span>
            </div>
            <!-- <div class="dotwidth"></div> -->
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="2" align="center" style="padding-top:20px">
          <span style="font-size: 25px;" >PAYMENT SLIP</span><br>
          <span style="font-size: 15px;" >ใบแจ้งการชำระเงิน</span>
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
          </div>
          <div style="width:350px;display: block;position: relative;">
            <div style="float: left;position: relative;background-color: #FFFFFF;padding-right: 5px;">
                เบอร์โทรศัพท์ &nbsp; <span>'.$brand_mobile.'</span>
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
              <td style="border:1px solid #D2D2D2;"  >ลำดับ / No.</td>
              <td style="border:1px solid #D2D2D2;"  >ค่าธรรมเนียมสมาชิก / Member Fee</td>
              <td style="border:1px solid #D2D2D2;"  >จำนวน / Amount</td>
            </tr>
              '.$data_payslip.'
            <tr>
              <td style="border:1px solid #D2D2D2;" align="right" colspan="2"> &nbsp;Transfer Service &nbsp;</td>
              <td style="border:1px solid #D2D2D2;" align="right">- 30 &nbsp;</td>
            </tr>
            <tr>
              <td style="border:1px solid #D2D2D2;" align="right" colspan="2"> &nbsp;รวมราคาทั้งสิ้น / Total &nbsp;</td>
              <td style="border:1px solid #D2D2D2;" align="right">'.($total_amount-30).' &nbsp;</td>
            </tr>
            <tr>
              <td style="border: 1px solid #D2D2D2;" align="right" colspan="2"> &nbsp;จำนวนเงินเป็นตัวอักษร &nbsp;</td><td style="border: 1px solid #D2D2D2;" align="right">'.convert($total_amount-30).' &nbsp;</td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td valign="top">
          <div style="display: block;width: 70%;border: 1px solid #D2D2D2;margin-top: 5px;">
               &nbsp;โอนเงินจากบัญชี : XXXXXX'.$recipient['bank_account']['last_digits'].'<br>
               &nbspไปยังบัญชี : XXXXXX'.$transfers['bank_account']['last_digits'].'
          </div>
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