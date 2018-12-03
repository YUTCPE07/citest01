<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=750">
    <link rel="shortcut icon" href="../../favicon.ico">
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

    $id_start = $_GET["start"];

    $id_end = $_GET["end"];

    $sql = "SELECT spco_SpecialCode,
                  spco_SpecialCodeID,
                  spco_Type
            FROM special_code
            WHERE spco_SpecialCodeID BETWEEN ".$id_start." AND ".$id_end."";

    $stm = $db->prepare($sql);

    $stm->bindParam(":id_start",$id_start);

    $stm->bindParam(":id_end",$id_end);

    $i = 1;

    if ($stm->execute()) {

      $result = $stm->fetch(PDO::FETCH_ASSOC);

      $HTML = '<center><h2>Special Code for '.$result["spco_Type"].'</h2>';

      $card = "SELECT mi_card.name
              FROM special_code_list
              LEFT JOIN mi_card
              ON special_code_list.spcl_ID = mi_card.card_id
              WHERE special_code_list.spco_SpecialCodeID = ".$result["spco_SpecialCodeID"]."";

      $stm_card = $db->prepare($card);

      $stm_card->execute();

      $count = "SELECT COUNT(spcl_SpecialCodeListID) AS count
                FROM special_code_list
                WHERE spco_SpecialCodeID = ".$result["spco_SpecialCodeID"]."";

      $stm_count = $db->prepare($count);

      $stm_count->execute();

      $count_card = $stm_count->fetch(PDO::FETCH_ASSOC);

      $x = 1;

      $HTML .= '<h4>';

      while ($card = $stm_card->fetch(PDO::FETCH_ASSOC)) {

        if ($x < $count_card['count']) {

          $HTML .= $card['name'].", ";

        } else {

          $HTML .= $card['name'];
        }

        $x++;

      }

      $HTML .= '</h4><table width="900" border="1" cellpadding="0" cellspacing="0" style="line-height:40px;font-size:18px">';

      $stm->execute();

      while ($result = $stm->fetch(PDO::FETCH_ASSOC)) {

        $HTML .= '<tr>
                  <td style="text-align:center" width="50px">'.$i++.'</td>
                  <td style="text-align:center" width="400px">'.$result["spco_SpecialCode"].'';

        if ($result = $stm->fetch(PDO::FETCH_ASSOC)) {

          $HTML .= '<td style="text-align:center" width="50px">'.$i++.'</td>
                    <td style="text-align:center" width="400px">'.$result["spco_SpecialCode"].'</td>
                  </tr>';

        } else {

          $HTML .= '<td colspan="2" width="450px"> </td>
                  </tr>';

        }
      }

      $HTML .= '</table></center>';
    }

echo $HTML;

?>
      </center>
  </body>
</html>

<script type="text/javascript">

window.print();
  
</script>