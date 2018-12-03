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

    $sql ='SELECT 
            invoice_h.*,
            invoice_b.*,
            outstanding_balance.*,
            mi_brand.name AS brand_name

            FROM invoice_h

            LEFT JOIN invoice_b
            ON invoice_h.invh_InvoiceHID = invoice_b.invh_InvoiceHID

            LEFT JOIN outstanding_balance
            ON invoice_h.ouba_OutstandingBalanceID = outstanding_balance.ouba_OutstandingBalanceID

            LEFT JOIN mi_brand
            ON mi_brand.brand_id = outstanding_balance.bran_BrandID

            WHERE invoice_h.ouba_OutstandingBalanceID="'.$id.'"';

    $stm_print = $db->prepare($sql);

    $stm_print->bindParam(":id",$id);

    $stm_print->execute();

    $HTML_body = "";

    $z = 0;

    $total_view = 0;

    while ($result_print = $stm_print->fetch(PDO::FETCH_ASSOC)){

      $ouba_OutstandingReceipt = $result_print["ouba_OutstandingReceipt"];
      $brand_name = $result_print["brand_name"];
      $ouba_CreatedDate = DateOnly($result_print['ouba_CreatedDate']);
      $invh_InvoiceReceipt = $result_print['invh_InvoiceReceipt'];

      if ($result_print['invb_Type'] == 'Card') {

        $sql_view = 'SELECT receipt_no,
                            SUM(total_amt) AS total_amt
                      FROM mb_member_register 
                      WHERE receipt_no="'.$result_print['invb_ReceiptNo'].'"
                      GROUP BY receipt_no';
      } else {

        $sql_view = 'SELECT hcbu_ReceiptNo AS receipt_no,
                            SUM(hcbu_TotalAmount) AS total_amt 
                    FROM hilight_coupon_buy 
                    WHERE hcbu_ReceiptNo="'.$result_print['invb_ReceiptNo'].'"
                    GROUP BY hcbu_ReceiptNo';
      }

      $oRes = $db->prepare($sql_view);
      $oRes->execute();

      while ($axRow_view = $oRes->fetch(PDO::FETCH_ASSOC)){

        $transfer_view = $axRow_view['total_amt']-$result_print['invb_TotalAmount'];

        $z++;

        $HTML_body .= '<tr>
                    <td style="padding-left:5px">'.$z.'</td>
                    <td style="padding-left:5px">'.$axRow_view['receipt_no'].'</td>
                    <td align="right" style="padding-right:5px">'.number_format($axRow_view['total_amt'],2).' ฿ </td>
                    <td align="right" style="padding-right:5px">'.number_format($result_print['invb_TotalAmount'],2).' ฿ </td>
                    <td align="right" style="padding-right:5px">'.number_format($transfer_view,2).' ฿ </td>
                  </tr>';

        $total_view += $transfer_view;

      }
    }


    $HTML = '<table width="1000" style="border: 1px solid #000000;padding: 30px 30px 10px 30px;line-height: 30px;">
          <tr>
            <td><b>
                Outstanding Receipt : '.$ouba_OutstandingReceipt.'
                <span style="float:right">Date : '.$ouba_CreatedDate.'</span>
                <br>
                Invoice No. : '.$invh_InvoiceReceipt.'
                <span style="float:right">'.$brand_name.'</span>
            </b></td>
          </tr>
          <tr>
            <td>
              <table width="1000" border="1" style="border-collapse:collapse;">
                <tr>
                  <td><b>No.</b></td>
                  <td><b>Receipt No.</b></td>
                  <td><b>Receipt Amount</b></td>
                  <td><b>Invoice Amount</b></td>
                  <td><b>Amount</b></td>
                </tr>
                '.$HTML_body.'
                <tr>
                <td colspan="4" align="center"><b>TOTAL</b></td>
                <td align="right" style="padding-right:5px"><b>'.number_format($total_view,2).' ฿ </b></td>
              </tr>';

echo $HTML;

?>
      </center>
  </body>
</html>

<script type="text/javascript">

window.print();
  
</script>