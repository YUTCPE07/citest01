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

    $sql_invh ='SELECT 
                invoice_b.*,
                invoice_h.*,
                mi_brand.name AS brand_name

                FROM invoice_b

                LEFT JOIN invoice_h
                ON invoice_h.invh_InvoiceHID = invoice_b.invh_InvoiceHID

                LEFT JOIN mi_brand
                ON mi_brand.brand_id = invoice_h.bran_BrandID

                WHERE invoice_h.invh_InvoiceHID='.$id;

    $stm_invh = $db->prepare($sql_invh);

    $stm_invh->bindParam(":id",$id);

    if ($stm_invh->execute()) {

      $result_invh = $stm_invh->fetch(PDO::FETCH_ASSOC);
    }


    $HTML = '<table width="1000" style="border:1px solid #000000;padding:30px 30px 10px 30px;line-height:30px;font-size:13px">
          <tr>
            <td><b>
              <span style="float:left">Date : '.DateOnly($result_invh["invh_CreatedDate"]).'</span>
              <span style="float:right">Brand : '.$result_invh["brand_name"].'</span>
              <br>
              <span style="float:left">Invoice : '.$result_invh["invh_InvoiceReceipt"].'</span>
            </b></td>
          </tr>
          <tr>
            <td>
              <table width="1000" border="1" style="border-collapse:collapse;">
                <tr>
                  <td>No.</td>
                  <td>Customer</td>
                  <td>Item</td>
                  <td>Type</td>
                  <td>Receipt Amount</td>
                  <td>Service Fee</td>
                  <td>Payment Type</td>
                  <td>% Fee</td>
                  <td>Transaction Fee</td>
                  <td>Sum</td>
                  <td>Vat</td>
                  <td>Total Amount</td>
                </tr>';

    if ($result_invh['invb_Type'] == 'Card') {

      $sql_invb = 'SELECT mb_member.firstname,
                            mb_member.lastname,
                            mb_member.mobile,
                            mb_member.email,
                            mi_card.name AS item_name,
                            "Card" AS item_type,
                            mi_token_type.name AS TokenName,
                            mb_member_register.total_amt AS total_amt,
                            mb_member_register.payr_PaymentType AS pay_type,
                            invoice_b.*
                    FROM invoice_b
                    LEFT JOIN mb_member_register
                    ON invoice_b.invb_ReceiptNo = mb_member_register.receipt_no
                    LEFT JOIN mb_member
                    ON mb_member.member_id = mb_member_register.member_id
                    LEFT JOIN mi_card
                    ON mi_card.card_id = mb_member_register.card_id
                    LEFT JOIN mi_token_type
                    ON mb_member_register.token_type_id = mi_token_type.token_type_id
                    WHERE invoice_b.invh_InvoiceHID="'.$id.'"';
    } else {

      $sql_invb = 'SELECT mb_member.firstname,
                            mb_member.lastname,
                            mb_member.mobile,
                            mb_member.email,
                            hilight_coupon.coup_Name AS item_name,
                            "Promotion" AS item_type,
                            mi_token_type.name AS TokenName,
                            hilight_coupon_buy.hcbu_TotalAmount AS total_amt,
                            hilight_coupon_buy.hcbu_PaymentType AS pay_type,
                            invoice_b.*
                    FROM invoice_b
                    LEFT JOIN hilight_coupon_buy
                    ON invoice_b.invb_ReceiptNo = hilight_coupon_buy.hcbu_ReceiptNo
                    LEFT JOIN mb_member
                    ON mb_member.member_id = hilight_coupon_buy.memb_MemberID
                    LEFT JOIN hilight_coupon
                    ON hilight_coupon.coup_CouponID = hilight_coupon_buy.hico_HilightCouponID
                    LEFT JOIN mi_token_type
                    ON hilight_coupon_buy.token_type_id = mi_token_type.token_type_id
                    WHERE invoice_b.invh_InvoiceHID="'.$id.'"';
    }


      $oRes = $db->prepare($sql_invb);
      $oRes->execute();

      $z = 0;

      $total_receipt = 0;
      $total_member = 0;
      $total_mi = 0;
      $total_payment = 0;
      $total_service = 0;
      $total_vat = 0;
      $total_amount = 0;
      $transfer_fee = 0;

      while ($axRow_invb = $oRes->fetch(PDO::FETCH_ASSOC)){

        $transfer_fee += $axRow_invoiceb['invb_TotalAmount'];

        $z++;

        $member_name = '';

        if ($axRow_invb['firstname'].' '.$axRow_invb['lastname']) {

          if ($axRow_invb['email']) {

            if ($axRow_invb['mobile']) {
                  
              $member_name = $axRow_invb['firstname'].' '.$axRow_invb['lastname'].'<br>'.$axRow_invb['email'].'<br>'.$axRow_invb['mobile'];

            } else { $member_name = $axRow_invb['firstname'].' '.$axRow_invb['lastname'].'<br>'.$axRow_invb['email']; }

          } else {

            if ($axRow_invb['mobile']) {
                  
              $member_name = $axRow_invb['firstname'].' '.$axRow_invb['lastname'].'<br>'.$axRow_invb['mobile'];

            } else { $member_name = $axRow_invb['firstname'].' '.$axRow_invb['lastname']; }
          }

        } else {

          if ($axRow_invb['email']) {

            if ($axRow_invb['mobile']) { $member_name = $axRow_invb['email'].'<br>'.$axRow_invb['mobile'];

            } else { $member_name = $axRow_invb['email']; }

          } else {

            if ($axRow_invb['mobile']) { $member_name = $axRow_invb['mobile'];

            } else { $member_name = ''; }
          }
        }

        $charge_percent = ($axRow_invb['invb_MI']*100)/$axRow_invb['total_amt'];

        $HTML .= '<tr>
                    <td style="padding-left:5px">'.$z.'</td>
                    <td style="padding-left:5px">'.$member_name.'</td>
                    <td style="padding-left:5px">'.$axRow_invb["item_name"].'</td>
                    <td style="padding-left:5px">'.$axRow_invb["item_type"].'</td>
                    <td align="right" style="padding-right:5px">'.number_format($axRow_invb["total_amt"],2).' ฿</td>
                    <td align="right" style="padding-right:5px">'.number_format($axRow_invb["invb_MemberFee"],2).' ฿</td>
                    <td align="center">'.$axRow_invb["pay_type"].'</td>
                    <td style="text-align:right" style="padding-right:5px">'.$charge_percent.' %</td>
                    <td align="right" style="padding-right:5px">'.number_format($axRow_invb["invb_MI"],2).' ฿</td>
                    <td align="right" style="padding-right:5px">'.number_format($axRow_invb["invb_ServiceCharge"],2).' ฿</td>
                    <td align="right" style="padding-right:5px">'.number_format($axRow_invb["invb_Vat"],2).' ฿</td>
                    <td align="right" style="padding-right:5px">'.number_format($axRow_invb["invb_TotalAmount"],2).' ฿</td>
                  </tr>';

        $total_receipt += $axRow_invb['total_amt'];
        $total_member += $axRow_invb['invb_MemberFee'];
        $total_mi += $axRow_invb['invb_MI'];
        $total_payment += $axRow_invb['invb_PaymentAmount'];
        $total_service += $axRow_invb['invb_ServiceCharge'];
        $total_vat += $axRow_invb['invb_Vat'];
        $total_amount += $axRow_invb['invb_TotalAmount'];

      }

        $HTML .= '<tr>
                    <td colspan="4" align="center"><b>Total</b></td>
                    <td align="right" style="padding-right:5px"><b>'.number_format($total_receipt,2).' ฿</b></td>
                    <td align="right" style="padding-right:5px"><b>'.number_format($total_member,2).' ฿</b></td>
                    <td colspan="2"></td>
                    <td align="right" style="padding-right:5px"><b>'.number_format($total_payment,2).' ฿</b></td>
                    <td align="right" style="padding-right:5px"><b>'.number_format($total_service,2).' ฿</b></td>
                    <td align="right" style="padding-right:5px"><b>'.number_format($total_vat,2).' ฿</b></td>
                    <td align="right" style="padding-right:5px"><b>'.number_format($total_amount,2).' ฿</b></td>
                  </tr>';

        $HTML .= '</table>
              </td>
            </tr>
          </table>';

echo $HTML;

?>
      </center>
  </body>
</html>

<script type="text/javascript">

window.print();
  
</script>