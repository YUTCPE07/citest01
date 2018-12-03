<?php

header ("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
header ("Pragma: no-cache");                          // HTTP/1.0

//===========================

include('../include/common_login.php');
include('../lib/function_normal.php');

//===========================


$oTmp = new TemplateEngine();

$oDB = new DBI();


$TASK = $_REQUEST['TASK'];


if($TASK == 'get_branch'){

	# CREATE USER

	$brand_id = $_REQUEST['brand_id'];

	$branch_id = $_REQUEST['branch_id'];



	$sql = "SELECT name,branch_id FROM mi_branch WHERE brand_id = '".$brand_id."'";

	$oRes = $oDB->Query($sql);

	
	if($oRes){

		$option='';

		while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

			if($branch_id==$axRow['branch_id']){		$select = 'selected="selected"';	}else{	$select='';	}

			$option.='<option  value="'.$axRow['branch_id'].'" '.$select.'>'.$axRow['name'].'</option>';

		}
	}

	$html='<label for="branch_id" class="lable-form">Branch <span class="text-rq">*</span></label> 

             <select id="branch_id" class="form-control text-md" name="branch_id">

             <option value="">Please Select ..</option>

			'.$option.'

			</select>';

	echo $html;

	exit;

	
} else if($TASK == 'get_sub_category') {

	

	$category_brand_id = $_REQUEST['category_brand_id'];

	$sub_category_brand_id = $_REQUEST['sub_category_brand_id'];

	

	$sql="SELECT name,sub_category_brand_id FROM mi_sub_category_brand WHERE category_brand_id = '".$category_brand_id."' ";

	$oRes = $oDB->Query($sql);

	

	if($oRes){

		$option='';

		while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

			

			if($sub_category_brand_id==$axRow['sub_category_brand_id']){$select = 'selected="selected"';	}else{	$select='';	}

			

			$option.='<option value="'.$axRow['sub_category_brand_id'].'" '.$select.'>'.$axRow['name'].'</option>';

		}

	}

	$html='<label for="sub_category_brand" class="lable-form">Sub Category Brand</label> 

                <select id="sub_category_brand" class="form-control text-md" name="sub_category_brand" >

	'.$option.'

	</select>';

	echo $html;

	exit;

}

else if($TASK=='get_product_category'){

	$product_id = $_REQUEST['product_id'];

	$category_id = $_REQUEST['category_id'];

	$brand_id = $_REQUEST['brand_id'];


	$sql = "SELECT name,products_id FROM mi_products WHERE category_id='".$category_id."' AND brand_id='".$brand_id."' AND flag_status=1 AND flag_del=0";

	$oRes = $oDB->Query($sql);

	$option = '<option value="">Please Select ..</option>';
		
	while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

		if($product_id==$axRow['products_id']){	$select = 'selected="select"';	}
		else{	$select='';	}

		$option .= '<option value="'.$axRow['products_id'].'" '.$select.'>'.$axRow['name'].'</option>';
	}



	$html = '<label class="lable-form">Products</label> 

            <select id="prod_ProductID" class="form-control text-md" name="prod_ProductID" onchange="product_img();">

		'.$option.'</select><br>

		<div class="adj_row" id="product_image"><span id="product_image"></span></div>';



	echo $html;

	exit;

	

}

else if($TASK=='get_Gallery'){

	$bran_BrandID = $_REQUEST['bran_BrandID'];
	$gall_GalleryID = $_REQUEST['gall_GalleryID'];

	$sql = "SELECT gall_Name, gall_GalleryID 
			FROM gallery 
			WHERE bran_BrandID IN (".$bran_BrandID.",0) 
			AND gall_Deleted!='T' 
			ORDER BY gall_Name ASC";

	$oRes = $oDB->Query($sql);

	$data = $oDB->QueryOne($sql);
		
	$option = '<option value="">Please Select ..</option>';

	if($oRes){

		if ($data) {
		
			while ($axRow = $oRes->FetchRow(DBI_ASSOC)){	

				if($gall_GalleryID == $axRow['gall_GalleryID']){	$select = 'selected="select"';	}else{	$select='';	}

				$option.='<option value="'.$axRow['gall_GalleryID'].'" '.$select.'>'.$axRow['gall_Name'].'</option>';

			}
		}
	}

	$html = '<select id="gallery_id" class="form-control text-md" name="gallery_id" required autofocus>'.$option.'</select>';

	echo $html;

	exit;
}


else if($TASK=='get_product_img'){

	$product_id = $_REQUEST['product_id'];
	

	$sql="SELECT image FROM mi_products WHERE products_id = '".$product_id."'";
	$products_image = $oDB->QueryOne($sql);
	

	$sql="SELECT path_image FROM mi_products WHERE products_id = '".$product_id."'";
	$products_path = $oDB->QueryOne($sql);


	if($products_image != ''){

		$image ='<label for="product_image" class="lable-form"></label>
			<img src="../../upload/'.$products_path.$products_image.'" width="150" height="150" class="img_upload"/><br><br>';

	}

	else {

		$image = '';
			
	}
	

	echo $image;

	exit;
	

}


else if($TASK=='get_age'){

	$age_1 = $_REQUEST['fage1'];
	$age_2 = $_REQUEST['fage2'];
	

	$sql = "SELECT mata_MasterTargetID,mata_NameEn FROM master_target WHERE mata_MasterTargetID > '".$age_1."' AND mafi_MasterFieldID=6 ORDER BY mata_MasterTargetID";

	$oRes = $oDB->Query($sql);

	if ($age_1) {

		if($oRes){

			$option='<option value="">Select ..</option>';

			while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

				
				if($age_2==$axRow['mata_MasterTargetID']){	$select = 'selected="selected"';	}else{	$select='';	}

				$option.='<option value="'.$axRow['mata_MasterTargetID'].'" '.$select.'>'.$axRow['mata_NameEn'].'</option>';

			}

		}

		$html='<select id="age_dp2" name="age_dp2" class="form-control">

		'.$option.'

		</select>';
	
	} else {

		$html = "";
	}

	echo $html;

	exit;

}


else if($TASK=='get_age_basic'){

	$age_1 = $_REQUEST['bage1'];
	$age_2 = $_REQUEST['bage2'];
	

	$sql = "SELECT mata_MasterTargetID,mata_NameEn FROM master_target WHERE mata_MasterTargetID > '".$age_1."' AND mafi_MasterFieldID=6 ORDER BY mata_MasterTargetID";

	$oRes = $oDB->Query($sql);

	if ($age_1!='All' && $age_1!='21' && $age_1!='') {

		if($oRes){

			$option='<option value="">Select ..</option>';

			while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

				
				if($age_2==$axRow['mata_MasterTargetID']){	$select = 'selected="selected"';	}else{	$select='';	}

				$option.='<option value="'.$axRow['mata_MasterTargetID'].'" '.$select.'>'.$axRow['mata_NameEn'].'</option>';
			}
		}

		$html='<select id="age_basic2" name="age_basic2" class="form-control">

		'.$option.'

		</select>';
	
	} else {

		$html = "";
	}

	echo $html;

	exit;

}

else if($TASK=='getCard'){

	$brand_id = $_REQUEST['brand_id'];
	
	$sql = "SELECT card_id,
					name,
					image,
					image_newupload,
					path_image 
				FROM mi_card 
				WHERE brand_id='".$brand_id."' 
				AND flag_del=0";

	$oRes = $oDB->Query($sql);

	if($oRes){

		$html = '<div style="overflow-x:scroll;width:100%">
				<table><tr>';

		$i=1;

		while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

			$html .= '<td>
						<table style="background-color:#EEE">
							<tr>
								<td style="text-align:center;width:30px">
									<input type="checkbox" id="check'.$axRow['card_id'].'" name="card[]" value="'.$axRow['card_id'].'" onclick="get'.$axRow['card_id'].'()">
								</td>
								<td style="text-align:center;width:120px">
									<label for="check'.$axRow['card_id'].'">';

			if ($axRow['image']) {

				$html .='<img src="../../upload/'.$axRow['path_image'].$axRow['image'].'"';

			} else if ($axRow['image_newupload']) {

				$html .='<img src="../../upload/'.$$axRow['path_image'].$axRow['image_newupload'].'"';

			} else {

				$html .= '<img src="../../images/card_privilege.jpg"';
			}

			$html .= 'id="img'.$axRow['card_id'].'" width="100" class="img-rounded image_border">
						</label>
					</td>
					<td>'.$axRow['name'].'</td>
				</tr>
			</table>
		</td>
		<td width="10px">&nbsp;</td>';

		$i++;

		}

		$html .= '</tr></table><br></div>
					<button class="btn btn-primary btn-xs" style="margin-left:5px;margin-top:10px;" type="submit">SUBMIT</button>
					<input type="hidden" id="brand" name="brand" value="'.$brand_id.'" />
					<input type="hidden" id="act" name="act" value="<%$act%>" />';
	}

	echo $html;

	exit;
}

else if($TASK=='get_collection_type'){

	$chang_uom_type = $_REQUEST['chang_uom_type'];

	$use_type = "use_uom_type";

	

	$sql = "SELECT name,value FROM mi_master WHERE type='".$use_type ."' AND value='".$chang_uom_type."'";

	$oRes = $oDB->Query($sql);

	

	if($oRes){

		$option='';

		while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

			

			if($product_id==$axRow['chang_uom_type']){	$select = 'selected="selected"';	}else{	$select='';	}

			

			$option.='<option value="'.$axRow['value'].'" '.$select.'>'.$axRow['name'].'</option>';

		}

	}

	$html='<label for="chang_uom_type" class="lable-form">Use UOM Type</label> 

                <select id="chang_uom_type" class="form-control text-md" name="chang_uom_type" >

	'.$option.'

	</select>';


	echo $html;

	exit;

}

else if($TASK=='getDistrict'){

	$id = $_REQUEST['id'];

	$count = $_REQUEST['count'];

	$sql = "SELECT district_name,id_district FROM mi_tg_district WHERE district_id='".$id ."' ";

	$oRes = $oDB->Query($sql);

	if($oRes){

		$option='';

		while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

			if($id==$axRow['id_district']){	$select = 'selected="selected"';	}else{	$select='';	}

			$option.='<option value="'.$axRow['id_district'].'" '.$select.'>'.$axRow['district_name'].'</option>';
		}
	}

	$html='<select id="district'.$count.'" class="form-control text-md" name="district'.$count.'">

		'.$option.'

	</select>

	<div id="district_opt'.($count+1).'"> </div>';

	echo $html;

	exit;

}

else if($TASK =='get_countBrand'){

	$count = $_REQUEST['count'];

	$select= 'selected="select"';

	$sql ="SELECT name,brand_id FROM mi_brand";

	$oRes = $oDB->Query($sql);

	if($oRes){

		$option ='';

		while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

			

			$option .='<option value="'.$axRow['brand_id'].'" '.$select.'>' .$axRow['name']. '</option>';

		}		

	}

	

	$html='<select id="brand'.$count.'" class="form-control text-md" name="brand'.$count.'">'.$option.'</select>

			<div id="brand_opt'.($count+1).'"class="adj_row">

				<label class="lable-form"></label> 

				<div id="brand'.($count+1).'"> </div>

			</div>' ;

	echo $html;

	exit;


}

else if($TASK =='Get_CardData'){

	$card_CardID = $_REQUEST['card_CardID'];

			$time_data = date("Y-m");

			$year_id = date("Y");

			$month_id = date("m");

			$month_id_2 = 0;

			$year_id_2 = 0;

			if (substr($month_id, 0, 1)=="0") {
				
				$month_id = substr($month_id, 1, 1);
			}

			$month_id--;

			$month = ["JAN.", "FEB.", "MAR.", "APR.", "MAY.", "JUN.", "JUL.", "AUG.", "SEP.", "OCT.", "NOV.", "DEC."];

			for ($i=0; $i < 12 ; $i++) { 

				if ($month_id == 12) { $month_id = 0;	$year_id++;	}
						
				$month_id_2 = $month_id;

				$year_id_2 = $year_id;

				$month_id++;

			}

			$member_expired = array();

			$html = '<table width="100%" class="table table-striped table-bordered myPopup">
								    <tr>
								        <td colspan="12" style="text-align:center"><b>จำนวนสมาชิกที่บัตรจะหมดอายุในเดือน &nbsp; '.$month[$month_id].' '.$year_id.' - '.$month[$month_id_2].' '.$year_id_2.'</b></td>
								    </tr>';

			$year_id = date("Y");

			$month_data = 0;

			$html .= '<tr class="th_table">';

			for ($j=0; $j < 12 ; $j++) { 

				if ($month_id == 12) { $month_id = 0;	$year_id++;	}
						
				$html .= '<td style="text-align:center"><b>'.$month[$month_id].' '.$year_id.'</b></td>';

				$month_id++;

			}

			$html .= '</tr><tr>';

			$year_id = date("Y");

			for ($j=0; $j < 12 ; $j++) { 

				if ($month_id == 12) { $month_id = 0;	$year_id++;	}

				$month_data = $month_id+1;

				$str_month = strlen($month_data);

				if ($str_month==1) {	$month_data = "0".$month_data;	}

				$sql_expired = "SELECT count(*) FROM mb_member_register WHERE date_expire LIKE '".$year_id."-".$month_data."%' AND card_id=".$card_CardID." AND flag_del=0";

				$expired_member = $oDB->QueryOne($sql_expired);
						
				$html .= '<td style="text-align:center">'.$expired_member.'</td>';

				$month_id++;

			}

			$html .= '</tr></table>';

	echo $html;

	exit;		

}

else if($TASK =='Get_CardPoint'){

	$bran_BrandID = $_REQUEST['bran_BrandID'];

	$card_CardID = $_REQUEST['card_CardID'];

	$sql ="SELECT name,card_id FROM mi_card WHERE brand_id=".$bran_BrandID;

	$oRes = $oDB->Query($sql);

	if($oRes){

		$option ='';

		$option .='<option value="">Please Select ..</option>';

		while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

			if($card_CardID==$axRow['card_id']){		

				$select = 'selected="selected"';	

			} else {	

				$select='';	

			}

			$option .='<option value="'.$axRow['card_id'].'" '.$select.'>' .$axRow['name']. '</option>';
		}

		$html='<select id="card_CardID" class="form-control text-md" name="card_CardID" onchange="PointCardSelect()">

			'.$option.'

			</select>' ;

		echo $html;

		exit;		

	} else {

		$html='<select id="card_CardID" class="form-control text-md" name="card_CardID">

				<option value="">Please Select ..</option>

				</select>

				' ;

		echo $html;

		exit;	

	}

}

else if($TASK =='Get_CardStamp'){

	$bran_BrandID = $_REQUEST['bran_BrandID'];

	$card_CardID = $_REQUEST['card_CardID'];

	$sql ="SELECT name,card_id FROM mi_card WHERE brand_id=".$bran_BrandID;

	$oRes = $oDB->Query($sql);

	if($oRes){

		$option ='';

		$option .='<option value="">Please Select ..</option>';

		while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

			if($card_CardID==$axRow['card_id']){		

				$select = 'selected="selected"';	

			} else {	

				$select='';	

			}

			$option .='<option value="'.$axRow['card_id'].'" '.$select.'>' .$axRow['name']. '</option>';
		}

		$html='<select id="card_CardID" class="form-control text-md" name="card_CardID" onchange="StampCardSelect()">

			'.$option.'

			</select>' ;

		echo $html;

		exit;		

	} else {

		$html='<select id="card_CardID" class="form-control text-md" name="card_CardID">

				<option value="">Please Select ..</option>

				</select>

				' ;

		echo $html;

		exit;	

	}

}

else if($TASK =='Get_CardID'){

	$bran_BrandID = $_REQUEST['bran_BrandID'];

	$card_CardID = $_REQUEST['card_CardID'];

	$sql ="SELECT name,card_id FROM mi_card WHERE brand_id=".$bran_BrandID;

	$oRes = $oDB->Query($sql);

	if($oRes){

		$option ='';

		$option .='<option value="">Please Select ..</option>';

		while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

			if($card_CardID==$axRow['card_id']){		

				$select = 'selected="selected"';	

			} else {	

				$select='';	

			}

			$option .='<option value="'.$axRow['card_id'].'" '.$select.'>' .$axRow['name']. '</option>';
		}

		if ($card_CardID!='undefined') { $disabled = 'disabled'; }
		else { $disabled = ''; }

		$html='<select id="card_CardID" class="form-control text-md" name="card_CardID" onchange="CardSelect()" '.$disabled.'>

			'.$option.'

			</select>' ;

		echo $html;

		exit;		

	} else {

		$html='<select id="card_CardID" class="form-control text-md" name="card_CardID">

				<option value="">Please Select ..</option>

				</select>

				' ;

		echo $html;

		exit;	

	}

}

else if($TASK =='Get_CardUse'){

	$bran_BrandID = $_REQUEST['bran_BrandID'];

	$sql = "SELECT name,card_id FROM mi_card WHERE brand_id=".$bran_BrandID;

	$oRes = $oDB->Query($sql);

	if($oRes){

		$option ='';

		$option .= '<option value="">Please Select ..</option>';

		while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

			$option .= '<option value="'.$axRow['card_id'].'">'.$axRow['name'].'</option>';

		}

		$html = '<select id="card_CardID" class="form-control text-md" name="card_CardID" onchange="CardUse()" <%if $data.card_CardID%>disabled<%/if%>>

			'.$option.'

			</select>' ;

		echo $html;

		exit;		

	} else {

		$html='<select id="card_CardID" class="form-control text-md" name="card_CardID">

				<option value="">Please Select ..</option>

				</select>

				' ;

		echo $html;

		exit;	

	}
}

else if($TASK =='Get_BranchRegister'){

	$bran_BrandID = $_REQUEST['bran_BrandID'];

	$sql = "SELECT name,branch_id FROM mi_branch WHERE flag_status='1' AND brand_id='".$bran_BrandID."'";

	$oRes = $oDB->Query($sql);

	if($oRes){

		$option ='';

		$option .= '<option value="">Please Select ..</option>';

		while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

			$option .= '<option value="'.$axRow['branch_id'].'">'.$axRow['name'].'</option>';

		}

		$html = '<select id="brnc_BranchID" class="form-control text-md" name="brnc_BranchID" onchange="BranchRegister();">
					'.$option.'
				</select>';

		echo $html;

		exit;		

	} else {

		$html='<select id="brnc_BranchID" class="form-control text-md" name="brnc_BranchID" disabled>
				<option value="">Please Select ..</option>
				</select>';

		echo $html;
		exit;	
	}
}

else if($TASK =='Get_BranchRedeem'){

	$bran_BrandID = $_REQUEST['bran_BrandID'];

	$sql = "SELECT name,branch_id FROM mi_branch WHERE flag_status='1' AND brand_id='".$bran_BrandID."'";

	$oRes = $oDB->Query($sql);

	if($oRes){

		$option ='';

		$option .= '<option value="">Please Select ..</option>';

		while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

			$option .= '<option value="'.$axRow['branch_id'].'">'.$axRow['name'].'</option>';

		}

		$html = '<select id="brnc_BranchID" class="form-control text-md" name="brnc_BranchID" onchange="BranchRedeem();">
					'.$option.'
				</select>';

		echo $html;

		exit;		

	} else {

		$html='<select id="brnc_BranchID" class="form-control text-md" name="brnc_BranchID" disabled>
				<option value="">Please Select ..</option>
				</select>';

		echo $html;
		exit;	
	}
}

else if($TASK =='Get_CardRegister'){

	$bran_BrandID = $_REQUEST['bran_BrandID'];
	$brnc_BranchID = $_REQUEST['brnc_BranchID'];

	$sql_card = "SELECT branch_id, card_id
				FROM mi_card 
				WHERE card_type_id!='6' 
				AND flag_status='1' 
				AND brand_id='".$bran_BrandID."'";

	$oRes_card = $oDB->Query($sql_card);
	$card_select = '';
	$card_option = '';

	while ($card = $oRes_card->FetchRow(DBI_ASSOC)) {

		if ($card['branch_id']!="") {

			$token = strtok($card['branch_id'],",");
			$card_id = array();
			$i = 0;
			while ($token !== false) {

				if ($token == $brnc_BranchID) {
	    			
	    			$card_id[$i] = $card['card_id'];
	    			$i++;
				}

	    		$token = strtok(",");
			}

			$arrlength = count($card_id);

			for($x = 0; $x < $arrlength; $x++) {

				$sql_data = 'SELECT name, card_id FROM mi_card WHERE card_id="'.$card_id[$x].'"';
				$oRes = $oDB->Query($sql_data);
				$card_data = $oRes->FetchRow(DBI_ASSOC);
	    			
	    		$card_option .= '<option value="'.$card_data['card_id'].'">'.$card_data['name'].'</option>';
			}
		}
	}

	if ($card_option=="" && $brnc_BranchID) {

		$card_select = '<center><br>
							<span class="text-rq" style="font-size:13px">No Card Can Register in this Branch</span>
						</center>';

	} else if ($card_option) {

		$card_select = '<div class="adj_row">
                    		<label class="lable-form">Card <span class="text-rq">*</span></label>
                    		<select id="card_CardID" class="form-control text-md" name="card_CardID" onchange="CardRegister()" required autofocus>
								<option value="">Please Select ..</option>
								'.$card_option.'
							</select>
						</div>

						<span id="form_register" class="fontBlack"></span>';
	}

	echo $card_select;
	exit();
}

else if($TASK =='Get_RewardRedeem'){

	$bran_BrandID = $_REQUEST['bran_BrandID'];
	$brnc_BranchID = $_REQUEST['brnc_BranchID'];

	$sql_redeem = "SELECT brnc_BranchID, rede_RewardRedeemID
					FROM reward_redeem 
					WHERE rede_Status = 'Active'
					AND bran_BrandID='".$bran_BrandID."'";

	$oRes_redeem = $oDB->Query($sql_redeem);
	$redeem_select = '';
	$redeem_option = '';

	while ($redeem = $oRes_redeem->FetchRow(DBI_ASSOC)) {

		if ($redeem['brnc_BranchID']!="") {

			$token = strtok($redeem['brnc_BranchID'],",");
			$redeem_id = array();
			$i = 0;
			while ($token !== false) {

				if ($token == $brnc_BranchID) {
	    			
	    			$redeem_id[$i] = $redeem['rede_RewardRedeemID'];
	    			$i++;
				}

	    		$token = strtok(",");
			}

			$arrlength = count($redeem_id);

			for($x = 0; $x < $arrlength; $x++) {

				$sql_data = 'SELECT reward_redeem.rede_Name, 
									reward_redeem.rede_RewardRedeemID
								FROM reward_redeem
								LEFT JOIN reward 
								ON reward.rewa_RewardID = reward_redeem.rewa_RewardID
								WHERE reward_redeem.rede_RewardRedeemID="'.$redeem_id[$x].'"';
				$oRes = $oDB->Query($sql_data);
				$redeem_data = $oRes->FetchRow(DBI_ASSOC);
	    			
	    		$redeem_option .= '<option value="'.$redeem_data['rede_RewardRedeemID'].'">'.$redeem_data['rede_Name'].'</option>';
			}
		}
	}

	if ($redeem_option=="" && $brnc_BranchID) {

		$redeem_select = '<center><br>
							<span class="text-rq" style="font-size:13px">No Reward can Redeem in this Branch</span>
						</center>';

	} else if ($redeem_option) {

		$redeem_select = '<div class="adj_row">
                    		<label class="lable-form">Redeem <span class="text-rq">*</span></label>
                    		<select id="rede_RewardRedeemID" class="form-control text-md" name="rede_RewardRedeemID" onchange="RewardRedeem()" required autofocus>
								<option value="">Please Select ..</option>
								'.$redeem_option.'
							</select>
						</div>

						<span id="form_redeem" class="fontBlack"></span>';
	}

	echo $redeem_select;
	exit();
}


else if($TASK =='Get_RegisterMember'){

	$card_CardID = $_REQUEST['card_id'];
	$member_id = $_REQUEST['member_id'];

	# MEMBER DATA

	$sql_member = 'SELECT * FROM mb_member WHERE member_id="'.$member_id.'"';
	$oRes = $oDB->Query($sql_member);
	$member = $oRes->FetchRow(DBI_ASSOC);

	# MEMBER

	if ($member['member_image'] && $member['member_image']!='user.png') {

		$member_image = '<img src="../../upload/member_upload/'.$member['member_image'].'" width="100" height="100" class="img-circle image_border"/>';

	} else if ($member['facebook_id']) {

		 $member_image = '<img src="http://graph.facebook.com/'.$member['facebook_id'].'/picture?type=square" width="100" height="100" class="img-circle image_border" />';

	} else {
				                    	
		$member_image = '<img src="../../images/user.png" width="100" height="100" class="img-circle image_border" />';
	}

	$html = '<br><br>'.$member_image;

	# CHECK MULTIPLE CARD

	$sql_multiple = 'SELECT flag_multiple 
						FROM mi_card 
						WHERE card_id="'.$card_CardID.'"';
	$multiple = $oDB->QueryOne($sql_multiple);

	# CHECK MEMBER REGISTER

	$sql_register = 'SELECT member_register_id AS id,
						member_card_code,
						member_brand_code,
						flag_del
						FROM mb_member_register 
						WHERE member_id="'.$member_id.'" 
						AND card_id="'.$card_CardID.'"
						ORDER BY member_register_id DESC';

	$id = $oDB->Query($sql_register);
	$regis = $id->FetchRow(DBI_ASSOC);

	if ($regis['id'] && $regis['flag_del']=='' && $multiple=='No') {

		$html .= '<br><br><span style="color:red;font-size:16px"><b>Already Register</b></span><br><br>';

	} else {

		$html .= '<table>';

		$topic = array("Profile", "Home Address", "Work Address", "Work", "Contact");

		for ($i=0; $i <5 ; $i++) { 

			$sql_field = 'SELECT a.*,b.*,c.*,

							a.mafi_MasterFieldID AS master_field_id,
							b.refo_Target,
							d.fity_Name AS field_type

							FROM master_field AS a

							LEFT JOIN register_form AS b
							ON b.mafi_MasterFieldID = a.mafi_MasterFieldID

							LEFT JOIN mi_card AS c
							ON b.card_CardID = c.card_id

							LEFT JOIN field_type AS d
							ON a.mafi_FieldType = d.fity_FieldTypeID

							WHERE a.mafi_Position = "'.$topic[$i].'"
							AND a.mafi_Deleted != "T"
							AND c.card_id = "'.$card_CardID.'"
							AND b.refo_FillIn = "Y"

							GROUP BY a.mafi_FieldName
							ORDER BY a.mafi_FieldOrder';

			$oRes = $oDB->Query($sql_field);
			$check_field = $oDB->QueryOne($sql_field);

			if ($check_field) {

				$html .= '<tr height="40px"><td colspan="3" style="text-align:center"><u><b>'.$topic[$i].'</b></u></td></tr>';

				while ($field = $oRes->FetchRow(DBI_ASSOC)){

					if ($field['refo_Require']=='Y') { 

						$text_rq = ' <span class="text-rq">*</span>';
						$rq_af = 'required autofocus'; 

					} else { $text_rq = '';	$rq_af = '';  }

					$html .= '	<tr height="40px"><td style="text-align:right">
										<b>'.$field['mafi_NameEn'].$text_rq.'</b></td>
										<td width="10px"></td>';

					if ($field['field_type']=='Text') {

						# MEMBER BRAND CODE & MEMBER CARD COE

						if ($field['master_field_id']=='48') { # CARD

							if ($regis['member_card_code']) { $disabled = 'readonly'; } 
							else { $disabled = ''; }

							$member[$field['mafi_FieldName']] = $regis['member_card_code'];
							
						} elseif ($field['master_field_id']=='49') { # BRAND

							if ($regis['member_brand_code']) { $disabled = 'readonly'; } 
							else { $disabled = ''; }

							$member[$field['mafi_FieldName']] = $regis['member_brand_code'];

						} else {

							$disabled = '';
						}

						$html .= '<td style="text-align:center"><input type="text" name="'.$field['mafi_FieldName'].'" class="form-control text-md" placeholder="Text" '.$rq_af.' '.$disabled.' value="'.$member[$field['mafi_FieldName']].'">';
						
					} else if ($field['field_type']=='Number') {

						$html .= '<td style="text-align:center"><input type="number" name="'.$field['mafi_FieldName'].'" class="form-control text-md" placeholder="Number" value="'.$member[$field['mafi_FieldName']].'" '.$rq_af.'>';
						
					} else if ($field['field_type']=='Date') {

						$data = $member[$field['mafi_FieldName']];
						$year_data = substr($data,0,4);
						$month_data = substr($data,5,2);
						$date_data = substr($data,8,2);

						# DAY OPTION

						$option_date = '';

						for ($x = 1; $x < 32; $x++) {

							if ($x == $date_data) { 

								$select = 'selected="selected"';
								$date_member = $x;

							} else { $select = ''; }

							if (strlen($x) == 1) { $d = '0'.$x; }
							else { $d = $x; }

							$option_date .= '<option value="'.$d.'" '.$select.'>'.$d.'</option>';
						}


						# MONTH OPTION

						$month = ["Jan.", "Feb.", "Mar.", "Apr.", "May.", "Jun.", "Jul.", "Aug.", "Sep.", "Oct.", "Nov.", "Dec."];
						$option_month = '';

						for ($x = 1; $x < 13; $x++) {

							if ($x == $month_data) { 

								$select = 'selected="selected"';
								$month_member = $month[$x-1];

							} else { $select = ''; }

							if (strlen($x) == 1) { $d = '0'.$x; }
							else { $d = $x; }

							$option_month .= '<option value="'.($d).'" '.$select.'>'.$month[$x-1].'</option>';
						}


						# YEAR OPTION

						$this_year = date('Y',time());
						$start_year = $this_year-100;
						$option_year = '';

						if ($year_data > $this_year) { $year_data = $year_data-543; }

						for ($x = $this_year; $x >= $start_year; $x--) {

							if ($x == $year_data) { 

								$select = 'selected="selected"';
								$year_member = $x;

							} else { $select = ''; }

							$option_year .= '<option value="'.$x.'" '.$select.')>'.$x.'</option>';
						}

						$html .= '<td>';

						$html .= '<span class="form-inline">
									<select id="date" class="form-control text-md" name="'.$field['mafi_FieldName'].'_date" style="width:70px" '.$rq_af.'>
										<option value=""> - - -</option>
										'.$option_date.'
									</select>
									<select id="month" class="form-control text-md" name="'.$field['mafi_FieldName'].'_month" style="width:80px" '.$rq_af.'>
										<option value=""> - - - -</option>
										'.$option_month.'
									</select>
									<select id="year" class="form-control text-md" name="'.$field['mafi_FieldName'].'_year" style="width:90px" '.$rq_af.'>
										<option value=""> - - - - -</option>
										'.$option_year.'
									</select></span>';
						
					} else if ($field['field_type']=='Radio') {

						$x = 0;

						$data = $member[$field['mafi_FieldName']];

						$html .= '<td><span class="form-inline">';

						$sql_target = 'SELECT *
										FROM master_target
										WHERE mafi_MasterFieldID = "'.$field['master_field_id'].'"';
						$oRes_target = $oDB->Query($sql_target);
						while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

							if ($data == $target['mata_MasterTargetID']) {

								if ($x==0) {

									$html .= '<input type="radio" name="'.$field['mafi_FieldName'].'" value="'.$target['mata_MasterTargetID'].'" checked> <label>'.$target['mata_NameEn'].'<label>';

								} else {

									$html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="'.$field['mafi_FieldName'].'" value="'.$target['mata_MasterTargetID'].'" checked> <label>'.$target['mata_NameEn'].'<label>';
								}

							} else {

								if ($x==0) {

									$html .= '<input type="radio" name="'.$field['mafi_FieldName'].'" value="'.$target['mata_MasterTargetID'].'" checked> <label>'.$target['mata_NameEn'].'<label>';

								} else {

									$html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="'.$field['mafi_FieldName'].'" value="'.$target['mata_MasterTargetID'].'"> <label>'.$target['mata_NameEn'].'<label>';
								}
							}

							$x++;
						}

						$html .= '</span>';

					} else if ($field['field_type']=='Checkbox') {

						$html .= '<td><span class="form-inline"><label>';

						$sql_target = 'SELECT *
										FROM master_target
										WHERE mafi_MasterFieldID = "'.$field['master_field_id'].'"';
						$oRes_target = $oDB->Query($sql_target);
						while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

							$html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="'.$field['mafi_FieldName'].'" value="'.$target['mata_MasterTargetID'].'"> '.$target['mata_NameEn'].'<br>';
						}

						$html .= '</label></span>';

					} else if ($field['field_type']=='Selection') {

						$data = $member[$field['mafi_FieldName']];

						if ($field['master_field_id'] == 33 || $field['master_field_id'] == 45) {

							$sql_target = 'SELECT * FROM province WHERE prov_Deleted = "" ORDER BY prov_Name';
							$oRes_target = $oDB->Query($sql_target);

							$html .= '<td><select name="'.$field['mafi_FieldName'].'" class="form-control" '.$rq_af.' style="width:250px">
										<option value="">Please Select ..</option>';
								
							while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

								$select = "";
								if ($data == $target['prov_ProvinceID']) { $select = "selected"; }

								$html .= '<option value="'.$target['prov_ProvinceID'].'" '.$select.'>'.$target['prov_Name'].'</option>';
							}

							$html .= '</select>';

						} elseif ($field['master_field_id'] == 34 || $field['master_field_id'] == 46) {

							$sql_target = 'SELECT * FROM country WHERE coun_PhoneCode!=0 ORDER BY coun_Nicename';
							$oRes_target = $oDB->Query($sql_target);

							$html .= '<td><select name="'.$field['mafi_FieldName'].'" class="form-control" '.$rq_af.' style="width:250px">
										<option value="">Please Select ..</option>';
								
							while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

								$select = "";
								if ($data == $target['coun_CountryID']) { $select = "selected"; }

								$html .= '<option value="'.$target['coun_CountryID'].'" '.$select.'>'.$target['coun_Nicename'].'</option>';
							}

							$html .= '</select>';

						} else {

							$sql_target = 'SELECT *
											FROM master_target
											WHERE mafi_MasterFieldID = "'.$field['master_field_id'].'"';
							$oRes_target = $oDB->Query($sql_target);

							$html .= '<td><select name="'.$field['mafi_FieldName'].'" class="form-control" '.$rq_af.' style="width:250px">
										<option value="">Please Select ..</option>';
								
							while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

								$select = "";
								if ($data == $target['mata_MasterTargetID']) { $select = "selected"; }

								$html .= '<option value="'.$target['mata_MasterTargetID'].'" '.$select.'>'.$target['mata_NameEn'].'</option>';
							}

							$html .= '</select>';
						}

					} else if ($field['field_type']=='Tel') {

						$data = $member[$field['mafi_FieldName']];

						$phone_code = '';

						$strlen = strlen($data);

						if ($strlen <= 10) { 

							$phone_num = substr($data, 1,9);
							$phone_code = substr($data, 0,1);

						} else { 

							$phone_num = substr($data, ($strlen-9),9); 
							$phone_code = substr($data, 0,($strlen-9));
						}

						if ($phone_code == "0" || $phone_code == "") { $phone_code = "+66"; }

						# PHONE CODE

						$sql_code = 'SELECT DISTINCT coun_PhoneCode 
										FROM country 
										WHERE coun_PhoneCode NOT IN (0,1,7) 
										ORDER BY coun_PhoneCode';
						$oRes_code = $oDB->Query($sql_code);
						$option_code = '';
						while ($axRow_code = $oRes_code->FetchRow(DBI_ASSOC)){

							$check_code = '';
							if ('+'.$axRow_code['coun_PhoneCode'] == $phone_code) { $check_code = 'selected'; }

							$option_code .= '<option value="+'.$axRow_code['coun_PhoneCode'].'" '.$check_code.'>+'.$axRow_code['coun_PhoneCode'].'</option>';
						}

						$html .= '<td><span class="form-inline">
                    				<select class="form-control text-md" id="code_'.$field['mafi_FieldName'].'" name="code_'.$field['mafi_FieldName'].'" '.$rq_af.'>'.$option_code.'</select>
                    				<input type="text" style="width:168px" name="'.$field['mafi_FieldName'].'" value="'.$phone_num.'" maxlength="9" class="form-control text-md" placeholder="Tel" '.$rq_af.'>
                    			</span>';
					}

					$html .= '	</td></tr>';
				}
			}
		}

		$sql_custom = 'SELECT custom_field.*,
						custom_form.cufo_Require,
						field_type.fity_Name AS field_type
						FROM custom_field
						LEFT JOIN custom_form
						ON custom_form.cufi_CustomFieldID = custom_field.cufi_CustomFieldID
						LEFT JOIN field_type
						ON custom_field.fity_FieldTypeID = field_type.fity_FieldTypeID
						WHERE custom_form.card_CardID = "'.$card_CardID.'"
						AND custom_form.cufo_FillIn = "Y"
						ORDER BY custom_field.cufi_FieldOrder';

		$oRes = $oDB->Query($sql_custom);
		$check_field = $oDB->QueryOne($sql_custom);

		if ($check_field) {

			$html .= '<tr height="40px"><td colspan="3" style="text-align:center"><u><b>Custom</b></u></td></tr>';

			while ($field = $oRes->FetchRow(DBI_ASSOC)){

				$sql_member_custom = 'SELECT reda_Value
										FROM custom_register_data 
										WHERE mebe_MemberID="'.$member_id.'"
										AND card_CardID="'.$card_CardID.'"
										AND cufi_CustomFieldID="'.$field['cufi_CustomFieldID'].'"';
				$data = $oDB->QueryOne($sql_member_custom);

				if ($field['cufo_Require']=='Y') { 

					$text_rq = ' <span class="text-rq">*</span>';
					$rq_af = 'required autofocus'; 

				} else { $text_rq = '';	$rq_af = '';  }

				$html .= '	<tr height="40px"><td style="text-align:right">
								<b>'.$field['cufi_Name'].$text_rq.'</b></td>
								<td width="10px"></td>';

				if ($field['field_type']=='Text') {

					$html .= '<td style="text-align:center"><input type="text" name="'.$field['cufi_FieldName'].'" class="form-control text-md" placeholder="Text" '.$rq_af.' value="'.$data.'">';
						
				} else if ($field['field_type']=='Number') {

					$html .= '<td style="text-align:center"><input type="number" name="'.$field['cufi_FieldName'].'" class="form-control text-md" placeholder="Number" '.$rq_af.' value="'.$data.'">';
						
				} else if ($field['field_type']=='Date') {

					$year_data = substr($data,0,4);
					$month_data = substr($data,5,2);
					$date_data = substr($data,8,2);

					# DAY OPTION

					$option_date = '';

					for ($x = 1; $x < 32; $x++) {

						if ($x == $date_data) { $select = 'selected="selected"'; }
						else { $select = ''; }

						if (strlen($x) == 1) { $d = '0'.$x; }
						else { $d = $x; }

						$option_date .= '<option value="'.$d.'" '.$select.'>'.$d.'</option>';
					}


					# MONTH OPTION

					$month = ["Jan.", "Feb.", "Mar.", "Apr.", "May.", "Jun.", "Jul.", "Aug.", "Sep.", "Oct.", "Nov.", "Dec."];
					$option_month = '';

					for ($x = 1; $x < 13; $x++) {

						if ($x == $month_data) { $select = 'selected="selected"'; }
						else { $select = ''; }

						if (strlen($x) == 1) { $d = '0'.$x; }
						else { $d = $x; }

						$option_month .= '<option value="'.($d).'" '.$select.'>'.$month[$x-1].'</option>';
					}


					# YEAR OPTION

					$this_year = date('Y',time());
					$start_year = $this_year-100;
					$option_year = '';

					if ($year_data > $this_year) { $year_data = $year_data-543; }

					for ($x = $this_year; $x >= $start_year; $x--) {

						if ($x == $year_data) { $select = 'selected="selected"'; }
						else { $select = ''; }

						$option_year .= '<option value="'.$x.'" '.$select.')>'.$x.'</option>';
					}

					$html .= '<td><span class="form-inline">
								<select id="date" class="form-control text-md" name="'.$field['mafi_FieldName'].'_date" style="width:70px" '.$rq_af.'>
									<option value=""> - - -</option>
									'.$option_date.'
								</select>
								<select id="month" class="form-control text-md" name="'.$field['mafi_FieldName'].'_month" style="width:80px" '.$rq_af.'>
									<option value=""> - - - -</option>
									'.$option_month.'
								</select>
								<select id="year" class="form-control text-md" name="'.$field['mafi_FieldName'].'_year" style="width:90px" '.$rq_af.'>
									<option value=""> - - - - -</option>
									'.$option_year.'
								</select></span>';
						
				} else if ($field['field_type']=='Radio') {

					$x = 0;

					$html .= '<td><span class="form-inline"><label>';

					$sql_target = 'SELECT *
									FROM custom_list_value
									WHERE cufi_CustomFieldID = "'.$field['cufi_CustomFieldID'].'"';
					$oRes_target = $oDB->Query($sql_target);
					while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

						if ($data != 0) {

							if ($data == $target['clva_CustomListValueID']) { $check = "checked"; }
							else { $check = ''; }

						} else {

							if ($x==0) { $check = "checked"; }
							else { $check = ''; }
						}

						$html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="'.$field['cufi_FieldName'].'" value="'.$target['clva_CustomListValueID'].'" '.$check.'> '.$target['clva_Name'].'';

						$x++;
					}

					$html .= '</label></span>';

				} else if ($field['field_type']=='Checkbox') {

					$html .= '<td><span class="form-inline"><label>';

					$sql_target = 'SELECT *
									FROM custom_list_value
									WHERE cufi_CustomFieldID = "'.$field['cufi_CustomFieldID'].'"';
					$oRes_target = $oDB->Query($sql_target);
					while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

						if ($data == $target['clva_CustomListValueID']) { $check_c = 'checked'; }
						else { $check_c = 'checked'; }

						$html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="'.$field['cufi_FieldName'].'" value="'.$target['clva_CustomListValueID'].'" '.$check_c.'> '.$target['clva_Name'].'<br>';
					}

					$html .= '</label></span>';

				} else if ($field['field_type']=='Selection') {

					$html .= '<td><select name="'.$field['cufi_FieldName'].'" class="form-control" '.$rq_af.' style="width:250px">
								<option value="">Please Select ..</option>';

					$sql_target = 'SELECT *
									FROM custom_list_value
									WHERE cufi_CustomFieldID = "'.$field['cufi_CustomFieldID'].'"';
					$oRes_target = $oDB->Query($sql_target);
					while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

						if ($data == $target['clva_CustomListValueID']) { $select = 'selected="selected"'; }
						else { $select = ''; }

						$html .= '<option value="'.$target['clva_CustomListValueID'].'" '.$select.'>'.$target['clva_Name'].'</option>';
					}

					$html .= '</select>';

				} else if ($field['field_type']=='Tel') {

					$strlen = strlen($data);

					if ($strlen <= 10) { 

						$phone_num = substr($data, 1,9);
						$phone_code = substr($data, 0,1);

					} else { 

						$phone_num = substr($data, ($strlen-9),9); 
						$phone_code = substr($data, 0,($strlen-9));
					}

					if ($phone_code == "0" || $phone_code == "") { $phone_code = "+66"; }

					# PHONE CODE

					$sql_code = 'SELECT DISTINCT coun_PhoneCode 
									FROM country 
									WHERE coun_PhoneCode NOT IN (0,1,7) 
									ORDER BY coun_PhoneCode';
					$oRes_code = $oDB->Query($sql_code);
					$option_code = '';
					while ($axRow_code = $oRes_code->FetchRow(DBI_ASSOC)){

						$check_code = '';
						if ('+'.$axRow_code['coun_PhoneCode'] == $phone_code) { $check_code = 'selected'; }

						$option_code .= '<option value="+'.$axRow_code['coun_PhoneCode'].'" '.$check_code.'>+'.$axRow_code['coun_PhoneCode'].'</option>';
					}

					$html .= '<td><span class="form-inline">
                    			<select class="form-control text-md" id="code_'.$field['cufi_FieldName'].'" name="code_'.$field['cufi_FieldName'].'" '.$rq_af.'>'.$option_code.'</select>
                    			<input type="text" style="width:168px" name="'.$field['cufi_FieldName'].'" value="'.$phone_num.'" maxlength="9" class="form-control text-md" placeholder="Tel" '.$rq_af.'>
                    		</span>';
				}

				$html .= '	</td></tr>';
			}
		}

		$html .= '		</table>
					</span>
					<br>
		            <div class="clear_all">
		                <button class="btn btn-success btn_hide" type="submit">SUBMIT</button>
		                <input type="hidden" id="act" name="act" value="save" />
		                <input type="hidden" id="member_id" name="member_id" value="'.$member_id.'" />
		                &nbsp;&nbsp;&nbsp;
		                <button class="btn btn-warning btn_hide" type="reset" onclick="window.location.href='."'".'register.php'."'".'">CANCEL</button>
		            
		            </div>
		            <br>';
	}

	echo $html;
	exit;
}


else if($TASK =='Get_RegisterExisting'){

	$card_CardID = $_REQUEST['card_id'];
	$member_id = $_REQUEST['member_id'];
	$member_brand_id = $_REQUEST['member_brand_id'];

	# MEMBER DATA

	$sql_member = 'SELECT * FROM mb_member WHERE member_id="'.$member_id.'"';
	$oRes = $oDB->Query($sql_member);
	$member = $oRes->FetchRow(DBI_ASSOC);

	# MEMBER BRAND DATA

	if ($member_brand_id) {

		$sql_member_brand = 'SELECT * FROM mb_member_brand WHERE member_brand_id="'.$member_brand_id.'"';
		$oRes_brand = $oDB->Query($sql_member_brand);
		$member_brand = $oRes_brand->FetchRow(DBI_ASSOC);

	} else {

		$oRes_brand = $oDB->Query($sql_member);
		$member_brand = $oRes_brand->FetchRow(DBI_ASSOC);
	}

	# MEMBER

	if ($member['member_image'] && $member['member_image']!='user.png') {

		$member_image = '<img src="../../upload/member_upload/'.$member['member_image'].'" width="100" height="100" class="img-circle image_border"/>';

	} else if ($member['facebook_id']) {

		 $member_image = '<img src="http://graph.facebook.com/'.$member['facebook_id'].'/picture?type=square" width="100" height="100" class="img-circle image_border" />';

	} else {
				                    	
		$member_image = '<img src="../../images/user.png" width="100" height="100" class="img-circle image_border" />';
	}

	$html = '<br><br>'.$member_image;

	# CHECK MULTIPLE CARD

	$sql_multiple = 'SELECT flag_multiple 
						FROM mi_card 
						WHERE card_id="'.$card_CardID.'"';
	$multiple = $oDB->QueryOne($sql_multiple);

	# CHECK MEMBER REGISTER

	$sql_register = 'SELECT member_register_id AS id,
						member_card_code,
						member_brand_code
						FROM mb_member_register 
						WHERE member_id="'.$member_id.'" 
						AND card_id="'.$card_CardID.'"
						AND flag_del=""';

	$id = $oDB->Query($sql_register);
	$regis = $id->FetchRow(DBI_ASSOC);

	if ($regis['id'] && $multiple=='No') {

		$html .= '<br><br><span style="color:red;font-size:16px"><b>Already Register</b></span><br><br>';

	} else {

		$html .= '<table>';

		$topic = array("Profile", "Home Address", "Work Address", "Work", "Contact");

		for ($i=0; $i <5 ; $i++) { 

			$sql_field = 'SELECT a.*,b.*,c.*,

							a.mafi_MasterFieldID AS master_field_id,
							b.refo_Target,
							d.fity_Name AS field_type

							FROM master_field AS a

							LEFT JOIN register_form AS b
							ON b.mafi_MasterFieldID = a.mafi_MasterFieldID

							LEFT JOIN mi_card AS c
							ON b.card_CardID = c.card_id

							LEFT JOIN field_type AS d
							ON a.mafi_FieldType = d.fity_FieldTypeID

							WHERE a.mafi_Position = "'.$topic[$i].'"
							AND a.mafi_Deleted != "T"
							AND c.card_id = "'.$card_CardID.'"
							AND b.refo_FillIn = "Y"

							GROUP BY a.mafi_FieldName
							ORDER BY a.mafi_FieldOrder';

			$oRes = $oDB->Query($sql_field);
			$check_field = $oDB->QueryOne($sql_field);

			if ($check_field) {

				$html .= '<tr height="40px"><td colspan="3" style="text-align:center"><u><b>'.$topic[$i].'</b></u></td></tr>';

				while ($field = $oRes->FetchRow(DBI_ASSOC)){

					if ($field['refo_Require']=='Y') { 

						$text_rq = ' <span class="text-rq">*</span>';
						$rq_af = 'required autofocus'; 

					} else { $text_rq = '';	$rq_af = '';  }

					$html .= '	<tr height="40px"><td style="text-align:right">
										<b>'.$field['mafi_NameEn'].$text_rq.'</b></td>
										<td width="10px"></td>';

					if ($field['field_type']=='Text') {

						# MEMBER BRAND CODE & MEMBER CARD COE

						if ($field['master_field_id']=='48') { # CARD

							if ($regis['member_card_code'] || $member_brand['member_card_code']) { 

								$disabled = 'readonly'; 

							} else { $disabled = ''; }

							if ($member_brand['member_card_code']) { $data = $member_brand['member_card_code']; }
							else { $data = $regis['member_card_code']; }

							$member[$field['mafi_FieldName']] = $data;
							
						} elseif ($field['master_field_id']=='49') { # BRAND

							if ($regis['member_brand_code'] || $member_brand['member_brand_code']) { 

								$disabled = 'readonly'; 

							} else { $disabled = ''; }

							if ($member_brand['member_brand_code']) { $data = $member_brand['member_brand_code']; }
							else { $data = $regis['member_brand_code']; }

							$member[$field['mafi_FieldName']] = $data;

						} else {

							$disabled = '';

							if ($member[$field['mafi_FieldName']]) { $data = $member[$field['mafi_FieldName']];
							} else { $data = $member_brand[$field['mafi_FieldName']]; }

							$member[$field['mafi_FieldName']] = $data;
						}

						$html .= '<td style="text-align:center"><input type="text" name="'.$field['mafi_FieldName'].'" class="form-control text-md" placeholder="Text" '.$rq_af.' '.$disabled.' value="'.$member[$field['mafi_FieldName']].'">';
						
					} else if ($field['field_type']=='Number') {

						if ($member[$field['mafi_FieldName']]) { $data = $member[$field['mafi_FieldName']];
						} else { $data = $member_brand[$field['mafi_FieldName']]; }

						$member[$field['mafi_FieldName']] = $data;

						$html .= '<td style="text-align:center"><input type="number" name="'.$field['mafi_FieldName'].'" class="form-control text-md" placeholder="Number" value="'.$member[$field['mafi_FieldName']].'" '.$rq_af.'>';
						
					} else if ($field['field_type']=='Date') {

						if ($member[$field['mafi_FieldName']]) { $data = $member[$field['mafi_FieldName']];
						} else { $data = $member_brand[$field['mafi_FieldName']]; }

						$data = $member[$field['mafi_FieldName']];
						$year_data = substr($data,0,4);
						$month_data = substr($data,5,2);
						$date_data = substr($data,8,2);

						# DAY OPTION

						$option_date = '';

						for ($x = 1; $x < 32; $x++) {

							if ($x == $date_data) { 

								$select = 'selected="selected"';
								$date_member = $x;

							} else { $select = ''; }

							if (strlen($x) == 1) { $d = '0'.$x; }
							else { $d = $x; }

							$option_date .= '<option value="'.$d.'" '.$select.'>'.$d.'</option>';
						}


						# MONTH OPTION

						$month = ["Jan.", "Feb.", "Mar.", "Apr.", "May.", "Jun.", "Jul.", "Aug.", "Sep.", "Oct.", "Nov.", "Dec."];
						$option_month = '';

						for ($x = 1; $x < 13; $x++) {

							if ($x == $month_data) { 

								$select = 'selected="selected"';
								$month_member = $month[$x-1];

							} else { $select = ''; }

							if (strlen($x) == 1) { $d = '0'.$x; }
							else { $d = $x; }

							$option_month .= '<option value="'.($d).'" '.$select.'>'.$month[$x-1].'</option>';
						}


						# YEAR OPTION

						$this_year = date('Y',time());
						$start_year = $this_year-100;
						$option_year = '';

						if ($year_data > $this_year) { $year_data = $year_data-543; }

						for ($x = $this_year; $x >= $start_year; $x--) {

							if ($x == $year_data) { 

								$select = 'selected="selected"';
								$year_member = $x;

							} else { $select = ''; }

							$option_year .= '<option value="'.$x.'" '.$select.')>'.$x.'</option>';
						}

						$html .= '<td>';

						$html .= '<span class="form-inline">
									<select id="date" class="form-control text-md" name="'.$field['mafi_FieldName'].'_date" style="width:70px" '.$rq_af.'>
										<option value=""> - - -</option>
										'.$option_date.'
									</select>
									<select id="month" class="form-control text-md" name="'.$field['mafi_FieldName'].'_month" style="width:80px" '.$rq_af.'>
										<option value=""> - - - -</option>
										'.$option_month.'
									</select>
									<select id="year" class="form-control text-md" name="'.$field['mafi_FieldName'].'_year" style="width:90px" '.$rq_af.'>
										<option value=""> - - - - -</option>
										'.$option_year.'
									</select></span>';
						
					} else if ($field['field_type']=='Radio') {

						$x = 0;

						if ($member[$field['mafi_FieldName']]) { $data = $member[$field['mafi_FieldName']];
						} else { $data = $member_brand[$field['mafi_FieldName']]; }

						$html .= '<td><span class="form-inline">';

						$sql_target = 'SELECT *
										FROM master_target
										WHERE mafi_MasterFieldID = "'.$field['master_field_id'].'"';
						$oRes_target = $oDB->Query($sql_target);
						while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

							if ($data == $target['mata_MasterTargetID']) {

								if ($x==0) {

									$html .= '<input type="radio" name="'.$field['mafi_FieldName'].'" value="'.$target['mata_MasterTargetID'].'" checked> <label>'.$target['mata_NameEn'].'<label>';

								} else {

									$html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="'.$field['mafi_FieldName'].'" value="'.$target['mata_MasterTargetID'].'" checked> <label>'.$target['mata_NameEn'].'<label>';
								}

							} else {

								if ($x==0) {

									$html .= '<input type="radio" name="'.$field['mafi_FieldName'].'" value="'.$target['mata_MasterTargetID'].'" checked> <label>'.$target['mata_NameEn'].'<label>';

								} else {

									$html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="'.$field['mafi_FieldName'].'" value="'.$target['mata_MasterTargetID'].'"> <label>'.$target['mata_NameEn'].'<label>';
								}
							}

							$x++;
						}

						$html .= '</span>';

					} else if ($field['field_type']=='Checkbox') {

						$html .= '<td><span class="form-inline"><label>';

						$sql_target = 'SELECT *
										FROM master_target
										WHERE mafi_MasterFieldID = "'.$field['master_field_id'].'"';
						$oRes_target = $oDB->Query($sql_target);
						while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

							$html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="'.$field['mafi_FieldName'].'" value="'.$target['mata_MasterTargetID'].'"> '.$target['mata_NameEn'].'<br>';
						}

						$html .= '</label></span>';

					} else if ($field['field_type']=='Selection') {

						if ($member[$field['mafi_FieldName']]) { $data = $member[$field['mafi_FieldName']];
						} else { $data = $member_brand[$field['mafi_FieldName']]; }

						if ($field['master_field_id'] == 33 || $field['master_field_id'] == 45) {

							$sql_target = 'SELECT * FROM province WHERE prov_Deleted = "" ORDER BY prov_Name';
							$oRes_target = $oDB->Query($sql_target);

							$html .= '<td><select name="'.$field['mafi_FieldName'].'" class="form-control" '.$rq_af.' style="width:250px">
										<option value="">Please Select ..</option>';
								
							while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

								$select = "";
								if ($data == $target['prov_ProvinceID']) { $select = "selected"; }

								$html .= '<option value="'.$target['prov_ProvinceID'].'" '.$select.'>'.$target['prov_Name'].'</option>';
							}

							$html .= '</select>';

						} elseif ($field['master_field_id'] == 34 || $field['master_field_id'] == 46) {

							$sql_target = 'SELECT * FROM country WHERE coun_PhoneCode!=0 ORDER BY coun_Nicename';
							$oRes_target = $oDB->Query($sql_target);

							$html .= '<td><select name="'.$field['mafi_FieldName'].'" class="form-control" '.$rq_af.' style="width:250px">
										<option value="">Please Select ..</option>';
								
							while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

								$select = "";
								if ($data == $target['coun_CountryID']) { $select = "selected"; }

								$html .= '<option value="'.$target['coun_CountryID'].'" '.$select.'>'.$target['coun_Nicename'].'</option>';
							}

							$html .= '</select>';

						} else {

							$sql_target = 'SELECT *
											FROM master_target
											WHERE mafi_MasterFieldID = "'.$field['master_field_id'].'"';
							$oRes_target = $oDB->Query($sql_target);

							$html .= '<td><select name="'.$field['mafi_FieldName'].'" class="form-control" '.$rq_af.' style="width:250px">
										<option value="">Please Select ..</option>';
								
							while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

								$select = "";
								if ($data == $target['mata_MasterTargetID']) { $select = "selected"; }

								$html .= '<option value="'.$target['mata_MasterTargetID'].'" '.$select.'>'.$target['mata_NameEn'].'</option>';
							}

							$html .= '</select>';
						}

					} else if ($field['field_type']=='Tel') {

						if ($member[$field['mafi_FieldName']]) { $data = $member[$field['mafi_FieldName']];
						} else { $data = $member_brand[$field['mafi_FieldName']]; }

						$phone_code = '';

						$strlen = strlen($data);

						if ($strlen <= 10) { 

							$phone_num = substr($data, 1,9);
							$phone_code = substr($data, 0,1);

						} else { 

							$phone_num = substr($data, ($strlen-9),9); 
							$phone_code = substr($data, 0,($strlen-9));
						}

						if ($phone_code == "0" || $phone_code == "") { $phone_code = "+66"; }

						# PHONE CODE

						$sql_code = 'SELECT DISTINCT coun_PhoneCode 
										FROM country 
										WHERE coun_PhoneCode NOT IN (0,1,7) 
										ORDER BY coun_PhoneCode';
						$oRes_code = $oDB->Query($sql_code);
						$option_code = '';
						while ($axRow_code = $oRes_code->FetchRow(DBI_ASSOC)){

							$check_code = '';
							if ('+'.$axRow_code['coun_PhoneCode'] == $phone_code) { $check_code = 'selected'; }

							$option_code .= '<option value="+'.$axRow_code['coun_PhoneCode'].'" '.$check_code.'>+'.$axRow_code['coun_PhoneCode'].'</option>';
						}

						$html .= '<td><span class="form-inline">
                    				<select class="form-control text-md" id="code_'.$field['mafi_FieldName'].'" name="code_'.$field['mafi_FieldName'].'" '.$rq_af.'>'.$option_code.'</select>
                    				<input type="text" style="width:168px" name="'.$field['mafi_FieldName'].'" value="'.$phone_num.'" maxlength="9" class="form-control text-md" placeholder="Tel" '.$rq_af.'>
                    			</span>';
					}

					$html .= '	</td></tr>';
				}
			}
		}

		$sql_custom = 'SELECT custom_field.*,
						custom_form.cufo_Require,
						field_type.fity_Name AS field_type
						FROM custom_field
						LEFT JOIN custom_form
						ON custom_form.cufi_CustomFieldID = custom_field.cufi_CustomFieldID
						LEFT JOIN field_type
						ON custom_field.fity_FieldTypeID = field_type.fity_FieldTypeID
						WHERE custom_form.card_CardID = "'.$card_CardID.'"
						AND custom_form.cufo_FillIn = "Y"
						ORDER BY custom_field.cufi_FieldOrder';

		$oRes = $oDB->Query($sql_custom);
		$check_field = $oDB->QueryOne($sql_custom);

		if ($check_field) {

			$html .= '<tr height="40px"><td colspan="3" style="text-align:center"><u><b>Custom</b></u></td></tr>';

			while ($field = $oRes->FetchRow(DBI_ASSOC)){

				$sql_member_custom = 'SELECT reda_Value
										FROM custom_register_data 
										WHERE mebe_MemberID="'.$member_id.'"
										AND card_CardID="'.$card_CardID.'"
										AND cufi_CustomFieldID="'.$field['cufi_CustomFieldID'].'"';
				$data = $oDB->QueryOne($sql_member_custom);

				if ($field['cufo_Require']=='Y') { 

					$text_rq = ' <span class="text-rq">*</span>';
					$rq_af = 'required autofocus'; 

				} else { $text_rq = '';	$rq_af = '';  }

				$html .= '	<tr height="40px"><td style="text-align:right">
								<b>'.$field['cufi_Name'].$text_rq.'</b></td>
								<td width="10px"></td>';

				if ($field['field_type']=='Text') {

					$html .= '<td style="text-align:center"><input type="text" name="'.$field['cufi_FieldName'].'" class="form-control text-md" placeholder="Text" '.$rq_af.' value="'.$data.'">';
						
				} else if ($field['field_type']=='Number') {

					$html .= '<td style="text-align:center"><input type="number" name="'.$field['cufi_FieldName'].'" class="form-control text-md" placeholder="Number" '.$rq_af.' value="'.$data.'">';
						
				} else if ($field['field_type']=='Date') {

					$year_data = substr($data,0,4);
					$month_data = substr($data,5,2);
					$date_data = substr($data,8,2);

					# DAY OPTION

					$option_date = '';

					for ($x = 1; $x < 32; $x++) {

						if ($x == $date_data) { $select = 'selected="selected"'; }
						else { $select = ''; }

						if (strlen($x) == 1) { $d = '0'.$x; }
						else { $d = $x; }

						$option_date .= '<option value="'.$d.'" '.$select.'>'.$d.'</option>';
					}


					# MONTH OPTION

					$month = ["Jan.", "Feb.", "Mar.", "Apr.", "May.", "Jun.", "Jul.", "Aug.", "Sep.", "Oct.", "Nov.", "Dec."];
					$option_month = '';

					for ($x = 1; $x < 13; $x++) {

						if ($x == $month_data) { $select = 'selected="selected"'; }
						else { $select = ''; }

						if (strlen($x) == 1) { $d = '0'.$x; }
						else { $d = $x; }

						$option_month .= '<option value="'.($d).'" '.$select.'>'.$month[$x-1].'</option>';
					}


					# YEAR OPTION

					$this_year = date('Y',time());
					$start_year = $this_year-100;
					$option_year = '';

					if ($year_data > $this_year) { $year_data = $year_data-543; }

					for ($x = $this_year; $x >= $start_year; $x--) {

						if ($x == $year_data) { $select = 'selected="selected"'; }
						else { $select = ''; }

						$option_year .= '<option value="'.$x.'" '.$select.')>'.$x.'</option>';
					}

					$html .= '<td><span class="form-inline">
								<select id="date" class="form-control text-md" name="'.$field['mafi_FieldName'].'_date" style="width:70px" '.$rq_af.'>
									<option value=""> - - -</option>
									'.$option_date.'
								</select>
								<select id="month" class="form-control text-md" name="'.$field['mafi_FieldName'].'_month" style="width:80px" '.$rq_af.'>
									<option value=""> - - - -</option>
									'.$option_month.'
								</select>
								<select id="year" class="form-control text-md" name="'.$field['mafi_FieldName'].'_year" style="width:90px" '.$rq_af.'>
									<option value=""> - - - - -</option>
									'.$option_year.'
								</select></span>';
						
				} else if ($field['field_type']=='Radio') {

					$x = 0;

					$html .= '<td><span class="form-inline"><label>';

					$sql_target = 'SELECT *
									FROM custom_list_value
									WHERE cufi_CustomFieldID = "'.$field['cufi_CustomFieldID'].'"';
					$oRes_target = $oDB->Query($sql_target);
					while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

						if ($data != 0) {

							if ($data == $target['clva_CustomListValueID']) { $check = "checked"; }
							else { $check = ''; }

						} else {

							if ($x==0) { $check = "checked"; }
							else { $check = ''; }
						}

						$html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="'.$field['cufi_FieldName'].'" value="'.$target['clva_CustomListValueID'].'" '.$check.'> '.$target['clva_Name'].'';

						$x++;
					}

					$html .= '</label></span>';

				} else if ($field['field_type']=='Checkbox') {

					$html .= '<td><span class="form-inline"><label>';

					$sql_target = 'SELECT *
									FROM custom_list_value
									WHERE cufi_CustomFieldID = "'.$field['cufi_CustomFieldID'].'"';
					$oRes_target = $oDB->Query($sql_target);
					while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

						if ($data == $target['clva_CustomListValueID']) { $check_c = 'checked'; }
						else { $check_c = 'checked'; }

						$html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="'.$field['cufi_FieldName'].'" value="'.$target['clva_CustomListValueID'].'" '.$check_c.'> '.$target['clva_Name'].'<br>';
					}

					$html .= '</label></span>';

				} else if ($field['field_type']=='Selection') {

					$html .= '<td><select name="'.$field['cufi_FieldName'].'" class="form-control" '.$rq_af.' style="width:250px">
								<option value="">Please Select ..</option>';

					$sql_target = 'SELECT *
									FROM custom_list_value
									WHERE cufi_CustomFieldID = "'.$field['cufi_CustomFieldID'].'"';
					$oRes_target = $oDB->Query($sql_target);
					while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

						if ($data == $target['clva_CustomListValueID']) { $select = 'selected="selected"'; }
						else { $select = ''; }

						$html .= '<option value="'.$target['clva_CustomListValueID'].'" '.$select.'>'.$target['clva_Name'].'</option>';
					}

					$html .= '</select>';

				} else if ($field['field_type']=='Tel') {

					$strlen = strlen($data);

					if ($strlen <= 10) { 

						$phone_num = substr($data, 1,9);
						$phone_code = substr($data, 0,1);

					} else { 

						$phone_num = substr($data, ($strlen-9),9); 
						$phone_code = substr($data, 0,($strlen-9));
					}

					if ($phone_code == "0" || $phone_code == "") { $phone_code = "+66"; }

					# PHONE CODE

					$sql_code = 'SELECT DISTINCT coun_PhoneCode 
									FROM country 
									WHERE coun_PhoneCode NOT IN (0,1,7) 
									ORDER BY coun_PhoneCode';
					$oRes_code = $oDB->Query($sql_code);
					$option_code = '';
					while ($axRow_code = $oRes_code->FetchRow(DBI_ASSOC)){

						$check_code = '';
						if ('+'.$axRow_code['coun_PhoneCode'] == $phone_code) { $check_code = 'selected'; }

						$option_code .= '<option value="+'.$axRow_code['coun_PhoneCode'].'" '.$check_code.'>+'.$axRow_code['coun_PhoneCode'].'</option>';
					}

					$html .= '<td><span class="form-inline">
                    			<select class="form-control text-md" id="code_'.$field['cufi_FieldName'].'" name="code_'.$field['cufi_FieldName'].'" '.$rq_af.'>'.$option_code.'</select>
                    			<input type="text" style="width:168px" name="'.$field['cufi_FieldName'].'" value="'.$phone_num.'" maxlength="9" class="form-control text-md" placeholder="Tel" '.$rq_af.'>
                    		</span>';
				}

				$html .= '	</td></tr>';
			}
		}

		$html .= '		</table>
					</span>
					<br>
		            <div class="clear_all">
		                <button class="btn btn-success btn_hide" type="submit">SUBMIT</button>
		                <input type="hidden" id="act" name="act" value="save" />
		                <input type="hidden" id="member_id" name="member_id" value="'.$member_id.'" />
		                &nbsp;&nbsp;&nbsp;
		                <button class="btn btn-warning btn_hide" type="reset" onclick="window.location.href='."'".'register.php'."'".'">CANCEL</button>
		            
		            </div>
		            <br>';
	}

	echo $html;
	exit;
}


else if($TASK =='Get_RegisterMemberData'){

	$search_member = trim_txt($_REQUEST['search_member_brand']);
	$card_CardID = trim_txt($_REQUEST['card_id']);
	$member_brand_id = trim_txt($_REQUEST['member_brand_id']);

	if ($search_member!='') {

		$sql_member = 'SELECT DISTINCT mb_member.* 
							FROM mb_member
							WHERE email LIKE "%'.$search_member.'%"
							OR mobile LIKE "'.$search_member.'%"
							OR firstname LIKE "'.$search_member.'%"
							OR lastname LIKE "'.$search_member.'%"';

		$oRes_member = $oDB->Query($sql_member);
		$member = $oRes_member->FetchRow(DBI_ASSOC);

		if ($member['member_id']) {

			$html = '<br><br><label class="adj_row">Choose Member</label><br>
					<div style="overflow-x: scroll;width:800px;"><table><tr>';

			$oRes = $oDB->Query($sql_member);
			while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

				if ($axRow['member_image'] && $axRow['member_image']!='user.png') {

			    $member_image = '<img src="../../upload/member_upload/'.$axRow['member_image'].'" width="50" height="50" class="img-circle image_border"/>';

				} else if ($axRow['facebook_id']) {

				    $member_image = '<img src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=square" width="50" height="50" class="img-circle image_border" />';

				} else {
				                    	
				    $member_image = '<img src="../../images/user.png" width="50" height="50" class="img-circle image_border" />';
				}

				$html .= '<td><button type="button" class="btn" id="'.$axRow['member_id'].'" onclick="SearchRegisterExisting('.$axRow['member_id'].','.$member_brand_id.')">
							<table><tr>
							<td width="60px" style="text-align:center">'.$member_image.'</td>
							<td width="10px">&nbsp;</td>
							<td>'.$axRow['firstname'].' '.$axRow['lastname'].'<br>
								'.$axRow['email'].'<br>'.$axRow['mobile'].'</td>
							</tr></table>
						</button></td><td width="5px">&nbsp;</td>';
			}

			$html .= '</tr></table><br></div>
					<span id="member_data"></span>';

			echo $html;
		
		} else {

			# MEMBER BRAND

			$sql_member_brand = 'SELECT * FROM mb_member_brand WHERE member_brand_id="'.$member_brand_id.'"';
			$oRes_brand = $oDB->Query($sql_member_brand);
			$member_brand = $oRes_brand->FetchRow(DBI_ASSOC);

			$html = '<br><br><label class="adj_row">Insert New Member</label><br><table>';

			$topic = array("Profile", "Home Address", "Work Address", "Work", "Contact");

			for ($i=0; $i <5 ; $i++) { 

				$sql_field = 'SELECT a.*,b.*,c.*,

								a.mafi_MasterFieldID AS master_field_id,
								b.refo_Target,
								d.fity_Name AS field_type

								FROM master_field AS a

								LEFT JOIN register_form AS b
								ON b.mafi_MasterFieldID = a.mafi_MasterFieldID

								LEFT JOIN mi_card AS c
								ON b.card_CardID = c.card_id

								LEFT JOIN field_type AS d
								ON a.mafi_FieldType = d.fity_FieldTypeID

								WHERE a.mafi_Position = "'.$topic[$i].'"
								AND a.mafi_Deleted != "T"
								AND c.card_id = "'.$card_CardID.'"
								AND b.refo_FillIn = "Y"

								GROUP BY a.mafi_FieldName
								ORDER BY a.mafi_FieldOrder';

				$oRes = $oDB->Query($sql_field);
				$check_field = $oDB->QueryOne($sql_field);

				if ($check_field) {

					$html .= '<tr height="40px"><td colspan="3" style="text-align:center"><u><b>'.$topic[$i].'</b></u></td></tr>';

					while ($field = $oRes->FetchRow(DBI_ASSOC)){

						if ($field['refo_Require']=='Y') { 

							$text_rq = ' <span class="text-rq">*</span>';
							$rq_af = 'required autofocus'; 

						} else { $text_rq = '';	$rq_af = '';  }

						$html .= '	<tr height="40px"><td style="text-align:right">
											<b>'.$field['mafi_NameEn'].$text_rq.'</b></td>
											<td width="10px"></td>';

						if ($field['master_field_id'] == 33 || $field['master_field_id'] == 45) {

							$data = $member_brand[$field['mafi_FieldName']];

							$sql_target = 'SELECT * FROM province WHERE prov_Deleted = "" ORDER BY prov_Name';
							$oRes_target = $oDB->Query($sql_target);

							$html .= '<td><select name="'.$field['mafi_FieldName'].'" class="form-control" '.$rq_af.' style="width:250px">
										<option value="">Please Select ..</option>';
								
							while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

								$select = "";
								if ($data == $target['prov_ProvinceID']) { $select = "selected"; }

								$html .= '<option value="'.$target['prov_ProvinceID'].'" '.$select.'>'.$target['prov_Name'].'</option>';
							}

							$html .= '</select>';

						} elseif ($field['master_field_id'] == 34 || $field['mafi_MasterFieldID'] == 46) {

							$data = $member_brand[$field['mafi_FieldName']];

							$sql_target = 'SELECT * FROM country WHERE coun_PhoneCode!=0 ORDER BY coun_Nicename';
								$oRes_target = $oDB->Query($sql_target);

							$html .= '<td><select name="'.$field['mafi_FieldName'].'" class="form-control" '.$rq_af.' style="width:250px">
										<option value="">Please Select ..</option>';
								
							while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

								$select = "";
								if ($data == $target['coun_CountryID']) { $select = "selected"; }

								$html .= '<option value="'.$target['coun_CountryID'].'" '.$select.'>'.$target['coun_Nicename'].'</option>';
							}

							$html .= '</select>';

						} elseif ($field['field_type']=='Text') {

							# MEMBER BRAND CODE & MEMBER CARD COE

							if ($field['master_field_id']=='48') { # CARD

								if ($member_brand['member_card_code']) { $disabled = 'readonly'; } 
								else { $disabled = ''; }

								$member[$field['mafi_FieldName']] = $member_brand['member_card_code'];
								
							} elseif ($field['master_field_id']=='49') { # BRAND

								if ($member_brand['member_brand_code']) { $disabled = 'readonly'; } 
								else { $disabled = ''; }

								$member[$field['mafi_FieldName']] = $member_brand['member_brand_code'];

							} else {

								$disabled = '';

								$member[$field['mafi_FieldName']] = $member_brand[$field['mafi_FieldName']];
							}

							$html .= '<td style="text-align:center"><input type="text" name="'.$field['mafi_FieldName'].'" class="form-control text-md" placeholder="Text" '.$rq_af.' '.$disabled.' value="'.$member[$field['mafi_FieldName']].'">';
							
						} elseif ($field['field_type']=='Number') {

							$member[$field['mafi_FieldName']] = $member_brand[$field['mafi_FieldName']];

							$html .= '<td style="text-align:center"><input type="number" name="'.$field['mafi_FieldName'].'" class="form-control text-md" placeholder="Number" '.$rq_af.' '.$disabled.' value="'.$member[$field['mafi_FieldName']].'">';
							
						} else if ($field['field_type']=='Date') {

							$data = $member_brand[$field['mafi_FieldName']];
							$year_data = substr($data,0,4);
							$month_data = substr($data,5,2);
							$date_data = substr($data,8,2);

							# DAY OPTION

							$option_date = '';

							for ($x = 1; $x < 32; $x++) {

								if ($x == $date_data) { 

									$select = 'selected="selected"';
									$date_member = $x;

								} else { $select = ''; }

								if (strlen($x) == 1) { $d = '0'.$x; }
								else { $d = $x; }

								$option_date .= '<option value="'.$d.'" '.$select.'>'.$d.'</option>';
							}


							# MONTH OPTION

							$month = ["Jan.", "Feb.", "Mar.", "Apr.", "May.", "Jun.", "Jul.", "Aug.", "Sep.", "Oct.", "Nov.", "Dec."];
							$option_month = '';

							for ($x = 1; $x < 13; $x++) {

								if ($x == $month_data) { 

									$select = 'selected="selected"';
									$month_member = $month[$x-1];

								} else { $select = ''; }

								if (strlen($x) == 1) { $d = '0'.$x; }
								else { $d = $x; }

								$option_month .= '<option value="'.($d).'" '.$select.'>'.$month[$x-1].'</option>';
							}


							# YEAR OPTION

							$this_year = date('Y',time());
							$start_year = $this_year-100;
							$option_year = '';

							if ($year_data > $this_year) { $year_data = $year_data-543; }

							for ($x = $this_year; $x >= $start_year; $x--) {

								if ($x == $year_data) { 

									$select = 'selected="selected"';
									$year_member = $x;

								} else { $select = ''; }

								$option_year .= '<option value="'.$x.'" '.$select.')>'.$x.'</option>';
							}

							$html .= '<td>';

							$html .= '<span class="form-inline">
										<select id="date" class="form-control text-md" name="'.$field['mafi_FieldName'].'_date" style="width:70px" '.$rq_af.'>
											<option value=""> - - -</option>
											'.$option_date.'
										</select>
										<select id="month" class="form-control text-md" name="'.$field['mafi_FieldName'].'_month" style="width:80px" '.$rq_af.'>
											<option value=""> - - - -</option>
											'.$option_month.'
										</select>
										<select id="year" class="form-control text-md" name="'.$field['mafi_FieldName'].'_year" style="width:90px" '.$rq_af.'>
											<option value=""> - - - - -</option>
											'.$option_year.'
										</select></span>';
							
						} else if ($field['field_type']=='Radio') {

							$x = 0;

							$data = $member_brand[$field['mafi_FieldName']];

							$html .= '<td><span class="form-inline"><label>';

							$sql_target = 'SELECT *
											FROM master_target
											WHERE mafi_MasterFieldID = "'.$field['master_field_id'].'"';
							$oRes_target = $oDB->Query($sql_target);
							while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

								if ($data != 0) {

									if ($data == $target['mata_MasterTargetID']) {

										if ($x==0) {

											$html .= '<input type="radio" name="'.$field['mafi_FieldName'].'" value="'.$target['mata_MasterTargetID'].'" checked> <label>'.$target['mata_NameEn'].'</label>';

										} else {

											$html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="'.$field['mafi_FieldName'].'" value="'.$target['mata_MasterTargetID'].'" checked> <label>'.$target['mata_NameEn'].'</label>';
										}

									} else {

										if ($x==0) {

											$html .= '<input type="radio" name="'.$field['mafi_FieldName'].'" value="'.$target['mata_MasterTargetID'].'"> <label>'.$target['mata_NameEn'].'</label>';

										} else {

											$html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="'.$field['mafi_FieldName'].'" value="'.$target['mata_MasterTargetID'].'"> <label>'.$target['mata_NameEn'].'</label>';
										}
									}
								}

								$x++;
							}

							$html .= '</span>';

						} else if ($field['field_type']=='Checkbox') {

							$html .= '<td><span class="form-inline"><label>';

							$sql_target = 'SELECT *
											FROM master_target
											WHERE mafi_MasterFieldID = "'.$field['master_field_id'].'"';
							$oRes_target = $oDB->Query($sql_target);
							while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

								$html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="'.$field['mafi_FieldName'].'" value="'.$target['mata_MasterTargetID'].'"> '.$target['mata_NameEn'].'<br>';
							}

							$html .= '</label></span>';

						} else if ($field['field_type']=='Selection') {

							$data = $member_brand[$field['mafi_FieldName']];

							$sql_target = 'SELECT *
											FROM master_target
											WHERE mafi_MasterFieldID = "'.$field['master_field_id'].'"';
							$oRes_target = $oDB->Query($sql_target);

							$html .= '<td><select name="'.$field['mafi_FieldName'].'" class="form-control" '.$rq_af.' style="width:250px">
										<option value="">Please Select ..</option>';
								
							while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

								$select = "";
								if ($data == $target['mata_MasterTargetID']) { $select = "selected"; }

								$html .= '<option value="'.$target['mata_MasterTargetID'].'" '.$select.'>'.$target['mata_NameEn'].'</option>';
							}

							$html .= '</select>';

						} else if ($field['field_type']=='Tel') {

							$data = $member_brand[$field['mafi_FieldName']];

							# PHONE CODE

							$phone_code = '';

							if ($data) { $disabled = 'readonly'; }
							else { $disabled = ''; }

							$strlen = strlen($data);

							if ($strlen <= 10) { 

								$phone_num = substr($data, 1,9);
								$phone_code = substr($data, 0,1);

							} else { 

								$phone_num = substr($data, ($strlen-9),9); 
								$phone_code = substr($data, 0,($strlen-9));
							}

							if ($phone_code == "0" || $phone_code == "") { $phone_code = "+66"; }

							# PHONE CODE

							$sql_code = 'SELECT DISTINCT coun_PhoneCode 
											FROM country 
											WHERE coun_PhoneCode NOT IN (0,1,7) 
											ORDER BY coun_PhoneCode';
							$oRes_code = $oDB->Query($sql_code);
							$option_code = '';
							while ($axRow_code = $oRes_code->FetchRow(DBI_ASSOC)){

								$check_code = '';
								if ('+'.$axRow_code['coun_PhoneCode'] == $phone_code) { $check_code = 'selected'; }

								$option_code .= '<option value="+'.$axRow_code['coun_PhoneCode'].'" '.$check_code.'>+'.$axRow_code['coun_PhoneCode'].'</option>';
							}

							$html .= '<td><span class="form-inline">
	                    				<select class="form-control text-md" id="code_'.$field['mafi_FieldName'].'" name="code_'.$field['mafi_FieldName'].'" '.$rq_af.'>'.$option_code.'</select>
	                    				<input type="text" style="width:168px" name="'.$field['mafi_FieldName'].'" value="'.$phone_num.'" maxlength="9" class="form-control text-md" placeholder="Tel" '.$rq_af.'>
	                    			</span>';
						}

						$html .= '	</td></tr>';
					}
				}
			}

			$sql_custom = 'SELECT custom_field.*,
							custom_form.cufo_Require,
							field_type.fity_Name AS field_type
							FROM custom_field
							LEFT JOIN custom_form
							ON custom_form.cufi_CustomFieldID = custom_field.cufi_CustomFieldID
							LEFT JOIN field_type
							ON custom_field.fity_FieldTypeID = field_type.fity_FieldTypeID
							WHERE custom_form.card_CardID = "'.$card_CardID.'"
							AND custom_form.cufo_FillIn = "Y"
							ORDER BY custom_field.cufi_FieldOrder';

			$oRes = $oDB->Query($sql_custom);
			$check_field = $oDB->QueryOne($sql_custom);

			if ($check_field) {

				$html .= '<tr height="40px"><td colspan="3" style="text-align:center"><u><b>Custom</b></u></td></tr>';

				while ($field = $oRes->FetchRow(DBI_ASSOC)){

					if ($field['cufo_Require']=='Y') { 

						$text_rq = ' <span class="text-rq">*</span>';
						$rq_af = 'required autofocus'; 

					} else { $text_rq = '';	$rq_af = '';  }

					$html .= '	<tr height="40px"><td style="text-align:right">
									<b>'.$field['cufi_Name'].$text_rq.'</b></td>
									<td width="10px"></td>';

					if ($field['field_type']=='Text') {

						$html .= '<td style="text-align:center"><input type="text" name="'.$field['cufi_FieldName'].'" class="form-control text-md" placeholder="Text" '.$rq_af.'>';
							
					} else if ($field['field_type']=='Number') {

						$html .= '<td style="text-align:center"><input type="number" name="'.$field['cufi_FieldName'].'" class="form-control text-md" placeholder="Number" '.$rq_af.'>';
							
					} else if ($field['field_type']=='Date') {

						# DAY OPTION

						$option_date = '';

						for ($x = 1; $x < 32; $x++) {

							if (strlen($x) == 1) { $d = '0'.$x; }
							else { $d = $x; }

							$option_date .= '<option value="'.$d.'">'.$d.'</option>';
						}


						# MONTH OPTION

						$month = ["Jan.", "Feb.", "Mar.", "Apr.", "May.", "Jun.", "Jul.", "Aug.", "Sep.", "Oct.", "Nov.", "Dec."];
						$option_month = '';

						for ($x = 1; $x < 13; $x++) {

							if (strlen($x) == 1) { $d = '0'.$x; }
							else { $d = $x; }

							$option_month .= '<option value="'.($d).'">'.$month[$x-1].'</option>';
						}


						# YEAR OPTION

						$this_year = date('Y',time());
						$start_year = $this_year-100;
						$end_year = $this_year;
						$option_year = '';

						for ($x = $start_year; $x <= $end_year; $x++) {

							$option_year .= '<option value="'.$x.'")>'.$x.'</option>';
						}

						$html .= '<td><span class="form-inline">
									<select id="date" class="form-control text-md" name="'.$field['mafi_FieldName'].'_date" style="width:70px" '.$rq_af.'>
										<option value=""> - - -</option>
										'.$option_date.'
									</select>
									<select id="month" class="form-control text-md" name="'.$field['mafi_FieldName'].'_month" style="width:80px" '.$rq_af.'>
										<option value=""> - - - -</option>
										'.$option_month.'
									</select>
									<select id="year" class="form-control text-md" name="'.$field['mafi_FieldName'].'_year" style="width:90px" '.$rq_af.'>
										<option value=""> - - - - -</option>
										'.$option_year.'
									</select></span>';
							
					} else if ($field['field_type']=='Radio') {

						$x = 0;

						$html .= '<td><span class="form-inline"><label>';

						$sql_target = 'SELECT *
										FROM custom_list_value
										WHERE cufi_CustomFieldID = "'.$field['cufi_CustomFieldID'].'"';
						$oRes_target = $oDB->Query($sql_target);
						while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

							$html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="'.$field['cufi_FieldName'].'" value="'.$target['clva_CustomListValueID'].'"> '.$target['clva_Name'].'';

							$x++;
						}

						$html .= '</label></span>';

					} else if ($field['field_type']=='Checkbox') {

						$html .= '<td><span class="form-inline"><label>';

						$sql_target = 'SELECT *
										FROM custom_list_value
										WHERE cufi_CustomFieldID = "'.$field['cufi_CustomFieldID'].'"';
						$oRes_target = $oDB->Query($sql_target);
						while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

							$html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="'.$field['cufi_FieldName'].'" value="'.$target['clva_CustomListValueID'].'"> '.$target['clva_Name'].'<br>';
						}

						$html .= '</label></span>';

					} else if ($field['field_type']=='Selection') {

						$html .= '<td><select name="'.$field['cufi_FieldName'].'" class="form-control" '.$rq_af.'>
									<option value="">Please Select ..</option>';

						$sql_target = 'SELECT *
										FROM custom_list_value
										WHERE cufi_CustomFieldID = "'.$field['cufi_CustomFieldID'].'"';
						$oRes_target = $oDB->Query($sql_target);
						while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

							$html .= '<option value="'.$target['clva_CustomListValueID'].'">'.$target['clva_Name'].'</option>';
						}

						$html .= '</select>';

					} else if ($field['field_type']=='Tel') {

						# PHONE CODE

						$sql_code = 'SELECT DISTINCT coun_PhoneCode 
										FROM country 
										WHERE coun_PhoneCode NOT IN (0,1,7) 
										ORDER BY coun_PhoneCode';
						$oRes_code = $oDB->Query($sql_code);
						$option_code = '';
						while ($axRow_code = $oRes_code->FetchRow(DBI_ASSOC)){

							$check_code = '';
							if ($axRow_code['coun_PhoneCode'] == '66') { $check_code = 'selected'; }

							$option_code .= '<option value="+'.$axRow_code['coun_PhoneCode'].'" '.$check_code.'>+'.$axRow_code['coun_PhoneCode'].'</option>';
						}

						$html .= '<td><span class="form-inline">
                    					<select class="form-control text-md" id="code_'.$field['cufi_FieldName'].'" name="code_'.$field['cufi_FieldName'].'" '.$rq_af.'>'.$option_code.'</select>
                    					<input type="text" style="width:168px" name="'.$field['cufi_FieldName'].'" maxlength="9" class="form-control text-md" placeholder="Tel" '.$rq_af.'>
                    				</span>';
					}

					$html .= '	</td></tr>';

				}
			}

			$html .= '		</table>
						</span>
						<br>
			            <div class="clear_all">
			                <button class="btn btn-success btn_hide" type="submit">SUBMIT</button>
			                <input type="hidden" id="act" name="act" value="save" />
			                &nbsp;&nbsp;&nbsp;
			                <button class="btn btn-warning btn_hide" type="reset" onclick="window.location.href='."'".'register.php'."'".'">CANCEL</button>
			            
			            </div>
			            <br>';

			echo $html;

		}

	} else {

		$html = '<br><br><b>No Member Data</b>';

		echo $html;
	}	

	exit;
}


else if($TASK =='Get_RegisterMemberCode'){

	$card_CardID = $_REQUEST['card_id'];
	$member_id = $_REQUEST['member_id'];

	$sql_member = 'SELECT mb_member_brand.*,
						mb_member.facebook_id,
						mb_member.member_image
					FROM mb_member_brand 
					LEFT JOIN mb_member 
					ON mb_member.member_id = mb_member_brand.member_id
					WHERE mb_member_brand.member_brand_id="'.$member_id.'"';

	$oRes = $oDB->Query($sql_member);
	$member = $oRes->FetchRow(DBI_ASSOC);

	$html = '<br><br>Member Card ID<br>
			<span style="font-size:16px;color:red">'.$member['member_card_code'].'</span>
			<br><br>
	        <span class="form-inline">
	            <label>Search Member <span class="text-rq">*</span>&nbsp;&nbsp;&nbsp;</label>
	            <input type="text" id="search_member_brand" name="search_member_brand" style="width:200px" class="form-control text-md" placeholder="Search" onchange="RegisterFunction('.$member_id.')">
	            <button type="button" class="btn btn-primary" id="button_search" onclick="SearchMemberData('.$member_id.')">Search</button>
	        </span>
	        <span id="member_brand_data"></span>
	        <br>';

	echo $html;
	exit();
}


else if($TASK =='Get_RedeemMember'){

	$redeem_id = $_REQUEST['redeem_id'];
	$bran_BrandID = $_REQUEST['brand_id'];
	$member_id = $_REQUEST['member_id'];

	$sql_member = 'SELECT 
						rr.coty_CollectionTypeID, 
					    rr.rera_RewardQty,
					    rr.rera_CardID,  
					    mmr.card_id card_CardID,
					    rw.*,
					    rw.rewa_Type,
					    rw.card_CardID reward_card_id,
					    IF(rewa_Type = "Card",
				            (SELECT CONCAT("../../upload/",mi_card.path_image,mi_card.image) 
				            FROM mi_card WHERE card_id = rw.card_CardID),
				            IFNULL(CONCAT("../../upload/",rw.rewa_ImagePath,rw.rewa_Image), "")
				      	) rewa_Image,
					    IF(rr.coty_CollectionTypeID = "0", rr.rera_RewardQty_Point,rr.rera_RewardQty_Stamp) as TotalUse,
      					ct.coty_Image coty_Image,
					    rd.rede_Time,
					    rd.rede_NumberTime,
					    rd.rede_RedeemLimit,
					    rd.rede_Repetition,
					    rd.rede_Qty,
					    rd.rede_QtyPer,
					    mb.*

				      FROM reward_redeem rd 

				      LEFT JOIN reward rw 
				      ON rd.rewa_RewardID = rw.rewa_RewardID

				      LEFT JOIN reward_ratio rr 
				      ON rd.rede_RewardRedeemID = rr.rede_RewardRedeemID

				      LEFT JOIN collection_type ct 
				      ON rr.coty_CollectionTypeID = ct.coty_CollectionTypeID 

				      INNER JOIN mi_brand b 
				      ON b.brand_id = rd.bran_BrandID 

				      INNER JOIN mb_member_register mmr 
				      ON b.brand_id = mmr.bran_BrandID

				      INNER JOIN mb_member mb 
				      ON mmr.member_id = mb.member_id

				      WHERE mmr.member_id = '.$member_id.'  
				      AND rd.rede_RewardRedeemID = '.$redeem_id.'';

	$oRes = $oDB->Query($sql_member);
	$member = $oRes->FetchRow(DBI_ASSOC);

	# CHECK MEMBER REGISTER

	$sql_register = 'SELECT member_brand_code
						FROM mb_member_register 
						WHERE member_id="'.$member_id.'" 
						AND bran_BrandID="'.$bran_BrandID.'"
						AND flag_del=""';

	$id = $oDB->Query($sql_register);
	$regis = $id->FetchRow(DBI_ASSOC);

	# MEMBER

	if ($member['member_image'] && $member['member_image']!='user.png') {

		$member_image = '<img src="../../upload/member_upload/'.$member['member_image'].'" width="100" height="100" class="img-circle image_border"/>';

	} else if ($member['facebook_id']) {

		 $member_image = '<img src="http://graph.facebook.com/'.$member['facebook_id'].'/picture?type=square" width="100" height="100" class="img-circle image_border" />';

	} else {
				                    	
		$member_image = '<img src="../../images/user.png" width="100" height="100" class="img-circle image_border" />';
	}

	if ($member['rewa_Type'] == 'Card') { 

		$image_class = 'img-rounded'; 
		$table_width = '180px';

	} else { 

		$image_class = '';
		$table_width = '120px';
	}


	# COLLECTION TYPE

	if ($member['coty_Image']=='') {

		$sql_image = 'SELECT coty_Image FROM collection_type WHERE coty_CollectionTypeID="3"';
	 	$member['coty_Image'] = $oDB->QueryOne($sql_image);
	}


	# TOTAL HAVE

	if ($member['coty_CollectionTypeID'] == "0") {

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
					AND coty_CollectionTypeID = ".$member['coty_CollectionTypeID']."
					AND mmr.bran_BrandID = ".$bran_BrandID." 
					AND mmt.mems_StatusExp ='F'";
	}
	
	$TotalHave = $oDB->QueryOne($strSQL);

	if ($member['rera_CardID'] == '') {

		if ($TotalHave == '' || $TotalHave == 0) { 

			$TotalHave = 0;
			$percent_have = 0;

		} else {

			$percent_have = ($TotalHave*100)/$member['TotalUse'];
		}

		if ($percent_have > 100) { $percent_have = 100; }
			
		$html = '<br><br>
				<table>
					<tr>
						<td style="width:120px;text-align:center">'.$member_image.'</td>
						<td><img src="'.$_SESSION['path_upload_collection'].$member['coty_Image'].'" style="width:30px;">
							<span style="font-size:18px;float:right;padding-top:2px"><b>
								<span style="color:#003369">'.number_format($TotalHave).'</span> / <span style="color:#AAA">'.number_format($member['TotalUse']).'</span></b></span>
							<br><br>
							<div style="width:150px;background-color:#AAA;">
			  					<div style="width:'.$percent_have.'%;height:8px;background-color:#003369;"></div>
							</div>
						</td>
						<td style="width:'.$table_width.';text-align:center">
							<img src="'.$member['rewa_Image'].'" height="100" class="image_border '.$image_class.'" />
						</td>
					</tr>
				</table>';
	} else {
			
		$html = '<br><br>'.$member_image;
	}

	$html .= '<table>';

	$topic = array("Profile", "Home Address", "Work Address", "Work", "Contact");

	for ($i=0; $i <5 ; $i++) { 

		$sql_field = 'SELECT a.*,
							a.mafi_MasterFieldID AS master_field_id,
							d.fity_Name AS field_type

						FROM master_field AS a

						LEFT JOIN field_type AS d
						ON a.mafi_FieldType = d.fity_FieldTypeID

						WHERE a.mafi_Position = "'.$topic[$i].'"
							AND a.mafi_Deleted != "T"
							AND a.mafi_MasterFieldID IN (2,3,5,6,20,23,49)

							GROUP BY a.mafi_FieldName
							ORDER BY a.mafi_FieldOrder';

		$oRes = $oDB->Query($sql_field);
		$check_field = $oDB->QueryOne($sql_field);

		if ($check_field) {

			$html .= '<tr height="40px">
						<td colspan="3" style="text-align:center"><u><b>'.$topic[$i].'</b></u></td>
					</tr>';

			while ($field = $oRes->FetchRow(DBI_ASSOC)){

				$html .= '	<tr>
								<td style="text-align:right"><b>'.$field['mafi_NameEn'].'</b></td>
								<td style="text-align:center" width="30px">:</td>';

				if ($field['field_type']=='Text') {

					# MEMBER BRAND CODE & MEMBER CARD COE

					if ($field['master_field_id']=='48') { # CARD

						if ($regis['member_card_code']=='') { $regis['member_card_code'] = '-'; }
						
						$member[$field['mafi_FieldName']] = $regis['member_card_code'];
							
					} elseif ($field['master_field_id']=='49') { # BRAND

						if ($regis['member_brand_code']=='') { $regis['member_brand_code'] = '-'; }
						
						$member[$field['mafi_FieldName']] = $regis['member_brand_code'];

					} else {

						if ($member[$field['mafi_FieldName']]=='') { $member[$field['mafi_FieldName']] = '-'; }
					}

					$html .= '<td>'.$member[$field['mafi_FieldName']].'';
						
				} else if ($field['field_type']=='Number') {

					if ($member[$field['mafi_FieldName']]==0) { $member[$field['mafi_FieldName']] = '-'; }

					$html .= '<td>'.$member[$field['mafi_FieldName']].'';
						
				} else if ($field['field_type']=='Date') {

					if ($member[$field['mafi_FieldName']] == '0000-00-00') { $data = '-'; }
					else { $data = DateOnly($member[$field['mafi_FieldName']]); }

					$html .= '<td>'.$data.'';
						
				} else if ($field['field_type']=='Radio') {

					$x = 0;

					$data = $member[$field['mafi_FieldName']];

					$html .= '<td><span class="form-inline">';

					$sql_target = 'SELECT *
									FROM master_target
									WHERE mafi_MasterFieldID = "'.$field['master_field_id'].'"';
					$oRes_target = $oDB->Query($sql_target);
					while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

						if ($data == $target['mata_MasterTargetID']) {

							if ($x==0) {

								$html .= '<span class="glyphicon glyphicon-check"></span> '.$target['mata_NameEn'].'<label>';

							} else {

								$html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-check"></span> '.$target['mata_NameEn'].'<label>';
							}

						} else {

							if ($x==0) {

								$html .= '<span class="glyphicon glyphicon-unchecked"></span> '.$target['mata_NameEn'].'<label>';

							} else {

								$html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-unchecked"></span> '.$target['mata_NameEn'].'<label>';
							}
						}

						$x++;
					}

					$html .= '</span>';

				} else if ($field['field_type']=='Checkbox') {

					$x = 0;

					$html .= '<td><span class="form-inline"><label>';

					$sql_target = 'SELECT *
									FROM master_target
									WHERE mafi_MasterFieldID = "'.$field['master_field_id'].'"';
					$oRes_target = $oDB->Query($sql_target);
					while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

						if ($data == $target['mata_MasterTargetID']) {

							if ($x==0) {

								$html .= '<span class="glyphicon glyphicon-check"></span> '.$target['mata_NameEn'].'<label>';

							} else {

								$html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-check"></span> '.$target['mata_NameEn'].'<label>';
							}

						} else {

							if ($x==0) {

								$html .= '<span class="glyphicon glyphicon-unchecked"></span> '.$target['mata_NameEn'].'<label>';

								} else {

								$html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-unchecked"></span> '.$target['mata_NameEn'].'<label>';
							}
						}
					}

					$html .= '</label></span>';

				} else if ($field['field_type']=='Selection') {

					$data = $member[$field['mafi_FieldName']];

					if ($member[$field['mafi_FieldName']] == '') { $member[$field['mafi_FieldName']] == '-'; }

					if ($field['master_field_id'] == 33 || $field['master_field_id'] == 45) {

						if ($member[$field['mafi_FieldName']] != '-') {

							$sql_target = 'SELECT prov_Name FROM province WHERE prov_ProvinceID = "'.$data.'"';
							$data = $oDB->QueryOne($sql_target);

							$html .= '<td>'.$data;
						}

					} elseif ($field['master_field_id'] == 34 || $field['master_field_id'] == 46) {

						if ($member[$field['mafi_FieldName']] != '-') {

							$sql_target = 'SELECT coun_NiceName FROM country WHERE coun_CountryID = "'.$data.'"';
							$data = $oDB->QueryOne($sql_target);

							$html .= '<td>'.$data;
						}

					} else {

						if ($member[$field['mafi_FieldName']] != '-') {

							$sql_target = 'SELECT mata_NameEn FROM master_target WHERE mata_MasterTargetID = "'.$data.'"';
							$data = $oDB->QueryOne($sql_target);

							$html .= '<td>'.$data;
						}
					}

				} else if ($field['field_type']=='Tel') {

					if ($member[$field['mafi_FieldName']] == '') { $member[$field['mafi_FieldName']] == '-'; }

					$html .= '<td>'.$member[$field['mafi_FieldName']];
				}

				$html .= '	</td></tr>';
			}
		}
	}

	$html .= '</table>';


	# CHECK REDEEM

	$status_redeem = 'True';
	$reason_redeem = '';


	# PROPERLY

		$properly_data = '';

		# AGE

		if ($member['rewa_Age'] != '') {

			$token = strtok($member['rewa_Age'], ",");
			$target_data = array();
			$z = 0;

			while ($token !== false) {

				$target_data[$z] =  $token;
				$token = strtok(",");
				$z++;
			}

			if ($target_data[0] == 4) { $target_data[0] = 0; }
			else {

				$sql_target = 'SELECT mata_NameEn
									FROM master_target
									WHERE mata_MasterTargetID="'.$target_data[0].'"';
		 		$target_data[0] = $oDB->QueryOne($sql_target);
			}

			if ($target_data[1] == 21) { $target_data[0] = 120; }
			else {

				$sql_target = 'SELECT mata_NameEn
									FROM master_target
									WHERE mata_MasterTargetID="'.$target_data[0].'"';
		 		$target_data[1] = $oDB->QueryOne($sql_target);
			}

			$age = floor((time() - strtotime($member['date_birth'])) / 31556926);

			if ($age >= $target_data[0] && $age <= $target_data[1]) { $font_color = 'green'; }
			else { $font_color = 'red'; $status_redeem = 'False'; $reason_redeem = 'ไม่ตรงตามเงื่อนไขการรับของรางวัล'; }

			$properly_data .= '<tr height="20px"><td style="text-align:right" width="300px">Age</td>
								<td style="text-align:center" width="30px">:</td>
								<td width="300px" style="color:'.$font_color.'">'.$age.' Years Old</td></tr>';
		}


		# GENDER

		if ($member['rewa_Gender'] == '1') { 

			if ($member['flag_gender'] == '1') { $font_color = 'green'; }
			else { $font_color = 'red'; $status_redeem = 'False'; $reason_redeem = 'ไม่ตรงตามเงื่อนไขการรับของรางวัล'; }

			$properly_data .= '<tr height="20px"><td style="text-align:right" width="300px">Gender</td>
								<td style="text-align:center" width="30px">:</td>
								<td width="300px" style="color:'.$font_color.'">Male</td></tr>';

		} elseif ($member['rewa_Gender'] == '2') {

			if ($member['flag_gender'] == '2') { $font_color = 'green'; }
			else { $font_color = 'red'; $status_redeem = 'False'; $reason_redeem = 'ไม่ตรงตามเงื่อนไขการรับของรางวัล'; }

			$properly_data .= '<tr height="20px"><td style="text-align:right" width="300px">Gender</td>
								<td style="text-align:center" width="30px">:</td>
								<td width="300px" style="color:'.$font_color.'">Female</td></tr>';
		}


		# MARITAL

		if ($member['rewa_Marital'] != '0') { 

			if ($member['flag_marital'] == $member['rewa_Marital']) { 

				$font_color = 'green'; 

				$sql_target = 'SELECT mata_NameEn
								FROM master_target
								WHERE mafi_MasterFieldID="7"
								AND mata_MasterTargetID="'.$member['flag_marital'].'"';
		 		$member['flag_marital'] = $oDB->QueryOne($sql_target);

			} elseif ($member['flag_marital'] != 0) {
				
				$font_color = 'red'; 
				$status_redeem = 'False'; 

				$sql_target = 'SELECT mata_NameEn
								FROM master_target
								WHERE mafi_MasterFieldID="7"
								AND mata_MasterTargetID="'.$member['flag_marital'].'"';
		 		$member['flag_marital'] = $oDB->QueryOne($sql_target);
			
			} else { 

				$member['flag_marital'] = '-'; 
				$font_color = 'red'; 
				$status_redeem = 'False';
				$reason_redeem = 'ไม่ตรงตามเงื่อนไขการรับของรางวัล';
			}

			$properly_data .= '<tr height="20px"><td style="text-align:right" width="300px">Marital</td>
								<td style="text-align:center" width="30px">:</td>
								<td width="300px" style="color:'.$font_color.'">'.$member['flag_marital'].'</td></tr>';
		}


		# EDUCATION

		if ($member['rewa_Education'] != '0') { 

			if ($member['educate_type'] == $member['rewa_Education']) { 

				$font_color = 'green'; 

				$sql_target = 'SELECT mata_NameEn
								FROM master_target
								WHERE mafi_MasterFieldID="12"
								AND mata_MasterTargetID="'.$member['educate_type'].'"';
		 		$member['educate_type'] = $oDB->QueryOne($sql_target);

			} elseif ($member['educate_type'] != 0) {
				
				$font_color = 'red'; 
				$status_redeem = 'False'; 

				$sql_target = 'SELECT mata_NameEn
								FROM master_target
								WHERE mafi_MasterFieldID="12"
								AND mata_MasterTargetID="'.$member['educate_type'].'"';
		 		$member['educate_type'] = $oDB->QueryOne($sql_target);
			
			} else { 

				$member['educate_type'] = '-'; 
				$font_color = 'red'; 
				$status_redeem = 'False';
				$reason_redeem = 'ไม่ตรงตามเงื่อนไขการรับของรางวัล';
			}

			$properly_data .= '<tr height="20px"><td style="text-align:right" width="300px">Education</td>
								<td style="text-align:center" width="30px">:</td>
								<td width="300px" style="color:'.$font_color.'">'.$member['educate_type'].'</td></tr>';
		}


		# ACTIVITY

		if ($member['rewa_Activity'] != '0') {

			if ($member['interest_activity_type'] == $member['rewa_Activity']) { 

				$font_color = 'green'; 

				$sql_target = 'SELECT mata_NameEn
								FROM master_target
								WHERE mafi_MasterFieldID="13"
								AND mata_MasterTargetID="'.$member['interest_activity_type'].'"';
		 		$member['interest_activity_type'] = $oDB->QueryOne($sql_target);

			} elseif ($member['interest_activity_type'] != 0) {
				
				$font_color = 'red'; 
				$status_redeem = 'False'; 

				$sql_target = 'SELECT mata_NameEn
								FROM master_target
								WHERE mafi_MasterFieldID="13"
								AND mata_MasterTargetID="'.$member['interest_activity_type'].'"';
		 		$member['interest_activity_type'] = $oDB->QueryOne($sql_target);
			
			} else { 

				$member['interest_activity_type'] = '-'; 
				$font_color = 'red'; 
				$status_redeem = 'False';
				$reason_redeem = 'ไม่ตรงตามเงื่อนไขการรับของรางวัล';
			}

			$properly_data .= '<tr height="20px"><td style="text-align:right" width="300px">Activity</td>
								<td style="text-align:center" width="30px">:</td>
								<td width="300px" style="color:'.$font_color.'">'.$member['interest_activity_type'].'</td></tr>';
		}


		# INCOME

		if ($member['rewa_MonthlyPersonalIncome'] != '0') {

			if ($member['monthly_personal_income_type'] == $member['rewa_MonthlyPersonalIncome']) { 

				$font_color = 'green'; 

				$sql_target = 'SELECT mata_NameEn
								FROM master_target
								WHERE mafi_MasterFieldID="19"
								AND mata_MasterTargetID="'.$member['monthly_personal_income_type'].'"';
		 		$member['monthly_personal_income_type'] = $oDB->QueryOne($sql_target);

			} elseif ($member['monthly_personal_income_type'] != 0) {
				
				$font_color = 'red'; 
				$status_redeem = 'False'; 

				$sql_target = 'SELECT mata_NameEn
								FROM master_target
								WHERE mafi_MasterFieldID="19"
								AND mata_MasterTargetID="'.$member['monthly_personal_income_type'].'"';
		 		$member['monthly_personal_income_type'] = $oDB->QueryOne($sql_target);
			
			} else { 

				$member['monthly_personal_income_type'] = '-'; 
				$font_color = 'red'; 
				$status_redeem = 'False';
				$reason_redeem = 'ไม่ตรงตามเงื่อนไขการรับของรางวัล';
			}

			$properly_data .= '<tr height="20px"><td style="text-align:right" width="300px">Monthly Personal Income</td>
								<td style="text-align:center" width="30px">:</td>
								<td width="300px" style="color:'.$font_color.'">'.$member['monthly_personal_income_type'].'</td></tr>';
		}


		# PROVINCE

		if ($member['rewa_Province'] != '0') {

			if ($member['home_province'] == $member['rewa_Province']) { 

				$font_color = 'green'; 

				$sql_province = 'SELECT prov_Name
								FROM province
								WHERE prov_ProvinceID="'.$member['home_province'].'"';
		 		$member['home_province'] = $oDB->QueryOne($sql_province);

			} elseif ($member['home_province'] != 0) {
				
				$font_color = 'red'; 
				$status_redeem = 'False'; 

				$sql_province = 'SELECT prov_Name
								FROM province
								WHERE prov_ProvinceID="'.$member['home_province'].'"';
		 		$member['home_province'] = $oDB->QueryOne($sql_province);
			
			} else { 

				$member['home_province'] = '-'; 
				$font_color = 'red'; 
				$status_redeem = 'False';
				$reason_redeem = 'ไม่ตรงตามเงื่อนไขการรับของรางวัล';
			}

			$properly_data .= '<tr height="20px"><td style="text-align:right" width="300px">Provice</td>
								<td style="text-align:center" width="30px">:</td>
								<td width="300px" style="color:'.$font_color.'">'.$member['home_province'].'</td></tr>';
		}


		# REWARD TARGET

		$sql_target = 'SELECT reta_Target, cufi_CustomFieldID
						FROM reward_target
						WHERE rewa_RewardID="'.$member['rewa_RewardID'].'" AND reta_Deleted=""';
		$oRes_target = $oDB->Query($sql_target);

		$reward_target = "";

		while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

			# FIELD

			$sql_field = 'SELECT cufi_Name 
							FROM custom_field 
							WHERE cufi_CustomFieldID="'.$target['cufi_CustomFieldID'].'"';
			$field = $oDB->QueryOne($sql_field);

			# MEMBER VALUE

			$sql_value = 'SELECT rede_Value 
							FROM custom_register_data 
							WHERE cufi_CustomFieldID="'.$target['cufi_CustomFieldID'].'"
							AND mebe_MemberID="'.$member_id.'"';
			$memb_value = $oDB->QueryOne($sql_value);

			if ($memb_value == $target['reta_Target']) { 

				$font_color = 'green'; 

				$sql_value = 'SELECT clva_Name 
								FROM custom_list_value 
								WHERE cufi_CustomFieldID="'.$target['cufi_CustomFieldID'].'"
								AND clva_CustomListValueID="'.$target['reta_Target'].'"';
				$prop_value = $oDB->QueryOne($sql_value);

			} elseif ($memb_value != 0) {
				
				$font_color = 'red'; 
				$status_redeem = 'False'; 

				$sql_value = 'SELECT clva_Name 
								FROM custom_list_value 
								WHERE cufi_CustomFieldID="'.$target['cufi_CustomFieldID'].'"
								AND clva_CustomListValueID="'.$memb_value.'"';
				$prop_value = $oDB->QueryOne($sql_value);
			
			} else { 

				$prop_value = '-'; 
				$font_color = 'red'; 
				$status_redeem = 'False';
				$reason_redeem = 'ไม่ตรงตามเงื่อนไขการรับของรางวัล';
			}

			$properly_data .= '<tr height="20px"><td style="text-align:right" width="300px">'.$field.'</td>
								<td style="text-align:center" width="30px">:</td>
								<td width="300px" style="color:'.$font_color.'">'.$prop_value.'</td></tr>';
		}

	if ($properly_data != '') {

		$html .= '<br><u>Properly</u>
					<table>
						'.$properly_data.'
					</table>';
	}

	if ($member['rera_CardID'] == '') {

		if ($TotalHave < $member['TotalUse']) { 

			$status_redeem = 'False';
			$reason_redeem = 'คะแนนสะสมไม่เพียงพอ';
		}

	} else {

		$sql_register = 'SELECT member_register_id 
							FROM mb_member_register 
							WHERE card_id IN ('.$member['rera_CardID'].')
							AND member_id="'.$member_id.'"
							AND flag_del=""';
		$register_id = $oDB->QueryOne($sql_register);

		if (!$register_id) {

			$status_redeem = 'False';
			$reason_redeem = 'ไม่ตรงตามเงื่อนไขการรับของรางวัล';
		}
	}

	if ($member['rede_RedeemLimit'] == 'Limit') { 

		$sql_value = 'SELECT COUNT(retr_RewardRedeemTransID) 
						FROM reward_redeem_trans 
						WHERE rede_RewardRedeemID="'.$redeem_id.'"
						AND retr_Deleted=""';
		$count_redeem = $oDB->QueryOne($sql_value);

		if ($count_redeem >= $member['rede_NumberTime']) {

			$status_redeem = 'False';
			$reason_redeem = 'สิทธิ์ในการแลกของรางวัลหมดแล้ว';
		}
	}

	if ($member['rede_Repetition'] == 'T' && $member['rede_QtyPer'] == 'Not Specific') { 

		$sql_value = 'SELECT COUNT(retr_RewardRedeemTransID) 
						FROM reward_redeem_trans 
						WHERE rede_RewardRedeemID="'.$redeem_id.'"
						AND retr_Deleted=""
						AND memb_MemberID="'.$member_id.'"';
		$count_redeem = $oDB->QueryOne($sql_value);

		if ($count_redeem >= $member['rede_Qty']) {

			$status_redeem = 'False';
			$reason_redeem = 'สิทธิ์ในการแลกของรางวัลหมดแล้ว';
		}
	}

	if ($member['rewa_Limit'] == 'T' && $member['rewa_Qty'] == 0) {

		$status_redeem = 'False';
		$reason_redeem = 'ของรางวัลหมด';
	}

	if ($member['rewa_Type'] == 'Card') {

		$sql_card = 'SELECT flag_multiple 
						FROM mi_card 
						WHERE card_id="'.$member['reward_card_id'].'"';
		$card_multiple = $oDB->QueryOne($sql_card);

		if ($card_multiple!='Yes') {

			$sql_register = 'SELECT member_register_id 
								FROM mb_member_register 
								WHERE card_id="'.$member['reward_card_id'].'"
								AND member_id="'.$member_id.'"
								AND flag_del=""';
			$register_id = $oDB->QueryOne($sql_register);

			if ($register_id) {

				$status_redeem = 'False';
				$reason_redeem = 'คุณมีบัตรสมาชิกนี้แล้ว';
			}
		}
	}

	if ($status_redeem == 'True') {

		$html .= '		<br>
				        <div class="clear_all">
				            <button class="btn btn-success btn_hide" type="submit">SUBMIT</button>
				            <input type="hidden" id="act" name="act" value="save" />
				            <input type="hidden" id="member_id" name="member_id" value="'.$member_id.'" />
				            <input type="hidden" id="redeem_id" name="redeem_id" value="'.$redeem_id.'" />
				            &nbsp;&nbsp;&nbsp;
				            <button class="btn btn-warning btn_hide" type="reset" onclick="window.location.href='."'".'redeem.php'."'".'">CANCEL</button>
				        </div>
				        <br>';
	
	} else {

		$html .= '<br><br>
					<span style="color:red;">'.$reason_redeem.'</span>
					<br><br>
				    <div class="clear_all">
				        <button class="btn btn-warning btn_hide" type="reset" onclick="window.location.href='."'".'redeem.php'."'".'">CANCEL</button>
				    </div>
				    <br>';
	}

	echo $html;
	exit;

}

else if($TASK =='Get_ChooseMember'){

	$type = $_REQUEST['type'];
	$card_CardID = $_REQUEST['card_CardID'];

	# DAY OPTION

	$option_date = '';

	for ($x = 1; $x < 32; $x++) {

		if (strlen($x) == 1) { $d = '0'.$x; }
		else { $d = $x; }

		$option_date .= '<option value='.$d.'>'.$d.'</option>';
	}


	# MONTH OPTION

	$month = ["Jan.", "Feb.", "Mar.", "Apr.", "May.", "Jun.", "Jul.", "Aug.", "Sep.", "Oct.", "Nov.", "Dec."];
	$option_month = '';

	for ($x = 1; $x < 13; $x++) {

		if (strlen($x) == 1) { $d = '0'.$x; }
		else { $d = $x; }

		$option_month .= '<option value="'.($d).'">'.$month[$x-1].'</option>';
	}


	# YEAR OPTION

	$this_year = date('Y',time());
	$start_year = $this_year-5;
	$option_year = '';

	for ($x = $this_year; $x >= $start_year; $x--) {

		$option_year .= '<option value="'.$x.'")>'.$x.'</option>';
	}


	if ($type=='' || $type=='New') {

		$html = '<table>';

		$topic = array("Profile", "Home Address", "Work Address", "Work", "Contact");

		for ($i=0; $i <5 ; $i++) { 

			$sql_field = 'SELECT a.*,b.*,c.*,

							a.mafi_MasterFieldID AS master_field_id,
							b.refo_Target,
							d.fity_Name AS field_type

							FROM master_field AS a

							LEFT JOIN register_form AS b
							ON b.mafi_MasterFieldID = a.mafi_MasterFieldID

							LEFT JOIN mi_card AS c
							ON b.card_CardID = c.card_id

							LEFT JOIN field_type AS d
							ON a.mafi_FieldType = d.fity_FieldTypeID

							WHERE a.mafi_Position = "'.$topic[$i].'"
							AND a.mafi_Deleted != "T"
							AND c.card_id = "'.$card_CardID.'"
							AND b.refo_FillIn = "Y"
							AND a.mafi_MasterFieldID NOT IN (20,23)
							AND b.mafi_MasterFieldID NOT IN (20,23)

							GROUP BY a.mafi_FieldName
							ORDER BY a.mafi_FieldOrder';

			$oRes = $oDB->Query($sql_field);
			$check_field = $oDB->QueryOne($sql_field);

			if ($topic[$i] == "Contact" && !$check_field) {

				$html .= '<tr height="40px"><td colspan="3" style="text-align:center"><u><b>'.$topic[$i].'</b></u></td></tr>';

				$sql_req = 'SELECT a.*,
								a.mafi_MasterFieldID AS master_field_id,
								d.fity_Name AS field_type
								FROM master_field AS a
								LEFT JOIN field_type AS d
								ON a.mafi_FieldType = d.fity_FieldTypeID
								WHERE a.mafi_Position = "Contact"
								AND a.mafi_Deleted != "T"
								AND a.mafi_MasterFieldID IN (20,23)
								GROUP BY a.mafi_FieldName
								ORDER BY a.mafi_FieldOrder';

				$oRes = $oDB->Query($sql_req);
				while ($field = $oRes->FetchRow(DBI_ASSOC)){

					$html .= '	<tr height="40px"><td style="text-align:right">
									<b>'.$field['mafi_NameEn'].' <span class="text-rq">*</span></b></td>
									<td width="10px"></td>';

					if ($field['field_type']=='Text') {

						$html .= '<td style="text-align:center"><input type="text" name="'.$field['mafi_FieldName'].'" class="form-control text-md" placeholder="Text" required autofocus>';
					
					} else if ($field['field_type']=='Tel') {

						$html .= '<td><span class="form-inline"><span style="border:solid 1px;color:#ccc;" class="form-control text-md"> +66 </span><input type="text" style="width:200px" name="'.$field['mafi_FieldName'].'" maxlength="9" class="form-control text-md" placeholder="Tel" required autofocus></span>';
					}

					$html .= '	</td></tr>';
				}
			}

			if ($check_field) {

				$html .= '<tr height="40px"><td colspan="3" style="text-align:center"><u><b>'.$topic[$i].'</b></u></td></tr>';

				while ($field = $oRes->FetchRow(DBI_ASSOC)){

					if ($field['refo_Require']=='Y') { 

						$text_rq = ' <span class="text-rq">*</span>';
						$rq_af = 'required autofocus'; 

					} else { $text_rq = '';	$rq_af = '';  }

					$html .= '	<tr height="40px"><td style="text-align:right">
									<b>'.$field['mafi_NameEn'].$text_rq.'</b></td>
									<td width="10px"></td>';

					if ($field['field_type']=='Text') {

						$html .= '<td style="text-align:center"><input type="text" name="'.$field['mafi_FieldName'].'" class="form-control text-md" placeholder="Text" '.$rq_af.'>';
					
					} else if ($field['field_type']=='Number') {

						$html .= '<td style="text-align:center"><input type="number" name="'.$field['mafi_FieldName'].'" class="form-control text-md" placeholder="Number" '.$rq_af.'>';
					
					} else if ($field['field_type']=='Date') {

						$html .= '<td style="text-align:center"><span class="form-inline">
									<select id="date" class="form-control text-md" name="'.$field['mafi_FieldName'].'_date" style="width:70px" '.$rq_af.'>
										<option value=""> - - -</option>
										'.$option_date.'
									</select>
									<select id="month" class="form-control text-md" name="'.$field['mafi_FieldName'].'_month" style="width:80px" '.$rq_af.'>
										<option value=""> - - - -</option>
										'.$option_month.'
									</select>
									<select id="year" class="form-control text-md" name="'.$field['mafi_FieldName'].'_year" style="width:90px" '.$rq_af.'>
										<option value=""> - - - - -</option>
										'.$option_year.'
									</select></span>';
					
					} else if ($field['field_type']=='Radio') {

						$x = 0;

						$html .= '<td><span class="form-inline"><label>';

						$sql_target = 'SELECT *
										FROM master_target
										WHERE mafi_MasterFieldID = "'.$field['master_field_id'].'"';
						$oRes_target = $oDB->Query($sql_target);
						while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

							if ($x==0) { $check = "checked"; }
							else { $check = ''; }

							$html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="'.$field['mafi_FieldName'].'" value="'.$target['mata_MasterTargetID'].'" '.$check.'> '.$target['mata_NameEn'].'';

							$x++;
						}

						$html .= '</label></span>';

					} else if ($field['field_type']=='Checkbox') {

						$html .= '<td><span class="form-inline"><label>';

						$sql_target = 'SELECT *
										FROM master_target
										WHERE mafi_MasterFieldID = "'.$field['master_field_id'].'"';
						$oRes_target = $oDB->Query($sql_target);
						while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

							$html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="'.$field['mafi_FieldName'].'" value="'.$target['mata_MasterTargetID'].'"> '.$target['mata_NameEn'].'<br>';
						}

						$html .= '</label></span>';

					} else if ($field['field_type']=='Selection') {

						$html .= '<td><select name="'.$field['mafi_FieldName'].'" class="form-control" '.$rq_af.'>
									<option value="">Please Select ..</option>';

						$sql_target = 'SELECT *
										FROM master_target
										WHERE mafi_MasterFieldID = "'.$field['master_field_id'].'"';
						$oRes_target = $oDB->Query($sql_target);
						while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

							$html .= '<option value="'.$target['mata_MasterTargetID'].'">'.$target['mata_NameEn'].'</option>';
						}

						$html .= '</select>';

					} else if ($field['field_type']=='Tel') {

						$html .= '<td><span class="form-inline"><span style="border:solid 1px;color:#ccc;" class="form-control text-md"> +66 </span><input type="text" style="width:200px" name="'.$field['mafi_FieldName'].'" maxlength="9" class="form-control text-md" placeholder="Tel" '.$rq_af.'></span>';
					}

					$html .= '	</td></tr>';
				}

				if ($topic[$i] == "Contact" && !$check_field) {

					$sql_req = 'SELECT a.*,
									a.mafi_MasterFieldID AS master_field_id,
									d.fity_Name AS field_type
									FROM master_field AS a
									LEFT JOIN field_type AS d
									ON a.mafi_FieldType = d.fity_FieldTypeID
									WHERE a.mafi_Position = "Contact"
									AND a.mafi_Deleted != "T"
									AND a.mafi_MasterFieldID IN (20,23)
									GROUP BY a.mafi_FieldName
									ORDER BY a.mafi_FieldOrder';

					$oRes = $oDB->Query($sql_req);
					while ($field = $oRes->FetchRow(DBI_ASSOC)){

						$html .= '	<tr height="40px"><td style="text-align:right">
										<b>'.$field['mafi_NameEn'].' <span class="text-rq">*</span></b></td>
										<td width="10px"></td>';

						if ($field['field_type']=='Text') {

							$html .= '<td style="text-align:center"><input type="text" name="'.$field['mafi_FieldName'].'" class="form-control text-md" placeholder="Text" required autofocus>';
						
						} else if ($field['field_type']=='Tel') {

							$html .= '<td><span class="form-inline"><span style="border:solid 1px;color:#ccc;" class="form-control text-md"> +66 </span><input type="text" style="width:200px" name="'.$field['mafi_FieldName'].'" maxlength="9" class="form-control text-md" placeholder="Tel" required autofocus></span>';
						}

						$html .= '	</td></tr>';
					}
				}
			}
		}

		$sql_custom = 'SELECT custom_field.*,
						custom_form.cufo_Require,
						field_type.fity_Name AS field_type
						FROM custom_field
						LEFT JOIN custom_form
						ON custom_form.cufi_CustomFieldID = custom_field.cufi_CustomFieldID
						LEFT JOIN field_type
						ON custom_field.fity_FieldTypeID = field_type.fity_FieldTypeID
						WHERE custom_form.card_CardID = "'.$card_CardID.'"
						AND custom_form.cufo_FillIn = "Y"
						ORDER BY custom_field.cufi_FieldOrder';

		$oRes = $oDB->Query($sql_custom);
		$check_field = $oDB->QueryOne($sql_custom);

		if ($check_field) {

			$html .= '<tr height="40px"><td colspan="3" style="text-align:center"><u><b>Custom</b></u></td></tr>';

			while ($field = $oRes->FetchRow(DBI_ASSOC)){

				if ($field['cufo_Require']=='Y') { 

					$text_rq = ' <span class="text-rq">*</span>';
					$rq_af = 'required autofocus'; 

				} else { $text_rq = '';	$rq_af = '';  }

				$html .= '	<tr height="40px"><td style="text-align:right">
								<b>'.$field['cufi_Name'].$text_rq.'</b></td>
								<td width="10px"></td>';

				if ($field['field_type']=='Text') {

					$html .= '<td style="text-align:center"><input type="text" name="'.$field['cufi_FieldName'].'" class="form-control text-md" placeholder="Text" '.$rq_af.'>';
					
				} else if ($field['field_type']=='Number') {

					$html .= '<td style="text-align:center"><input type="number" name="'.$field['cufi_FieldName'].'" class="form-control text-md" placeholder="Number" '.$rq_af.'>';
					
				} else if ($field['field_type']=='Date') {

					$html .= '<td style="text-align:center"><span class="form-inline">
								<select id="date" class="form-control text-md" name="'.$field['cufi_FieldName'].'_date" style="width:70px" '.$rq_af.'>
									<option value="">--</option>
									'.$option_date.'
								</select>
								<select id="month" class="form-control text-md" name="'.$field['cufi_FieldName'].'_month" style="width:80px" '.$rq_af.'>
									<option value="">---</option>
									'.$option_month.'
								</select>
								<select id="year" class="form-control text-md" name="'.$field['cufi_FieldName'].'_year" style="width:90px" '.$rq_af.'>
									<option value="">----</option>
									'.$option_year.'
								</select></span>';
					
				} else if ($field['field_type']=='Radio') {

					$x = 0;

					$html .= '<td><span class="form-inline"><label>';

					$sql_target = 'SELECT *
									FROM custom_list_value
									WHERE cufi_CustomFieldID = "'.$field['cufi_CustomFieldID'].'"';
					$oRes_target = $oDB->Query($sql_target);
					while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

						if ($x==0) { $check = "checked"; }
						else { $check = ''; }

						$html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="'.$field['cufi_FieldName'].'" value="'.$target['clva_CustomListValueID'].'" '.$check.'> '.$target['clva_Name'].'';

						$x++;
					}

					$html .= '</label></span>';

				} else if ($field['field_type']=='Checkbox') {

					$html .= '<td><span class="form-inline"><label>';

					$sql_target = 'SELECT *
									FROM custom_list_value
									WHERE cufi_CustomFieldID = "'.$field['cufi_CustomFieldID'].'"';
					$oRes_target = $oDB->Query($sql_target);
					while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

						if ($data == $target['clva_CustomListValueID']) { $check_c = 'checked'; }
						else { $check_c = 'checked'; }

						$html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="'.$field['cufi_FieldName'].'" value="'.$target['clva_CustomListValueID'].'" '.$check_c.'> '.$target['clva_Name'].'<br>';
					}

					$html .= '</label></span>';

				} else if ($field['field_type']=='Selection') {

					$html .= '<td><select name="'.$field['cufi_FieldName'].'" class="form-control" '.$rq_af.'>
								<option value="">Please Select ..</option>';

					$sql_target = 'SELECT *
									FROM custom_list_value
									WHERE cufi_CustomFieldID = "'.$field['cufi_CustomFieldID'].'"';
					$oRes_target = $oDB->Query($sql_target);
					while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

						if ($data == $target['clva_CustomListValueID']) { $check_c = 'selected'; }
						else { $check_c = ''; }

						$html .= '<option value="'.$target['clva_CustomListValueID'].'" '.$check_c.'>'.$target['clva_Name'].'</option>';
					}

					$html .= '</select>';

				} else if ($field['field_type']=='Tel') {

					$html .= '<td><span class="form-inline"><span style="border:solid 1px;color:#ccc;" class="form-control text-md"> +66 </span><input type="text" style="width:200px" name="'.$field['cufi_FieldName'].'" maxlength="9" class="form-control text-md" placeholder="Tel" '.$rq_af.'></span>';
				}

				$html .= '	</td></tr>';

			}
		}

		// # PASSWORD

		// $html .= '<tr height="40px"><td colspan="3" style="text-align:center"><u><b>Password</b></u></td></tr>
		// 			<tr height="40px"><td style="text-align:right">
		// 				<b>Password <span class="text-rq">*</span></b></td>
		// 				<td width="10px"></td>
		// 				<td style="text-align:center">
		// 					<input type="password" name="pass1" class="form-control text-md" placeholder="Password" required>
		// 				</td></tr>
		// 			<tr height="40px"><td style="text-align:right">
		// 				<b>Comfirm Password <span class="text-rq">*</span></b></td>
		// 				<td width="10px"></td>
		// 				<td style="text-align:center">
		// 					<input type="password" name="pass2" class="form-control text-md" placeholder="Password" required>
		// 				</td></tr>';

		$html .= '		</table>
					</span>
					<br>
		            <div class="clear_all">
		                <button class="btn btn-success btn_hide" type="submit">SUBMIT</button>
		                <input type="hidden" id="act" name="act" value="save" />
		                &nbsp;&nbsp;&nbsp;
		                <button class="btn btn-warning btn_hide" type="reset" onclick="window.location.href='."'".'register.php'."'".'">CANCEL</button>
		            </div>
		            <br>';

	} else {

		$html = '<span class="form-inline">
                    <input type="text" id="search_member" class="form-control text-md" name="search_member">
                    <input type="button" class="btn btn-primary" value="Search" onclick="SearchMember()">
                </span>
                <span id="member_data">
                <br><br></span>';

	}

	echo $html;

	exit;	
}

else if($TASK =='Get_FormRegister'){

	$card_CardID = $_REQUEST['card_CardID'];

	if ($card_CardID) {

		$sql_card = 'SELECT mi_card.*,
						mi_card_type.name AS type_name
						FROM mi_card
						LEFT JOIN mi_card_type
						ON mi_card_type.card_type_id = mi_card.card_type_id
						WHERE card_id="'.$card_CardID.'"';

		$oRes = $oDB->Query($sql_card);
		$card = $oRes->FetchRow(DBI_ASSOC);

		# IMAGE

		if ($card['image']) {

			$image = '<img src="../../upload/'.$card['path_image'].$card['image'].'" height="100" class="img-rounded image_border"/>';

		} else if ($card['image_newupload']) {

			$image = '<img src="../../upload/'.$card['path_image'].$card['image_newupload'].'" height="100" class="img-rounded image_border"/>';

		} else {
					                    	
			$image = '<img src="../../images/card_privilege.jpg" height="100" class="img-rounded image_border" />';
		}

		# DESCRIPTION

		if ($card['description'] == '') { $card['description'] = '-'; }

		# PERIOD TYPE

		if ($card['period_type']==4) { $period = 'Member Life Time'; }
		else if ($card['period_type']==3) { $period = $card['period_type_other'].' Years'; }
		else if ($card['period_type']==2) { $period = $card['period_type_other'].' Months'; }
		else { $period = 'Expired Date ('.DateOnly($card['date_expired']).')'; }

		# EXPIRED DATE

		$StaringDate = date("Y-m-d", strtotime(date("Y-m-d") . " - 1 day"));

		if ($card['period_type']==1) { # SPECIFIC
			
			$StaringDate = DateOnly($card['date_expired']);

		} else if ($card['period_type']==2) { # MONTH
			
			$StaringDate = date("d M Y", strtotime(date("Y-m-d", strtotime($StaringDate)) . " + ".$card['period_type_other']." month"));;

		} else if ($card['period_type']==3) { # YEAR
			
			$StaringDate = date("d M Y", strtotime(date("Y-m-d", strtotime($StaringDate)) . " + ".$card['period_type_other']." year"));;

		} else if ($card['period_type']==4) { # LIFF TIME
			
			$StaringDate = "Member Life Time";
		} else {

			$StaringDate = "- - -";
		}

		# DAY OPTION

		$selected = '';

		$option_date = '';

		for ($x = 1; $x < 32; $x++) {

			if ($x == date('d',time())) { $select = 'selected="selected"'; }
			else { $select = ''; }

			if (strlen($x) == 1) { $d = '0'.$x; }
			else { $d = $x; }

			$option_date .= '<option value="'.$d.'" '.$select.'>'.$d.'</option>';
		}


		# MONTH OPTION

		$month = ["Jan.", "Feb.", "Mar.", "Apr.", "May.", "Jun.", "Jul.", "Aug.", "Sep.", "Oct.", "Nov.", "Dec."];
		$option_month = '';

		for ($x = 1; $x < 13; $x++) {

			if (strlen($x) == 1) { $d = '0'.$x; }
			else { $d = $x; }

			if ($d == date('m',time())) { $select = 'selected="selected"'; }
			else { $select = ''; }

			$option_month .= '<option value="'.($d).'" '.$select.'>'.$month[$x-1].'</option>';
		}


		# YEAR OPTION

		$this_year = date('Y',time());
		$start_year = $this_year-5;
		$end_year = $this_year+5;
		$option_year = '';

		for ($x = $start_year; $x <= $end_year; $x++) {

			if ($x == $this_year) { $select = 'selected="selected"'; }
			else { $select = ''; }

			$option_year .= '<option value="'.$x.'" '.$select.')>'.$x.'</option>';
		}

		$html = '<hr><center>
					'.$image.'<br><br>
					<label><table>
						<tr height="20px"><td style="text-align:right" width="300px">Name</td>
							<td style="text-align:center" width="30px">:</td>
							<td>'.$card['name'].'</td></tr>
						<tr height="20px"><td style="text-align:right">Type</td>
							<td style="text-align:center" width="30px">:</td>
							<td>'.$card['type_name'].'</td></tr>
						<tr height="20px"><td style="text-align:right">Period</td>
							<td style="text-align:center" width="30px">:</td>
							<td>'.$period.'</td></tr>
						<tr height="20px"><td style="text-align:right">Member Fee</td>
							<td style="text-align:center" width="30px">:</td>
							<td>'.number_format($card['member_fee'],2).' ฿</td></tr>
						<tr height="20px"><td style="text-align:right" valign="top">Description</td>
							<td style="text-align:center" valign="top" width="30px">:</td>
							<td width="300px">'.nl2br($card['description']).'</td></tr>
					</table></label>
				<hr>';

		if ($card['flag_multiple']=="Yes") { 

			$html .= '<span class="form-inline">
						<label>Card &nbsp;&nbsp;&nbsp;</label>
						<input type="number" min="1" max="100" id="multiple_card" name="multiple_card" class="form-control text-md" style="width:100px" value="1">
						<label>&nbsp;&nbsp;&nbsp; Qty</label>
					</span><br><br>'; 
		} else {

			$html .= '<span class="form-inline">
						<label>Card &nbsp;&nbsp;&nbsp;</label>
						<input type="number" class="form-control text-md" style="width:100px;text-align:center" value="1" disabled>
						<label>&nbsp;&nbsp;&nbsp; Qty</label>
					</span><br><br>'; 
		}


		# LAST DATE REGISTER

		if ($card['date_last_register']!='0000-00-00') {

			$html .= '<label>Last Register Date</label><br>
						'.DateOnly($card['date_last_register']).'<br><br>';
		}


		# PHONE CODE

		$sql_code = 'SELECT DISTINCT coun_PhoneCode 
						FROM country 
						WHERE coun_PhoneCode NOT IN (0,1,7) 
						ORDER BY coun_PhoneCode';
		$oRes_code = $oDB->Query($sql_code);
		$option_code = '';
		while ($axRow_code = $oRes_code->FetchRow(DBI_ASSOC)){

			$check_code = '';
			if ($axRow_code['coun_PhoneCode']=='66') { $check_code = 'selected'; }

			$option_code .= '<option value="+'.$axRow_code['coun_PhoneCode'].'" '.$check_code.'>+'.$axRow_code['coun_PhoneCode'].'</option>';
		}

		$html .= '	<label>Start Date</label><br>
					<span class="form-inline">
						<select id="start_date" class="form-control text-md" name="start_date" style="width:70px" onchange="ExpiredDate()" required autofocus>
							'.$option_date.'
						</select>
						<select id="start_month" class="form-control text-md" name="start_month" style="width:80px" onchange="ExpiredDate()" required autofocus>
							'.$option_month.'
						</select>
						<select id="start_year" class="form-control text-md" name="start_year" style="width:90px" onchange="ExpiredDate()" required autofocus>
							'.$option_year.'
						</select><br><br>
						<label>Expiry Date</label><br>
						<span id="expired_date" class="text-rq" style="font-size:15px">'.$StaringDate.'</span>
					</span>
				<hr>';

		# EXISTING CARD

		if ($card['flag_existing']=='Yes') {

			$html .= '	<label>Member</label>
						<br>
	                    <span class="form-inline">
	                    	<label>Member Card ID <span class="text-rq">*</span>&nbsp;&nbsp;&nbsp;</label>
	                    	<input type="text" id="search_member_code" name="search_member_code" style="width:200px" class="form-control text-md" placeholder="Text" onchange="CardFunction()">
	                    	<input type="button" class="btn btn-primary" value="Search" onclick="SearchMemberCard()">
	                    </span>
	                    <span id="member_data"><br></span>
	                    <br>';

			echo $html;

		} else {

			$html .= '	<label>Member</label>
						<br>
	                    <span class="form-inline">
	                    	<label>Mobile <span class="text-rq">*</span>&nbsp;&nbsp;&nbsp;</label>
	                    	<span class="form-inline">
	                    		<select class="form-control text-md" id="code_member" name="code_member">'.$option_code.'</select>
	                    		<input type="text" id="search_member" name="search_member" style="width:200px" maxlength="9" class="form-control text-md" placeholder="Tel" onchange="MemberFunction()">
	                    	</span>
	                    	<input type="button" class="btn btn-primary" value="Search" onclick="SearchMember()">
	                    </span>
	                    <span id="member_data"><br></span>
	                    <br>';

			echo $html;
		}
	}

	exit;	
}

else if($TASK =='Get_FormRedeem'){

	$rede_RewardRedeemID = $_REQUEST['rede_RewardRedeemID'];

	if ($rede_RewardRedeemID) {

		$sql_redeem = 'SELECT reward.*,
						reward_redeem.*,
						reward_ratio.*
						FROM reward_redeem
						LEFT JOIN reward
						ON reward.rewa_RewardID = reward_redeem.rewa_RewardID
						LEFT JOIN reward_ratio
						ON reward_ratio.rede_RewardRedeemID = reward_redeem.rede_RewardRedeemID
						WHERE reward_redeem.rede_RewardRedeemID="'.$rede_RewardRedeemID.'"';

		$oRes = $oDB->Query($sql_redeem);
		$redeem = $oRes->FetchRow(DBI_ASSOC);


		# REWARD

		if ($redeem['rewa_Type'] == 'Card') {

			$sql_card = 'SELECT image, image_newupload,path_image FROM mi_card WHERE card_id="'.$redeem['card_CardID'].'"';
			$oRes_card = $oDB->Query($sql_card);
			$axRow_card = $oRes_card->FetchRow(DBI_ASSOC);

			# REWARDS IMAGE

			if($axRow_card['image']!=''){

				$image = '<img src="../../upload/'.$axRow_card['path_image'].$axRow_card['image'].'" class="img-rounded image_border" height="100"/>';

			} else if($axRow_card['image_newupload']!=''){

				$image = '<img src="../../upload/'.$axRow_card['path_image'].$axRow_card['image_newupload'].'" class="img-rounded image_border" height="100"/>';

			} else {

				$image = '<img src="../../images/400x400.png" class="img-rounded image_border" height="100"/>';
			}

		} else {

			# REWARDS IMAGE

			if($redeem['rewa_Image']!=''){

				$image = '<img src="../../upload/'.$redeem['rewa_ImagePath'].$redeem['rewa_Image'].'" class="image_border" width="100" height="100"/>';

			} else {

				$image = '<img src="../../images/400x400.png" class="image_border" width="100" height="100"/>';
			}
		}


		# RATIO

		$ratio = '';

		$coty_Image = '';

		if ($redeem['coty_CollectionTypeID'] && $redeem['rera_RewardQty_Stamp']) {

			$sql_image = 'SELECT coty_Image FROM collection_type WHERE coty_CollectionTypeID="'.$redeem['coty_CollectionTypeID'].'"';

	 		$coty_Image = $oDB->QueryOne($sql_image);

			$coty_Image = '<img src="'.$_SESSION['path_upload_collection'].$coty_Image.'" style="margin-bottom:5px" width="12" height="12"/>';

			$ratio = $coty_Image.' '.$redeem['rera_RewardQty_Stamp']. ' / '.$redeem['rera_RewardQty'];
		
		} elseif ($redeem['rera_RewardQty_Point']) {

			$sql_image = 'SELECT coty_Image FROM collection_type WHERE coty_CollectionTypeID="3"';
	 		$coty_Image = $oDB->QueryOne($sql_image);

			$coty_Image = '<img src="'.$_SESSION['path_upload_collection'].$coty_Image.'" style="margin-bottom:5px" width="12" height="12"/>';

			$ratio .= $coty_Image.' &nbsp; '.$redeem['rera_RewardQty_Point']. ' / '.$redeem['rera_RewardQty'];

			$ratio = $coty_Image.' '.$redeem['rera_RewardQty_Point'];
		}

		if ($redeem['rera_CardID']!='') {

			$token = strtok($redeem['rera_CardID'] , ",");
			$card = array();

			$j = 0;

			while ($token !== false) {

	    		$card[$j] =  $token;
	    		$token = strtok(",");
	    		$j++;
			}

			$arrlength = count($card);
			$card_data = "";

			for($x = 0; $x < $arrlength; $x++) {

				$sql_card = 'SELECT name FROM mi_card WHERE card_id="'.$card[$x].'"';
				$name = $oDB->QueryOne($sql_card);

				if ($x == 0) { $card_data .= $name; }
				else { $card_data .= ', '.$name; }
			}

			$ratio = 'Card Regiter ('.$card_data.')';
		}


		$amount = '';

		if ($redeem['rera_AmountPlus']!='0.00') {

			$amount = '<tr height="20px">
							<td style="text-align:right">Amount</td>
							<td style="text-align:center" width="30px">:</td>
							<td>'.$redeem['rera_AmountPlus'].' ฿</td>
						</tr>';
		}



		# TIME

		if($redeem['rede_Time']=='T') {	

			$redeem['rede_StartDate'] = DateTime($redeem['rede_StartDate']);	

			$redeem['rede_EndDate'] = DateTime($redeem['rede_EndDate']);

			$redeem_time = $redeem['rede_StartDate'].' - '.$redeem['rede_EndDate'];

		} else if ($redeem['rede_Time']=='F') {	

			$redeem_time = "Not Specific";	
		}


		# DESCRIPTION

		if ($redeem['rede_Description'] == '') { $redeem['rede_Description'] = '-'; }


		# CONDITION

		if ($redeem['rede_Condition'] == '') { $redeem['rede_Condition'] = '-'; }


		# REDEEM LIMIT

		$limit = '';

		if ($redeem['rede_RedeemLimit'] == 'Unlimit') { 

			$limit = $redeem['rede_RedeemLimit'];

		} else {

			$limit = $redeem['rede_NumberTime']; 
		}


		# REPETITION

		if ($redeem['rede_QtyPer'] == 'Not') { $redeem['rede_QtyPer'] = 'Not Specific'; }

		if ($redeem['rede_Repetition'] == 'T') { 

			if ($redeem['rede_QtyPerData'] != '') { $redeem['rede_QtyPerData'] = ' ('.$redeem['rede_QtyPerData'].')'; }

			$rede_Repetition = $redeem['rede_Qty'].' Per '.$redeem['rede_QtyPer'].' '.$redeem['rede_QtyPerData'];

		} else { 

			$rede_Repetition = '-';
		}



		# PROPERLY

			$properly = '';
			$properly_data = '';

			# AGE

			if ($redeem['rewa_Age'] != '') {	

	            $token = strtok($redeem['rewa_Age'] , ",");

				$target_data = array();

				$z = 0;

				while ($token !== false) {

					$target_data[$z] =  $token;
					$token = strtok(",");
					$z++;
				}

				$arrlength = count($target_data);

				$age = "";

				for($x=0; $x<$arrlength; $x++) {

					if ($x == 1) { $age .= ' - '; }

					$sql_target = 'SELECT mata_NameEn
									FROM master_target
									WHERE mata_MasterTargetID="'.$target_data[$x].'"';

		 			$age .= $oDB->QueryOne($sql_target);
				}

				$properly_data .= '<tr height="20px"><td style="text-align:right" width="300px">Age</td>
									<td style="text-align:center" width="30px">:</td>
									<td width="300px">'.$age.' Years Old</td></tr>';
			}


			# GENDER

			if ($redeem['rewa_Gender'] == '1') {  

				$properly_data .= '<tr height="20px"><td style="text-align:right" width="300px">Gender</td>
									<td style="text-align:center" width="30px">:</td>
									<td width="300px">Male</td></tr>';

			} elseif ($redeem['rewa_Gender'] == '2') {

				$properly_data .= '<tr height="20px"><td style="text-align:right" width="300px">Gender</td>
									<td style="text-align:center" width="30px">:</td>
									<td width="300px">Female</td></tr>';
			}


			# MARITAL

			if ($redeem['rewa_Marital'] != '0') { 

				$sql_target = 'SELECT mata_NameEn
								FROM master_target
								WHERE mafi_MasterFieldID="7"
								AND mata_MasterTargetID="'.$redeem['rewa_Marital'].'"';

	 			$redeem['rewa_Marital'] = $oDB->QueryOne($sql_target);

				$properly_data .= '<tr height="20px"><td style="text-align:right" width="300px">Marital</td>
									<td style="text-align:center" width="30px">:</td>
									<td width="300px">'.$redeem['rewa_Marital'].'</td></tr>';
			}


			# EDUCATION

			if ($redeem['rewa_Education'] != '0') { 

				$sql_target = 'SELECT mata_NameEn
								FROM master_target
								WHERE mafi_MasterFieldID="12"
								AND mata_MasterTargetID="'.$redeem['rewa_Education'].'"';

	 			$redeem['rewa_Education'] = $oDB->QueryOne($sql_target);

				$properly_data .= '<tr height="20px"><td style="text-align:right" width="300px">Education</td>
									<td style="text-align:center" width="30px">:</td>
									<td width="300px">'.$redeem['rewa_Education'].'</td></tr>';
			}


			# ACTIVITY

			if ($redeem['rewa_Activity'] != '0') {

				$sql_target = 'SELECT mata_NameEn
								FROM master_target
								WHERE mafi_MasterFieldID="13"
								AND mata_MasterTargetID="'.$redeem['rewa_Activity'].'"';

	 			$redeem['rewa_Activity'] = $oDB->QueryOne($sql_target);

				$properly_data .= '<tr height="20px"><td style="text-align:right" width="300px">Activity</td>
									<td style="text-align:center" width="30px">:</td>
									<td width="300px">'.$redeem['rewa_Activity'].'</td></tr>';
			}


			# INCOME

			if ($redeem['rewa_MonthlyPersonalIncome'] != '0') {

				$sql_target = 'SELECT mata_NameEn
								FROM master_target
								WHERE mafi_MasterFieldID="19" 
								AND mata_MasterTargetID="'.$redeem['rewa_MonthlyPersonalIncome'].'"';

	 			$redeem['rewa_MonthlyPersonalIncome'] = $oDB->QueryOne($sql_target);

				$properly_data .= '<tr height="20px"><td style="text-align:right" width="300px">Monthly Personal Income</td>
									<td style="text-align:center" width="30px">:</td>
									<td width="300px">'.$redeem['rewa_MonthlyPersonalIncome'].'</td></tr>';
			}


			# PROVINCE

			if ($redeem['rewa_Province'] != '0') {

				$sql_province = 'SELECT prov_Name
								FROM province
								WHERE prov_ProvinceID="'.$redeem['rewa_Province'].'"';

	 			$redeem['rewa_Province'] = $oDB->QueryOne($sql_province);

				$properly_data .= '<tr height="20px"><td style="text-align:right" width="300px">Provice</td>
									<td style="text-align:center" width="30px">:</td>
									<td width="300px">'.$redeem['rewa_Province'].'</td></tr>';
			}


			# REWARD TARGET

			$sql_target = 'SELECT reta_Target, cufi_CustomFieldID
							FROM reward_target
							WHERE rewa_RewardID="'.$redeem['rewa_RewardID'].'" AND reta_Deleted=""';

			$oRes_target = $oDB->Query($sql_target);

			$reward_target = "";

			while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

				# FIELD

				$sql_field = 'SELECT cufi_Name 
								FROM custom_field 
								WHERE cufi_CustomFieldID="'.$target['cufi_CustomFieldID'].'"';

				$field = $oDB->QueryOne($sql_field);

				# VALUE

				$sql_value = 'SELECT clva_Name 
								FROM custom_list_value 
								WHERE cufi_CustomFieldID="'.$target['cufi_CustomFieldID'].'"
								AND clva_CustomListValueID="'.$target['reta_Target'].'"';

				$value = $oDB->QueryOne($sql_value);

				$properly_data .= '<tr height="20px"><td style="text-align:right" width="300px">'.$field.'</td>
									<td style="text-align:center" width="30px">:</td>
									<td width="300px">'.$value.'</td></tr>';
			}

		if ($properly_data != '') {

			$properly = '<br><u>Properly</u>
						<table>
							'.$properly_data.'
						</table>';
		}



		$html = '<hr><center>
					'.$image.'<br><br>

					<label><u>Detail</u>
					<table>
						<tr height="20px"><td style="text-align:right" width="300px">Redeem</td>
							<td style="text-align:center" width="30px">:</td>
							<td width="300px">'.$redeem['rede_Name'].'</td></tr>
						<tr height="20px"><td style="text-align:right">Reward</td>
							<td style="text-align:center" width="30px">:</td>
							<td>'.$redeem['rewa_Name'].'</td></tr>
						<tr height="20px"><td style="text-align:right">Type</td>
							<td style="text-align:center" width="30px">:</td>
							<td>'.$redeem['rewa_Type'].'</td></tr>
						<tr height="20px"><td style="text-align:right">Redeem Ratio</td>
							<td style="text-align:center" width="30px">:</td>
							<td>'.$ratio.'</td></tr>
						<tr height="20px"><td style="text-align:right">Redeem Period</td>
							<td style="text-align:center" width="30px">:</td>
							<td>'.$redeem_time.'</td></tr>
						<tr height="20px"><td style="text-align:right">Redeem Times</td>
							<td style="text-align:center" width="30px">:</td>
							<td>'.$limit.'</td></tr>
						<tr height="20px"><td style="text-align:right">Repetition</td>
							<td style="text-align:center" width="30px">:</td>
							<td>'.$rede_Repetition.'</td></tr>
						'.$amount.'
						<tr height="20px"><td style="text-align:right" valign="top">Decription</td>
							<td style="text-align:center" valign="top" width="30px">:</td>
							<td valign="top">'.$redeem['rede_Description'].'</td></tr>
						<tr height="20px"><td style="text-align:right" valign="top">Condition</td>
							<td style="text-align:center" valign="top" width="30px">:</td>
							<td valign="top">'.$redeem['rede_Condition'].'</td></tr>
					</table>
					'.$properly.'
					</label>
				<hr>';

		# DAY OPTION

		$selected = '';

		$option_date = '';

		for ($x = 1; $x < 32; $x++) {

			if ($x == date('d',time())) { $select = 'selected="selected"'; }
			else { $select = ''; }

			if (strlen($x) == 1) { $d = '0'.$x; }
			else { $d = $x; }

			$option_date .= '<option value="'.$d.'" '.$select.'>'.$d.'</option>';
		}


		# MONTH OPTION

		$month = ["Jan.", "Feb.", "Mar.", "Apr.", "May.", "Jun.", "Jul.", "Aug.", "Sep.", "Oct.", "Nov.", "Dec."];
		$option_month = '';

		for ($x = 1; $x < 13; $x++) {

			if (strlen($x) == 1) { $d = '0'.$x; }
			else { $d = $x; }

			if ($d == date('m',time())) { $select = 'selected="selected"'; }
			else { $select = ''; }

			$option_month .= '<option value="'.($d).'" '.$select.'>'.$month[$x-1].'</option>';
		}


		# YEAR OPTION

		$this_year = date('Y',time());
		$start_year = $this_year-5;
		$end_year = $this_year+5;
		$option_year = '';

		for ($x = $start_year; $x <= $end_year; $x++) {

			if ($x == $this_year) { $select = 'selected="selected"'; }
			else { $select = ''; }

			$option_year .= '<option value="'.$x.'" '.$select.')>'.$x.'</option>';
		}


		$html .= '	<label>Redeem Date</label><br>
					<span class="form-inline">
						<select id="redeem_date" class="form-control text-md" name="redeem_date" style="width:70px" required autofocus>
							'.$option_date.'
						</select>
						<select id="redeem_month" class="form-control text-md" name="redeem_month" style="width:80px" required autofocus>
							'.$option_month.'
						</select>
						<select id="redeem_year" class="form-control text-md" name="redeem_year" style="width:90px" required autofocus>
							'.$option_year.'
						</select>
					</span>
				<hr>
					<label>Member</label>
					<br>
                    <span class="form-inline">
                    	<label>Search <span class="text-rq">*</span>&nbsp;&nbsp;&nbsp;</label>
                    	<span class="form-inline">
                    		<input type="text" style="display:none">
                    		<input type="text" id="search_member" name="search_member" style="width:200px" class="form-control text-md" onchange="RedeemFunction()">
                    	</span>
                    	<input type="button" class="btn btn-primary" value="Search" onclick="SearchMember()">
                    </span>
                    <span id="member_data"><br></span>
                    <br>';

		echo $html;
	}

	exit;	
}


else if($TASK =='Get_CardName'){

	$bran_BrandID = $_REQUEST['bran_BrandID'];

	$card_CardID = $_REQUEST['card_CardID'];

	$sql ="SELECT name,card_id FROM mi_card WHERE brand_id=".$bran_BrandID;

	$oRes = $oDB->Query($sql);

	if($oRes){

		$option ='';

		while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

			$token = strtok($card_CardID , ",");

			$card = array();

			$i = 0;

			while ($token !== false) {
    			
    			$card[$i] =  $token;
    			$token = strtok(",");
    			$i++;

			}

			$arrlength = count($card);

			$select='';

			for($x = 0; $x < $arrlength; $x++) {

				if($card[$x]==$axRow['card_id']){		

					$select = 'selected="selected"';	

				}

			}

			$option .='<option value="'.$axRow['card_id'].'" '.$select.'>' .$axRow['name']. '</option>';

		}

		$html='<select id="card_CardID" class="form-control text-md" name="card_CardID[]" required autofocus multiple <%if $data.card_CardID%>disabled<%/if%>>

			'.$option.'

			</select>';

		echo $html;

		exit;		

	} else {

		$html='<select id="card_CardID" class="form-control text-md" name="card_CardID[]">

				<option value="">Please Select ..</option>

				</select>

				' ;

		echo $html;

		exit;	

	}

}


else if($TASK =='Get_Privilege'){

	$card_CardID = $_REQUEST['card_CardID'];
	$brnc_BranchID = $_REQUEST['branch_id'];

	$option ='';

	$sql_priv ="SELECT privilege.priv_Name, privilege.priv_PrivilegeID 
				FROM mi_card_register
				LEFT JOIN privilege
				ON privilege.priv_PrivilegeID = mi_card_register.privilege_id
				WHERE mi_card_register.card_id=".$card_CardID." 
				AND mi_card_register.branch_id=".$brnc_BranchID." 
				AND mi_card_register.status='0'
				AND privilege.priv_PrivilegeID!=''";

	$oRes_priv = $oDB->Query($sql_priv);
	$check_priv = $oDB->QueryOne($sql_priv);

	if($check_priv){

		$option .= '<optgroup label="Privilege">';

		while ($axRow = $oRes_priv->FetchRow(DBI_ASSOC)){

			$option .= '<option value="p'.$axRow['priv_PrivilegeID'].'">' .$axRow['priv_Name']. '</option>';
		}

		$option .= '</optgroup>';	

	}

	$sql_coup ="SELECT coupon.coup_Name, coupon.coup_CouponID 
				FROM mi_card_register
				LEFT JOIN coupon
				ON coupon.coup_CouponID = mi_card_register.coupon_id
				WHERE mi_card_register.card_id=".$card_CardID." 
				AND mi_card_register.branch_id=".$brnc_BranchID." 
				AND mi_card_register.status='0'
				AND coupon.coup_Birthday=''
				AND coupon.coup_CouponID!=''";

	$oRes_coup = $oDB->Query($sql_coup);
	$check_coup = $oDB->QueryOne($sql_coup);

	if($check_coup){

		$option .= '<optgroup label="Coupon">';

		while ($axRow = $oRes_coup->FetchRow(DBI_ASSOC)){

			$option .= '<option value="c'.$axRow['coup_CouponID'].'">' .$axRow['coup_Name']. '</option>';
		}

		$option .= '</optgroup>';	

	}

	$sql_hbd ="SELECT coupon.coup_Name, coupon.coup_CouponID 
				FROM mi_card_register
				LEFT JOIN coupon
				ON coupon.coup_CouponID = mi_card_register.coupon_id
				WHERE mi_card_register.card_id=".$card_CardID." 
				AND mi_card_register.branch_id=".$brnc_BranchID." 
				AND mi_card_register.status='0'
				AND coupon.coup_Birthday='T'
				AND coupon.coup_CouponID!=''";

	$oRes_hbd = $oDB->Query($sql_hbd);
	$check_hbd = $oDB->QueryOne($sql_hbd);

	if($check_hbd){

		$option .= '<optgroup label="Birthday Coupon">';

		while ($axRow = $oRes_hbd->FetchRow(DBI_ASSOC)){

			$option .= '<option value="h'.$axRow['coup_CouponID'].'">' .$axRow['coup_Name'].'</option>';
		}

		$option .= '</optgroup>';	

	}

	$sql_acti = "SELECT activity.acti_Name, activity.acti_ActivityID 
				FROM mi_card_register
				LEFT JOIN activity
				ON activity.acti_ActivityID = mi_card_register.activity_id
				WHERE mi_card_register.card_id=".$card_CardID." 
				AND mi_card_register.branch_id=".$brnc_BranchID." 
				AND mi_card_register.status='0'
				AND activity.acti_ActivityID !=''";

	$oRes_acti = $oDB->Query($sql_acti);
	$check_acti = $oDB->QueryOne($sql_acti);

	if($check_acti){

		$option .= '<optgroup label="Activity">';

		while ($axRow = $oRes_acti->FetchRow(DBI_ASSOC)){

			$option .= '<option value="a'.$axRow['acti_ActivityID'].'">' .$axRow['acti_Name'].'</option>';
		}

		$option .= '</optgroup>';	

	}

	$html = '<select id="privilege_id" class="form-control text-md" name="privilege_id" onchange="MemberUse()" required autofocus>

			<option value="">Please Select ..</option>

			'.$option.'

			</select>';

	echo $html;

	exit;	

}


else if($TASK =='Get_BranchName'){

	$bran_BrandID = $_REQUEST['bran_BrandID'];

	$brnc_BranchID = $_REQUEST['brnc_BranchID'];

	$require = $_REQUEST['require'];

	$sql = "SELECT name,branch_id FROM mi_branch WHERE brand_id=".$bran_BrandID;

	$oRes = $oDB->Query($sql);

	if($oRes){

		$option ='';

		while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

			$token = strtok($brnc_BranchID , ",");

			$branch = array();

			$i = 0;

			while ($token !== false) {
    			
    			$branch[$i] =  $token;
    			$token = strtok(",");
    			$i++;
			}

			$arrlength = count($branch);

			$select='';

			for($x = 0; $x < $arrlength; $x++) {

				if($branch[$x]==$axRow['branch_id']){		

					$select = 'selected="selected"';	
				}
			}

			$option .='<option value="'.$axRow['branch_id'].'" '.$select.'>'.$axRow['name'].'</option>';
		}

		if ($require=='F') {

			$html = '<select id="brnc_BranchID" class="form-control text-md" name="brnc_BranchID[]" multiple>
					'.$option.'
				</select>';	

		} else {

			$html = '<select id="brnc_BranchID" class="form-control text-md" name="brnc_BranchID[]" required autofocus multiple>
					'.$option.'
				</select>';	
		}

	} else {

		$html = '<select id="brnc_BranchID" class="form-control text-md" name="brnc_BranchID[]">
					<option value="">Please Select ..</option>
				</select>' ;
	}

	echo $html;

	exit;	
}


else if($TASK =='Get_BranchID'){

	$bran_BrandID = $_REQUEST['bran_BrandID'];

	$sql = "SELECT name,branch_id FROM mi_branch WHERE brand_id=".$bran_BrandID;

	$oRes = $oDB->Query($sql);

	if($oRes){

		$option ='';

		while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

			$option .='<option value="'.$axRow['branch_id'].'">' .$axRow['name']. '</option>';
		}

		$html='<select id="brnc_BranchID" class="form-control text-md" name="brnc_BranchID" onchange="MemberUse()" required autofocus>

			<option value="">Please Select ..</option>

			'.$option.'

			</select>';	

	} else {

		$html='<select id="brnc_BranchID" class="form-control text-md" name="brnc_BranchID">

				<option value="">Please Select ..</option>

				</select>

				' ;

	}

	echo $html;

	exit;	

}


else if($TASK =='Get_MemberUse'){

	$card_CardID = $_REQUEST['card_id'];
	$brnc_BranchID = $_REQUEST['branch_id'];
	$privilege_id = $_REQUEST['privilege_id'];

	if ($card_CardID && $brnc_BranchID && $privilege_id) {

		$type = substr($privilege_id,0,1);

		if ($type == 'p') {

			$id = substr($privilege_id,1,strlen($privilege_id));
			$sql_priv = 'SELECT priv_Name AS name,
						priv_Status AS status,
						priv_Image AS image,
						priv_ImageNew AS image_new,
						priv_ImagePath AS path_image,
						priv_Motivation AS plan,
						priv_MotivationID AS plan_id
						FROM privilege WHERE priv_PrivilegeID = "'.$id.'"';

			$type = 'Privilege';
			$plan = 'Privilege';

		} else if ($type == 'c') {

			$id = substr($privilege_id,1,strlen($privilege_id));
			$sql_priv = 'SELECT coup_Name AS name,
						coup_Status AS status,
						coup_Image AS image,
						coup_ImageNew AS image_new,
						coup_ImagePath AS path_image,
						coup_Motivation AS plan,
						coup_MotivationID AS plan_id
						FROM coupon WHERE coup_CouponID = "'.$id.'"';

			$type = 'Coupon'; 
			$plan = 'Coupon'; 

		} else if ($type == 'h') {

			$id = substr($privilege_id,1,strlen($privilege_id));
			$sql_priv = 'SELECT coup_Name AS name,
						coup_Status AS status,
						coup_Image AS image,
						coup_ImageNew AS image_new,
						coup_ImagePath AS path_image,
						coup_Motivation AS plan,
						coup_MotivationID AS plan_id
						FROM coupon WHERE coup_CouponID = "'.$id.'"';

			$type = 'Birthday Coupon';
			$plan = 'Coupon'; 

		}  else {

			$id = substr($privilege_id,1,strlen($privilege_id));
			$sql_priv = 'SELECT acti_Name AS name,
						acti_Status AS status,
						acti_Image AS image,
						acti_ImageNew AS image_new,
						acti_ImagePath AS path_image,
						acti_Motivation AS plan,
						acti_MotivationID AS plan_id
						FROM activity WHERE acti_ActivityID = "'.$id.'"';

			$type = 'Activity';
			$plan = 'Activity'; 
		}

		$oRes = $oDB->Query($sql_priv);
		$axRow = $oRes->FetchRow(DBI_ASSOC);

		# MOTIVATION

		if ($axRow['plan']=='Point') {

			# POINT

			$sql_point = 'SELECT * FROM motivation_plan_point 
							WHERE mopp_PrivilegeType="'.$plan.'"
							AND mopp_PrivilegeID="'.$id.'"';
			$point_priv = $oDB->Query($sql_point);
			$point = $point_priv->FetchRow(DBI_ASSOC);

			$motivation = $point['mopp_UseAmount'].' ฿ / '.$point['mopp_PointQty'].' Point Qty ('.$point['mopp_Method'].')';

		} else if ($axRow['plan']=='Stamp') {

			# STAMP

			$sql_stamp = 'SELECT motivation_plan_stamp.*,
							collection_type.coty_Image
							FROM motivation_plan_stamp
							LEFT JOIN collection_type
							ON collection_type.coty_CollectionTypeID = motivation_plan_stamp.mops_CollectionTypeID
							WHERE motivation_plan_stamp.mops_PrivilegeType="'.$plan.'"
							AND motivation_plan_stamp.mops_PrivilegeID="'.$id.'"';
			$stamp_priv = $oDB->Query($sql_stamp);
			$stamp = $stamp_priv->FetchRow(DBI_ASSOC);

			$motivation = $stamp['mops_StampQty'].' <img src="'.$_SESSION['path_upload_collection'].$stamp['coty_Image'].'" width="15" style="margin-bottom:7px"/> / '.$stamp['mops_TimeQty'].' Times';
			
		} else { $motivation = '-'; }


		$data = '<div class="adj_row">           
      				<label class="lable-form">Entry Date</label>
      				<input type="text" style="text-align:center" class="form-control text-md" value="'.date('d F o H:i:s').'" disabled>
    			</div><center>
			<br>
      		<table class="myPopup">
	        	<tr style="height:30px">
	        		<td rowspan="5" width="300px" valign="top" style="text-align:center;">';

	    if ($axRow['image_new']) {

	    	$data .= '<img src="../../upload/'.$axRow['path_image'].$axRow['image_new'].'" width="240" height="150" class="image_border"/>';
	    
	    } else if ($axRow['image']) {

	    	$data .= '<img src="../../upload/'.$axRow['path_image'].$axRow['image'].'" width="240" height="150" class="image_border"/>';
	    
	    } else {

	    	$data .= '<img src="../../images/card_privilege.png" width="240" height="150" class="image_border"/>';
	    } 

		$data .= '</td>
			        <td style="text-align:right"><b>Name</b></td>
			        <td style="text-align:center;width:20px"><b>:</b></td>
			        <td><b>'.$axRow['name'].'</b></td></tr>
		        </tr>
		        <tr style="height:30px">
		            <td style="text-align:right"><b>Type</b></td>
		            <td style="text-align:center;"><b>:</b></td>
		            <td><b>'.$type.'</b></td></tr>
		        <tr style="height:30px">
		            <td style="text-align:right"><b>Status</b></td>
		            <td style="text-align:center;"><b>:</b></td>
		            <td><b>'.$axRow['status'].'</b></td></tr>
		        <tr style="height:30px">
		            <td style="text-align:right"><b>Motivation</b></td>
		            <td style="text-align:center;"><b>:</b></td>
		            <td><b>'.$motivation.'</b></td></tr>
		        <tr style="height:30px">
		            <td style="text-align:right" valign="top"><b></b></td>
		            <td style="text-align:center;" valign="top"><b></b></td>
		            <td valign="top"><b></b></td></tr>

	    	</table>

      		<br>';

      	$table_w = '500';

		if ($axRow['plan']=='Point') { $table_w += 250; }
		else if ($axRow['plan']=='Stamp') { $table_w += 100; }
		else { $table_w += 100; }

      	$data .= '<div style="table-layout:fixed;width:'.$table_w.'px">
				<table id="example" class="table-bordered" style="width:'.$table_w.'px;height:35px">
					<tr class="th_table">
					    <td style="text-align:center;width:50px"><b>Select</b></td>
					    <td style="text-align:center;width:200px"><b>Member Profile</b></td>';

		if ($axRow['plan']=='Point') {

			$data .= '<td style="text-align:center;width:140px"><b>Receieve No.</b></td>';
			$data .= '<td style="text-align:center;width:110px"><b>Amount</b></td>';
		
		} else if ($axRow['plan']=='Stamp') {

			$data .= '<td style="text-align:center;width:100px"><b>Times to Use</b></td>';

		} else {

			$data .= '<td style="text-align:center;width:100px"><b>Times to Use</b></td>';
		}

		$data .= '<td style="text-align:center;width:250px"><b>Use Date / Collect Date</b></td>';

		$data .= '</tr>
				</table>

	      		<div style="height:300px;overflow-y:auto;overflow-x:hidden;">
				<table id="example" class="table table-hover table-bordered" style="width:'.$table_w.'px">';

		$sql_member = 'SELECT mb_member.*,
						mb_member_register.period_type,
						mb_member_register.date_expire
						FROM mb_member_register
						LEFT JOIN mb_member
						ON mb_member.member_id = mb_member_register.member_id
						WHERE mb_member_register.card_id="'.$card_CardID.'" AND mb_member_register.flag_del=""
						ORDER BY mb_member.email ASC';

		$oRes_member = $oDB->Query($sql_member);
		$check_member = $oDB->QueryOne($sql_member);


			# DAY OPTION

			$this_day = date('j');
			$option_date = '';

			for ($x = 1; $x < 32; $x++) {

				if ($x == $this_day) { $select = 'selected="selected"'; }
				else { $select = ''; }

				if (strlen($x) == 1) { $d = '0'.$x; }
				else { $d = $x; }

				$option_date .= '<option value="'.$d.'" '.$select.'>'.$d.'</option>';
			}


			# MONTH OPTION

			$this_month = date('n');
			$month = ["Jan.", "Feb.", "Mar.", "Apr.", "May.", "Jun.", "Jul.", "Aug.", "Sep.", "Oct.", "Nov.", "Dec."];
			$option_month = '';

			for ($x = 1; $x < 13; $x++) {

				if (strlen($x) == 1) { $d = '0'.$x; }
				else { $d = $x; }

				if ($d == $this_month) { $select = 'selected="selected"'; }
				else { $select = ''; }

				$option_month .= '<option value="'.($d).'" '.$select.'>'.$month[$x-1].'</option>';
			}


			# YEAR OPTION

			$this_year = date('Y',time());
			$start_year = $this_year-100;
			$option_year = '';

			for ($x = $start_year; $x <= $this_year; $x++) {

				if ($x == $this_year) { $select = 'selected="selected"'; }
				else { $select = ''; }

				$option_year .= '<option value="'.$x.'" '.$select.'>'.$x.'</option>';
			}

			# TIME OPTION

			$this_hour = date('H',time());
			$this_min = date('i',time());
			$option_hour = "";
			$option_min = "";

			for ($x = 0; $x <= 24; $x++) {

				if (strlen($x) == 1) { $d = '0'.($x); }
				else { $d = $x; }

				if ($x == 0) { $select = 'selected="selected"'; }
				else { $select = ''; }

				$option_hour .= '<option value="'.$d.'" '.$select.'>'.$d.'</option>';
			}

			for ($x = 0; $x < 60; $x++) {

				if (strlen($x) == 1) { $d = '0'.($x); }
				else { $d = $x; }

				if ($x == 0) { $select = 'selected="selected"'; }
				else { $select = ''; }

				$option_min .= '<option value="'.$d.'" '.$select.'>'.$d.'</option>';
			}


		if ($check_member) {

			while ($member = $oRes_member->FetchRow(DBI_ASSOC)) {

				if ($member['period_type']=='4' || strtotime("now")<strtotime($member['date_expire'])) {

					if ($member['firstname']=="" && $member['firstname']=="") { $member_name = '-'; }
					else { $member_name = $member['firstname'].' '.$member['lastname']; }
					
					$data .= '<tr>
						    <td style="text-align:center;width:50px">
						    <input type="checkbox" name="member_id[]" value="'.$member['member_id'].'"></td>
						    <td style="width:200px">'.$member_name.'<br>'.$member['email'].'<br>'.$member['mobile'].'</td>';

					if ($axRow['plan']=='Stamp') {

						$data .= '<td style="width:80px">
									<input class="form-control text-md" type="number" name="'.$member['member_id'].'_time" maxlength="2" min="1" max="99" style="width:80px" value="1" placeholder="times"></td>';
					
					} else if ($axRow['plan']=='Point') {

						$data .= '<td style="text-align:center;width:120px;">
									<input class="form-control text-md" type="text" name="'.$member['member_id'].'_no" style="width:120px" placeholder="Receieve No."></td>
								<td style="text-align:center;width:100px">
									<input class="form-control text-md" type="number" name="'.$member['member_id'].'_amount" style="width:100px" placeholder="Amount"></td>';

					} else {

						if ($type=="Birthday Coupon" || $type=="Activity") {

							$data .= '<td style="width:100px;text-align:center"><b>1</b></td>';

						} else {

							$data .= '<td style="width:80px">
									<input class="form-control text-md" type="number" name="'.$member['member_id'].'_time" maxlength="2" min="1" max="99" style="width:80px" value="1" placeholder="times"></td>';
						}
					}

					$data .= '<td width="250px" style="text-align:center">
					        	<span class="form-inline">
								    <select id="date_'.$member['member_id'].'" class="form-control text-md" name="date_'.$member['member_id'].'" style="width:65px">
								        '.$option_date.'
								    </select>
								    <select id="month_'.$member['member_id'].'" class="form-control text-md" name="month_'.$member['member_id'].'" style="width:70px">
								        '.$option_month.'
								    </select>
								    <select id="year_'.$member['member_id'].'" class="form-control text-md" name="year_'.$member['member_id'].'" style="width:80px">
								        '.$option_year.'
								    </select>
								</span><br>
					        	<span class="form-inline">
								    <select id="hour_'.$member['member_id'].'" class="form-control text-md" name="hour_'.$member['member_id'].'" style="width:65px">
								        '.$option_hour.'
								    </select>
								    &nbsp; : &nbsp;
								    <select id="min_'.$member['member_id'].'" class="form-control text-md" name="min_'.$member['member_id'].'" style="width:70px">
								        '.$option_min.'
								    </select>
								</span></td>';

					$data .= '</tr>';
				}
			}

		} else {
					
			$data .= '<tr><td colspan="4" style="text-align:center">No Member Data</td></tr>';
		}

		$data .= '</table></div></div></center><br>';

	} else {

		$data = '<br><center><span style="color:red"><b>Please Select ';

		if (!$card_CardID) { $data .= 'Card'; } 
		else if (!$brnc_BranchID) { $data .= 'Branch'; } 
		else { $data .= 'Privilege'; }

		$data .= '</b></span></center><br>';
	}

	echo $data;

	exit;	

}


else if($TASK =='Get_Point'){

	$card_CardID = $_REQUEST['card_CardID'];

	$sql_brand_id = 'SELECT brand_id FROM mi_card WHERE card_id = "'.$card_CardID.'"';

	$brand_id = $oDB->QueryOne($sql_brand_id);

	$sql_branch = 'SELECT name, branch_id FROM mi_branch WHERE brand_id = "'.$brand_id.'"';

	
	$sql_privilege = 'SELECT DISTINCT privilege.priv_Name, privilege.priv_Status, privilege.priv_PrivilegeID
						FROM mi_card_register
						LEFT JOIN privilege
						ON mi_card_register.privilege_id = privilege.priv_PrivilegeID
						WHERE mi_card_register.card_id="'.$card_CardID.'"
						AND mi_card_register.status="0"
						AND mi_card_register.privilege_id!=""
						AND (privilege.priv_Motivation="Point" OR privilege.priv_Motivation="Point&Stamp")';

	$sql_coupon = 'SELECT DISTINCT coupon.coup_Name, coupon.coup_Status, coupon.coup_CouponID
					FROM mi_card_register
					LEFT JOIN coupon
					ON mi_card_register.coupon_id = coupon.coup_CouponID
					WHERE mi_card_register.card_id="'.$card_CardID.'"
					AND mi_card_register.status="0"
					AND mi_card_register.coupon_id!=""
					AND coupon.coup_Birthday!="T"
					AND coupon.coup_Motivation="Point"';

	$sql_hbd = 'SELECT DISTINCT coupon.coup_Name, coupon.coup_Status, coupon.coup_CouponID
				FROM mi_card_register
				LEFT JOIN coupon
				ON mi_card_register.coupon_id = coupon.coup_CouponID
				WHERE mi_card_register.card_id="'.$card_CardID.'"
				AND mi_card_register.status="0"
				AND mi_card_register.coupon_id!=""
				AND coupon.coup_Birthday="T"
				AND coupon.coup_Motivation="Point"';

	$sql_activity = 'SELECT DISTINCT activity.acti_Name, activity.acti_Status, activity.acti_ActivityID
						FROM mi_card_register
						LEFT JOIN activity
						ON mi_card_register.activity_id = activity.acti_ActivityID
						WHERE mi_card_register.card_id="'.$card_CardID.'"
						AND mi_card_register.status="0"
						AND mi_card_register.activity_id!=""
						AND activity.acti_Motivation="Point"';

	$oRes_branch = $oDB->Query($sql_branch);

	$oRes_privilege = $oDB->Query($sql_privilege);
	$check_priv = $oDB->QueryOne($sql_privilege);

	$oRes_coupon = $oDB->Query($sql_coupon);
	$check_coup = $oDB->QueryOne($sql_coupon);

	$oRes_hbd = $oDB->Query($sql_hbd);
	$check_hbd = $oDB->QueryOne($sql_hbd);

	$oRes_activity = $oDB->Query($sql_activity);
	$check_acti = $oDB->QueryOne($sql_activity);


	# LOOP PRIVILEGE

	$html = '';

	if ($check_priv) {

		$html = "<div class='form-group'><div class='col-md-12'><div id='parent' class='table-responsive'>
				<table id='myTable' class='table table-bordered' id='branch_and_privilege' style='background-color:white;'>
					<thead>
						<td>Branch \ Privilege</td>";

		$count_privilege = 0;

		while ($axRow_privilege = $oRes_privilege->FetchRow(DBI_ASSOC)) {

			if ($axRow_privilege['priv_Status'] == "Pending") {

				$status_pri = "(Pending)<br>";
			} 
			else {	

				$status_pri = "";

			}

			$html .= "<td style='text-align:center'>".$axRow_privilege['priv_Name']." <br>".$status_pri."

						<button type='button' class='btn btn-default btn-sm' id='".$axRow_privilege['priv_PrivilegeID']."' onclick='all_priv(this.id)'>
							<span class='glyphicon glyphicon-check' aria-hidden='true'></span>
						</button>

						<button type='button' class='btn btn-default btn-sm' id='".$axRow_privilege['priv_PrivilegeID']."' onclick='unall_priv(this.id)'>
							<span class='glyphicon glyphicon-unchecked' aria-hidden='true'></span>
						</button>

					</td>";

			$privilege_id[$count_privilege]  = $axRow_privilege['priv_PrivilegeID'];

			$count_privilege++;

		}

		$html .= "</thead><tbody>";

		# LOOP BRANCH

		while ($axRow_branch = $oRes_branch->FetchRow(DBI_ASSOC)) {

			$html .= "<tr><td class='td_head'>".$axRow_branch['name']."

						<span style='float:right'>
							<button type='button' class='btn btn-default btn-sm' id='".$axRow_branch['branch_id']."' onclick='all_brnc(this.id)'>
								<span class='glyphicon glyphicon-check' aria-hidden='true'></span>
							</button>
							
							<button type='button' class='btn btn-default btn-sm' id='".$axRow_branch['branch_id']."' onclick='unall_brnc(this.id)'>
								<span class='glyphicon glyphicon-unchecked' aria-hidden='true'></span>
							</button>
						</span>

						</td>";

			for ($i=0; $i < $count_privilege; $i++) {

				$sql_point_del = 'SELECT 

									mopo_Deleted
												
									FROM motivation_point
												
									WHERE card_CardID = "'.$card_CardID.'"
												
									AND brnc_BranchID = '.$axRow_branch['branch_id'].'
												
									AND priv_PrivilegeID = '.$privilege_id[$i].'';

				$point_del = $oDB->QueryOne($sql_point_del);


				$html .= "<td class='td_privilege".$privilege_id[$i]." td_branch".$axRow_branch['branch_id']."' style='text-align:center'>
								
							<input type='checkbox' name='check_".$axRow_branch['branch_id']."_".$privilege_id[$i]."' value='1' class='p".$privilege_id[$i]." bp".$axRow_branch['branch_id']."'";

				if ($point_del=='') {	

					$html .= " checked='checked'";
				}
				
				$html .= "></td>";

			}
				
			$html .= "</tr>";

		}

		$html .= "</tbody></table></div></div></div>";
	}




	## COUPON ##

	$html_coupon = '';

	if ($check_coup) {

		$html_coupon = "<div class='form-group'><div class='col-md-12'><div id='parent' class='table-responsive'>
				<table id='myTable' class='table table-bordered' id='branch_and_privilege' style='background-color:white;' >
					<thead>
						<td>Branch \ Coupon</td>";

		# LOOP COUPON

		$count_coupon = 0;

		while ($axRow_coupon = $oRes_coupon->FetchRow(DBI_ASSOC)) {

			if ($axRow_coupon['coup_Status'] == "Pending") {

				$status_cou = "(Pending)<br>";
			} 
			else {	

				$status_cou = "";

			}

			$html_coupon .= "<td style='text-align:center'>".$axRow_coupon['coup_Name']." <br>".$status_cou."

						<button type='button' class='btn btn-default btn-sm' id='".$axRow_coupon['coup_CouponID']."' onclick='all_coup(this.id)'>
							<span class='glyphicon glyphicon-check' aria-hidden='true'></span>
						</button>

						<button type='button' class='btn btn-default btn-sm' id='".$axRow_coupon['coup_CouponID']."' onclick='unall_coup(this.id)'>
							<span class='glyphicon glyphicon-unchecked' aria-hidden='true'></span>
						</button>

					</td>";

			$coupon_id[$count_coupon]  = $axRow_coupon['coup_CouponID'];

			$count_coupon++;

		}

		$html_coupon .= "</thead><tbody>";

		# LOOP BRANCH

		$oRes_branch = $oDB->Query($sql_branch);

		while ($axRow_branch = $oRes_branch->FetchRow(DBI_ASSOC)) {

			$html_coupon .= "<tr><td class='td_head'>".$axRow_branch['name']."

						<span style='float:right'>
							<button type='button' class='btn btn-default btn-sm' id='".$axRow_branch['branch_id']."' onclick='all_brnc_coup(this.id)'>
								<span class='glyphicon glyphicon-check' aria-hidden='true'></span>
							</button>
							
							<button type='button' class='btn btn-default btn-sm' id='".$axRow_branch['branch_id']."' onclick='unall_brnc_coup(this.id)'>
								<span class='glyphicon glyphicon-unchecked' aria-hidden='true'></span>
							</button>
						</span>

						</td>";

			for ($i=0; $i < $count_coupon; $i++) {

				$sql_point_del = 'SELECT 

									mopo_Deleted
												
									FROM motivation_point
												
									WHERE card_CardID = "'.$card_CardID.'"
												
									AND brnc_BranchID = '.$axRow_branch['branch_id'].'
												
									AND coup_CouponID = '.$coupon_id[$i].'';

				$point_del = $oDB->QueryOne($sql_point_del);


				$html_coupon .= "<td style='text-align:center' >
								
								<input type='checkbox' name='check_c".$axRow_branch['branch_id']."_".$coupon_id[$i]."' value='1' class='c".$coupon_id[$i]." bc".$axRow_branch['branch_id']."'";

				if ($point_del=='') {	

					$html_coupon .= " checked='checked'";	
				}
				
				$html_coupon .= "></td>";

			}
				
			$html_coupon .= "</tr>";

		}

		$html_coupon .= "</tbody></table></div></div></div>";
	}




	## BIRTHDAY COUPON ##

	$html_hbd = '';

	if ($check_hbd) {

		$html_hbd = "<div class='form-group'><div class='col-md-12'><div id='parent' class='table-responsive'>
				<table id='myTable' class='table table-bordered' id='branch_and_privilege' style='background-color:white;' >
					<thead>
						<td>Branch \ Birthday Coupon</td>";

		# LOOP HBD

		$count_hbd = 0;

		while ($axRow_hbd = $oRes_hbd->FetchRow(DBI_ASSOC)) {

			if ($axRow_hbd['coup_Status'] == "Pending") {

				$status_hbd = "(Pending)<br>";
			} 
			else {	

				$status_hbd = "";

			}

			$html_hbd .= "<td style='text-align:center'>".$axRow_hbd['coup_Name']." <br>".$status_hbd."

						<button type='button' class='btn btn-default btn-sm' id='".$axRow_hbd['coup_CouponID']."' onclick='all_hbd(this.id)'>
							<span class='glyphicon glyphicon-check' aria-hidden='true'></span>
						</button>

						<button type='button' class='btn btn-default btn-sm' id='".$axRow_hbd['coup_CouponID']."' onclick='unall_hbd(this.id)'>
							<span class='glyphicon glyphicon-unchecked' aria-hidden='true'></span>
						</button>

					</td>";

			$hbd_id[$count_hbd]  = $axRow_hbd['coup_CouponID'];

			$count_hbd++;

		}

		$html_hbd .= "</thead><tbody>";

		# LOOP BRANCH

		$oRes_branch = $oDB->Query($sql_branch);

		while ($axRow_branch = $oRes_branch->FetchRow(DBI_ASSOC)) {

			$html_hbd .= "<tr><td class='td_head'>".$axRow_branch['name']."

						<span style='float:right'>
							<button type='button' class='btn btn-default btn-sm' id='".$axRow_branch['branch_id']."' onclick='all_brnc_hbd(this.id)'>
								<span class='glyphicon glyphicon-check' aria-hidden='true'></span>
							</button>
							
							<button type='button' class='btn btn-default btn-sm' id='".$axRow_branch['branch_id']."' onclick='unall_brnc_hbd(this.id)'>
								<span class='glyphicon glyphicon-unchecked' aria-hidden='true'></span>
							</button>
						</span>

						</td>";

			for ($i=0; $i < $count_hbd; $i++) {

				$sql_point_del = 'SELECT 

									mopo_Deleted
												
									FROM motivation_point
												
									WHERE card_CardID = "'.$card_CardID.'"
												
									AND brnc_BranchID = '.$axRow_branch['branch_id'].'
												
									AND coup_CouponID = '.$hbd_id[$i].'';

				$point_del = $oDB->QueryOne($sql_point_del);


				$html_hbd .= "<td style='text-align:center' >
								
							<input type='checkbox' name='check_h".$axRow_branch['branch_id']."_".$hbd_id[$i]."' value='1' class='h".$hbd_id[$i]." bh".$axRow_branch['branch_id']."'";

				if ($point_del=='') {	

					$html_hbd .= " checked='checked'";	

				}
				
				$html_hbd .= "></td>";

			}
				
			$html_hbd .= "</tr>";

		}

		$html_hbd .= "</tbody></table></div></div></div>";
	}




	## ACTIVITY ##

	$html_activity = '';

	if ($check_acti) {

		$html_activity = "<div class='form-group'><div class='col-md-12'><div id='parent' class='table-responsive'>
				<table id='myTable' class='table table-bordered' style='background-color:white;' >
					<thead>
						<td>Branch \ Activity</td>";

		# LOOP ACTIVITY

		$count_activity = 0;

		while ($axRow_activity = $oRes_activity->FetchRow(DBI_ASSOC)) {

			if ($axRow_activity['acti_Status'] == "Pending") {

				$status_act = "(Pending)<br>";
			} 
			else {	

				$status_act = "";

			}

			$html_activity .= "<td style='text-align:center'>".$axRow_activity['acti_Name']." <br>".$status_act."

						<button type='button' class='btn btn-default btn-sm' id='".$axRow_activity['acti_ActivityID']."' onclick='all_acti(this.id)'>
							<span class='glyphicon glyphicon-check' aria-hidden='true'></span>
						</button>

						<button type='button' class='btn btn-default btn-sm' id='".$axRow_activity['acti_ActivityID']."' onclick='unall_acti(this.id)'>
							<span class='glyphicon glyphicon-unchecked' aria-hidden='true'></span>
						</button>

					</td>";

			$activity_id[$count_activity]  = $axRow_activity['acti_ActivityID'];

			$count_activity++;

		}

		$html_activity .= "</thead><tbody>";

		# LOOP BRANCH

		$oRes_branch = $oDB->Query($sql_branch);

		while ($axRow_branch = $oRes_branch->FetchRow(DBI_ASSOC)) {

			$html_activity .= "<tr><td class='td_head'>".$axRow_branch['name']."

						<span style='float:right'>
							<button type='button' class='btn btn-default btn-sm' id='".$axRow_branch['branch_id']."' onclick='all_brnc_acti(this.id)'>
								<span class='glyphicon glyphicon-check' aria-hidden='true'></span>
							</button>
							
							<button type='button' class='btn btn-default btn-sm' id='".$axRow_branch['branch_id']."' onclick='unall_brnc_acti(this.id)'>
								<span class='glyphicon glyphicon-unchecked' aria-hidden='true'></span>
							</button>
						</span>

						</td>";

			for ($i=0; $i < $count_activity; $i++) {

				$sql_point_del = 'SELECT 

									mopo_Deleted
												
									FROM motivation_point
												
									WHERE card_CardID = "'.$card_CardID.'"
												
									AND brnc_BranchID = '.$axRow_branch['branch_id'].'
												
									AND acti_ActivityID = '.$activity_id[$i].'';

				$point_del = $oDB->QueryOne($sql_point_del);


				$html_activity .= "<td style='text-align:center' >
								
								<input type='checkbox' name='check_a".$axRow_branch['branch_id']."_".$activity_id[$i]."' value='1' class='a".$activity_id[$i]." ba".$axRow_branch['branch_id']."'";

				if ($point_del=='') {	

					$html_activity .= " checked='checked'";	
				}
				
				$html_activity .= "></td>";

			}
				
			$html_activity .= "</tr>";

		}

		$html_activity .= "</tbody></table></div></div></div>";
	}



	echo $html.'<br>'.$html_coupon.'<br>'.$html_hbd.'<br>'.$html_activity;

	exit;		

}


else if($TASK =='Get_Stamp'){

	$card_CardID = $_REQUEST['card_CardID'];

	$sql_brand_id = 'SELECT brand_id FROM mi_card WHERE card_id = "'.$card_CardID.'"';

	$brand_id = $oDB->QueryOne($sql_brand_id);

	$sql_branch = 'SELECT name, branch_id FROM mi_branch WHERE brand_id = "'.$brand_id.'"';

	
	$sql_privilege = 'SELECT DISTINCT privilege.priv_Name, privilege.priv_Status, privilege.priv_PrivilegeID
						FROM mi_card_register
						LEFT JOIN privilege
						ON mi_card_register.privilege_id = privilege.priv_PrivilegeID
						WHERE mi_card_register.card_id="'.$card_CardID.'"
						AND mi_card_register.status="0"
						AND mi_card_register.privilege_id!=""
						AND (privilege.priv_Motivation="Stamp" OR privilege.priv_Motivation="Point&Stamp")';

	$sql_coupon = 'SELECT DISTINCT coupon.coup_Name, coupon.coup_Status, coupon.coup_CouponID
					FROM mi_card_register
					LEFT JOIN coupon
					ON mi_card_register.coupon_id = coupon.coup_CouponID
					WHERE mi_card_register.card_id="'.$card_CardID.'"
					AND mi_card_register.status="0"
					AND mi_card_register.coupon_id!=""
					AND coupon.coup_Birthday!="T"
					AND coupon.coup_Motivation="Stamp"';

	$sql_hbd = 'SELECT DISTINCT coupon.coup_Name, coupon.coup_Status, coupon.coup_CouponID
				FROM mi_card_register
				LEFT JOIN coupon
				ON mi_card_register.coupon_id = coupon.coup_CouponID
				WHERE mi_card_register.card_id="'.$card_CardID.'"
				AND mi_card_register.status="0"
				AND mi_card_register.coupon_id!=""
				AND coupon.coup_Birthday="T"
				AND coupon.coup_Motivation="Stamp"';

	$sql_activity = 'SELECT DISTINCT activity.acti_Name, activity.acti_Status, activity.acti_ActivityID
						FROM mi_card_register
						LEFT JOIN activity
						ON mi_card_register.activity_id = activity.acti_ActivityID
						WHERE mi_card_register.card_id="'.$card_CardID.'"
						AND mi_card_register.status="0"
						AND mi_card_register.activity_id!=""
						AND activity.acti_Motivation="Stamp"';

	$oRes_branch = $oDB->Query($sql_branch);

	$oRes_privilege = $oDB->Query($sql_privilege);
	$check_priv = $oDB->QueryOne($sql_privilege);

	$oRes_coupon = $oDB->Query($sql_coupon);
	$check_coup = $oDB->QueryOne($sql_coupon);

	$oRes_hbd = $oDB->Query($sql_hbd);
	$check_hbd = $oDB->QueryOne($sql_hbd);

	$oRes_activity = $oDB->Query($sql_activity);
	$check_acti = $oDB->QueryOne($sql_activity);





	# PRIVILEGE

	$html = '';

	if ($check_priv) {

		$html = "<div class='form-group'><div class='col-md-12'><div id='parent' class='table-responsive'>
				<table id='myTable' class='table table-bordered' id='branch_and_privilege' style='background-color:white;' >
					<thead>
						<td>Branch \ Privilege</td>";

		# LOOP PRIVILEGE

		$count_privilege = 0;

		while ($axRow_privilege = $oRes_privilege->FetchRow(DBI_ASSOC)) {

			if ($axRow_privilege['priv_Status'] == "Pending") {

				$status_pri = "(Pending)<br>";
			} 
			else {	

				$status_pri = "";

			}

			$stamp_pri = "";

			$sql_stamp_pri 	= 'SELECT 

							most_StampQty

							FROM motivation_stamp 

							WHERE card_CardID = "'.$card_CardID.'"

							AND priv_PrivilegeID = "'.$axRow_privilege['priv_PrivilegeID'].'"

							GROUP BY bran_BrandID';

			$stamp_pri = $oDB->QueryOne($sql_stamp_pri);

			$sql_time_pri 	= 'SELECT 

							most_TimeQty

							FROM motivation_stamp 

							WHERE card_CardID = "'.$card_CardID.'"

							AND priv_PrivilegeID = "'.$axRow_privilege['priv_PrivilegeID'].'"

							GROUP BY bran_BrandID';

			$time_pri = $oDB->QueryOne($sql_time_pri);

			$sql_check 	= 'SELECT 

							status

							FROM mi_card_register 

							WHERE card_id = "'.$card_CardID.'"

							AND privilege_id = "'.$axRow_privilege['priv_PrivilegeID'].'"';

			$check_data = $oDB->Query($sql_check);

			$disabled_input = "disabled";

			$stamp_qty = "";

			$stamp_time = "";

			while ($axRow_check = $check_data->FetchRow(DBI_ASSOC)) {

				if ($axRow_check['status'] == '0') {

					if ($stamp_pri != 0) {

						$disabled_input = "";

						$stamp_qty = "value='".$stamp_pri."' ";

						$stamp_time = "value='".$time_pri."' ";

					} else {

						$disabled_input = "";
					}
				}
			}

			$html .= "<td style='text-align:center'>".$axRow_privilege['priv_Name']." <br>".$status_pri."

						<!-- <span class='form-inline'>

						<input type='text' class='form-control text-md' style='width:50px;height:20px' onkeypress='CheckNum()' maxlength='3' name='time_".$axRow_privilege['priv_PrivilegeID']."' ".$stamp_time.$disabled_input.">

						ต่อ

						จำนวน

						<input type='text' class='form-control text-md' style='width:50px;height:20px' onkeypress='CheckNum()' maxlength='3' name='stamp_".$axRow_privilege['priv_PrivilegeID']."' ".$stamp_qty.$disabled_input.">

						Stamp

						</span><br> -->

						<button type='button' class='btn btn-default btn-sm' id='".$axRow_privilege['priv_PrivilegeID']."' onclick='all_priv(this.id)' style='margin-top:5px'>
							<span class='glyphicon glyphicon-check' aria-hidden='true'></span>
						</button>

						<button type='button' class='btn btn-default btn-sm' id='".$axRow_privilege['priv_PrivilegeID']."' onclick='unall_priv(this.id)' style='margin-top:5px'>
							<span class='glyphicon glyphicon-unchecked' aria-hidden='true'></span>
						</button>

					</td>";

			$privilege_id[$count_privilege]  = $axRow_privilege['priv_PrivilegeID'];

			$count_privilege++;

		}

		$html .= "</thead><tbody>";

		# LOOP BRANCH

		while ($axRow_branch = $oRes_branch->FetchRow(DBI_ASSOC)) {

			$html .= "<tr><td class='td_head'>".$axRow_branch['name']."

						<span style='float:right'>
							<button type='button' class='btn btn-default btn-sm' id='".$axRow_branch['branch_id']."' onclick='all_brnc(this.id)'>
								<span class='glyphicon glyphicon-check' aria-hidden='true'></span>
							</button>
							
							<button type='button' class='btn btn-default btn-sm' id='".$axRow_branch['branch_id']."' onclick='unall_brnc(this.id)'>
								<span class='glyphicon glyphicon-unchecked' aria-hidden='true'></span>
							</button>
						</span>

						</td>";

			for ($i=0; $i < $count_privilege; $i++) {

				$sql_stamp_del = 'SELECT 

									most_Deleted
												
									FROM motivation_stamp
												
									WHERE card_CardID = "'.$card_CardID.'"
												
									AND brnc_BranchID = '.$axRow_branch['branch_id'].'
												
									AND priv_PrivilegeID = '.$privilege_id[$i].'';

				$stamp_del = $oDB->QueryOne($sql_stamp_del);


				$html .= "<td style='text-align:center'>
								
							<input type='checkbox' name='check_".$axRow_branch['branch_id']."_".$privilege_id[$i]."' value='1' class='p".$privilege_id[$i]." bp".$axRow_branch['branch_id']."'";

				if ($stamp_del=='') {	

					$html .= " checked='checked'";	
				}
				
				$html .= "></td>";
			}
				
			$html .= "</tr>";
		}

		$html .= "</tbody></table></div></div></div>";
	}




	## COUPON ##

	$html_coupon = '';

	if ($check_coup) {

		$html_coupon = "<div class='form-group'><div class='col-md-12'><div id='parent' class='table-responsive'>
				<table id='myTable' class='table table-bordered' id='branch_and_privilege' style='background-color:white;' >
					<thead>
						<td>Branch \ Coupon</td>";

		# LOOP COUPON

		$count_coupon = 0;

		while ($axRow_coupon = $oRes_coupon->FetchRow(DBI_ASSOC)) {

			if ($axRow_coupon['coup_Status'] == "Pending") {

				$status_cou = "(Pending)<br>";
			} 
			else {	

				$status_cou = "";

			}

			$stamp_cou = "";

			$sql_stamp_cou 	= 'SELECT 

							most_StampQty

							FROM motivation_stamp 

							WHERE card_CardID = "'.$card_CardID.'"

							AND coup_CouponID = "'.$axRow_coupon['coup_CouponID'].'"

							GROUP BY bran_BrandID';

			$stamp_cou = $oDB->QueryOne($sql_stamp_cou);

			$sql_time_cou 	= 'SELECT 

							most_TimeQty

							FROM motivation_stamp 

							WHERE card_CardID = "'.$card_CardID.'"

							AND coup_CouponID = "'.$axRow_coupon['coup_CouponID'].'"

							GROUP BY bran_BrandID';

			$time_cou = $oDB->QueryOne($sql_time_cou);

			$sql_check 	= 'SELECT 

							status

							FROM mi_card_register 

							WHERE card_id = "'.$card_CardID.'"

							AND coupon_id = "'.$axRow_coupon['coup_CouponID'].'"';

			$check_data = $oDB->Query($sql_check);

			$disabled_input = "disabled";

			$stamp_qty = "";

			$stamp_time = "";

			while ($axRow_check = $check_data->FetchRow(DBI_ASSOC)) {

				if ($axRow_check['status'] == '0') {

					if ($stamp_cou != 0) {

						$disabled_input = "";

						$stamp_qty = "value='".$stamp_cou."' ";

						$stamp_time = "value='".$time_cou."' ";

					} else {

						$disabled_input = "";
					}
				}
			}

			$html_coupon .= "<td style='text-align:center'>".$axRow_coupon['coup_Name']." <br>".$status_cou."

						<!-- <span class='form-inline'>

						<input type='text' class='form-control text-md' style='width:50px;height:20px' onkeypress='CheckNum()' maxlength='3' name='time_c".$axRow_coupon['coup_CouponID']."' ".$stamp_time.$disabled_input.">

						ต่อ
						จำนวน

						<input type='text' class='form-control text-md' style='width:50px;height:20px' onkeypress='CheckNum()' maxlength='3' name='stamp_c".$axRow_coupon['coup_CouponID']."' ".$stamp_qty.$disabled_input.">

						Stamp

						</span><br> -->

						<button type='button' class='btn btn-default btn-sm' id='".$axRow_coupon['coup_CouponID']."' onclick='all_coup(this.id)' style='margin-top:5px'>
							<span class='glyphicon glyphicon-check' aria-hidden='true'></span>
						</button>

						<button type='button' class='btn btn-default btn-sm' id='".$axRow_coupon['coup_CouponID']."' onclick='unall_coup(this.id)' style='margin-top:5px'>
							<span class='glyphicon glyphicon-unchecked' aria-hidden='true'></span>
						</button>

					</td>";

			$coupon_id[$count_coupon]  = $axRow_coupon['coup_CouponID'];

			$count_coupon++;

		}

		$html_coupon .= "</thead><tbody>";

		# LOOP BRANCH

		$oRes_branch = $oDB->Query($sql_branch);

		while ($axRow_branch = $oRes_branch->FetchRow(DBI_ASSOC)) {

			$html_coupon .= "<tr><td class='td_head'>".$axRow_branch['name']."

						<span style='float:right'>
							<button type='button' class='btn btn-default btn-sm' id='".$axRow_branch['branch_id']."' onclick='all_brnc_coup(this.id)'>
								<span class='glyphicon glyphicon-check' aria-hidden='true'></span>
							</button>
							
							<button type='button' class='btn btn-default btn-sm' id='".$axRow_branch['branch_id']."' onclick='unall_brnc_coup(this.id)'>
								<span class='glyphicon glyphicon-unchecked' aria-hidden='true'></span>
							</button>
						</span>

						</td>";

			for ($i=0; $i < $count_coupon; $i++) {

				$sql_stamp_del = 'SELECT 

									most_Deleted
												
									FROM motivation_stamp
												
									WHERE card_CardID = "'.$card_CardID.'"
												
									AND brnc_BranchID = '.$axRow_branch['branch_id'].'
												
									AND coup_CouponID = '.$coupon_id[$i].'';

				$stamp_del = $oDB->QueryOne($sql_stamp_del);


				$html_coupon .= "<td style='text-align:center' >
								
								<input type='checkbox' name='check_c".$axRow_branch['branch_id']."_".$coupon_id[$i]."' value='1' class='c".$coupon_id[$i]." bc".$axRow_branch['branch_id']."'";

				if ($stamp_del=='') {	

					$html_coupon .= " checked='checked'";	
				}
				
				$html_coupon .= "></td>";
			}
				
			$html_coupon .= "</tr>";
		}

		$html_coupon .= "</tbody></table></div></div></div>";
	}




	## BIRTHDAY COUPON ##

	$html_hbd = '';

	if ($check_hbd) {

		$html_hbd = "<div class='form-group'><div class='col-md-12'><div id='parent' class='table-responsive'>
				<table id='myTable' class='table table-bordered' id='branch_and_privilege' style='background-color:white;' >
					<thead>
						<td>Branch \ Birthday Coupon</td>";

		# LOOP COUPON

		$count_hbd = 0;

		while ($axRow_hbd = $oRes_hbd->FetchRow(DBI_ASSOC)) {

			if ($axRow_hbd['coup_Status'] == "Pending") {

				$status_hbd = "(Pending)<br>";
			} 
			else {	

				$status_hbd = "";

			}

			$stamp_hbd = "";

			$sql_stamp_hbd 	= 'SELECT 

							most_StampQty

							FROM motivation_stamp 

							WHERE card_CardID = "'.$card_CardID.'"

							AND coup_CouponID = "'.$axRow_hbd['coup_CouponID'].'"

							GROUP BY bran_BrandID';

			$stamp_hbd = $oDB->QueryOne($sql_stamp_hbd);

			$sql_time_hbd 	= 'SELECT 

							most_TimeQty

							FROM motivation_stamp 

							WHERE card_CardID = "'.$card_CardID.'"

							AND coup_CouponID = "'.$axRow_coupon['coup_CouponID'].'"

							GROUP BY bran_BrandID';

			$time_hbd = $oDB->QueryOne($sql_time_hbd);

			$sql_check 	= 'SELECT 

							status

							FROM mi_card_register 

							WHERE card_id = "'.$card_CardID.'"

							AND coupon_id = "'.$axRow_coupon['coup_CouponID'].'"';

			$check_data = $oDB->Query($sql_check);

			$disabled_input = "disabled";

			$stamp_qty = "";

			$stamp_time = "";

			while ($axRow_check = $check_data->FetchRow(DBI_ASSOC)) {

				if ($axRow_check['status'] == '0') {

					if ($stamp_hbd != 0) {

						$disabled_input = "";

						$stamp_qty = "value='".$stamp_hbd."' ";

						$stamp_time = "value='".$time_hbd."' ";

					} else {

						$disabled_input = "";
					}
				}
			}

			$html_hbd .= "<td style='text-align:center'>".$axRow_hbd['coup_Name']." <br>".$status_hbd."

						<!-- <span class='form-inline'>

						<input type='text' class='form-control text-md' style='width:50px;height:20px' onkeypress='CheckNum()' maxlength='3' name='time_h".$axRow_hbd['coup_CouponID']."' ".$stamp_time.$disabled_input.">

						ต่อ
						จำนวน

						<input type='text' class='form-control text-md' style='width:50px;height:20px' onkeypress='CheckNum()' maxlength='3' name='stamp_h".$axRow_hbd['coup_CouponID']."' ".$stamp_qty.$disabled_input.">

						Stamp

						</span><br> -->

						<button type='button' class='btn btn-default btn-sm' id='".$axRow_hbd['coup_CouponID']."' onclick='all_hbd(this.id)' style='margin-top:5px'>
							<span class='glyphicon glyphicon-check' aria-hidden='true'></span>
						</button>

						<button type='button' class='btn btn-default btn-sm' id='".$axRow_hbd['coup_CouponID']."' onclick='unall_hbd(this.id)' style='margin-top:5px'>
							<span class='glyphicon glyphicon-unchecked' aria-hidden='true'></span>
						</button>

					</td>";

			$hbd_id[$count_hbd]  = $axRow_hbd['coup_CouponID'];

			$count_hbd++;

		}

		$html_hbd .= "</thead><tbody>";

		# LOOP BRANCH

		$oRes_branch = $oDB->Query($sql_branch);

		while ($axRow_branch = $oRes_branch->FetchRow(DBI_ASSOC)) {

			$html_hbd .= "<tr><td class='td_head'>".$axRow_branch['name']."

						<span style='float:right'>
							<button type='button' class='btn btn-default btn-sm' id='".$axRow_branch['branch_id']."' onclick='all_brnc_hbd(this.id)'>
								<span class='glyphicon glyphicon-check' aria-hidden='true'></span>
							</button>
							
							<button type='button' class='btn btn-default btn-sm' id='".$axRow_branch['branch_id']."' onclick='unall_brnc_hbd(this.id)'>
								<span class='glyphicon glyphicon-unchecked' aria-hidden='true'></span>
							</button>
						</span>

						</td>";

			for ($i=0; $i < $count_hbd; $i++) {

				$sql_stamp_del = 'SELECT 

									most_Deleted
												
									FROM motivation_stamp
												
									WHERE card_CardID = "'.$card_CardID.'"
												
									AND brnc_BranchID = '.$axRow_branch['branch_id'].'
												
									AND coup_CouponID = '.$coupon_id[$i].'';

				$stamp_del = $oDB->QueryOne($sql_stamp_del);


				$html_hbd .= "<td style='text-align:center' >
								
								<input type='checkbox' name='check_h".$axRow_branch['branch_id']."_".$hbd_id[$i]."' value='1' class='h".$hbd_id[$i]." bh".$axRow_branch['branch_id']."'";

				if ($stamp_del=='') {	

					$html_hbd .= " checked='checked'";	
				}
				
				$html_hbd .= "></td>";
			}
				
			$html_hbd .= "</tr>";
		}

		$html_hbd .= "</tbody></table></div></div></div>";
	}




	## ACTIVITY ##

	$html_activity = '';

	if ($check_acti) {

		$html_activity = "<div class='form-group'><div class='col-md-12'><div id='parent' class='table-responsive'>
					<table id='myTable' class='table table-bordered' id='branch_and_privilege' style='background-color:white;' >
					<thead>
						<td>Branch \ Activity</td>";

		# LOOP ACTIVITY

		$count_activity = 0;

		while ($axRow_activity = $oRes_activity->FetchRow(DBI_ASSOC)) {

			if ($axRow_activity['acti_Status'] == "Pending") {

				$status_act = "(Pending)<br>";
			} 
			else {	

				$status_act = "";

			}

			$stamp_cou = "";

			$sql_stamp_act 	= 'SELECT 

							most_StampQty

							FROM motivation_stamp 

							WHERE card_CardID = "'.$card_CardID.'"

							AND acti_activityID = "'.$axRow_activity['acti_ActivityID'].'"

							GROUP BY bran_BrandID';

			$stamp_act = $oDB->QueryOne($sql_stamp_act);

			$sql_time_act 	= 'SELECT 

							most_TimeQty

							FROM motivation_stamp 

							WHERE card_CardID = "'.$card_CardID.'"

							AND acti_ActivityID = "'.$axRow_activity['acti_ActivityID'].'"

							GROUP BY bran_BrandID';

			$time_act = $oDB->QueryOne($sql_time_act);

			$sql_check 	= 'SELECT 

							status

							FROM mi_card_register 

							WHERE card_id = "'.$card_CardID.'"

							AND activity_id = "'.$axRow_activity['acti_ActivityID'].'"';

			$check_data = $oDB->Query($sql_check);

			$disabled_input = "disabled";

			$stamp_qty = "";

			$stamp_time = "";

			while ($axRow_check = $check_data->FetchRow(DBI_ASSOC)) {

				if ($axRow_check['status'] == '0') {

					if ($stamp_act != 0) {

						$disabled_input = "";

						$stamp_qty = "value='".$stamp_act."' ";

						$stamp_time = "value='".$time_act."' ";

					} else {

						$disabled_input = "";
					}
				}
			}

			$html_activity .= "<td style='text-align:center'>".$axRow_activity['acti_Name'].$birthday." <br>".$status_cou."

						<!-- <span class='form-inline'>

						<input type='text' class='form-control text-md' style='width:50px;height:20px' onkeypress='CheckNum()' maxlength='3' name='time_a".$axRow_activity['acti_ActivityID']."' ".$stamp_time.$disabled_input.">

						ต่อ
						จำนวน

						<input type='text' class='form-control text-md' style='width:50px;height:20px' onkeypress='CheckNum()' maxlength='3' name='stamp_a".$axRow_activity['acti_ActivityID']."' ".$stamp_qty.$disabled_input.">

						Stamp


						</span><br> -->

						<button type='button' class='btn btn-default btn-sm' id='".$axRow_activity['acti_ActivityID']."' onclick='all_acti(this.id)' style='margin-top:5px'>
							<span class='glyphicon glyphicon-check' aria-hidden='true'></span>
						</button>

						<button type='button' class='btn btn-default btn-sm' id='".$axRow_activity['acti_ActivityID']."' onclick='unall_acti(this.id)' style='margin-top:5px'>
							<span class='glyphicon glyphicon-unchecked' aria-hidden='true'></span>
						</button>

					</td>";

			$activity_id[$count_activity]  = $axRow_activity['acti_ActivityID'];

			$count_activity++;

		}

		$html_activity .= "</thead><tbody>";

		# LOOP BRANCH

		$oRes_branch = $oDB->Query($sql_branch);

		while ($axRow_branch = $oRes_branch->FetchRow(DBI_ASSOC)) {

			$html_activity .= "<tr><td class='td_head'>".$axRow_branch['name']."

						<span style='float:right'>
							<button type='button' class='btn btn-default btn-sm' id='".$axRow_branch['branch_id']."' onclick='all_brnc_acti(this.id)'>
								<span class='glyphicon glyphicon-check' aria-hidden='true'></span>
							</button>
							
							<button type='button' class='btn btn-default btn-sm' id='".$axRow_branch['branch_id']."' onclick='unall_brnc_acti(this.id)'>
								<span class='glyphicon glyphicon-unchecked' aria-hidden='true'></span>
							</button>
						</span>

						</td>";

			for ($i=0; $i < $count_activity; $i++) {

				$sql_stamp_del = 'SELECT 

									most_Deleted
												
									FROM motivation_stamp
												
									WHERE card_CardID = "'.$card_CardID.'"
												
									AND brnc_BranchID = '.$axRow_branch['branch_id'].'
												
									AND acti_ActivityID = '.$activity_id[$i].'';

				$stamp_del = $oDB->QueryOne($sql_stamp_del);


				$html_activity .= "<td style='text-align:center' >
								
								<input type='checkbox' name='check_a".$axRow_branch['branch_id']."_".$activity_id[$i]."' value='1' class='a".$activity_id[$i]." ba".$axRow_branch['branch_id']."'";

				if ($stamp_del=='') {	

					$html_activity .= " checked='checked'";	
				}
				
				$html_activity .= "></td>";
			}
				
			$html_activity .= "</tr>";

		}
				
		$html_activity .= "</tbody></table></div></div></div>";
	}



	echo $html.'<br>'.$html_coupon.'<br>'.$html_hbd.'<br>'.$html_activity;

	exit;		

}


else if($TASK =='Get_RegisterForm'){

	$card_CardID = $_REQUEST['card_CardID'];
	$platform = $_REQUEST['platform'];

	if ($card_CardID) {

		$sql = "SELECT image,image_newupload,member_fee,period_type,period_type_other,flag_status,date_expired,path_image FROM mi_card WHERE card_id='".$card_CardID."'";
		$oRes = $oDB->Query($sql);
		$axRow = $oRes->FetchRow(DBI_ASSOC);

		# CARD IMAGE

		if ($axRow['image']) {
			
			$card_img ='<img src="../../upload/'.$axRow['path_image'].$axRow['image'].'" width="200" class="img-rounded image_border">';
		
		} else if ($axRow['image_newupload']) {
			
			$card_img ='<img src="../../upload/'.$axRow['path_image'].$axRow['image_newupload'].'" width="200" class="img-rounded image_border">';
		
		} else {
			
			$card_img = '<img src="../../images/card_privilege.jpg" width="200" class="img-rounded image_border">';
		}

		# STATUS

		if ($axRow['flag_status']==1) {	$status = 'Active';	}
		else {	$status = 'Pending';	}

		# PERIOD TYPE

		if ($axRow['period_type'] == '1') { 

			$axRow['period_type'] = 'Expired Date<br>('.DateOnly($axRow['date_expired']).')';	

		} else if ($axRow['period_type'] == '2') { 

			$axRow['period_type'] = $axRow['period_type_other'].' Months';	

		} else if ($axRow['period_type'] == '3') { 

			$axRow['period_type'] = $axRow['period_type_other'].' Years';	

		} else if ($axRow['period_type'] == '4') { 

			$axRow['period_type'] = 'Member Life Time';	
		}

		# PLATFORM

		if ($platform=='new') {	$type = 'New Member';	}
		else {	$type = 'Existing Membere';	}

		$html = '<br><table style="width:75%" class="myPopup">
					<tr>
						<td width="220px" style="text-align:center" rowspan="4">'.$card_img.'</td>
						<td style="text-align:right">Member Type</td>
						<td width="20px" style="text-align:center">:</td>
						<td>'.$type.'</td>
					</tr>
					<tr>
						<td style="text-align:right">Status</td>
						<td style="text-align:center">:</td>
						<td>'.$status.'</td>
					</tr>
					<tr>
						<td style="text-align:right">Member Fee</td>
						<td style="text-align:center">:</td>
						<td>'.number_format($axRow['member_fee'],2).'</td>
					</tr>
					<tr>
						<td style="text-align:right">Period Type</td>
						<td style="text-align:center">:</td>
						<td>'.$axRow['period_type'].'</td>
					</tr>
				</table>';

		$html .= '<br><br><table style="width:95%" class="table table-striped table-bordered myPopup">
					<thead><tr class="th_table">
						<th style="text-align:center"><b>Target Member Type</b></th>
						<th width="15%" style="text-align:center"><b>Fill In</b></th>
						<th width="15%" style="text-align:center"><b>Require</b></th>
						<th colspan="2" width="40%" style="text-align:center"><b>Target</b></th>
					</tr></thead>
					<tbody>';


		$topic = array("Profile", "Home Address", "Work Address", "Work", "Contact");

		for ($i=0; $i <5 ; $i++) { 

			$sql_form = 'SELECT

							a.*,
							b.mafi_NameEn,
							b.mafi_MasterFieldID

							FROM register_form AS a
							LEFT JOIN master_field AS b
							ON b.mafi_MasterFieldID = a.mafi_MasterFieldID

							WHERE b.mafi_Position = "'.$topic[$i].'"
							AND b.mafi_Deleted != "T"
							AND a.card_CardID = "'.$card_CardID.'"
							AND a.refo_FillIn = "Y"

							ORDER BY b.mafi_FieldOrder';

			$oRes_form = $oDB->Query($sql_form);
	 			
	 		$check_form = $oDB->QueryOne($sql_form);

			if ($check_form) {

				$html .= '<tr>
							<td colspan="5" style="text-align:center;background-color:#F2F2F2"><b>'.$topic[$i].'</b></td>
							</tr>';

				while ($axRow = $oRes_form->FetchRow(DBI_ASSOC)){

					$html .= '<tr>
	                                <td style="text-align:center"><b>'.$axRow['mafi_NameEn'].'</b></td>
	                                <td style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>';

	                if ($axRow['refo_Require']=="Y") {

						$html .= '<td style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>';

	                } else {

						$html .= '<td></td>';
	                }

	                if ($axRow['refo_Target']!="") {

	                	if ($axRow['mafi_MasterFieldID']=="6") {

	                		$token = strtok($axRow['refo_Target'] , ",");

							$target_data = array();

							$i = 0;

							while ($token !== false) {
						    			
						    	$target_data[$i] =  $token;
						    	$token = strtok(",");
						    	$i++;
							}

							$arrlength = count($target_data);

							$target = "";

							for($x=0; $x<$arrlength; $x++) {

								if ($x == 1) { $target .= ' - '; }

								$sql_target = 'SELECT mata_NameEn
												FROM master_target
												WHERE mata_MasterTargetID="'.$target_data[$x].'"';
		 			
		 						$target .= $oDB->QueryOne($sql_target);
							}

							$target .= ' Age Restriction';

	                	} else {

							$sql_target = 'SELECT mata_NameEn
											FROM master_target
											WHERE mata_MasterTargetID="'.$axRow['refo_Target'].'"';
	 			
	 						$target = $oDB->QueryOne($sql_target);
	                	}

						$html .= '<td width="15%" style="text-align:center"><span class="glyphicon glyphicon-ok"></span>
									</td>
									<td>'.$target.'</td>';

	                } else {

						$html .= '<td width="15%"></td>
									<td></td>';
	                }

	                $html .= '</tr>';
				}

				// if ($check_form && $topic[$i] == 'Contact') {

				// 	$html .= '<tr>
	   //                          <td style="text-align:center" width="50%"><b>Mobile</b></td>
	   //                          <td style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>
	   //                          <td style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>
	   //                          <td width="15%"></td>
				// 				<td></td>
				// 			</tr>
				// 			<tr>	
	   //                          <td style="text-align:center" width="50%"><b>Email</b></td>
	   //                          <td style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>
	   //                          <td style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>
	   //                          <td width="15%"></td>
				// 				<td></td>
				// 			</tr>';

				// }

			} 

			// else if (!$check_form && $topic[$i] == 'Contact') {

			// 	$html .= '<tr>
			// 				<td colspan="5" style="text-align:center;background-color:#F2F2F2"><b>Contact</b></td>
			// 				</tr>
			// 				<tr>
	  //                           <td style="text-align:center" width="50%"><b>Mobile</b></td>
	  //                           <td style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>
	  //                           <td style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>
	  //                           <td width="15%"></td>
			// 					<td></td>
			// 				</tr>
			// 				<tr>
	  //                           <td style="text-align:center" width="50%"><b>Email</b></td>
	  //                           <td style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>
	  //                           <td style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>
	  //                           <td width="15%"></td>
			// 					<td></td>
			// 				</tr>';
			// }
		}

		# CUSTOM

		$sql_custom = 'SELECT

						a.*,
						b.cufi_Name

						FROM custom_form AS a
						LEFT JOIN custom_field AS b
						ON b.cufi_CustomFieldID = a.cufi_CustomFieldID

						WHERE b.cufi_Deleted != "T"
						AND a.card_CardID = "'.$card_CardID.'"
						AND a.cufo_FillIn = "Y"

						ORDER BY b.cufi_FieldOrder';

		$oRes_custom = $oDB->Query($sql_custom);
 			
 		$check_custom = $oDB->QueryOne($sql_custom);

		if ($check_custom) {

			$html .= '<tr>
						<td colspan="5" style="text-align:center;background-color:#F2F2F2"><b>Custom</b></td>
						</tr>';

			while ($axRow_custom = $oRes_custom->FetchRow(DBI_ASSOC)){

				$html .= '<tr>
	                        <td style="text-align:center"><b>'.$axRow_custom['cufi_Name'].'</b></td>
	                        <td style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>';

	            if ($axRow_custom['cufo_FillIn']=="Y") {

					$html .= '<td style="text-align:center"><span class="glyphicon glyphicon-ok"></span></td>';

	            } else {

					$html .= '<td></td>';
	            }

	            if ($axRow_custom['cufo_Target']!="") {

					$sql_target = 'SELECT clva_Name
									FROM custom_list_value
									WHERE clva_CustomListValueID="'.$axRow_custom['cufo_Target'].'"';
 			
 					$target = $oDB->QueryOne($sql_target);

					$html .= '<td style="text-align:center" width="15%"><span class="glyphicon glyphicon-ok"></span></td>
									<td>'.$target.'</td>';

	            } else {

					$html .= '<td></td>
								<td></td>';
	            }

	            $html .= '</tr>';
	        }
	    }

		$html .= '</tbody></table>
					
					<a href="upload_member.php?act=Template&id='.$card_CardID.'&platform='.$platform.'">
					<button type="button" class="btn btn-primary btn-sm">Download Template</button></a>';
	
	} else {

		$html = '';
	}

	echo $html;

	exit;


}


else if($TASK =='Get_MemberSend'){

	$card_CardID = $_REQUEST['card_CardID'];

	if ($card_CardID) {

		$sql = "SELECT member_brand_id,email,firstname,lastname,mobile,send_sms,send_email
				FROM mb_member_brand 
				WHERE card_id='".$card_CardID."'
				AND flag_del!='T' AND member_register_id='0'";

		$oRes = $oDB->Query($sql);
 			
 		$check = $oDB->QueryOne($sql);


		$html = '<br><center><table style="width:90%" class="table table-striped table-bordered myPopup">
					<thead><tr class="th_table">
						<th colspan="4" style="text-align:center"><b>Member Data</b></th>
						<th colspan="4" style="text-align:center"><b>Send</b></th>
					</tr>
					<tr class="th_table">
						<th rowspan="2" style="text-align:center"><b>No.</b></th>
						<th rowspan="2" style="text-align:center"><b>Name</b></th>
						<th rowspan="2" style="text-align:center"><b>Email</b></th>
						<th rowspan="2" style="text-align:center"><b>Mobile</b></th>
						<th colspan="2" style="text-align:center">
							<button type="button" class="btn btn-default btn-sm" onclick="all_email()">
								<span class="glyphicon glyphicon-check" aria-hidden="true"></span>
							</button>

							<button type="button" class="btn btn-default btn-sm" onclick="unall_email()">
								<span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span></th>
						<th colspan="2" style="text-align:center">
							<button type="button" class="btn btn-default btn-sm" onclick="all_sms()">
								<span class="glyphicon glyphicon-check" aria-hidden="true"></span>
							</button>

							<button type="button" class="btn btn-default btn-sm" onclick="unall_sms()">
								<span class="glyphicon glyphicon-unchecked" aria-hidden="true"></span></th>
					</tr>
					<tr class="th_table">
						<th style="text-align:center"><b>Email</b></th>
						<th style="text-align:center;padding-bottom:13px"><span class="glyphicon glyphicon-time"></span></th>
						<th style="text-align:center"><b>SMS</b></th>
						<th style="text-align:center;padding-bottom:13px"><span class="glyphicon glyphicon-time"></span></th>
					</tr></thead>
					<tbody>';

		if ($check) {

			$i = 0;

			while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

				$i++;

				$html .= '<tr>
							<td>'.$i.'</td>
							<td>'.$axRow['firstname'].' '.$axRow['lastname'].'</td>
							<td>'.$axRow['email'].'</td>
							<td>'.$axRow['mobile'].'</td>
							<td style="text-align:center"><input type="checkbox" class="email" name="email_id[]" value="'.$axRow['member_brand_id'].'"></td>
							<td style="text-align:center">'.$axRow['send_email'].'</td>
							<td style="text-align:center"><input type="checkbox" class="sms" name="sms_id[]" value="'.$axRow['member_brand_id'].'"></td>
							<td style="text-align:center">'.$axRow['send_sms'].'</td>
						</tr>';
			}

		} else {

			$html .= '<tr>
						<td colspan="8" style="text-align:center;"><b>No Member Data</b></td>
						</tr>';
		}

		$html .= '</tbody></table></center>';
	
	} else {

		$html = '';
	}

	echo $html;

	exit;


}


else if($TASK =='Get_IconImage'){

	$collection_id = $_REQUEST['mosh_CollectionTypeID'];

	$sql ="SELECT coty_Image FROM collection_type WHERE coty_CollectionTypeID = ".$collection_id;

	$icon = $oDB->QueryOne($sql);

	$html = "";

	if($icon){

		$html ='<img src="../../upload/collection_upload/'.$icon.'" width="30px" height="30px" >';	

	}

	echo $html;

	exit;


}


else if($TASK =='Get_CollectionImage'){

	$plan = $_REQUEST['plan_id'];

	$html = "";

	if ($plan != '') {

		$type = substr($plan,0,1);
		
		$id_plan = substr($plan,1);

		if ($id_plan != 0) {

			if ($type == 'p') {

				$sql = "SELECT coty_Image FROM collection_type WHERE coty_CollectionTypeID = 3";

			} else {

				$plan = "SELECT mops_CollectionTypeID FROM motivation_plan_stamp WHERE mops_MotivationStampID=".$id_plan;
				$icon = $oDB->QueryOne($plan);

				$sql = "SELECT coty_Image FROM collection_type WHERE coty_CollectionTypeID = ".$icon;
			}

			$icon = $oDB->QueryOne($sql);

			if($icon){

				$html ='<img src="../../upload/collection_upload/'.$icon.'" width="30px" height="30px" >';	
			}
		}
	}

	echo $html;

	exit;

}


else if($TASK =='Get_RewardImage'){

	$reward_id = $_REQUEST['rewa_RewardID'];

	$sql ="SELECT rewa_Image,rewa_Type,card_CardID,rewa_ImagePath FROM reward WHERE rewa_RewardID = ".$reward_id;

	$oRes = $oDB->Query($sql);

	$html = "";

	if($oRes){

		while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

			if ($axRow['rewa_Type'] == 'Card') { 
				$sql ="SELECT image FROM mi_card WHERE card_id = ".$axRow['card_CardID'];
				$image = $oDB->QueryOne($sql);
				$sql ="SELECT path_image FROM mi_card WHERE card_id = ".$axRow['card_CardID'];
				$path_image = $oDB->QueryOne($sql);
				$path_upload = '../../upload/'.$path_image; 
				$class = 'img-rounded';

			} else {  

				$path_upload = '../../upload/'.$axRow['rewa_ImagePath'];  
				$image = $axRow["rewa_Image"];
				$class = '';
				$script = '';
			}

			$html ='
					<div class="adj_row">
					<label class="lable-form">Reward Category</label>
					<label>'.$axRow["rewa_Type"].'</label>
					</div>

					<div class="adj_row">
					<label class="lable-form">Reward Image</label>
					<img src="'.$path_upload.$image.'" height="150px" class="'.$class.'">
					</div>';	

		}
	}

	echo $html;

	exit;


}


else if($TASK =='Get_RewardName'){

	$bran_BrandID = $_REQUEST['bran_BrandID'];
	$rewa_RewardID = $_REQUEST['rewa_RewardID'];

	$option ='';
	$option .='<option value="">Please Select ..</option>';

	$sql_acti ="SELECT rewa_RewardID,rewa_Name FROM reward WHERE rewa_Type='Activity' AND bran_BrandID=".$bran_BrandID;

	$check_acti ="SELECT rewa_RewardID FROM reward WHERE rewa_Type='Activity' AND rewa_Status='Active' AND bran_BrandID=".$bran_BrandID;

	$oRes_acti = $oDB->Query($sql_acti);
	$check_acti = $oDB->QueryOne($check_acti);

	if ($check_acti) {

		$option .= '<optgroup label="Activity">';

		while ($axRow = $oRes_acti->FetchRow(DBI_ASSOC)){

			if ($rewa_RewardID == $axRow['rewa_RewardID']) { $select = 'selected="selected"'; }
			else { $select = ''; }

			$option .='<option value="'.$axRow['rewa_RewardID'].'" '.$select.'>' .$axRow['rewa_Name']. '</option>';
		}

		$option .= '</optgroup>';
	}

	$sql_card ="SELECT rewa_RewardID,rewa_Name FROM reward WHERE rewa_Type='Card' AND rewa_Status='Active' AND bran_BrandID=".$bran_BrandID;

	$check_card ="SELECT rewa_RewardID FROM reward WHERE rewa_Type='Card' AND rewa_Status='Active' AND bran_BrandID=".$bran_BrandID;

	$oRes_card = $oDB->Query($sql_card);
	$check_card = $oDB->QueryOne($check_card);

	if ($check_card) {

		$option .= '<optgroup label="Card">';

		while ($axRow = $oRes_card->FetchRow(DBI_ASSOC)){

			if ($rewa_RewardID == $axRow['rewa_RewardID']) { $select = 'selected="selected"'; }
			else { $select = ''; }

			$option .='<option value="'.$axRow['rewa_RewardID'].'" onclick="RewardType()" '.$select.'>' .$axRow['rewa_Name']. '</option>';
		}

		$option .= '</optgroup>';
	}

	$sql_coup ="SELECT rewa_RewardID,rewa_Name FROM reward WHERE rewa_Type='Coupon' AND rewa_Status='Active' AND bran_BrandID=".$bran_BrandID;

	$check_coup ="SELECT rewa_RewardID FROM reward WHERE rewa_Type='Coupon' AND rewa_Status='Active' AND bran_BrandID=".$bran_BrandID;

	$oRes_coup = $oDB->Query($sql_coup);
	$check_coup = $oDB->QueryOne($check_coup);

	if ($check_coup) {

		$option .= '<optgroup label="Coupon">';

		while ($axRow = $oRes_coup->FetchRow(DBI_ASSOC)){

			if ($rewa_RewardID == $axRow['rewa_RewardID']) { $select = 'selected="selected"'; }
			else { $select = ''; }

			$option .='<option value="'.$axRow['rewa_RewardID'].'" '.$select.'>' .$axRow['rewa_Name']. '</option>';
		}

		$option .= '</optgroup>';
	}

	$sql_item ="SELECT rewa_RewardID,rewa_Name FROM reward WHERE rewa_Type='Discount' AND rewa_Status='Active' AND bran_BrandID=".$bran_BrandID;

	$check_item ="SELECT rewa_RewardID FROM reward WHERE rewa_Type='Discount' AND rewa_Status='Active' AND bran_BrandID=".$bran_BrandID;

	$oRes_item = $oDB->Query($sql_item);
	$check_item = $oDB->QueryOne($check_item);

	if ($check_item) {

		$option .= '<optgroup label="Discount">';

		while ($axRow = $oRes_item->FetchRow(DBI_ASSOC)){

			if ($rewa_RewardID == $axRow['rewa_RewardID']) { $select = 'selected="selected"'; }
			else { $select = ''; }

			$option .='<option value="'.$axRow['rewa_RewardID'].'" '.$select.'>' .$axRow['rewa_Name']. '</option>';
		}

		$option .= '</optgroup>';
	}

	$sql_item ="SELECT rewa_RewardID,rewa_Name FROM reward WHERE rewa_Type='Item' AND rewa_Status='Active' AND bran_BrandID=".$bran_BrandID;

	$check_item ="SELECT rewa_RewardID FROM reward WHERE rewa_Type='Item' AND rewa_Status='Active' AND bran_BrandID=".$bran_BrandID;

	$oRes_item = $oDB->Query($sql_item);
	$check_item = $oDB->QueryOne($check_item);

	if ($check_item) {

		$option .= '<optgroup label="Item">';

		while ($axRow = $oRes_item->FetchRow(DBI_ASSOC)){

			if ($rewa_RewardID == $axRow['rewa_RewardID']) { $select = 'selected="selected"'; }
			else { $select = ''; }

			$option .='<option value="'.$axRow['rewa_RewardID'].'" '.$select.'>' .$axRow['rewa_Name']. '</option>';
		}

		$option .= '</optgroup>';
	}

	$sql_priv ="SELECT rewa_RewardID,rewa_Name FROM reward WHERE rewa_Type='Privilege' AND rewa_Status='Active' AND bran_BrandID=".$bran_BrandID;

	$check_priv ="SELECT rewa_RewardID FROM reward WHERE rewa_Type='Privilege' AND rewa_Status='Active' AND bran_BrandID=".$bran_BrandID;

	$oRes_priv = $oDB->Query($sql_priv);
	$check_priv = $oDB->QueryOne($check_priv);

	if ($check_priv) {

		$option .= '<optgroup label="Privilege">';

		while ($axRow = $oRes_priv->FetchRow(DBI_ASSOC)){

			if ($rewa_RewardID == $axRow['rewa_RewardID']) { $select = 'selected="selected"'; }
			else { $select = ''; }

			$option .='<option value="'.$axRow['rewa_RewardID'].'" '.$select.'>' .$axRow['rewa_Name']. '</option>';
		}

		$option .= '</optgroup>';
	}


	$html = '<select id="rewa_RewardID" class="form-control text-md" name="rewa_RewardID" onchange="RewardImage();RedeemQty();RedeemAction()" required autofocus>'.$option.'</select>' ;

	echo $html;

	exit;	

}


else if($TASK =='Get_PercentOff'){

	$sale_price = $_REQUEST['sale_price'];
	$original_price = $_REQUEST['original_price'];

	$percent_off = 0;

	if ($original_price != 0) {

		$percent_off = (($original_price-$sale_price)/$original_price)*100;
	}

	echo '<span class="text-rq">('.number_format($percent_off).'% Off)</span>';

	exit;
}


else if($TASK =='Get_StartEndDate'){

	$start_date = substr($_REQUEST['start_date'], 0, 10);
	$end_date = substr($_REQUEST['end_date'], 0, 10);

	$year = "";
	$month = "";
	$day = "";

	$data = "";

	if ($start_date && $end_date) {
		
		$total = (strtotime($end_date) - strtotime($start_date))/(60*60*24);
		
		$html = (strtotime($end_date) - strtotime($start_date))/(60*60*24);

		$total++;

		$html++;

		if ($html >= 365) {
			
			$year = floor($html/365)." Years";
			$html = $html-((floor($html/365))*365);

			if ($html >= 30) {
			
				$month = " ".floor($html/30)." Months";
				$html = $html-((floor($html/30))*30);

				if ($html != 0) {

					$day = " ".$html." Days (Total ".$total." Days)";

				}

			} else {

				$day = " ".$html." Days (Total ".$total." Days)";

			}

			$data = '<label class="lable-form"></label>

           			<b>'.$year.$month.$day.'</b>';

		} else if ($html >= 30) {
			
			$month = floor($html/30)." Months";
			$html = $html-((floor($html/30))*30);

			if ($html < 30 && $html != 0) {

				$day = " ".$html." Days (Total ".$total." Days)";

			}

			$data = '<label class="lable-form"></label>

           			<b>'.$month.$day.'</b>';

		} else {

			$day = $html." Days";

			$data = '<label class="lable-form"></label>

           			<b>'.$day.'</b>';
		}
	
	}

	echo $data;

	exit;

}


else if($TASK =='Get_StartEndDateSpecial'){

	$start_date = substr($_REQUEST['start_date'], 0, 10);
	$end_date = substr($_REQUEST['end_date'], 0, 10);

	$year = "";
	$month = "";
	$day = "";

	$data = "";

	if ($start_date && $end_date) {
		
		$total = (strtotime($end_date) - strtotime($start_date))/(60*60*24);
		
		$html = (strtotime($end_date) - strtotime($start_date))/(60*60*24);

		$total++;

		$html++;

		if ($html >= 365) {
			
			$year = floor($html/365)." Years";
			$html = $html-((floor($html/365))*365);

			if ($html >= 30) {
			
				$month = " ".floor($html/30)." Months";
				$html = $html-((floor($html/30))*30);

				if ($html != 0) {

					$day = " ".$html." Days (Total ".$total." Days)";

				}

			} else {

				$day = " ".$html." Days (Total ".$total." Days)";

			}

			$data = '<label class="lable-form"></label>

           			<b>'.$year.$month.$day.'</b>';

		} else if ($html >= 30) {
			
			$month = floor($html/30)." Months";
			$html = $html-((floor($html/30))*30);

			if ($html < 30 && $html != 0) {

				$day = " ".$html." Days (Total ".$total." Days)";

			}

			$data = '<label class="lable-form"></label>

           			<b>'.$month.$day.'</b>';

		} else {

			$day = $html." Days";

			$data = '<label class="lable-form"></label>

           			<b>'.$day.'</b>';
		}
	
	}

	echo $data;

	exit;	

}


else if($TASK =='Get_RedeemQtyPer'){

	$per_data = $_REQUEST['per_data'];
	$qty_data = $_REQUEST['data'];

	$html = "";
	$data = "";

	$token = strtok($qty_data , ",");

	$qty_data = array();

	$i = 0;

	while ($token !== false) {
    			
    	$qty_data[$i] =  $token;
    	$token = strtok(",");
    	$i++;
	}

	$arrlength = count($qty_data);

	$select='';

	if ($per_data=="Weekly") {
		
		$data = '<label class="lable-form"></label>';

		$select='';

		for($x = 0; $x < $arrlength; $x++) { if($qty_data[$x]=="Sun"){	$select = 'checked="checked"';	}	}

		$data .= '<input type="checkbox" id="QtyPerMemberData" name="QtyPerData[]" '.$select.' value="Sun"> Sun &nbsp; ';

		$select='';

		for($x = 0; $x < $arrlength; $x++) { if($qty_data[$x]=="Mon"){	$select = 'checked="checked"';	}	}

		$data .= '<input type="checkbox" id="QtyPerMemberData" name="QtyPerData[]"'.$select.' value="Mon"> Mon &nbsp; ';

		$select='';

		for($x = 0; $x < $arrlength; $x++) { if($qty_data[$x]=="Tue"){	$select = 'checked="checked"';	}	}

		$data .= '<input type="checkbox" id="QtyPerMemberData" name="QtyPerData[]"'.$select.' value="Tue"> Tue &nbsp; ';

		$select='';

		for($x = 0; $x < $arrlength; $x++) { if($qty_data[$x]=="Wed"){	$select = 'checked="checked"';	}	}

		$data .= '<input type="checkbox" id="QtyPerMemberData" name="QtyPerData[]"'.$select.' value="Wed"> Wed &nbsp; ';

		$select='';

		for($x = 0; $x < $arrlength; $x++) { if($qty_data[$x]=="Thu"){	$select = 'checked="checked"';	}	}

		$data .= '<input type="checkbox" id="QtyPerMemberData" name="QtyPerData[]"'.$select.' value="Thu"> Thu &nbsp; ';

		$select='';

		for($x = 0; $x < $arrlength; $x++) { if($qty_data[$x]=="Fri"){	$select = 'checked="checked"';	}	}

		$data .= '<input type="checkbox" id="QtyPerMemberData" name="QtyPerData[]"'.$select.' value="Fri"> Fri &nbsp; ';

		$select='';

		for($x = 0; $x < $arrlength; $x++) { if($qty_data[$x]=="Sat"){	$select = 'checked="checked"';	}	}

		$data .= '<input type="checkbox" id="QtyPerMemberData" name="QtyPerData[]"'.$select.' value="Sat"> Sat';
	
	} 

	if ($per_data=="Monthly") {

		for ($i=1; $i <=31 ; $i++) {

			$num = $i;

			if (strlen($num)==1) {
				$num = "0".$num;
			}

			$select='';

			for($x = 0; $x < $arrlength; $x++) {

				if($qty_data[$x]==$num){		

					$select = 'checked="checked"';	
				}
			}

			$html .= '<input type="checkbox" name="QtyPerData[]" '.$select.' value="'.$num.'"> '.$num.' &nbsp; '; 

			if ($i==10 || $i==20) {
				$html .= "<br><label class='lable-form'></label>";
			}
		}

		$data = '<label class="lable-form"></label>

            '.$html.'';
	}

	echo $data;

	exit;	
}


else if($TASK =='Get_QtyPer'){

	$per_data = $_REQUEST['per_data'];

	$qty_data = $_REQUEST['data'];

	$html = "";

	$data = "";

	$token = strtok($qty_data , ",");

	$qty_data = array();

	$i = 0;

	while ($token !== false) {
    			
    	$qty_data[$i] =  $token;
    	$token = strtok(",");
    	$i++;

	}

	$arrlength = count($qty_data);

	$select='';

	if ($per_data=="Weekly") {
		
		$data = '<label class="lable-form"></label>';

		$select='';

		for($x = 0; $x < $arrlength; $x++) { if($qty_data[$x]=="Sun"){	$select = 'checked="checked"';	}	}

		$data .= '<input type="checkbox" id="QtyPerMemberData" name="QtyPerData[]" '.$select.' value="Sun"> Sun &nbsp; ';

		$select='';

		for($x = 0; $x < $arrlength; $x++) { if($qty_data[$x]=="Mon"){	$select = 'checked="checked"';	}	}

		$data .= '<input type="checkbox" id="QtyPerMemberData" name="QtyPerData[]"'.$select.' value="Mon"> Mon &nbsp; ';

		$select='';

		for($x = 0; $x < $arrlength; $x++) { if($qty_data[$x]=="Tue"){	$select = 'checked="checked"';	}	}

		$data .= '<input type="checkbox" id="QtyPerMemberData" name="QtyPerData[]"'.$select.' value="Tue"> Tue &nbsp; ';

		$select='';

		for($x = 0; $x < $arrlength; $x++) { if($qty_data[$x]=="Wed"){	$select = 'checked="checked"';	}	}

		$data .= '<input type="checkbox" id="QtyPerMemberData" name="QtyPerData[]"'.$select.' value="Wed"> Wed &nbsp; ';

		$select='';

		for($x = 0; $x < $arrlength; $x++) { if($qty_data[$x]=="Thu"){	$select = 'checked="checked"';	}	}

		$data .= '<input type="checkbox" id="QtyPerMemberData" name="QtyPerData[]"'.$select.' value="Thu"> Thu &nbsp; ';

		$select='';

		for($x = 0; $x < $arrlength; $x++) { if($qty_data[$x]=="Fri"){	$select = 'checked="checked"';	}	}

		$data .= '<input type="checkbox" id="QtyPerMemberData" name="QtyPerData[]"'.$select.' value="Fri"> Fri &nbsp; ';

		$select='';

		for($x = 0; $x < $arrlength; $x++) { if($qty_data[$x]=="Sat"){	$select = 'checked="checked"';	}	}

		$data .= '<input type="checkbox" id="QtyPerMemberData" name="QtyPerData[]"'.$select.' value="Sat"> Sat';
	
	} 

	if ($per_data=="Monthly") {

		for ($i=1; $i <=31 ; $i++) {

			$num = $i;

			if (strlen($num)==1) {
				$num = "0".$num;
			}

			$select='';

			for($x = 0; $x < $arrlength; $x++) {

				if($qty_data[$x]==$num){		

					$select = 'checked="checked"';	

				}

			}

			$html .= '<input type="checkbox" name="QtyPerData[]" '.$select.' value="'.$num.'"> '.$num.' &nbsp; '; 

			if ($i==10 || $i==20) {
				$html .= "<br><label class='lable-form'></label>";
			}
		}

		$data = '<label class="lable-form"></label>

            '.$html.'';

	}

	echo $data;

	exit;	

}


else if($TASK =='Get_QtyPerMember'){

	$per_data = $_REQUEST['per_data'];

	$qty_data = $_REQUEST['data'];

	$html = "";

	$data = "";

	$token = strtok($qty_data , ",");

	$qty_data = array();

	$i = 0;

	while ($token !== false) {
    			
    	$qty_data[$i] =  $token;
    	$token = strtok(",");
    	$i++;

	}

	$arrlength = count($qty_data);

	if ($per_data=="Weekly") {
		
		$data = '<label class="lable-form"></label>';

		$select='';

		for($x = 0; $x < $arrlength; $x++) { if($qty_data[$x]=="Sun"){	$select = 'checked="checked"';	}	}

		$data .= '<input type="checkbox" id="QtyPerMemberData" name="QtyPerMemberData[]" '.$select.' value="Sun"> Sun &nbsp; ';

		$select='';

		for($x = 0; $x < $arrlength; $x++) { if($qty_data[$x]=="Mon"){	$select = 'checked="checked"';	}	}

		$data .= '<input type="checkbox" id="QtyPerMemberData" name="QtyPerMemberData[]"'.$select.' value="Mon"> Mon &nbsp; ';

		$select='';

		for($x = 0; $x < $arrlength; $x++) { if($qty_data[$x]=="Tue"){	$select = 'checked="checked"';	}	}

		$data .= '<input type="checkbox" id="QtyPerMemberData" name="QtyPerMemberData[]"'.$select.' value="Tue"> Tue &nbsp; ';

		$select='';

		for($x = 0; $x < $arrlength; $x++) { if($qty_data[$x]=="Wed"){	$select = 'checked="checked"';	}	}

		$data .= '<input type="checkbox" id="QtyPerMemberData" name="QtyPerMemberData[]"'.$select.' value="Wed"> Wed &nbsp; ';

		$select='';

		for($x = 0; $x < $arrlength; $x++) { if($qty_data[$x]=="Thu"){	$select = 'checked="checked"';	}	}

		$data .= '<input type="checkbox" id="QtyPerMemberData" name="QtyPerMemberData[]"'.$select.' value="Thu"> Thu &nbsp; ';

		$select='';

		for($x = 0; $x < $arrlength; $x++) { if($qty_data[$x]=="Fri"){	$select = 'checked="checked"';	}	}

		$data .= '<input type="checkbox" id="QtyPerMemberData" name="QtyPerMemberData[]"'.$select.' value="Fri"> Fri &nbsp; ';

		$select='';

		for($x = 0; $x < $arrlength; $x++) { if($qty_data[$x]=="Sat"){	$select = 'checked="checked"';	}	}

		$data .= '<input type="checkbox" id="QtyPerMemberData" name="QtyPerMemberData[]"'.$select.' value="Sat"> Sat';
	
	} 

	if ($per_data=="Monthly") {

		for ($i=1; $i <=31 ; $i++) {

			$num = $i;

			if (strlen($num)==1) {
				$num = "0".$num;
			}

			$select='';

			for($x = 0; $x < $arrlength; $x++) {

				if($qty_data[$x]==$num){		

					$select = 'checked="checked"';	

				}

			}

			$html .= '<input type="checkbox" name="QtyPerMemberData[]" '.$select.' value="'.$num.'"> '.$num.' &nbsp; '; 

			if ($i==10 || $i==20) {
				$html .= "<br><label class='lable-form'></label>";
			}
		}

		$data = '<label class="lable-form"></label>

            '.$html.'';

	}

	echo $data;

	exit;	

}


else if($TASK =='Get_MemberType'){

	$bran_BrandID = $_REQUEST['bran_BrandID'];

	// $prco_MemberType = $_REQUEST['spco_MemberType'];

	$table = "";

	// if ($bran_BrandID && $prco_MemberType=="Register") {

	if ($bran_BrandID) {

		$sql_card = "SELECT card_id FROM mi_card WHERE brand_id = ".$bran_BrandID;

		$oRes_card = $oDB->Query($sql_card);

		$card_id = "";

		while($axRow = $oRes_card->FetchRow(DBI_ASSOC)) {

			$card_id .= $axRow['card_id'].",";

		}

		$str_card = strlen($card_id);
		$card_id = substr($card_id,0,$str_card-1);


		if ($card_id) {

			$sql_member = "SELECT DISTINCT
					mb_member_register.member_id,
					mb_member.email,
					mb_member.mobile
					FROM mb_member_register
					RIGHT JOIN mb_member
					ON mb_member.member_id = mb_member_register.member_id
					WHERE mb_member_register.card_id IN (".$card_id.")
					ORDER BY mb_member.email";

			$oRes_member = $oDB->Query($sql_member);

		    $table = '<center><div style="width:50%"><table class="table table-bordered" cellspacing="0" style="background-color:white; text-align:center;" id="table_register"><tr class="th_table th_text" style="text-align:center; font-weight:bold"><td width="5%">Select</td><td width="45%">Email</td><td width="45%">Phone</td></tr>';

			while($axRow = $oRes_member->FetchRow(DBI_ASSOC)) {

				$table .= '<tr>';
				$table .= '<td><input type="checkbox" name="memb_MemberID[]" value="'.$axRow['member_id'].'"></td>';
				$table .= '<td>'.$axRow['email'].'</td>';
				$table .= '<td>'.$axRow['mobile'].'</td>';
				$table .= '</tr>';

			}

		    $table .= '</table></div></center>';
		
		} else {

			$table = "";
		}
	}
	

	echo $table;

	exit;	

}

else if($TASK == 'Get_RewardCard'){

	$bran_BrandID = $_REQUEST['bran_BrandID'];

	$card_CardID = $_REQUEST['card_CardID'];

	$sql ="SELECT name,card_id FROM mi_card WHERE brand_id=".$bran_BrandID." AND flag_del=0 AND flag_status=1";

	$oRes = $oDB->Query($sql);

	$html = '';

	if($oRes){

		$option ='';

		$option .='<option value="">Please Select ..</option>';

		while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

			if($card_CardID==$axRow['card_id']){ $select = 'selected="selected"'; } 
			else { $select=''; }

			$option .='<option value="'.$axRow['card_id'].'" '.$select.'>' .$axRow['name']. '</option>';
		}

		$html = '<select id="card_CardID" class="form-control text-md" name="card_CardID" onchange="RewardCard()">
				'.$option.'
				</select>' ;	

		echo $html;

	} else {

		$html = '<select id="card_CardID" class="form-control text-md" name="card_CardID">
				<option value="">Please Select ..</option>
				</select>' ;	

		echo $html;
	}

	exit;
}

else if($TASK == 'Get_RewardCardData'){

	$card_CardID = $_REQUEST['card_CardID'];

	$sql = "SELECT * FROM mi_card WHERE card_id=".$card_CardID;

	$oRes = $oDB->Query($sql);

	$html = '';

	if($oRes){

		$axRow = $oRes->FetchRow(DBI_ASSOC);

		if ($axRow['period_type'] == 4) { $period = 'Member Life Time'; }
		else if ($axRow['period_type'] == 3) {  $period = $axRow['period_type_other'].' Years';  }
		else if ($axRow['period_type'] == 2) {  $period = $axRow['period_type_other'].' Months';  }
		else {  $period = 'Expried Date ('.DateOnly($axRow['date_expired']).')';  }

		$html = '<div class="adj_row">
					<label class="lable-form"></label>
					<img src="../../upload/'.$axRow["path_image"].$axRow["image"].'" height="150" class="img-rounded image_border"/>
		        </div>
		        <div class="adj_row">
					<label class="lable-form"></label>
					<label>Member Fee : '.number_format($axRow['member_fee'],2).' ฿</label>
		        </div>
		        <div class="adj_row">
					<label class="lable-form"></label>
					<label>Period : '.$period.'</label>
		        </div>';

	}

	echo $html;

	exit;
}

else if($TASK == 'Get_RedeemCard'){

	$bran_BrandID = $_REQUEST['bran_BrandID'];
	$rera_CardID = $_REQUEST['rera_CardID'];

	$sql = "SELECT card_id,name FROM mi_card WHERE brand_id=".$bran_BrandID." AND flag_del='0'";

	$oRes = $oDB->Query($sql);

	if($bran_BrandID){

		$option ='';

		while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

			$token = strtok($rera_CardID , ",");

			$card = array();

			$i = 0;

			while ($token !== false) {
    			
    			$card[$i] =  $token;
    			$token = strtok(",");
    			$i++;

			}

			$arrlength = count($card);

			$select='';

			for($x = 0; $x < $arrlength; $x++) {

				if($card[$x]==$axRow['card_id']){		

					$select = 'selected="selected"';	

				}
			}

			$option .='<option value="'.$axRow['card_id'].'" '.$select.'>'.$axRow['name'].'</option>';
		}

	} else { $option=''; }

	$html='<label class="lable-form">Card <span class="text-rq">*</span></label> 

             <select id="rera_CardID" class="form-control text-md" name="rera_CardID[]" multiple autofocus>

			'.$option.'

			</select>';

	echo $html;

	exit;
}

else if($TASK == 'Get_RedeemQty'){

	$rewa_RewardID = $_REQUEST['rewa_RewardID'];
	$rera_RewardQty = $_REQUEST['rera_RewardQty'];

	$sql = "SELECT rewa_Type FROM reward WHERE rewa_RewardID=".$rewa_RewardID."";

	$rewa_Type = $oDB->QueryOne($sql);

	if ($rera_RewardQty == 'undefined') { $rera_RewardQty = ''; }

	$html = '';

	if($rewa_Type=='Card'){

		$html = '<input type="text" id="rera_RewardQty" name="RewardQty" value="1" class="form-control text-md" style="margin-left:5px;width:120px" disabled>';	
	
	} else {

		$html = '<input type="text" id="rera_RewardQty" name="RewardQty" value="'.$rera_RewardQty.'" class="form-control text-md"placeholder="Number" onkeypress="CheckNum()" required autofocus style="margin-left:5px;width:120px">';	

	}

	echo $html;

	exit;
}

else if($TASK == 'Get_RedeemAction'){

	$rewa_RewardID = $_REQUEST['rewa_RewardID'];
	$rede_Expired = $_REQUEST['rede_Expired'];
	$rede_AutoRedeem = $_REQUEST['rede_AutoRedeem'];

	$sql ="SELECT rewa_Type FROM reward WHERE rewa_RewardID=".$rewa_RewardID."";

	$rewa_Type = $oDB->QueryOne($sql);

	$html = '';

	if($rewa_Type=='Card'){

		// $html = '<div class="adj_row">
		// 			<label class="lable-form">Auto Redeem</label> 
		//             <label>
		//                 <input type="radio" id="rede_AutoRedeem" name="rede_AutoRedeem" value="T" ';

		// if ($rede_AutoRedeem == 'T' || !$rede_AutoRedeem) { $html .= ' checked="checked" '; }

		// $html .=        '> Yes

		//                 &nbsp;&nbsp;&nbsp;

		//                 <input type="radio" id="rede_AutoRedeem" name="rede_AutoRedeem" value="F"';

		// if ($rede_AutoRedeem == 'F') { $html .= ' checked="checked" '; }

		// $html .=        '> No
		//             </label>
		// 		</div>';

		$html .= '<div class="adj_row">
					<label class="lable-form">Expiration Date</label> 
		            <label>
		                <input type="radio" id="rede_Expired" name="rede_Expired" value="Original" checked="checked" ';

		if ($rede_Expired == 'Original') { $html .= ' checked="checked" '; }

		$html .=        '> Use Original Expiration Date

		                &nbsp;&nbsp;&nbsp;

		                <input type="radio" id="rede_Expired" name="rede_Expired" value="Extend"';

		if ($rede_Expired == 'Extend') { $html .= ' checked="checked" '; }

		$html .=        '> Extend The Expiration Date
		            </label>
				</div>';	
	}

	echo $html;

	exit;
}

else if($TASK == 'Get_MemberData'){

	$search_member = trim_txt($_REQUEST['search_member']);

	$where_brand = '';

	if($_SESSION['user_type_id_ses']>1){

		$where_brand = ' AND mb_member_register.bran_BrandID = "'.$_SESSION['user_brand_id'].'"';
	}

	if ($search_member!='') {

		if ($search_member) {

			$f_txt = substr($search_member,0,1);

			if ($f_txt == '0') { $search_member = substr($search_member,1); } 
			elseif ($f_txt == '+') { $search_member = substr($search_member,3); }
		}

		$sql_member = 'SELECT DISTINCT mb_member.* 
							FROM mb_member 
							LEFT JOIN mb_member_register
							ON mb_member_register.member_id = mb_member.member_id
							WHERE (mb_member.firstname LIKE "%'.$search_member.'%" 
							OR mb_member.lastname LIKE "%'.$search_member.'%" 
							OR mb_member.facebook_name LIKE "%'.$search_member.'%"
							OR mb_member.nickname LIKE "%'.$search_member.'%" 
							OR mb_member.email LIKE "%'.$search_member.'%"
							OR mb_member.mobile LIKE "%'.$search_member.'%"
							OR mb_member_register.member_brand_code LIKE "%'.$search_member.'%"
							OR mb_member_register.member_card_code LIKE "%'.$search_member.'%")
							AND mb_member_register.flag_del=""
							'.$where_brand;

		$oRes_member = $oDB->Query($sql_member);
		$member = $oRes_member->FetchRow(DBI_ASSOC);

		if ($member['member_id']) {

			$html = '<hr><center><label class="adj_row">Choose Member</label><br>
					<div style="overflow-x: scroll;width:800px;"><table><tr>';

			$oRes_member = $oDB->Query($sql_member);
			while ($axRow = $oRes_member->FetchRow(DBI_ASSOC)){

				if ($axRow['member_image'] && $axRow['member_image']!='user.png') {

			    $member_image = '<img src="../../upload/member_upload/'.$axRow['member_image'].'" width="50" height="50" class="img-circle image_border"/>';

				} else if ($axRow['facebook_id']) {

				    $member_image = '<img src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=square" width="50" height="50" class="img-circle image_border" />';

				} else {
				                    	
				    $member_image = '<img src="../../images/user.png" width="50" height="50" class="img-circle image_border" />';
				}

				$html .= '<td><button type="button" class="btn" id="'.$axRow['member_id'].'" onclick="SearchRegister('.$axRow['member_id'].')">
							<table><tr>
							<td width="60px" style="text-align:center">'.$member_image.'</td>
							<td width="10px">&nbsp;</td>
							<td style="text-align:left">'.$axRow['firstname'].' '.$axRow['lastname'].'<br>
								'.$axRow['email'].'<br>'.$axRow['mobile'].'</td>
							</tr></table>
						</button></td><td width="5px">&nbsp;</td>';
			}

			$html .= '</tr></table><br></div></center>';

			echo $html;
		}

	} else {

		$html = '<hr><center><b>No Member Data</b></center>';

		echo $html;
	}	

	exit;
}

else if($TASK == 'Get_MemberRedeem'){

	$search_member = trim_txt($_REQUEST['search_member']);
	$brand_id = trim_txt($_REQUEST['brand_id']);

	if ($search_member!='') {

		if ($search_member) {

			$f_txt = substr($search_member,0,1);

			if ($f_txt == '0') { $search_member = substr($search_member,1); } 
			elseif ($f_txt == '+') { $search_member = substr($search_member,3); }
		}

		$sql_member = 'SELECT DISTINCT mb_member.* 
							FROM mb_member 
							LEFT JOIN mb_member_register
							ON mb_member_register.member_id = mb_member.member_id
							WHERE (mb_member.firstname LIKE "%'.$search_member.'%" 
							OR mb_member.lastname LIKE "%'.$search_member.'%" 
							OR mb_member.facebook_name LIKE "%'.$search_member.'%"
							OR mb_member.nickname LIKE "%'.$search_member.'%" 
							OR mb_member.email LIKE "%'.$search_member.'%"
							OR mb_member.mobile LIKE "%'.$search_member.'%"
							OR mb_member_register.member_brand_code LIKE "%'.$search_member.'%"
							OR mb_member_register.member_card_code LIKE "%'.$search_member.'%")
							AND mb_member_register.flag_del=""
							AND mb_member_register.bran_BrandID = "'.$brand_id.'"';

		$oRes_member = $oDB->Query($sql_member);
		$member = $oRes_member->FetchRow(DBI_ASSOC);

		if ($member['member_id']) {

			$html = '<hr><center><label class="adj_row">Choose Member</label><br>
					<div style="overflow-x: scroll;width:800px;"><table><tr>';

			$oRes_member = $oDB->Query($sql_member);
			while ($axRow = $oRes_member->FetchRow(DBI_ASSOC)){

				if ($axRow['member_image'] && $axRow['member_image']!='user.png') {

			    $member_image = '<img src="../../upload/member_upload/'.$axRow['member_image'].'" width="50" height="50" class="img-circle image_border"/>';

				} else if ($axRow['facebook_id']) {

				    $member_image = '<img src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=square" width="50" height="50" class="img-circle image_border" />';

				} else {
				                    	
				    $member_image = '<img src="../../images/user.png" width="50" height="50" class="img-circle image_border" />';
				}

				$html .= '<td><button type="button" class="btn" id="'.$axRow['member_id'].'" onclick="SearchRedeem('.$axRow['member_id'].')">
							<table><tr>
							<td width="60px" style="text-align:center">'.$member_image.'</td>
							<td width="10px">&nbsp;</td>
							<td style="text-align:left">'.$axRow['firstname'].' '.$axRow['lastname'].'<br>
								'.$axRow['email'].'<br>'.$axRow['mobile'].'</td>
							</tr></table>
						</button></td><td width="5px">&nbsp;</td>';
			}

			$html .= '</tr></table><br></div></center>';

			echo $html;

		} else {

			$html = '<hr><center><b>No Member Data</b></center>';

			echo $html;
		}

	} else {

		$html = '<hr><center><b>No Member Data</b></center>';

		echo $html;
	}	

	exit;
}

else if($TASK == 'Get_MemberInsert'){

	$search_member = trim_txt($_REQUEST['search_member']);
	$code_member = trim_txt($_REQUEST['code_member']);
	$card_CardID = trim_txt($_REQUEST['card_id']);

	if ($search_member!='') {

		$sql_member = 'SELECT DISTINCT mb_member.* 
							FROM mb_member
							WHERE mobile LIKE "%'.$search_member.'%"
							OR mobile LIKE "'.$code_member.$search_member.'%"';

		$oRes_member = $oDB->Query($sql_member);
		$member = $oRes_member->FetchRow(DBI_ASSOC);

		if ($member['member_id']) {

			$html = '<br><br><label class="adj_row">Choose Member</label><br>
					<div style="overflow-x: scroll;width:800px;"><table><tr>';

			$oRes = $oDB->Query($sql_member);
			while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

				if ($axRow['member_image'] && $axRow['member_image']!='user.png') {

			    $member_image = '<img src="../../upload/member_upload/'.$axRow['member_image'].'" width="50" height="50" class="img-circle image_border"/>';

				} else if ($axRow['facebook_id']) {

				    $member_image = '<img src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=square" width="50" height="50" class="img-circle image_border" />';

				} else {
				                    	
				    $member_image = '<img src="../../images/user.png" width="50" height="50" class="img-circle image_border" />';
				}

				$html .= '<td><button type="button" class="btn" id="'.$axRow['member_id'].'" onclick="SearchRegister('.$axRow['member_id'].')">
							<table><tr>
							<td width="60px" style="text-align:center">'.$member_image.'</td>
							<td width="10px">&nbsp;</td>
							<td>'.$axRow['firstname'].' '.$axRow['lastname'].'<br>
								'.$axRow['email'].'<br>'.$axRow['mobile'].'</td>
							</tr></table>
						</button></td><td width="5px">&nbsp;</td>';
			}

			$html .= '</tr></table><br></div>';

			echo $html;
		
		} else {

			$html = '<br><br><label class="adj_row">Insert New Member</label><br><table>';

			$topic = array("Profile", "Home Address", "Work Address", "Work", "Contact");

			for ($i=0; $i <5 ; $i++) { 

				$sql_field = 'SELECT a.*,b.*,c.*,

								a.mafi_MasterFieldID AS master_field_id,
								b.refo_Target,
								d.fity_Name AS field_type

								FROM master_field AS a

								LEFT JOIN register_form AS b
								ON b.mafi_MasterFieldID = a.mafi_MasterFieldID

								LEFT JOIN mi_card AS c
								ON b.card_CardID = c.card_id

								LEFT JOIN field_type AS d
								ON a.mafi_FieldType = d.fity_FieldTypeID

								WHERE a.mafi_Position = "'.$topic[$i].'"
								AND a.mafi_Deleted != "T"
								AND c.card_id = "'.$card_CardID.'"
								AND b.refo_FillIn = "Y"

								GROUP BY a.mafi_FieldName
								ORDER BY a.mafi_FieldOrder';

				$oRes = $oDB->Query($sql_field);
				$check_field = $oDB->QueryOne($sql_field);

				if ($check_field) {

					$html .= '<tr height="40px"><td colspan="3" style="text-align:center"><u><b>'.$topic[$i].'</b></u></td></tr>';

					while ($field = $oRes->FetchRow(DBI_ASSOC)){

						if ($field['refo_Require']=='Y') { 

							$text_rq = ' <span class="text-rq">*</span>';
							$rq_af = 'required autofocus'; 

						} else { $text_rq = '';	$rq_af = '';  }

						$html .= '	<tr height="40px"><td style="text-align:right">
											<b>'.$field['mafi_NameEn'].$text_rq.'</b></td>
											<td width="10px"></td>';

						if ($field['master_field_id'] == 33 || $field['master_field_id'] == 45) {

							$html .= '<td><select name="'.$field['mafi_FieldName'].'" class="form-control" style="width:250px" '.$rq_af.'>
											<option value="">Please Select ..</option>';

							$sql_target = 'SELECT * FROM province WHERE prov_Deleted = "" ORDER BY prov_Name';
							$oRes_target = $oDB->Query($sql_target);
							while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

								$html .= '<option value="'.$target['prov_Name'].'">'.$target['prov_Name'].'</option>';
							}

							$html .= '</select>';

						} elseif ($field['master_field_id'] == 34 || $field['mafi_MasterFieldID'] == 46) {

							$html .= '<td><select name="'.$field['mafi_FieldName'].'" class="form-control" style="width:250px" '.$rq_af.'>
											<option value="">Please Select ..</option>';

							$sql_target = 'SELECT * FROM country WHERE coun_PhoneCode!=0 ORDER BY coun_Nicename';
							$oRes_target = $oDB->Query($sql_target);
							while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

								$html .= '<option value="'.$target['coun_CountryID'].'">'.$target['coun_Nicename'].'</option>';
							}

							$html .= '</select>';

						} elseif ($field['field_type']=='Text') {

							$html .= '<td style="text-align:center"><input type="text" name="'.$field['mafi_FieldName'].'" class="form-control text-md" placeholder="Text" '.$rq_af.' value="'.$member[$field['mafi_FieldName']].'">';
							
						} elseif ($field['field_type']=='Number') {

							$html .= '<td style="text-align:center"><input type="number" name="'.$field['mafi_FieldName'].'" class="form-control text-md" placeholder="Number" '.$rq_af.'>';
							
						} else if ($field['field_type']=='Date') {

							# DAY OPTION

							$option_date = '';

							for ($x = 1; $x < 32; $x++) {

								if (strlen($x) == 1) { $d = '0'.$x; }
								else { $d = $x; }

								$option_date .= '<option value="'.$d.'">'.$d.'</option>';
							}


							# MONTH OPTION

							$month = ["Jan.", "Feb.", "Mar.", "Apr.", "May.", "Jun.", "Jul.", "Aug.", "Sep.", "Oct.", "Nov.", "Dec."];
							$option_month = '';

							for ($x = 1; $x < 13; $x++) {

								if (strlen($x) == 1) { $d = '0'.$x; }
								else { $d = $x; }

								$option_month .= '<option value="'.($d).'">'.$month[$x-1].'</option>';
							}


							# YEAR OPTION

							$this_year = date('Y',time());
							$start_year = $this_year-100;
							$end_year = $this_year;
							$option_year = '';

							for ($x = $start_year; $x <= $end_year; $x++) {

								if ($x == $this_year) { $select = 'selected="selected"'; }
								else { $select = ''; }

								$option_year .= '<option value="'.$x.'" '.$select.'>'.$x.'</option>';
							}

							$html .= '<td><span class="form-inline">
										<select id="date" class="form-control text-md" name="'.$field['mafi_FieldName'].'_date" style="width:70px" '.$rq_af.'>
											<option value=""> - - -</option>
											'.$option_date.'
										</select>
										<select id="month" class="form-control text-md" name="'.$field['mafi_FieldName'].'_month" style="width:80px" '.$rq_af.'>
											<option value=""> - - - -</option>
											'.$option_month.'
										</select>
										<select id="year" class="form-control text-md" name="'.$field['mafi_FieldName'].'_year" style="width:90px" '.$rq_af.'>
											<option value=""> - - - - -</option>
											'.$option_year.'
										</select></span>';
							
						} else if ($field['field_type']=='Radio') {

							$x = 0;

							$html .= '<td><span class="form-inline"><label>';

							$sql_target = 'SELECT *
											FROM master_target
											WHERE mafi_MasterFieldID = "'.$field['master_field_id'].'"';
							$oRes_target = $oDB->Query($sql_target);
							while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

								$html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="'.$field['mafi_FieldName'].'" value="'.$target['mata_MasterTargetID'].'"> '.$target['mata_NameEn'].'';

								$x++;
							}

							$html .= '</label></span>';

						} else if ($field['field_type']=='Checkbox') {

							$html .= '<td><span class="form-inline"><label>';

							$sql_target = 'SELECT *
											FROM master_target
											WHERE mafi_MasterFieldID = "'.$field['master_field_id'].'"';
							$oRes_target = $oDB->Query($sql_target);
							while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

								$html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="'.$field['mafi_FieldName'].'" value="'.$target['mata_MasterTargetID'].'"> '.$target['mata_NameEn'].'<br>';
							}

							$html .= '</label></span>';

						} else if ($field['field_type']=='Selection') {

							$html .= '<td>'.$target['mata_MasterTargetID'].'
										<select name="'.$field['mafi_FieldName'].'" class="form-control" style="width:250px" '.$rq_af.'>
											<option value="">Please Select ..</option>';

							$sql_target = 'SELECT *
											FROM master_target
											WHERE mafi_MasterFieldID = "'.$field['master_field_id'].'"';
							$oRes_target = $oDB->Query($sql_target);
							while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

								$html .= '<option value="'.$target['mata_MasterTargetID'].'">'.$target['mata_NameEn'].'</option>';
							}

							$html .= '</select>';

						} else if ($field['field_type']=='Tel') {

							# PHONE CODE

							$sql_code = 'SELECT DISTINCT coun_PhoneCode 
											FROM country 
											WHERE coun_PhoneCode NOT IN (0,1,7) 
											ORDER BY coun_PhoneCode';
							$oRes_code = $oDB->Query($sql_code);
							$option_code = '';
							while ($axRow_code = $oRes_code->FetchRow(DBI_ASSOC)){

								$check_code = '';
								if ($axRow_code['coun_PhoneCode'] == '66') { $check_code = 'selected'; }

								$option_code .= '<option value="+'.$axRow_code['coun_PhoneCode'].'" '.$check_code.'>+'.$axRow_code['coun_PhoneCode'].'</option>';
							}

							$html .= '<td><span class="form-inline">
                    					<select class="form-control text-md" id="code_'.$field['mafi_FieldName'].'" name="code_'.$field['mafi_FieldName'].'" '.$rq_af.'>'.$option_code.'</select>
                    					<input type="text" style="width:168px" name="'.$field['mafi_FieldName'].'" maxlength="9" class="form-control text-md" placeholder="Tel" '.$rq_af.'>
                    				</span>';
						}

						$html .= '	</td></tr>';
					}
				}
			}

			$sql_custom = 'SELECT custom_field.*,
							custom_form.cufo_Require,
							field_type.fity_Name AS field_type
							FROM custom_field
							LEFT JOIN custom_form
							ON custom_form.cufi_CustomFieldID = custom_field.cufi_CustomFieldID
							LEFT JOIN field_type
							ON custom_field.fity_FieldTypeID = field_type.fity_FieldTypeID
							WHERE custom_form.card_CardID = "'.$card_CardID.'"
							AND custom_form.cufo_FillIn = "Y"
							ORDER BY custom_field.cufi_FieldOrder';

			$oRes = $oDB->Query($sql_custom);
			$check_field = $oDB->QueryOne($sql_custom);

			if ($check_field) {

				$html .= '<tr height="40px"><td colspan="3" style="text-align:center"><u><b>Custom</b></u></td></tr>';

				while ($field = $oRes->FetchRow(DBI_ASSOC)){

					if ($field['cufo_Require']=='Y') { 

						$text_rq = ' <span class="text-rq">*</span>';
						$rq_af = 'required autofocus'; 

					} else { $text_rq = '';	$rq_af = '';  }

					$html .= '	<tr height="40px"><td style="text-align:right">
									<b>'.$field['cufi_Name'].$text_rq.'</b></td>
									<td width="10px"></td>';

					if ($field['field_type']=='Text') {

						$html .= '<td style="text-align:center"><input type="text" name="'.$field['cufi_FieldName'].'" class="form-control text-md" placeholder="Text" '.$rq_af.'>';
							
					} else if ($field['field_type']=='Number') {

						$html .= '<td style="text-align:center"><input type="number" name="'.$field['cufi_FieldName'].'" class="form-control text-md" placeholder="Number" '.$rq_af.'>';
							
					} else if ($field['field_type']=='Date') {

						# DAY OPTION

						$option_date = '';

						for ($x = 1; $x < 32; $x++) {

							if (strlen($x) == 1) { $d = '0'.$x; }
							else { $d = $x; }

							$option_date .= '<option value="'.$d.'">'.$d.'</option>';
						}


						# MONTH OPTION

						$month = ["Jan.", "Feb.", "Mar.", "Apr.", "May.", "Jun.", "Jul.", "Aug.", "Sep.", "Oct.", "Nov.", "Dec."];
						$option_month = '';

						for ($x = 1; $x < 13; $x++) {

							if (strlen($x) == 1) { $d = '0'.$x; }
							else { $d = $x; }

							$option_month .= '<option value="'.($d).'">'.$month[$x-1].'</option>';
						}


						# YEAR OPTION

						$this_year = date('Y',time());
						$start_year = $this_year-100;
						$end_year = $this_year;
						$option_year = '';

						for ($x = $start_year; $x <= $end_year; $x++) {

							$option_year .= '<option value="'.$x.'")>'.$x.'</option>';
						}

						$html .= '<td><span class="form-inline">
									<select id="date" class="form-control text-md" name="'.$field['mafi_FieldName'].'_date" style="width:70px" '.$rq_af.'>
										<option value=""> - - -</option>
										'.$option_date.'
									</select>
									<select id="month" class="form-control text-md" name="'.$field['mafi_FieldName'].'_month" style="width:80px" '.$rq_af.'>
										<option value=""> - - - -</option>
										'.$option_month.'
									</select>
									<select id="year" class="form-control text-md" name="'.$field['mafi_FieldName'].'_year" style="width:90px" '.$rq_af.'>
										<option value=""> - - - - -</option>
										'.$option_year.'
									</select></span>';
							
					} else if ($field['field_type']=='Radio') {

						$x = 0;

						$html .= '<td><span class="form-inline"><label>';

						$sql_target = 'SELECT *
										FROM custom_list_value
										WHERE cufi_CustomFieldID = "'.$field['cufi_CustomFieldID'].'"';
						$oRes_target = $oDB->Query($sql_target);
						while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

							$html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="'.$field['cufi_FieldName'].'" value="'.$target['clva_CustomListValueID'].'"> '.$target['clva_Name'].'';

							$x++;
						}

						$html .= '</label></span>';

					} else if ($field['field_type']=='Checkbox') {

						$html .= '<td><span class="form-inline"><label>';

						$sql_target = 'SELECT *
										FROM custom_list_value
										WHERE cufi_CustomFieldID = "'.$field['cufi_CustomFieldID'].'"';
						$oRes_target = $oDB->Query($sql_target);
						while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

							$html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" name="'.$field['cufi_FieldName'].'" value="'.$target['clva_CustomListValueID'].'"> '.$target['clva_Name'].'<br>';
						}

						$html .= '</label></span>';

					} else if ($field['field_type']=='Selection') {

						$html .= '<td><select name="'.$field['cufi_FieldName'].'" class="form-control" '.$rq_af.'>
									<option value="">Please Select ..</option>';

						$sql_target = 'SELECT *
										FROM custom_list_value
										WHERE cufi_CustomFieldID = "'.$field['cufi_CustomFieldID'].'"';
						$oRes_target = $oDB->Query($sql_target);
						while ($target = $oRes_target->FetchRow(DBI_ASSOC)){

							$html .= '<option value="'.$target['clva_CustomListValueID'].'">'.$target['clva_Name'].'</option>';
						}

						$html .= '</select>';

					} else if ($field['field_type']=='Tel') {

						# PHONE CODE

						$sql_code = 'SELECT DISTINCT coun_PhoneCode 
										FROM country 
										WHERE coun_PhoneCode NOT IN (0,1,7) 
										ORDER BY coun_PhoneCode';
						$oRes_code = $oDB->Query($sql_code);
						$option_code = '';
						while ($axRow_code = $oRes_code->FetchRow(DBI_ASSOC)){

							$check_code = '';
							if ($axRow_code['coun_PhoneCode'] == '66') { $check_code = 'selected'; }

							$option_code .= '<option value="+'.$axRow_code['coun_PhoneCode'].'" '.$check_code.'>+'.$axRow_code['coun_PhoneCode'].'</option>';
						}

						$html .= '<td><span class="form-inline">
                    					<select class="form-control text-md" id="code_'.$field['cufi_FieldName'].'" name="code_'.$field['cufi_FieldName'].'" '.$rq_af.'>'.$option_code.'</select>
                    					<input type="text" style="width:168px" name="'.$field['cufi_FieldName'].'" maxlength="9" class="form-control text-md" placeholder="Tel" '.$rq_af.'>
                    				</span>';
					}

					$html .= '	</td></tr>';

				}
			}

			$html .= '		</table>
						</span>
						<br>
			            <div class="clear_all">
			                <button class="btn btn-success btn_hide" type="submit">SUBMIT</button>
			                <input type="hidden" id="act" name="act" value="save" />
			                <input type="hidden" id="member_id" name="member_id" value="'.$member['member_id'].'" />
			                &nbsp;&nbsp;&nbsp;
			                <button class="btn btn-warning btn_hide" type="reset" onclick="window.location.href='."'".'register.php'."'".'">CANCEL</button>
			            
			            </div>
			            <br>';

			echo $html;

		}

	} else {

		$html = '<br><br><b>No Member Data</b>';

		echo $html;
	}	

	exit;
}

else if($TASK == 'Get_MemberCodeInsert'){

	$search_member = trim_txt($_REQUEST['search_member']);
	$card_CardID = trim_txt($_REQUEST['card_id']);

	if ($search_member!='') {

		$sql_member = 'SELECT DISTINCT mb_member_brand.*,
								IF(mb_member_brand.member_card_code!="",mb_member_brand.member_card_code,"-") AS member_card_code,
								IF(mb_member_brand.member_brand_code!="",mb_member_brand.member_brand_code,"-") AS member_brand_code,
								mb_member.facebook_id,
								mb_member.member_image
							FROM mb_member_brand
							LEFT JOIN mb_member
							ON mb_member.member_id = mb_member_brand.member_id
							WHERE member_card_code LIKE "%'.$search_member.'%"
							AND mb_member_brand.card_id = "'.$card_CardID.'"
							AND mb_member_brand.member_register_id = "0"';

		$oRes_member = $oDB->Query($sql_member);
		$member = $oRes_member->FetchRow(DBI_ASSOC);

		if ($member['member_brand_id']) {

			$html = '<br><br><label class="adj_row">Choose Member</label><br>
					<div style="overflow-x: scroll;width:800px;"><table><tr>';

			$oRes = $oDB->Query($sql_member);
			while ($axRow = $oRes->FetchRow(DBI_ASSOC)){

				if ($axRow['member_image'] && $axRow['member_image']!='user.png') {

			    $member_image = '<img src="../../upload/member_upload/'.$axRow['member_image'].'" width="50" height="50" class="img-circle image_border"/>';

				} else if ($axRow['facebook_id']) {

				    $member_image = '<img src="http://graph.facebook.com/'.$axRow['facebook_id'].'/picture?type=square" width="50" height="50" class="img-circle image_border" />';

				} else {
				                    	
				    $member_image = '<img src="../../images/user.png" width="50" height="50" class="img-circle image_border" />';
				}

				$html .= '<td><button type="button" class="btn" id="'.$axRow['member_brand_id'].'" onclick="SearchMemberRegister('.$axRow['member_brand_id'].')">
							<table><tr>
							<td width="60px" style="text-align:center">'.$member_image.'</td>
							<td width="10px">&nbsp;</td>
							<td style="text-align:left">'.$axRow['firstname'].' '.$axRow['lastname'].'<br>
								Card ID : '.$axRow['member_card_code'].'<br>
								Brand ID : '.$axRow['member_brand_code'].'</td>
							</tr></table>
						</button></td><td width="5px">&nbsp;</td>';
			}

			$html .= '</tr></table><br></div>';

			echo $html;
		
		} else {

			$html = '<br><br><b>No Member Data</b>';

			echo $html;
		}

	} else {

		$html = '<br><br><b>No Member Data</b>';

		echo $html;
	}	

	exit;
}

else if($TASK == 'Get_MemberRegister'){

	$member_id = $_REQUEST['member_id'];

	$where_brand = '';

	if($_SESSION['user_type_id_ses']>1){

		$where_brand = ' AND mb_member_register.bran_BrandID = "'.$_SESSION['user_brand_id'].'"';
	}

	if ($member_id != 'undefined') {

		$sql_member = 'SELECT * FROM mb_member WHERE member_id="'.$member_id.'"';
		$oRes = $oDB->Query($sql_member);
		$member = $oRes->FetchRow(DBI_ASSOC);

		if ($member['member_image'] && $member['member_image']!='user.png') {

			$image = '<img src="../../upload/member_upload/'.$member['member_image'].'" width="100" height="100" class="img-circle image_border"/>';

		} else if ($member['facebook_id']) {

			$image = '<img src="http://graph.facebook.com/'.$member['facebook_id'].'/picture?type=square" width="100" height="100" class="img-circle image_border" />';

		} else {
					                    	
			$image = '<img src="../../images/user.png" width="100" height="100" class="img-circle image_border" />';
		}

		$html = '<hr><center>
					<table><tr>
						<td width="120px">'.$image.'</td>
						<td valign="top" style="text-align:right"><label>Name<br><br>Email<br><br>Mobile</label></td>
						<td valign="top" width="20px" style="text-align:center"><label>:<br><br>:<br><br>:</label></td>
						<td valign="top"><label>'.$member['firstname'].' '.$member['lastname'].'<br><br>
							'.$member['email'].'<br><br>'.$member['mobile'].'</label></td>
					</tr></table>';

		$sql_regis = 'SELECT DISTINCT 
							mi_card.name AS card_name,
							mi_card.image AS card_image,
							mi_card.path_image,
							mi_card.price_type AS card_type,
							mi_card.card_id AS card_id,
							mi_card.flag_multiple,
							mb_member_register.period_type,
							mb_member_register.date_expire,
							mb_member_register.member_register_id
						FROM mb_member_register 
						LEFT JOIN mi_card
						ON mi_card.card_id = mb_member_register.card_id
						WHERE mb_member_register.member_id="'.$member_id.'"
						AND (mb_member_register.date_start="0000-00-00" 
						OR mb_member_register.date_start<="'.date('Y-m-d').'")
						AND mb_member_register.flag_del=""
						'.$where_brand.'
						GROUP BY mi_card.card_id';

		$oRes = $oDB->Query($sql_regis);
		$card = $oRes->FetchRow(DBI_ASSOC);

		if ($card['card_name']) {

			$html .= '<span id="member_card"><hr><label class="adj_row">Choose Card</label><br>
					<div style="overflow-x: scroll;width:800px;"><table><tr>';

			$oRes_card = $oDB->Query($sql_regis);
			while ($axRow = $oRes_card->FetchRow(DBI_ASSOC)){

				if (strtotime($axRow['date_expire'])>strtotime("now") OR $axRow['period_type']=="4") {

					if ($axRow['card_image']) {

				    	$card_image = '<img src="../../upload/'.$axRow['path_image'].$axRow['card_image'].'" height="50" class="img-rounded image_border"/>';

					} else {
					                    	
					    $card_image = '<img src="../../images/card_privilege.jpg" height="50" class="img-rounded image_border" />';
					}

					$html .= '<td><button type="button" class="btn" id="'.$axRow['member_register_id'].'" onclick="SearchCard('.$axRow['card_id'].','.$member_id.')">
								<table><tr>
								<td width="60px" style="text-align:center">'.$card_image.'</td>
								<td width="10px">&nbsp;</td>
								<td>'.$axRow['card_name'].'<br>'.$axRow['card_type'].'</td>
								</tr></table>
							</button></td><td width="5px">&nbsp;</td>';
				}
			}

			$html .= '</tr></table><br></div></span>';

		} else {

			$html .= '<hr><b>No Register Data</b><br><br>';
		}

		$html .= '</center>';

		echo $html;
	}

	exit;
}

else if($TASK == 'Get_MemberCard'){

	$member_id = $_REQUEST['member_id'];
	$card_id = $_REQUEST['card_id'];
	$register_id = $_REQUEST['register_id'];

	if ($card_id != 'undefined') {

		# CARD

		$sql_card = 'SELECT mi_card.name,
							mi_card.image,
							mi_card.image_newupload,
							mi_card.path_image,
							mi_card.card_id,
							mi_brand.logo_image,
							mi_brand.path_logo,
							mb_member_register.date_expire,
							mb_member_register.date_start,
							mb_member_register.period_type,
							mb_member_register.bran_BrandID,
							mb_member_register.member_id
					FROM mb_member_register
					LEFT JOIN mi_card
					ON mb_member_register.card_id = mi_card.card_id
					LEFT JOIN mi_brand
					ON mb_member_register.bran_BrandID = mi_brand.brand_id
					WHERE mb_member_register.card_id="'.$card_id.'"
					AND mb_member_register.member_id="'.$member_id.'"
					AND mb_member_register.flag_del=""
					AND (mb_member_register.date_start="0000-00-00" 
					OR mb_member_register.date_start<="'.date('Y-m-d').'")';

		$oRes = $oDB->Query($sql_card);
		$card = $oRes->FetchRow(DBI_ASSOC);

		# PRIVILEGE

		$sql_priv = "SELECT DISTINCT privilege.priv_Name AS name, 
									privilege.priv_Image AS image, 
									privilege.priv_ImagePath AS path_image, 
									privilege.priv_PrivilegeID AS id,
									privilege.priv_LimitUse,
									privilege.priv_OneTimePer,
									privilege.priv_Motivation,
									privilege.priv_MotivationID,
									privilege.priv_StartDateSpecial,
									privilege.priv_EndDateSpecial,
									IF(privilege.priv_StartDateSpecial = '0000-00-00' 
									OR privilege.priv_EndDateSpecial = '0000-00-00', '-',
									CONCAT(DATE_FORMAT(privilege.priv_StartDateSpecial,'%d/%m/%Y'),' - ',
									DATE_FORMAT(privilege.priv_EndDateSpecial,'%d/%m/%Y'))) AS TextDate
					FROM mi_card_register
					LEFT JOIN privilege
					ON mi_card_register.privilege_id = privilege.priv_PrivilegeID
					LEFT JOIN mi_card
					ON mi_card.card_id = mi_card_register.card_id
					WHERE mi_card_register.card_id=".$card['card_id']."
					AND mi_card_register.status='0'
					AND mi_card_register.privilege_id!='0'
					AND privilege.priv_Status='Active'";

		$oRes_priv = $oDB->Query($sql_priv);

		# COUPON

		$sql_coup = "SELECT DISTINCT coupon.coup_Name AS name, 
									coupon.coup_Image AS image, 
									coupon.coup_ImagePath AS path_image, 
									coupon.coup_CouponID AS id,
									coupon.coup_QtyPerMember,
									coupon.coup_RepetitionMember,
									coupon.coup_QtyMember,
									coupon.coup_QtyPerMemberData,
									coupon.coup_SpecialPeriodType,
									coupon.coup_QtyPer,
									coupon.coup_Repetition,
									coupon.coup_Qty,
									coupon.coup_QtyPerData,
									coupon.coup_TotalQty,
									coupon.coup_Method,
									coupon.coup_Motivation,
									coupon.coup_MotivationID,
									coupon.coup_StartDate,
									coupon.coup_EndDate,
									coupon.coup_StartDateSpecial,
									coupon.coup_EndDateSpecial,
									IF(coupon.coup_StartDate = '0000-00-00' OR coupon.coup_EndDate = '0000-00-00', 
									'-', CONCAT(DATE_FORMAT(coupon.coup_StartDate,'%d/%m/%Y'),' - ',
									DATE_FORMAT(coupon.coup_EndDate,'%d/%m/%Y'))) as TextDate,
									IF(coupon.coup_StartDateSpecial = '0000-00-00' 
									OR coupon.coup_EndDateSpecial = '0000-00-00', 
									'-', CONCAT(DATE_FORMAT(coupon.coup_StartDateSpecial,'%d/%m/%Y'),' - ',
									DATE_FORMAT(coupon.coup_EndDateSpecial,'%d/%m/%Y'))) as TextDateSpecial
					FROM mi_card_register
					LEFT JOIN coupon
					ON mi_card_register.coupon_id = coupon.coup_CouponID
					LEFT JOIN mi_card
					ON mi_card.card_id = mi_card_register.card_id
					WHERE mi_card_register.card_id=".$card['card_id']."
					AND mi_card_register.status='0'
					AND mi_card_register.coupon_id!='0'
					AND coupon.coup_Birthday!='T'
					AND coupon.coup_Status='Active'";

		$oRes_coup = $oDB->Query($sql_coup);

		# HBD

		$sql_hbd = "SELECT DISTINCT coupon.coup_Name AS name, 
									coupon.coup_Image AS image, 
									coupon.coup_ImagePath AS path_image, 
									coupon.coup_CouponID AS id,
									coupon.coup_Method,
									coupon.coup_Motivation,
									coupon.coup_MotivationID
					FROM mi_card_register
					LEFT JOIN coupon
					ON mi_card_register.coupon_id = coupon.coup_CouponID
					LEFT JOIN mi_card
					ON mi_card.card_id = mi_card_register.card_id
					WHERE mi_card_register.card_id=".$card['card_id']."
					AND mi_card_register.status='0'
					AND mi_card_register.coupon_id!='0'
					AND coupon.coup_Birthday='T'
					AND coupon.coup_Status='Active'";

		$oRes_hbd = $oDB->Query($sql_hbd);

		# ACTIVITY

		$sql_acti = "SELECT DISTINCT activity.acti_Name AS name, 
									activity.acti_Image AS image, 
									activity.acti_ImagePath AS path_image, 
									activity.acti_ActivityID AS id,
									activity.acti_SpecialPeriodType,
									IF(activity.acti_StartDate = '0000-00-00' OR activity.acti_EndDate = '0000-00-00', '-',CONCAT(DATE_FORMAT(activity.acti_StartDate,'%d/%m/%Y'),' - ',DATE_FORMAT(activity.acti_EndDate,'%d/%m/%Y'))) as TextDate,
									activity.acti_StartDateSpecial, 
									activity.acti_EndDateSpecial, 
									activity.acti_StartDate,
									activity.acti_EndDate,
									activity.acti_Method,
									activity.acti_Motivation,
									activity.acti_MotivationID,
									activity.acti_StartTime,
									activity.acti_EndTime,
									activity.acti_QtyPerMember,
									activity.acti_QtyMember,
									activity.acti_RepetitionMember,
									activity.acti_QtyPerMemberData,
									activity.acti_QtyPer,
									activity.acti_Qty,
									activity.acti_MaxQty,
									activity.acti_Repetition,
									activity.acti_QtyPerData,
									activity.acti_TotalQty,
									activity.acti_Reservation,
									activity.acti_StartDateReservation,
									activity.acti_EndDateReservation,
									activity.acti_StartTimeReservation,
									activity.acti_EndTimeReservation
					FROM mi_card_register
					LEFT JOIN activity
					ON mi_card_register.activity_id = activity.acti_ActivityID
					LEFT JOIN mi_card
					ON mi_card.card_id = mi_card_register.card_id
					WHERE mi_card_register.card_id=".$card['card_id']."
					AND mi_card_register.status='0'
					AND mi_card_register.activity_id!='0'
					AND activity.acti_Status='Active'";

		$oRes_acti = $oDB->Query($sql_acti);

		# LOGO

		if ($card['logo_image']) {

			$logo = '<img src="../../upload/'.$card['path_logo'].$card['logo_image'].'" height="100" class="image_border"/>';

		} else {
					                    	
			$logo = '<img src="../../images/400x400.png" height="100" class="image_border" />';
		}

		# IMAGE

		if ($card['image']) {

			$image = '<img src="../../upload/'.$card['path_image'].$card['image'].'" height="100" class="img-rounded image_border"/>';

		} else if ($card['image_newupload']) {

			$image = '<img src="../../upload/'.$card['path_image'].$card['image_newupload'].'" height="100" class="img-rounded image_border"/>';

		} else {
					                    	
			$image = '<img src="../../images/card_privilege.jpg" height="100" class="img-rounded image_border" />';
		}

		# EXPIRED DATE

		if ($card['period_type']==4) { $expired = 'Member Life Time'; }
		else if ($card['period_type']==1) { $expired = DateOnly($card['date_expire']); }
		else { 

			$expired = DateOnly($card['date_expire']);
		}

		$html = '<hr><center>
					'.$image.'<br><br>
					<label>'.$card['name'].'</label><br><br>';

		# CHECK MULTIPLE

		$sql_multiple = 'SELECT flag_multiple 
							FROM mi_card
							LEFT JOIN mb_member_register
							ON mb_member_register.card_id = mi_card.card_id 
							WHERE mb_member_register.member_id="'.$member_id.'"
							AND mb_member_register.card_id="'.$card_id.'"';
		$multiple = $oDB->QueryOne($sql_multiple);

		$count_card = 1;

		if ($multiple == 'Yes') {

			$html .= '<table class="table table-striped table-bordered" style="width:300px">
							<thead>
							<tr class="th_table">
								<td style="text-align:center">Select</td>
								<td style="text-align:center">Expiry Date</td>
								<td style="text-align:center">Start Date</td>
								<td style="text-align:center">Status</td>
							</tr>
							</thead>';

			# REGISTER ID

			if ($register_id) {

				$token = strtok($register_id,",");
				$register = array();
				$r = 0;
				while ($token !== false) {
		    			
		    		$register[$r] =  $token;
		    		$token = strtok(",");
		    		$r++;
				}

				$count_card = $r;
			}

			# MULTIPLE REGISTER

			$sql_regis = 'SELECT member_register_id AS id, 
								date_expire,
								date_start,
								date_create,
								period_type 
							FROM mb_member_register
							WHERE member_id="'.$card['member_id'].'"
							AND card_id="'.$card['card_id'].'"
							AND flag_del=""
							AND (date_start="0000-00-00" 
							OR date_start<="'.date('Y-m-d').'")
							ORDER BY date_expire';
			$oRes_regis = $oDB->Query($sql_regis);

			$status_card = 'F';

			while ($regis = $oRes_regis->FetchRow(DBI_ASSOC)) {

				# COUPON 

				$status_all = 'F';
				$status_member = 'F';
				$card_status = '';
				$oRes_coup = $oDB->Query($sql_coup);

				while ($axRow = $oRes_coup->FetchRow(DBI_ASSOC)) {

					if ($axRow['coup_Repetition']=='T') {

						if ($axRow['coup_QtyPer'] == 'Not') {

							$count_all = 0;

							$sql_count = 'SELECT COUNT(meco_MemberCouponID)
											FROM member_coupon_trans
											WHERE coup_CouponID="'.$axRow['id'].'"
											AND card_CardID="'.$card['card_id'].'"
											AND meco_Deleted=""';
							$count_all = $oDB->QueryOne($sql_count);

							if ($count_all != $axRow['coup_Qty']) { $status_all = "T"; }
						}

					} else { $status_all = "T"; }

					if ($axRow['coup_RepetitionMember']=='T') {

						if ($axRow['coup_QtyPerMember'] == 'Not Specific') {

							$count_member = 0;

							$sql_count = 'SELECT COUNT(meco_MemberCouponID)
											FROM member_coupon_trans
											WHERE coup_CouponID="'.$axRow['id'].'"
											AND mere_MemberRegisterID = '.$regis['id'].'
											AND meco_Deleted=""';
							$count_member = $oDB->QueryOne($sql_count);

							if ($count_member != $axRow['coup_QtyMember']) { $status_member = "T"; }
						}

					} else { $status_member = "T"; }
				}

				if ($status_all=='T' || $status_member=='T') {

					$status_card = 'T';

					$check = '';

					foreach ($register as $regis_id) {

						if ($regis_id == $regis['id']) { $check = 'checked'; }
					}

					if ($regis['period_type']=='4') { 

						$card_status = 'Active'; 
						$date_expire = '-';

					} else {

						$date_expire = DateOnly(date('m/d/Y', strtotime($regis['date_expire']. ' - 1 day')));

						if ($regis['date_expire']<=date('Y-m-d')) { $card_status = 'Expired'; }
						else {  $card_status = 'Active';  }
					}

					if ($regis['date_start']=='0000-00-00') { $date_start = $regis['date_create']; }
					else { $date_start = $regis['date_start']; }

					$html .= '<tr>
									<td style="text-align:center">
										<input type="checkbox" name="regis_id[]" id="regis_id" value="'.$regis['id'].'" '.$check.' onclick="SearchCard('.$card_id.','.$member_id.')"></td>
									<td style="text-align:center">'.$date_expire.'</td>
									<td style="text-align:center">'.DateOnly($date_start).'</td>
									<td style="text-align:center">'.$card_status.'</td>
								</tr>';
				}
			}

			if ($status_card == 'F') {

				$html .= '<tr><td style="text-align:center" colspan="4">No Card Can Use</td></tr>';
			}

			$html .= '</table>';

		} else {

			$html .= '<label>Expired Date &nbsp; : &nbsp; '.$expired.'</label>';

			# REGISTER ID

			$sql_regis = 'SELECT member_register_id
							FROM mb_member_register
							WHERE member_id="'.$member_id.'"
							AND card_id="'.$card_id.'"';
			$register_id = $oDB->QueryOne($sql_regis);
		}


		# DAY OPTION

		$this_day = date('j');
		$option_date = '';

		for ($x = 1; $x < 32; $x++) {

			if ($x == $this_day) { $select = 'selected="selected"'; }
			else { $select = ''; }

			if (strlen($x) == 1) { $d = '0'.$x; }
			else { $d = $x; }

			$option_date .= '<option value="'.$d.'" '.$select.'>'.$d.'</option>';
		}


		# MONTH OPTION

		$this_month = date('n');
		$month = ["Jan.", "Feb.", "Mar.", "Apr.", "May.", "Jun.", "Jul.", "Aug.", "Sep.", "Oct.", "Nov.", "Dec."];
		$option_month = '';

		for ($x = 1; $x < 13; $x++) {

			if ($x == $this_month) { $select = 'selected="selected"'; }
			else { $select = ''; }

			if (strlen($x) == 1) { $d = '0'.$x; }
			else { $d = $x; }

			$option_month .= '<option value="'.($d).'" '.$select.'>'.$month[$x-1].'</option>';
		}


		# YEAR OPTION

		$this_year = date('Y',time());
		$start_year = $this_year-5;
		$option_year = '';

		for ($x = $start_year; $x <= $this_year; $x++) {

			if ($x == $this_year) { $select = 'selected="selected"'; }
			else { $select = ''; }

			$option_year .= '<option value="'.$x.'" '.$select.'>'.$x.'</option>';
		}


		# TIME OPTION

		$this_hour = date('H',time());
		$this_min = date('i',time());
		$option_hour = "";
		$option_min = "";

		for ($x = 0; $x <= 24; $x++) {

			if (strlen($x) == 1) { $d = '0'.($x); }
			else { $d = $x; }

			if ($x == 0) { $select = 'selected="selected"'; }
			else { $select = ''; }

			$option_hour .= '<option value="'.$d.'" '.$select.'>'.$d.'</option>';
		}

		for ($x = 0; $x < 60; $x++) {

			if (strlen($x) == 1) { $d = '0'.($x); }
			else { $d = $x; }

			if ($x == 0) { $select = 'selected="selected"'; }
			else { $select = ''; }

			$option_min .= '<option value="'.$d.'" '.$select.'>'.$d.'</option>';
		}


		# CHECK REGISTER ID

		if ($register_id == '') {

		} else {

			$oRes_priv = $oDB->Query($sql_priv);
			$priv = $oRes_priv->FetchRow(DBI_ASSOC);

			$oRes_coup = $oDB->Query($sql_coup);
			$coup = $oRes_coup->FetchRow(DBI_ASSOC);

			$oRes_hbd = $oDB->Query($sql_hbd);
			$hbd = $oRes_hbd->FetchRow(DBI_ASSOC);

			$oRes_acti = $oDB->Query($sql_acti);
			$acti = $oRes_acti->FetchRow(DBI_ASSOC);

			if ($priv['name'] || $coup['name'] || $hbd['name'] || $acti['name']) {

			    $table_normal = '';
			    $table_point = '';
			    $table_stamp = '';

				# PRIVILEGE

				$oRes_priv = $oDB->Query($sql_priv);
				while ($axRow = $oRes_priv->FetchRow(DBI_ASSOC)){

					# PRIVILEGE DATA

					if ($axRow['image']) {

					    $image = '<img src="../../upload/'.$axRow['path_image'].$axRow['image'].'" height="100" class="image_border"/>';

					} else {
							                    	
					   $image = '<img src="../../images/card_privilege.jpg" height="100" class="image_border" />';
					}

					if ($axRow['priv_OneTimePer'] == '') { $axRow['priv_OneTimePer'] = '-'; }
					else { 

						$axRow['priv_OneTimePer'] = $count_card.' Times Per '.$axRow['priv_OneTimePer'];
					}

					$period = '';

					if ($axRow['priv_StartDateSpecial'] != '0000-00-00' && $axRow['priv_EndDateSpecial'] != '0000-00-00') { 

						$period = DateOnly($axRow['priv_StartDateSpecial']).' - '.DateOnly($axRow['priv_EndDateSpecial']); 
					} else { $period = '-'; }

					$motivation = '';

					if ($axRow['priv_Motivation']=='Point') {

						# POINT

						$sql_point = 'SELECT * FROM motivation_plan_point 
										WHERE mopp_MotivationPointID="'.$axRow['priv_MotivationID'].'"';
						$point_priv = $oDB->Query($sql_point);
						$point = $point_priv->FetchRow(DBI_ASSOC);

						$motivation = $point['mopp_UseAmount'].' ฿ / '.$point['mopp_PointQty'].' Point Qty ('.$point['mopp_Method'].')';

						$table_point .= '<tr>
				        					<td width="5%" rowspan="2" style="text-align:center">
				        						<input type="checkbox" name="pp[]" value="'.$axRow['id'].'"></td>
				        					<td width="160px" rowspan="2" style="text-align:center">'.$image.'</td>
				        					<td rowspan="2" style="text-align:right;line-height:170%" width="90px">
				        						<b>Name<br>
				        						Type<br>
				        						Limited Use<br>
				        						Period<br>
				        						Motivation</b></td>
				        					<td rowspan="2" style="line-height:170%">
				        						'.$axRow['name'].'<br>
				        						Privilege<br>
				        						'.$axRow['priv_OneTimePer'].'<br>
				        						'.$period.'<br>
				        						'.$motivation.'</td>
				        					<td width="120px"><input type="text" style="width:120px" class="form-control text-md" placeholder="Receieve No." name="precieve_'.$axRow['id'].'"></td>
				        					<td width="250px" rowspan="2" style="text-align:center">
				        						<span class="form-inline">
								                    <select id="date_'.$axRow['id'].'" class="form-control text-md" name="date_'.$axRow['id'].'" style="width:65px">
									                    '.$option_date.'
								                    </select>
								                    <select id="month_'.$axRow['id'].'" class="form-control text-md" name="month_'.$axRow['id'].'" style="width:70px">
									                    '.$option_month.'
								                    </select>
									                <select id="year_'.$axRow['id'].'" class="form-control text-md" name="year_'.$axRow['id'].'" style="width:80px">
									                    '.$option_year.'
									                </select></span><br>
						        				<span class="form-inline">
									                <select id="hour_'.$axRow['id'].'" class="form-control text-md" name="hour_'.$axRow['id'].'" style="width:65px">
									                    '.$option_hour.'
								                    </select>
								                    &nbsp : &nbsp;
								                    <select id="min_'.$axRow['id'].'" class="form-control text-md" name="min_'.$axRow['id'].'" style="width:70px">
									                    '.$option_min.'
								                    </select></span></td>
				        				</tr>
				        				<tr><td><input type="number" style="width:120px" class="form-control text-md" placeholder="Amount" name="pamount_'.$axRow['id'].'"></td></tr>';

					} else if ($axRow['priv_Motivation']=='Stamp') {

						# STAMP

						$sql_stamp = 'SELECT motivation_plan_stamp.*,
											collection_type.coty_Image
										FROM motivation_plan_stamp
										LEFT JOIN collection_type
										ON collection_type.coty_CollectionTypeID = motivation_plan_stamp.mops_CollectionTypeID
										WHERE motivation_plan_stamp.mops_MotivationStampID="'.$axRow['priv_MotivationID'].'"';
						$stamp_priv = $oDB->Query($sql_stamp);
						$stamp = $stamp_priv->FetchRow(DBI_ASSOC);

						$motivation = $stamp['mops_StampQty'].' <img src="'.$_SESSION['path_upload_collection'].$stamp['coty_Image'].'" width="15" style="margin-bottom:7px"/> / 1 Times';

						$table_stamp .= '<tr>
					       					<td width="5%" style="text-align:center">
					       						<input type="checkbox" name="sp[]" value="'.$axRow['id'].'"></td>
					       					<td width="160px" style="text-align:center">'.$image.'</td>
					       					<td style="text-align:right;line-height:170%" width="90px">
					       						<b>Name<br>
					       						Type<br>
					       						Limited Use<br>
					       						Period<br>
					       						Motivation</b></td>
					       					<td style="line-height:170%">
					       						'.$axRow['name'].'<br>
					       						Privilege<br>
					       						'.$axRow['priv_OneTimePer'].'<br>
					       						'.$period.'<br>
					       						'.$motivation.'</td>
					       					<td width="80px" style="text-align:center">-</td>
					       					<td width="80px"><input type="number" style="width:80px" class="form-control text-md" value="1" name="sptime_'.$axRow['id'].'"></td>
					       					<td width="250px" style="text-align:center">
					     						<span class="form-inline">
								                    <select id="date_'.$axRow['id'].'" class="form-control text-md" name="date_'.$axRow['id'].'" style="width:65px">
									                    '.$option_date.'
								                    </select>
								                    <select id="month_'.$axRow['id'].'" class="form-control text-md" name="month_'.$axRow['id'].'" style="width:70px">
									                    '.$option_month.'
								                    </select>
								                    <select id="year_'.$axRow['id'].'" class="form-control text-md" name="year_'.$axRow['id'].'" style="width:80px">
									                    '.$option_year.'
								                    </select></span><br>
					       						<span class="form-inline">
								                    <select id="hour_'.$axRow['id'].'" class="form-control text-md" name="hour_'.$axRow['id'].'" style="width:65px">
									                    '.$option_hour.'
								                    </select>
								                    &nbsp : &nbsp;
								                    <select id="min_'.$axRow['id'].'" class="form-control text-md" name="min_'.$axRow['id'].'" style="width:70px">
									                    '.$option_min.'
								                    </select></span></td>
					       				</tr>';
					} else {

						$table_normal .= '<tr>
					       					<td width="5%" style="text-align:center">
					       						<input type="checkbox" name="np[]" value="'.$axRow['id'].'"></td>
					       					<td width="160px" style="text-align:center">'.$image.'</td>
					       					<td style="text-align:right;line-height:170%" width="90px">
					       						<b>Name<br>
					       						Type<br>
					       						Limited Use<br>
					       						Period</b></td>
					       					<td style="line-height:170%">
					       						'.$axRow['name'].'<br>
					       						Privilege<br>
					       						'.$axRow['priv_OneTimePer'].'<br>
					       						'.$period.'</td>
					       					<td width="80px" style="text-align:center">-</td>
					       					<td width="80px"><input type="number" style="width:80px" class="form-control text-md" value="1" name="nptime_'.$axRow['id'].'"></td>
					       					<td width="250px" style="text-align:center">
					       						<span class="form-inline">
								                    <select id="date_'.$axRow['id'].'" class="form-control text-md" name="date_'.$axRow['id'].'" style="width:65px">
									                    '.$option_date.'
								                    </select>
								                    <select id="month_'.$axRow['id'].'" class="form-control text-md" name="month_'.$axRow['id'].'" style="width:70px">
									                    '.$option_month.'
								                    </select>
								                    <select id="year_'.$axRow['id'].'" class="form-control text-md" name="year_'.$axRow['id'].'" style="width:80px">
									                    '.$option_year.'
								                    </select></span><br>
					       						<span class="form-inline">
								                    <select id="hour_'.$axRow['id'].'" class="form-control text-md" name="hour_'.$axRow['id'].'" style="width:65px">
									                    '.$option_hour.'
								                    </select>
								                    &nbsp : &nbsp;
								                    <select id="min_'.$axRow['id'].'" class="form-control text-md" name="min_'.$axRow['id'].'" style="width:70px">
									                    '.$option_min.'
								                    </select></span></td>
					       				</tr>';
					}					
				}

				# COUPON

				$oRes_coup = $oDB->Query($sql_coup);
				while ($axRow = $oRes_coup->FetchRow(DBI_ASSOC)){

					# SHOW COUPON

					if ($axRow['image']) {

					    $image = '<img src="../../upload/'.$axRow['path_image'].$axRow['image'].'" height="100" class="image_border"/>';

					} else {
							                    	
						$image = '<img src="../../images/card_privilege.jpg" height="100" class="image_border" />';
					}

					$period = '';

					if ($axRow['coup_StartDateSpecial']!='0000-00-00' && $axRow['coup_EndDateSpecial']!='0000-00-00'){
								
						$period = DateOnly($axRow['coup_StartDateSpecial']).' - '.DateOnly($axRow['coup_EndDateSpecial']);
							
					} else if ($axRow['coup_StartDate']!='0000-00-00' && $axRow['coup_EndDate']!='0000-00-00') {
								
						$period = DateOnly($axRow['coup_StartDate']).' - '.DateOnly($axRow['coup_EndDate']);

					} else { $period = '-'; }

					$remaining = '';

					$time_all = 0;
					$status_all = "T";

					if ($axRow['coup_Repetition']=='T') {

						$remaining = $axRow['coup_Qty'].' Per '.$axRow['coup_QtyPer'];

						if ($axRow['coup_QtyPer'] == 'Not') {

							$count_all = 0;
							$sql_count = 'SELECT COUNT(meco_MemberCouponID)
												FROM member_coupon_trans
												WHERE coup_CouponID="'.$axRow['id'].'"
												AND card_CardID="'.$card['card_id'].'"
												AND meco_Deleted=""';
							$count_all = $oDB->QueryOne($sql_count);
							if ($count_all=="") { $count_all = 0; }
							$time_all = $axRow['coup_Qty']-$count_all;

							if ($count_all == $axRow['coup_Qty']) { $status_all = "F"; }
						}

					} else { $remaining = '-'; }

					$time_member = 0;
					$status_member = "T";

					if ($axRow['coup_RepetitionMember']=='T') {

						if ($axRow['coup_QtyPerMember'] == 'Not Specific') {

							$per_member = 'Person';

						} else { $per_member = $axRow['coup_QtyPerMember']; }

						$total = ($axRow['coup_QtyMember']*$count_card).' Times Per '.$per_member;

						if ($axRow['coup_QtyPerMember'] == 'Not Specific') {

							$count_member = 0;
							$sql_count = 'SELECT COUNT(meco_MemberCouponID)
												FROM member_coupon_trans
												WHERE coup_CouponID="'.$axRow['id'].'"
												AND mere_MemberRegisterID IN ('.$register_id.')
												AND meco_Deleted=""';
							$count_member = $oDB->QueryOne($sql_count);
							if ($count_member=="") { $count_member = 0; }
									
							$time_member = ($axRow['coup_QtyMember']*$count_card)-$count_member;

							if ($count_member == $axRow['coup_QtyMember']*$count_card) { $status_member = "F"; }
						}

					} else { $total = '-'; }

					$status_qty = "F";

					if ($status_member == "F") {

						$rm_qty = "<span class='glyphicon glyphicon-remove'></span>";
							
					} else {

						if ($status_all == "F") {

							$rm_qty = "<span class='glyphicon glyphicon-remove'></span>";
								
						} else {

							if ($time_all!=0 && $time_member!=0) {

								if ($time_member>$time_all) { $rm_qty = $time_all; }
								else { $rm_qty = $time_member; }

								$status_qty = "T";

							} else {

								if ($time_all==0 && $time_member==0) { $rm_qty = "-"; }
								else if ($time_member==0) { $rm_qty = $time_all; $status_qty = "T"; }
								else { $rm_qty = $time_member; $status_qty = "T"; }
							}
						}
					}

					$motivation = '';

					if ($status_all!='F' && $status_member!='F') {

						if ($axRow['coup_Motivation']=='Point') {

							# POINT

							$sql_point = 'SELECT * FROM motivation_plan_point 
												WHERE mopp_MotivationPointID="'.$axRow['coup_MotivationID'].'"';
							$point_priv = $oDB->Query($sql_point);
							$point = $point_priv->FetchRow(DBI_ASSOC);
	 
							$motivation = $point['mopp_UseAmount'].' ฿ / '.$point['mopp_PointQty'].' Point Qty ('.$point['mopp_Method'].')';

							$table_point .= '<tr>
					        					<td width="5%" rowspan="2" style="text-align:center">
					        						<input type="checkbox" name="pc[]" value="'.$axRow['id'].'"></td>
						        				<td width="160px" rowspan="2" style="text-align:center">'.$image.'</td>
						        				<td rowspan="2" style="text-align:right;line-height:170%" width="90px">
						        					<b>Name<br>
						        					Type<br>
						        					Remaining<br>
						       						Total Limited<br>
						       						Period<br>
						       						Motivation</b></td>
						       					<td rowspan="2" style="line-height:170%">
						       						'.$axRow['name'].'<br>
						       						Coupon<br>
						       						'.$remaining.'<br>
						       						'.$total.'<br>
					        						'.$period.'<br>
					        						'.$motivation.'</td>
					        					<td width="120px"><input type="text" style="width:120px" class="form-control text-md" placeholder="Receieve No." name="crecieve_'.$axRow['id'].'"></td>
					        					<td width="250px" rowspan="2" style="text-align:center">
					        						<span class="form-inline">
									                    <select id="date_'.$axRow['id'].'" class="form-control text-md" name="date_'.$axRow['id'].'" style="width:65px">
									            	        '.$option_date.'
								                    	</select>
									                    <select id="month_'.$axRow['id'].'" class="form-control text-md" name="month_'.$axRow['id'].'" style="width:70px">
									                    	'.$option_month.'
									                    </select>
									                    <select id="year_'.$axRow['id'].'" class="form-control text-md" name="year_'.$axRow['id'].'" style="width:80px">
									            	        '.$option_year.'
									                    </select></span><br>
						        					<span class="form-inline">
									                    <select id="hour_'.$axRow['id'].'" class="form-control text-md" name="hour_'.$axRow['id'].'" style="width:65px">
									                	    '.$option_hour.'
									                    </select>
									                    &nbsp : &nbsp;
									                    <select id="min_'.$axRow['id'].'" class="form-control text-md" name="min_'.$axRow['id'].'" style="width:70px">
									                    '.$option_min.'
									                    </select></span></td>
						        			</tr>
						        			<tr><td><input type="number" style="width:120px" class="form-control text-md" placeholder="Amount" name="camount_'.$axRow['id'].'"></td></tr>';

						} else if ($axRow['coup_Motivation']=='Stamp') {

							# OPTION QTY

							if ($status_qty=="T") {
									
								$option_qty = '<select class="form-control text-md" name="sctime_'.$axRow['id'].'" style="width:80px">';

								for ($i=1; $i <= $rm_qty ; $i++) { 

									$option_qty .= '<option value="'.$i.'">'.$i.'</option>';
								}

								$option_qty .= '</select>';

							} else {
									
								$option_qty = '<input type="number" style="width:80px" class="form-control text-md" value="1" name="sctime_'.$axRow['id'].'">';
							}

							# STAMP

							$sql_stamp = 'SELECT motivation_plan_stamp.*,
												collection_type.coty_Image
												FROM motivation_plan_stamp
												LEFT JOIN collection_type
												ON collection_type.coty_CollectionTypeID = motivation_plan_stamp.mops_CollectionTypeID
												WHERE motivation_plan_stamp.mops_MotivationStampID="'.$axRow['coup_MotivationID'].'"';
							$stamp_priv = $oDB->Query($sql_stamp);
							$stamp = $stamp_priv->FetchRow(DBI_ASSOC);

							$motivation = $stamp['mops_StampQty'].' <img src="'.$_SESSION['path_upload_collection'].$stamp['coty_Image'].'" width="15" style="margin-bottom:7px"/> / '.$stamp['mops_TimeQty'].' Times';

							$table_stamp .= '<tr>
						       					<td width="5%" style="text-align:center">
						       						<input type="checkbox" name="sc[]" value="'.$axRow['id'].'"></td>
						       					<td width="160px" style="text-align:center">'.$image.'</td>
						       					<td style="text-align:right;line-height:170%" width="90px">
						       						<b>Name<br>
						       						Type<br>
						       						Remaining<br>
						       						Total Limited<br>
						       						Period<br>
						       						Motivation</b></td>
						       					<td style="line-height:170%">
						       						'.$axRow['name'].'<br>
						       						Coupon<br>
						       						'.$remaining.'<br>
						       						'.$total.'<br>
						       						'.$period.'<br>
						       						'.$motivation.'</td>
						       					<td width="80px" style="text-align:center">'.$rm_qty.'</td>
						       					<td width="80px">'.$option_qty.'</td>
						       					<td width="250px" style="text-align:center">
						       						<span class="form-inline">
									                    <select id="date_'.$axRow['id'].'" class="form-control text-md" name="date_'.$axRow['id'].'" style="width:65px">
										                    '.$option_date.'
									                    </select>
									                    <select id="month_'.$axRow['id'].'" class="form-control text-md" name="month_'.$axRow['id'].'" style="width:70px">
										                    '.$option_month.'
									                    </select>
									                    <select id="year_'.$axRow['id'].'" class="form-control text-md" name="year_'.$axRow['id'].'" style="width:80px">
										                    '.$option_year.'
									                    </select></span><br>
						        					<span class="form-inline">
									                    <select id="hour_'.$axRow['id'].'" class="form-control text-md" name="hour_'.$axRow['id'].'" style="width:65px">
									                    '.$option_hour.'
									                    </select>
									                    &nbsp : &nbsp;
									                    <select id="min_'.$axRow['id'].'" class="form-control text-md" name="min_'.$axRow['id'].'" style="width:70px">
									                    '.$option_min.'
									                    </select></span></td>
						       				</tr>';
						} else {

							# OPTION QTY

							if ($status_qty=="T") {
									
								$option_qty = '<select class="form-control text-md" name="nctime_'.$axRow['id'].'" style="width:80px">';

								for ($i=1; $i <= $rm_qty ; $i++) { 

									$option_qty .= '<option value="'.$i.'">'.$i.'</option>';
								}

								$option_qty .= '</select>';

							} else {
									
								$option_qty = '<input type="number" style="width:80px" class="form-control text-md" value="1" name="nctime_'.$axRow['id'].'">';
							}

							$table_normal .= '<tr>
						       					<td width="5%" style="text-align:center">
						       						<input type="checkbox" name="nc[]" value="'.$axRow['id'].'"></td>
						       					<td width="160px" style="text-align:center">'.$image.'</td>
						       					<td style="text-align:right;line-height:170%" width="90px">
						       						<b>Name<br>
						       						Type<br>
						       						Remaining<br>
						       						Total Limited<br>
						       						Period</b></td>
						       					<td style="line-height:170%">
						       						'.$axRow['name'].'<br>
						       						Coupon<br>
						       						'.$remaining.'<br>
						       						'.$total.'<br>
						       						'.$period.'</td>
						       					<td width="80px" style="text-align:center">'.$rm_qty.'</td>
						       					<td width="80px">'.$option_qty.'</td>
						       					<td width="250px" style="text-align:center">
						       						<span class="form-inline">
									                    <select id="date_'.$axRow['id'].'" class="form-control text-md" name="date_'.$axRow['id'].'" style="width:65px">
										                    '.$option_date.'
									                    </select>
									                    <select id="month_'.$axRow['id'].'" class="form-control text-md" name="month_'.$axRow['id'].'" style="width:70px">
										                    '.$option_month.'
									                    </select>
									                    <select id="year_'.$axRow['id'].'" class="form-control text-md" name="year_'.$axRow['id'].'" style="width:80px">
										                    '.$option_year.'
									                    </select></span><br>
						        					<span class="form-inline">
									                    <select id="hour_'.$axRow['id'].'" class="form-control text-md" name="hour_'.$axRow['id'].'" style="width:65px">
										                    '.$option_hour.'
									                    </select>
									                    &nbsp : &nbsp;
									                    <select id="min_'.$axRow['id'].'" class="form-control text-md" name="min_'.$axRow['id'].'" style="width:70px">
										                    '.$option_min.'
									                    </select></span></td>
						        			</tr>';
						}
					}
				}

				# HBD

				$oRes_hbd = $oDB->Query($sql_hbd);
				while ($axRow = $oRes_hbd->FetchRow(DBI_ASSOC)){

					# BIRTHDAY COUPON DATA 

					if ($axRow['image']) {

				    	$image = '<img src="../../upload/'.$axRow['path_image'].$axRow['image'].'" height="100" class="image_border"/>';

					} else {
							                    	
					    $image = '<img src="../../images/card_privilege.jpg" height="100" class="image_border" />';
					}

					# TIME USE

					if ($count_card == 1) {

						$time_use = '<b>1</b>';

					} else {

						$time_use = '<input type="number" style="width:80px" class="form-control text-md" value="1" name="nhtime_'.$axRow['id'].'">';
					}

					$motivation = '';

					if ($axRow['coup_Motivation']=='Point') {

						# POINT

						$sql_point = 'SELECT * FROM motivation_plan_point 
										WHERE mopp_MotivationPointID="'.$axRow['coup_MotivationID'].'"';
						$point_priv = $oDB->Query($sql_point);
						$point = $point_priv->FetchRow(DBI_ASSOC);

						$motivation = $point['mopp_UseAmount'].' ฿ / '.$point['mopp_PointQty'].' Point Qty ('.$point['mopp_Method'].')';

						$table_point .= '<tr>
				        					<td width="5%" rowspan="2" style="text-align:center">
				        						<input type="checkbox" name="ph[]" value="'.$axRow['id'].'"></td>
				        					<td width="160px" rowspan="2" style="text-align:center">'.$image.'</td>
				        					<td rowspan="2" style="text-align:right;line-height:170%" width="90px">
				        						<b>Name<br>
				        						Type<br>
				        						Period<br>
				        						Motivation</b></td>
				        					<td rowspan="2" style="line-height:170%">
				        						'.$axRow['name'].'<br>
				        						Birthday Coupon<br>
				        						'.$count_card.' Times Per '.$axRow['coup_Method'].'<br>
				        						'.$motivation.'</td>
				        					<td width="120px"><input type="text" style="width:120px" class="form-control text-md" placeholder="Receieve No." name="hrecieve_'.$axRow['id'].'"></td>
				        					<td width="250px" rowspan="2" style="text-align:center">
				        						<span class="form-inline">
								                    <select id="date_'.$axRow['id'].'" class="form-control text-md" name="date_'.$axRow['id'].'" style="width:65px">
									                    '.$option_date.'
								                    </select>
								                    <select id="month_'.$axRow['id'].'" class="form-control text-md" name="month_'.$axRow['id'].'" style="width:70px">
									                    '.$option_month.'
								                    </select>
								                    <select id="year_'.$axRow['id'].'" class="form-control text-md" name="year_'.$axRow['id'].'" style="width:80px">
									                    '.$option_year.'
								                    </select></span><br>
				        						<span class="form-inline">
								                    <select id="hour_'.$axRow['id'].'" class="form-control text-md" name="hour_'.$axRow['id'].'" style="width:65px">
									                    '.$option_hour.'
								                    </select>
								                    &nbsp : &nbsp;
								                    <select id="min_'.$axRow['id'].'" class="form-control text-md" name="min_'.$axRow['id'].'" style="width:70px">
									                    '.$option_min.'
								                    </select></span></td>
				        				</tr>
				        				<tr><td><input type="number" style="width:120px" class="form-control text-md" placeholder="Amount" name="hamount_'.$axRow['id'].'"></td></tr>';

					} else if ($axRow['coup_Motivation']=='Stamp') {

						# STAMP

						$sql_stamp = 'SELECT motivation_plan_stamp.*,
											collection_type.coty_Image
										FROM motivation_plan_stamp
										LEFT JOIN collection_type
										ON collection_type.coty_CollectionTypeID = motivation_plan_stamp.mops_CollectionTypeID
										WHERE motivation_plan_stamp.mops_MotivationStampID="'.$axRow['coup_MotivationID'].'"';
						$stamp_priv = $oDB->Query($sql_stamp);
						$stamp = $stamp_priv->FetchRow(DBI_ASSOC);

						$motivation = $stamp['mops_StampQty'].' <img src="'.$_SESSION['path_upload_collection'].$stamp['coty_Image'].'" width="15" style="margin-bottom:7px"/> / '.$stamp['mops_TimeQty'].' Times';

						$table_stamp .= '<tr>
				        					<td width="5%" style="text-align:center">
				        						<input type="checkbox" name="sh[]" value="'.$axRow['id'].'"></td>
				        					<td width="160px" style="text-align:center">'.$image.'</td>
				        					<td rowspan="2" style="text-align:right;line-height:170%" width="90px">
				        						<b>Name<br>
				        						Type<br>
				        						Period<br>
				        						Motivation</b></td>
				        					<td rowspan="2" style="line-height:170%">
				        						'.$axRow['name'].'<br>
				        						Birthday Coupon<br>
				        						'.$count_card.' Times Per '.$axRow['coup_Method'].'<br>
				        						'.$motivation.'</td>
				        					<td width="80px" style="text-align:center">-</td>
				        					<td width="80px" style="text-align:center">'.$time_use.'</td>
				        					<td width="250px" style="text-align:center">
				        						<span class="form-inline">
								                    <select id="date_'.$axRow['id'].'" class="form-control text-md" name="date_'.$axRow['id'].'" style="width:65px">
									                    '.$option_date.'
								                    </select>
								                    <select id="month_'.$axRow['id'].'" class="form-control text-md" name="month_'.$axRow['id'].'" style="width:70px">
									                    '.$option_month.'
								                    </select>
								                    <select id="year_'.$axRow['id'].'" class="form-control text-md" name="year_'.$axRow['id'].'" style="width:80px">
									                    '.$option_year.'
								                    </select></span><br>
				        						<span class="form-inline">
								                    <select id="hour_'.$axRow['id'].'" class="form-control text-md" name="hour_'.$axRow['id'].'" style="width:65px">
									                    '.$option_hour.'
								                    </select>
								                    &nbsp : &nbsp;
								                    <select id="min_'.$axRow['id'].'" class="form-control text-md" name="min_'.$axRow['id'].'" style="width:70px">
									                    '.$option_min.'
								                    </select></span></td>
				        				</tr>';
					} else {

						$table_normal .= '<tr>
				        					<td width="5%" style="text-align:center">
				        						<input type="checkbox" name="nh[]" value="'.$axRow['id'].'"></td>
				        					<td width="160px" style="text-align:center">'.$image.'</td>
				        					<td style="text-align:right;line-height:170%" width="90px">
				        						<b>Name<br>
				        						Type<br>
				        						Period</b></td>
				        					<td style="line-height:170%">
				        						'.$axRow['name'].'<br>
				        						Birthday Coupon<br>
				        						'.$count_card.' Times Per '.$axRow['coup_Method'].'</td>
				        					<td width="80px" style="text-align:center">-</td>
				        					<td width="80px" style="text-align:center">'.$time_use.'</td>
				        					<td width="250px" style="text-align:center">
				        						<span class="form-inline">
								                    <select id="date_'.$axRow['id'].'" class="form-control text-md" name="date_'.$axRow['id'].'" style="width:65px">
									                    '.$option_date.'
								                    </select>
								                    <select id="month_'.$axRow['id'].'" class="form-control text-md" name="month_'.$axRow['id'].'" style="width:70px">
									                    '.$option_month.'
								                    </select>
								                    <select id="year_'.$axRow['id'].'" class="form-control text-md" name="year_'.$axRow['id'].'" style="width:80px">
									                    '.$option_year.'
								                    </select></span><br>
				        						<span class="form-inline">
								                    <select id="hour_'.$axRow['id'].'" class="form-control text-md" name="hour_'.$axRow['id'].'" style="width:65px">
									                    '.$option_hour.'
								                    </select>
								                    &nbsp : &nbsp;
								                    <select id="min_'.$axRow['id'].'" class="form-control text-md" name="min_'.$axRow['id'].'" style="width:70px">
									                    '.$option_min.'
								                    </select></span></td>
				        				</tr>';
					}
				}

				# ACTIVITY

				$oRes_acti = $oDB->Query($sql_acti);
				while ($axRow = $oRes_acti->FetchRow(DBI_ASSOC)){

					# CHECK ACTIVITY USE

					$acti_Method = $axRow["acti_Method"];
					$acti_StartDate = $axRow["acti_StartDate"];
					$acti_EndDate = $axRow["acti_EndDate"];
					$acti_StartTime = $axRow["acti_StartTime"];
					$acti_EndTime = $axRow["acti_EndTime"];
					$acti_StartDateSpecial = $axRow["acti_StartDateSpecial"];
					$acti_EndDateSpecial = $axRow["acti_EndDateSpecial"];
					$acti_QtyPerMember = $axRow["acti_QtyPerMember"];
					$acti_RepetitionMember = $axRow["acti_RepetitionMember"];
					$acti_QtyMember = $axRow["acti_QtyMember"];
					$acti_QtyPerMemberData = $axRow["acti_QtyPerMemberData"];
					$acti_QtyPer = $axRow["acti_QtyPer"];
					$acti_Repetition = $axRow["acti_Repetition"];
					$acti_Qty = $axRow["acti_Qty"];
					$acti_QtyPerData = $axRow["acti_QtyPerData"];
					$acti_TotalQty = $axRow["acti_TotalQty"];
					$acti_MaxQty = $axRow["acti_MaxQty"];
					$acti_Reservation = $axRow["acti_Reservation"];
					$acti_StartDateReservation = $axRow["acti_StartDateReservation"];
					$acti_EndDateReservation = $axRow["acti_EndDateReservation"];
					$acti_StartTimeReservation = $axRow["acti_StartTimeReservation"];
					$acti_EndTimeReservation = $axRow["acti_EndTimeReservation"];

					# ACTIVITY DATA

					if ($axRow['image']) {

				    	$image = '<img src="../../upload/'.$axRow['path_image'].$axRow['image'].'" height="100" class="image_border"/>';

					} else {
							                    	
					    $image = '<img src="../../images/card_privilege.jpg" height="100" class="image_border" />';
					}

					$activity = '';

					if ($axRow['acti_StartDate']!='0000-00-00' && $axRow['acti_EndDate']!='0000-00-00') {
								
						$activity = DateOnly($axRow['acti_StartDate']).' - '.DateOnly($axRow['acti_EndDate']);
							
					} else { $activity = '-'; }

					$time = '';

					if ($axRow['acti_StartTime']!='00:00:00' && $axRow['acti_EndTime']!='00:00:00') {
								
						$time = TimeOnly($axRow['acti_StartTime']).' - '.TimeOnly($axRow['acti_EndTime']);
							
					} else { $time = '-'; }

					$reservation = '';

					if ($axRow['acti_StartDateReservation']!='0000-00-00' && $axRow['acti_EndDateReservation']!='0000-00-00') {
								
						$reservation = DateOnly($axRow['acti_StartDateReservation']).' - '.DateOnly($axRow['acti_EndDateReservation']);
							
					} else { $reservation = '-'; }

					$motivation = '';

					if ($axRow['acti_Motivation']=='Point') {

						# POINT

						$sql_point = 'SELECT * FROM motivation_plan_point 
										WHERE mopp_MotivationPointID="'.$axRow['acti_MotivationID'].'"';
						$point_priv = $oDB->Query($sql_point);
						$point = $point_priv->FetchRow(DBI_ASSOC);

						$motivation = $point['mopp_UseAmount'].' ฿ / '.$point['mopp_PointQty'].' Point Qty ('.$point['mopp_Method'].')';

						$table_point .= '<tr>
				        					<td width="5%" rowspan="2" style="text-align:center">
				        						<input type="checkbox" name="pa[]" value="'.$axRow['id'].'"></td>
				        					<td width="160px" rowspan="2" style="text-align:center">'.$image.'</td>
				        					<td rowspan="2" style="text-align:right;line-height:170%" width="90px">
				        						<b>Name<br>
				        						Type<br>
				        						Activity<br>
				        						Time<br>
				        						Reservation<br>
				        						Motivation</b></td>
				        					<td rowspan="2" style="line-height:170%">
				        						'.$axRow['name'].'<br>
				        						Activity<br>
				        						'.$activity.'<br>
				        						'.$time.'<br>
				        						'.$reservation.'<br>
				        						'.$motivation.'</td>
				        					<td width="120px"><input type="text" style="width:120px" class="form-control text-md" placeholder="Receieve No." name="arecieve_'.$axRow['id'].'"></td>
				        					<td width="250px" rowspan="2" style="text-align:center">
				        						<span class="form-inline">
								                    <select id="date_'.$axRow['id'].'" class="form-control text-md" name="date_'.$axRow['id'].'" style="width:65px">
									                    '.$option_date.'
								                    </select>
								                    <select id="month_'.$axRow['id'].'" class="form-control text-md" name="month_'.$axRow['id'].'" style="width:70px">
									                    '.$option_month.'
								                    </select>
								                    <select id="year_'.$axRow['id'].'" class="form-control text-md" name="year_'.$axRow['id'].'" style="width:80px">
									                    '.$option_year.'
								                    </select></span><br>
				        						<span class="form-inline">
								                    <select id="hour_'.$axRow['id'].'" class="form-control text-md" name="hour_'.$axRow['id'].'" style="width:65px">
									                    '.$option_hour.'
								                    </select>
								                    &nbsp : &nbsp;
								                    <select id="min_'.$axRow['id'].'" class="form-control text-md" name="min_'.$axRow['id'].'" style="width:70px">
									                    '.$option_min.'
								                    </select></span></td>
				        				</tr>
				        				<tr><td><input type="number" style="width:120px" class="form-control text-md" placeholder="Amount" name="aamount_'.$axRow['id'].'"></td></tr>';

					} else if ($axRow['acti_Motivation']=='Stamp') {

						# STAMP

						$sql_stamp = 'SELECT motivation_plan_stamp.*,
											collection_type.coty_Image
											FROM motivation_plan_stamp
											LEFT JOIN collection_type
											ON collection_type.coty_CollectionTypeID = motivation_plan_stamp.mops_CollectionTypeID
											WHERE motivation_plan_stamp.mops_MotivationStampID="'.$axRow['acti_MotivationID'].'"';
						$stamp_priv = $oDB->Query($sql_stamp);
						$stamp = $stamp_priv->FetchRow(DBI_ASSOC);

						$motivation = $stamp['mops_StampQty'].' <img src="'.$_SESSION['path_upload_collection'].$stamp['coty_Image'].'" width="15" style="margin-bottom:7px"/> / '.$stamp['mops_TimeQty'].' Times';

						$table_stamp .= '<tr>
				        					<td width="5%" style="text-align:center">
				        						<input type="checkbox" name="sa[]" value="'.$axRow['id'].'"></td>
				        					<td width="160px" style="text-align:center">'.$image.'</td>
				        					<td style="text-align:right;line-height:170%" width="90px">
				        						<b>Name<br>
				        						Type<br>
				        						Activity<br>
				        						Time
				        						Reservation<br>
				        						Motivation</b></td>
				        					<td style="line-height:170%">
				        						'.$axRow['name'].'<br>
				        						Activity<br>
				        						'.$activity.'<br>
				        						'.$time.'<br>
				        						'.$reservation.'<br>
				        						'.$motivation.'</td>
				        					<td style="text-align:center">-</td>
				        					<td style="text-align:center">1</td>
				        					<td width="250px" style="text-align:center">
				        						<span class="form-inline">
								                    <select id="date_'.$axRow['id'].'" class="form-control text-md" name="date_'.$axRow['id'].'" style="width:65px">
									                    '.$option_date.'
								                    </select>
								                    <select id="month_'.$axRow['id'].'" class="form-control text-md" name="month_'.$axRow['id'].'" style="width:70px">
									                    '.$option_month.'
								                    </select>
								                    <select id="year_'.$axRow['id'].'" class="form-control text-md" name="year_'.$axRow['id'].'" style="width:80px">
									                    '.$option_year.'
								                    </select></span><br>
				        						<span class="form-inline">
								                    <select id="hour_'.$axRow['id'].'" class="form-control text-md" name="hour_'.$axRow['id'].'" style="width:65px">
									                    '.$option_hour.'
								                    </select>
								                    &nbsp : &nbsp;
								                    <select id="min_'.$axRow['id'].'" class="form-control text-md" name="min_'.$axRow['id'].'" style="width:70px">
									                    '.$option_min.'
								                    </select></span></td>
				        				</tr>';
					} else {

						$table_normal .= '<tr>
				        					<td width="5%" style="text-align:center">
				        						<input type="checkbox" name="na[]" value="'.$axRow['id'].'"></td>
				        					<td width="160px" style="text-align:center">'.$image.'</td>
				        					<td style="text-align:right;line-height:170%" width="90px">
				        						<b>Name</b><br>
				        						<b>Type</b><br>
				        						<b>Activity</b><br>
				        						<b>Time</b>
				        						<b>Reservation</b><br></td>
				        					<td style="line-height:170%">
				        						'.$axRow['name'].'<br>
				        						Activity<br>
				        						'.$activity.'<br>
				        						'.$time.'<br>
				        						'.$reservation.'</td>
				        					<td style="text-align:center">-</td>
				        					<td style="text-align:center"><b>1</b></td>
				        					<td width="250px" style="text-align:center">
				        						<span class="form-inline">
								                    <select id="date_'.$axRow['id'].'" class="form-control text-md" name="date_'.$axRow['id'].'" style="width:65px">
									                    '.$option_date.'
								                    </select>
								                    <select id="month_'.$axRow['id'].'" class="form-control text-md" name="month_'.$axRow['id'].'" style="width:70px">
									                    '.$option_month.'
								                    </select>
								                    <select id="year_'.$axRow['id'].'" class="form-control text-md" name="year_'.$axRow['id'].'" style="width:80px">
									                    '.$option_year.'
								                    </select></span><br>
				        						<span class="form-inline">
								                    <select id="hour_'.$axRow['id'].'" class="form-control text-md" name="hour_'.$axRow['id'].'" style="width:65px">
									                    '.$option_hour.'
								                    </select>
								                    &nbsp : &nbsp;
								                    <select id="min_'.$axRow['id'].'" class="form-control text-md" name="min_'.$axRow['id'].'" style="width:70px">
									                    '.$option_min.'
								                    </select></span></td>
				        				</tr>';
					}
				}

				if ($table_normal || $table_point || $table_stamp) {

					$html .= '<span id="card_privilege"><hr><label class="adj_row">Choose Branch</label><br>';

					# BRANCH

					$html .= '<span class="form-inline">
								<label>Branch <span class="text-rq">*</span>&nbsp;&nbsp;&nbsp;</label>

			                    <select id="branch_id" name="branch_id" class="form-control text-md" required autofocus>';

			        if ($_SESSION['user_type_id_ses']>2) {

			        	$where_branch = ' AND branch_id = "'.$_SESSION['user_branch_id'].'"';
				        
			        } else { 

			        	$where_branch = ''; 
			        	$html .= '<option value="">Please Select ..</option>';
			        }

					$sql_branch = 'SELECT branch_id, name FROM mi_branch WHERE brand_id="'.$card['bran_BrandID'].'"'.$where_branch;

					$oRes_branch = $oDB->Query($sql_branch);
					while ($axRow = $oRes_branch->FetchRow(DBI_ASSOC)){

						$html .= '<option value="'.$axRow['branch_id'].'">'.$axRow['name'].'</option>';
					}

					$html .= '</select>
			                    </span><br><br>

	    						<span class="form-inline">
	 	    						<label>Entry Date&nbsp;&nbsp;&nbsp;</label>
	      								<input type="text" style="text-align:center" class="form-control text-md" value="'.date('d M o - H:i').'" disabled>
	      						</span>
	   							<br><br>';

					# NORMAL

					if ($table_normal) {

						$html .= '<table class="table table-striped table-bordered">
			        				<tr>
			        					<td colspan="7"><b>Normal Privilege</b></td>
			        				</tr>
			        				<tr class="th_table">
			        					<td width="5%"><b>Select</b></td>
			        					<td width="160px"><b>Privilege</b></td>
			        					<td colspan="2"><b>Detail</b></td>
			        					<td><b>Remaining</b></td>
			        					<td><b>Qty</b></td>
					        			<td><b>Use Date / Collect Date</b></td>
			        				</tr>
			        				'.$table_normal.'
			        			</table>';
			        }

					# POINT

					if ($table_point) {

						$html .= '<table class="table table-striped table-bordered">
			        				<tr>
			        					<td colspan="6"><b>Point Collect Privilege</b></td>
			        				</tr>
			        				<tr class="th_table">
			        					<td width="5%"><b>Select</b></td>
			        					<td width="160px"><b>Privilege</b></td>
			        					<td colspan="2"><b>Detail</b></td>
					        			<td><b>Point Collect</b></td>
					        			<td><b>Use Date / Collect Date</b></td>
			        				</tr>
			        				'.$table_point.'
			        			</table>';
			        }

					# STAMP

					if ($table_stamp) {

						$html .= '<table class="table table-striped table-bordered">
			        				<tr>
			        					<td colspan="7"><b>Stamp Collect Privilege</b></td>
			        				</tr>
			        				<tr class="th_table">
			        					<td width="5%"><b>Select</b></td>
			        					<td width="160px"><b>Privilege</b></td>
			        					<td colspan="2"><b>Detail</b></td>
			        					<td><b>Remaining</b></td>
			        					<td><b>Qty</b></td>
					        			<td><b>Use Date / Collect Date</b></td>
			        				</tr>
			        				'.$table_stamp.'
			        			</table>';
			        }

			        $html .= '<button class="btn btn-success btn_hide" type="submit" id="submit_create">SUBMIT</button>

			                <input type="hidden" id="act" name="act" value="save" />
			                <input type="hidden" id="member_id" name="member_id" value="'.$member_id.'" />
			                <input type="hidden" id="card_id" name="card_id" value="'.$card_id.'" />
			                <input type="hidden" id="register_id" name="register_id" value="'.$register_id.'" />

			                &nbsp;&nbsp;&nbsp;';

			        $html .= '<button class="btn btn-warning btn_hide" type="reset" onclick="window.location.href='."'".'privilege.php'."'".'">CANCEL</button>';
					
				} else {

					$html .= '<span id="card_privilege"><hr><label class="adj_row">No Privilege Can Use</label><br>';
				}

			} else {

				$html .= '<hr><b>No Privilege Register</b>';

			}
		}

		$html .= '</center>';

		echo $html;
	}

	exit;
}

else if($TASK == 'Get_CustomField'){

	$bran_BrandID = $_REQUEST['bran_BrandID'];

	$rewa_RewardID = $_REQUEST['rewa_RewardID'];

	if ($bran_BrandID!='') {

		$html = '';

		$sql_custom = 'SELECT * 
						FROM custom_field 
						WHERE bran_BrandID="'.$bran_BrandID.'" 
						AND cufi_Deleted=""
						AND fity_FieldTypeID IN (3,4,5)';
		$oRes_custom = $oDB->Query($sql_custom);
		while ($custom = $oRes_custom->FetchRow(DBI_ASSOC)){

			# LIST VALUE

			$sql_value = 'SELECT * 
							FROM custom_list_value
							WHERE cufi_CustomFieldID="'.$custom['cufi_CustomFieldID'].'"';
			$oRes_value = $oDB->Query($sql_value);

			# DATA TARGET

			$sql_target = 'SELECT reta_Target
							FROM reward_target
							WHERE cufi_CustomFieldID="'.$custom['cufi_CustomFieldID'].'"
							AND rewa_RewardID="'.$rewa_RewardID.'"
							AND reta_Deleted=""';
			$target = $oDB->QueryOne($sql_target);

			if ($target) { $check = "checked"; }
			else { $check = ""; }

			$html .= '
						<div class="adj_row">
							<label class="lable-form"></label>
							<span class="form-inline">
							<table>
								<tr>
									<td width="150px"><input type="checkbox" name="'.$custom['cufi_FieldName'].'_check" id="'.$custom['cufi_FieldName'].'_check" value="1" '.$check.' ><b> '.$custom['cufi_Name'].'</b></td>
									<td><select id="'.$custom['cufi_FieldName'].'" class="form-control text-md" style="margin-left:10px" name="'.$custom['cufi_FieldName'].'">
		                        		<option value="">Please Select ..</option>';
				
			while ($value = $oRes_value->FetchRow(DBI_ASSOC)){

				if ($target == $value['clva_CustomListValueID']) { $select= "selected"; }
				else { $select = ""; }

				$html .= '<option value="'.$value['clva_CustomListValueID'].'" '.$select.'>'.$value['clva_Name'].'</option>';

			}
    
		    $html .= '</select></td></tr></table></span></div>';
		}

		echo $html;
	}	

	exit;
}

else if($TASK == 'Get_CustomPrivilege'){

	$bran_BrandID = $_REQUEST['bran_BrandID'];

	$cufo_PrivilegeID = $_REQUEST['cufo_PrivilegeID'];

	$cufo_Type = $_REQUEST['cufo_Type'];

	$html = '';

	if ($bran_BrandID!='') {

		$sql_custom = 'SELECT * 
						FROM custom_field 
						WHERE bran_BrandID="'.$bran_BrandID.'" 
						AND cufi_Type="Privilege"
						AND cufi_Deleted=""';
		$check_custom = $oDB->QueryOne($sql_custom);

		if ($check_custom) {

			$oRes_custom = $oDB->Query($sql_custom);
			while ($custom = $oRes_custom->FetchRow(DBI_ASSOC)){

				# DATA

				$sql_data = 'SELECT cufo_CustomFormID
								FROM custom_form_privilege
								WHERE cufi_CustomFieldID="'.$custom['cufi_CustomFieldID'].'"
								AND cufo_PrivilegeID="'.$cufo_PrivilegeID.'"
								AND cufo_Type="'.$cufo_Type.'"';
				$data_id = $oDB->QueryOne($sql_data);

				$sql_data = 'SELECT cufo_Deleted
								FROM custom_form_privilege
								WHERE cufi_CustomFieldID="'.$custom['cufi_CustomFieldID'].'"
								AND cufo_PrivilegeID="'.$cufo_PrivilegeID.'"
								AND cufo_Type="'.$cufo_Type.'"';
				$data = $oDB->QueryOne($sql_data);

				if ($data_id && $data!='T') { $check = "checked"; }
				else { $check = ""; }

				$html .= '
							<div class="adj_row">
								<label class="lable-form">
									<input type="checkbox" name="'.$custom['cufi_FieldName'].'_check" value="1" '.$check.'></label>
								<label style="margin-top:8px">'.$custom['cufi_Name'].'</lable>
							</div>';
			}

		} else {

			$html = '<center><b>not have Custom Field</b></center>';
		}
	}	

	echo $html;

	exit;
}

else if($TASK == 'Get_Country'){

	$country_id = $_REQUEST['country_id'];

	$province_id = $_REQUEST['province_id'];

	if ($country_id!='') {

		$option = '';

		$sql_province = 'SELECT * 
						FROM province 
						WHERE coun_CountryID="'.$country_id.'" 
						AND prov_Deleted!="T"
						ORDER BY prov_Name';
		$oRes_province = $oDB->Query($sql_province);

		while ($province = $oRes_province->FetchRow(DBI_ASSOC)){

			if ($province_id == $province['prov_ProvinceID']) { $select= "selected"; }
			else { $select = ""; }
				
			$option .= '<option value="'.$province['prov_ProvinceID'].'" '.$select.'>'.$province['prov_Name'].'</option>';
		}
	
	} else { $option = ''; }

	$html = '<select id="province_id" class="form-control text-md" name="province_id" onchange="ProvinceSelect()" required autofocus>
		    	<option value="">Please Select ..</option>
		    	'.$option.'
		    </select>';

	echo $html;	

	exit;
}

else if($TASK == 'Get_Province'){

	$region_id = $_REQUEST['region_id'];

	$province_id = $_REQUEST['province_id'];

	if ($region_id!='') {

		$option = '';

		$sql_province = 'SELECT * 
						FROM province 
						WHERE prov_RegionID="'.$region_id.'" 
						AND prov_Deleted!="T"
						ORDER BY prov_Name';
		$oRes_province = $oDB->Query($sql_province);

		while ($province = $oRes_province->FetchRow(DBI_ASSOC)){

			if ($province_id == $province['prov_ProvinceID']) { $select= "selected"; }
				else { $select = ""; }
				
			$option .= '<option value="'.$province['prov_ProvinceID'].'" '.$select.'>'.$province['prov_Name'].'</option>';
		}
	
	} else { $option = '';}

	$html = '<select id="province_id" class="form-control text-md" name="province_id" onchange="ProvinceSelect()" required autofocus>
		    	<option value="">Please Select ..</option>
		    	'.$option.'
		    </select>';

	echo $html;	

	exit;
}

else if($TASK == 'Get_DistrictID'){

	$district_id = $_REQUEST['district_id'];

	$province_id = $_REQUEST['province_id'];

	if ($province_id!='') {

		$option = '';

		$sql_district = 'SELECT dist_DistrictID, dist_Name
						FROM district 
						WHERE dist_ProvinceID="'.$province_id.'" 
						AND dist_Deleted!="T"
						ORDER BY dist_Name';
		$oRes_district = $oDB->Query($sql_district);

		while ($district = $oRes_district->FetchRow(DBI_ASSOC)){

			if ($district_id == $district['dist_DistrictID']) { $select= "selected"; }
				else { $select = ""; }
				
			$option .= '<option value="'.$district['dist_DistrictID'].'" '.$select.'>'.$district['dist_Name'].'</option>';
		}
	
	} else { $option = '';}

	$html = '<select id="district" class="form-control text-md" name="district" onchange="DistrictSelect()" required autofocus>
		    	<option value="">Please Select ..</option>
		    	'.$option.'
		    </select>';

	echo $html;	

	exit;
}

else if($TASK == 'Get_District'){

	$district_id = $_REQUEST['district_id'];

	$province_id = $_REQUEST['province_id'];

	if ($province_id!='') {

		$option = '';

		$sql_district = 'SELECT * 
						FROM district 
						WHERE dist_ProvinceID="'.$province_id.'" 
						AND dist_Deleted!="T"
						ORDER BY dist_Name';
		$oRes_district = $oDB->Query($sql_district);

		while ($district = $oRes_district->FetchRow(DBI_ASSOC)){

			if ($district_id == $district['dist_Name']) { $select= "selected"; }
				else { $select = ""; }
				
			$option .= '<option value="'.$district['dist_Name'].'" '.$select.'>'.$district['dist_Name'].'</option>';
		}
	
	} else { $option = '';}

	$html = '<select id="district" class="form-control text-md" name="district" onchange="DistrictSelect()" required autofocus>
		    	<option value="">Please Select ..</option>
		    	'.$option.'
		    </select>';

	echo $html;	

	exit;
}

else if($TASK == 'Get_SubDistrictID'){

	$district_id = $_REQUEST['district_id'];

	$sub_id = $_REQUEST['sub_id'];

	if ($district_id) {

		$option = '';

		$sql_sub = 'SELECT sudi_Name,
							sudi_SubDistrictID
						FROM sub_district 
						WHERE sudi_DistrictID="'.$district_id.'" 
						AND sudi_Deleted!="T"
						ORDER BY sudi_Name';
		$oRes_sub = $oDB->Query($sql_sub);

		while ($sub = $oRes_sub->FetchRow(DBI_ASSOC)){

			if ($sub_id == $sub['sudi_SubDistrictID']) { $select= "selected"; }
			else { $select = ""; }
				
			$option .= '<option value="'.$sub['sudi_SubDistrictID'].'" '.$select.'>'.$sub['sudi_Name'].'</option>';
		}
	
	} else { $option = '';}

	$html = '<select id="sub_district" class="form-control text-md" name="sub_district" onchange="LandmarkSelect()" required autofocus>
		    	<option value="">Please Select ..</option>
		    	'.$option.'
		    </select>';

	echo $html;	

	exit;
}

else if($TASK == 'Get_SubDistrict'){

	$district_id = $_REQUEST['district_id'];

	$sub_id = $_REQUEST['sub_id'];

	if ($district_id!='undefined') {

		$sql_dis = "SELECT dist_DistrictID FROM district WHERE dist_Name='".$district_id."'";
		$dis_id = $oDB->QueryOne($sql_dis);

		$option = '';

		$sql_sub = 'SELECT * 
						FROM sub_district 
						WHERE sudi_DistrictID="'.$dis_id.'" 
						AND sudi_Deleted!="T"
						ORDER BY sudi_Name';
		$oRes_sub = $oDB->Query($sql_sub);

		while ($sub = $oRes_sub->FetchRow(DBI_ASSOC)){

			if ($sub_id == $sub['sudi_Name']) { $select= "selected"; }
				else { $select = ""; }
				
			$option .= '<option value="'.$sub['sudi_Name'].'" '.$select.'>'.$sub['sudi_Name'].'</option>';
		}
	
	} else { $option = '';}

	$html = '<select id="sub_district" class="form-control text-md" name="sub_district" required autofocus>
		    	<option value="">Please Select ..</option>
		    	'.$option.'
		    </select>';

	echo $html;	

	exit;
}

else if($TASK == 'Get_LandmarkID'){

	$sub_district_id = $_REQUEST['sub_district_id'];

	$landmark_id = $_REQUEST['landmark_id'];

	if ($sub_district_id) {

		$option = '';

		$sql_landmark = 'SELECT land_Name,
							land_LandmarkID
						FROM landmark 
						WHERE sudi_SubDistrictID="'.$sub_district_id.'" 
						AND land_Deleted!="T"
						ORDER BY land_Name';
		$oRes_landmark = $oDB->Query($sql_landmark);

		while ($landmark = $oRes_landmark->FetchRow(DBI_ASSOC)){

			if ($landmark_id == $landmark['land_LandmarkID']) { $select= "selected"; }
			else { $select = ""; }
				
			$option .= '<option value="'.$landmark['land_LandmarkID'].'" '.$select.'>'.$landmark['land_Name'].'</option>';
		}
	
	} else { $option = '';}

	if ($landmark_id == 0) { $select= "selected"; }
	else { $select = ""; }

	$html = '<select id="landmark_id" class="form-control text-md" name="landmark_id" onchange="AddressSelect()" required autofocus>
		    	<option value="0">Please Select ..</option>
		    	'.$option.'
		    </select>';

	echo $html;	

	exit;
}

else if($TASK == 'Get_Address'){

	$landmark_id = $_REQUEST['landmark_id'];
	$branch_id = $_REQUEST['branch_id'];

	$html = '';

	if ($landmark_id!=0) {

		$sql_landmark = 'SELECT landmark.land_AddressNo,
							landmark.land_Moo,
							landmark.land_Junction,
							landmark.land_Soi,
							landmark.land_Road,
							landmark.land_PostCode,
							landmark.land_Latitude,
							landmark.land_Longitude,
							landmark.land_Floor,
							mi_branch.landmark_floor,
							mi_branch.show_map
						FROM landmark 
						LEFT JOIN mi_branch
						ON mi_branch.landmark_id = landmark.land_LandmarkID
						WHERE landmark.land_LandmarkID="'.$landmark_id.'"';
		$oRes_landmark = $oDB->Query($sql_landmark);
		$landmark = $oRes_landmark->FetchRow(DBI_ASSOC);

		if ($landmark['show_map']=="T") { $check = "checked"; }
		else { $check = ""; }

		if ($landmark['land_Floor']!=0) {

			$html .= '<div class="adj_row">
						<label for="landmark_floor" class="lable-form">Floor <span class="text-rq">*</span></label>
						<select id="landmark_floor" class="form-control text-md" name="landmark_floor" required autofocus>
							<option value="">Please Select ..</option>';

			for ($i=1; $i <= $landmark['land_Floor']; $i++) { 

				if ($i == $landmark['landmark_floor']) { $select = 'selected'; }
				else { $select = ''; }

				$html .= '<option value="'.$i.'" '.$select.'>'.$i.'</option>';
			}

			$html .= '	</select>
					</div>';
		}

		$html .= '
				<div class="adj_row">
					<label for="address" class="lable-form">Address</label>
					<input type="text" id="address" name="address" value="'.$landmark['land_AddressNo'].'" class="form-control text-md" placeholder="Text" readonly> 
				</div>
				<div class="adj_row">
					<label for="district" class="lable-form">Moo</label> 
					<input type="text" id="moo" name="moo" value="'.$landmark['land_Moo'].'" class="form-control text-md" placeholder="Text" readonly>
				</div>
				<div class="adj_row">
					<label for="junction" class="lable-form">Junction</label> 
					<input type="text" id="junction" name="junction" value="'.$landmark['land_Junction'].'" class="form-control text-md" placeholder="Text" readonly>
				</div>
				<div class="adj_row">
					<label for="soi" class="lable-form">Soi</label> 
					<input type="text" id="soi" name="soi" value="'.$landmark['land_Soi'].'" class="form-control text-md" placeholder="Text" readonly>
				</div>
				<div class="adj_row">
					<label for="road" class="lable-form">Street</label> 
					<input type="text" id="road" name="road" value="'.$landmark['land_Road'].'" class="form-control text-md" placeholder="Text" readonly>
				</div>
				<div class="adj_row">
					<label for="postcode" class="lable-form">Postcode</label> 
					<input type="text" id="postcode" name="postcode" value="'.$landmark['land_PostCode'].'" class="form-control text-md" placeholder="Text" maxlength="5" onkeypress="CheckNum()" readonly>
				</div>
				<div class="adj_row">
					<label for="postcode" class="lable-form">Show Map</label> 
					<input type="checkbox" id="show_map" name="show_map" value="T" '.$check.'>
				</div>
				<div class="adj_row">
					<div id="map_canvas" style="margin-left:40%;width:250px;"></div>
				</div>
				<div class="adj_row">
					<label for="map_latitude" class="lable-form">Map Latitude</label> 
					<input type="text" id="map_latitude" name="map_latitude" value="'.$landmark['land_Latitude'].'" class="form-control text-md" placeholder="Number" readonly onkeypress="isNumber()">
				</div>
				<div class="adj_row">
					<label for="map_longitude" class="lable-form">Map Longitude</label>
					<input type="text" id="map_longitude" name="map_longitude" value="'.$landmark['land_Longitude'].'" class="form-control text-md" placeholder="Number" readonly onkeypress="isNumber()">
				</div>';
	} else {

		$sql_branch = 'SELECT address_no,
							moo,
							junction,
							soi,
							road,
							postcode,
							show_map,
							map_latitude,
							map_longitude
						FROM mi_branch
						WHERE branch_id="'.$branch_id.'"';
		$oRes_branch = $oDB->Query($sql_branch);
		$branch = $oRes_branch->FetchRow(DBI_ASSOC);

		if ($branch['show_map']=="T") { $check = "checked"; }
		else { $check = ""; }

		$html = '<div class="adj_row">
					<label for="address" class="lable-form">Address <span class="text-rq">*</span></label>
					<input type="text" id="address" name="address" value="'.$branch['address_no'].'" class="form-control text-md" placeholder="Text" required autofocus> 
				</div>
				<div class="adj_row">
					<label for="district" class="lable-form">Moo</label> 
					<input type="text" id="moo" name="moo" value="'.$branch['moo'].'" class="form-control text-md" placeholder="Text">
				</div>
				<div class="adj_row">
					<label for="junction" class="lable-form">Junction</label> 
					<input type="text" id="junction" name="junction" value="'.$branch['junction'].'" class="form-control text-md" placeholder="Text">
				</div>
				<div class="adj_row">
					<label for="soi" class="lable-form">Soi</label> 
					<input type="text" id="soi" name="soi" value="'.$branch['soi'].'" class="form-control text-md" placeholder="Text">
				</div>
				<div class="adj_row">
					<label for="road" class="lable-form">Street</label> 
					<input type="text" id="road" name="road" value="'.$branch['road'].'" class="form-control text-md" placeholder="Text">
				</div>
				<div class="adj_row">
					<label for="postcode" class="lable-form">Postcode</label> 
					<input type="text" id="postcode" name="postcode" value="'.$branch['postcode'].'" class="form-control text-md" placeholder="Text" maxlength="5" onkeypress="CheckNum()">
				</div>
				<div class="adj_row">
					<label for="postcode" class="lable-form">Show Map</label> 
					<input type="checkbox" id="show_map" name="show_map" value="T" '.$check.'>
				</div>
				<div class="adj_row">
					<div id="map_canvas" style="margin-left:40%;width:250px;"></div>
				</div>
				<div class="adj_row">
					<label for="map_latitude" class="lable-form">Map Latitude <span class="text-rq">*</span></label> 
					<input type="text" id="map_latitude" name="map_latitude" value="'.$branch['map_latitude'].'" class="form-control text-md" placeholder="Number" required onkeypress="isNumber()">
				</div>
				<div class="adj_row">
					<label for="map_longitude" class="lable-form">Map Longitude <span class="text-rq">*</span></label>
					<input type="text" id="map_longitude" name="map_longitude" value="'.$branch['map_longitude'].'" class="form-control text-md" placeholder="Number" required onkeypress="isNumber()">
				</div>';
	}

	echo $html;	

	exit;
}

else if($TASK == 'Get_RandomCode'){

	$code_pre = $_REQUEST['code_pre'];
	$code_no = $_REQUEST['code_no'];

	$html = '<center><div style="width:200px">
			<table class="table table-bordered" style="background-color:white;text-align:center">
				<tr class="th_table">
					<td style="text-align:center" width="20px"><b>No</b></td>
					<td style="text-align:center" width="180px"><b>Code</b></td>
				</tr>';

	$_SESSION['random_code'] = "";

	for ($i=0; $i<$code_no ; $i++) { 

		$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz0123456789";
		$code = ''; 

		$code_str = 10;

		if ($code_pre) {

			$code_str = strlen($code_pre);
			$code_str = 10-$code_str;
		}

		$code = $code_pre . substr(str_shuffle($chars), 0, $code_str);

		$sql_get_code = 'SELECT spco_SpecialCode FROM special_code WHERE spco_SpecialCode='.$code;
		$get_code = $oDB->QueryOne($sql_get_code);

		while ($get_code) {

			$code = $code_pre . substr(str_shuffle($chars), 0, $code_str);

			$sql_get_code = 'SELECT spco_SpecialCode FROM special_code WHERE spco_SpecialCode='.$code;
			$get_code = $oDB->QueryOne($sql_get_code);
		}

		$_SESSION['random_code'][] = $code;

		$html .= '<tr>
					<td style="text-align:center">'.($i+1).'</td>
					<td style="text-align:center"><input type="text" style="width:180px" class="form-control text-md" value="'.$code.'" disabled></td>
					</tr>';
	}

	$html .= '</table></div></center>';

	echo $html;	

	exit;

} else if($TASK == 'Get_MotivationPlan'){

	$brand_id = $_REQUEST['brand_id'];
	$privilege_id = $_REQUEST['privilege_id'];
	$type = $_REQUEST['type'];
	$path = '';

	if ($privilege_id=='undefined') { $privilege_id = 0;	}

	if ($type=="p") { $path="priv_"; $table="privilege"; $path_id="priv_PrivilegeID"; } 
	else if ($type=="c") { $path="coup_"; $table="coupon"; $path_id="coup_CouponID"; } 
	else if ($type=="a") { $path="acti_"; $table="activity"; $path_id="acti_ActivityID"; }
	else { $path="coup_"; $table="hilight_coupon"; $path_id="coup_CouponID"; }

	if ($privilege_id!=0) {

		$plan_sql = "SELECT ".$path."Motivation AS plan_type, ".$path."MotivationID AS plan_id FROM ".$table." WHERE ".$path_id."=".$privilege_id;

		$get_plan = $oDB->Query($plan_sql);

		$plan = $get_plan->FetchRow(DBI_ASSOC);
	}

	$option =' ';

	if ($brand_id) {

		$sql = "SELECT mopp_Name, mopp_MotivationPointID, mopp_UseAmount, mopp_PointQty FROM motivation_plan_point WHERE bran_BrandID='".$brand_id."' AND mopp_Status='T' OR mopp_PrivilegeID=".$privilege_id."";

		$check_point = $oDB->QueryOne($sql);
		$get_point = $oDB->Query($sql);

		if ($check_point) {

			$option .= '<optgroup label="Point">';

			while ($point = $get_point->FetchRow(DBI_ASSOC)) {

				$select = "";

				if ($privilege_id!=0) {

					if ($plan['plan_type']=="Point") {
						
						if ($plan['plan_id'] == $point['mopp_MotivationPointID']) { $select = 'selected'; }
						else { $select = ''; }
					}
				}
    			
    			$option .= '<option value="p'.$point['mopp_MotivationPointID'].'" '.$select.'>'.$point['mopp_Name'].'&nbsp; ('.number_format($point['mopp_UseAmount'],2).' ฿ / '.$point['mopp_PointQty'].' Point)</option>';
			}

			$option .= '</optgroup>';
		}

		$sql = "SELECT mops_Name, mops_MotivationStampID, mops_StampQty, mops_CollectionTypeID FROM motivation_plan_stamp WHERE bran_BrandID='".$brand_id."' AND mops_Status='T' OR mops_PrivilegeID=".$privilege_id."";

		$check_stamp = $oDB->QueryOne($sql);
		$get_stamp = $oDB->Query($sql);

		if ($check_stamp) {

			$option .= '<optgroup label="Stamp">';

			while ($stamp = $get_stamp->FetchRow(DBI_ASSOC)) {

				$icon = "SELECT coty_Name FROM collection_type WHERE coty_CollectionTypeID=".$stamp['mops_CollectionTypeID'];
				$icon_name = $oDB->QueryOne($icon);

				$select = "";

				if ($privilege_id!=0) {

					if ($plan['plan_type']=="Stamp") {
						
						if ($plan['plan_id'] == $stamp['mops_MotivationStampID']) { $select = 'selected'; }
						else { $select = ''; }
					}
				}
    			
    			$option .= '<option value="s'.$stamp['mops_MotivationStampID'].'" '.$select.'>'.$stamp['mops_Name'].'&nbsp; (1 Times / '.$stamp['mops_StampQty'].' '.$icon_name.')</option>';
			}

			$option .= '</optgroup>';
		}
	}

	$html = '<select id="'.$path.'Motivation" class="form-control text-md" name="'.$path.'Motivation" onchange="CollectionImage(this.value)" required autofocus>';

	$select = "";

	if ($privilege_id!=0) {

		if ($plan['plan_type']=='None' || $plan['plan_type']=='') { $select = "selected"; }
		else { $select = ""; }
	}

	$html .= '<option value="None" '.$select.'>Not Select</option>';

	$html .= $option;

	$html .= '</select>';

	echo $html;	

	exit;
}

else if($TASK == 'Get_ExpiredDate'){

	$card_id = $_REQUEST['card'];
	$day = $_REQUEST['day'];
	$month = $_REQUEST['month'];
	$year = $_REQUEST['year'];

	if ($day=='' && $month=='' && $year=='') { $StaringDate = date("Y-m-d"); }
	else { $StaringDate = $year.'-'.$month.'-'.$day; }

	$StaringDate = date("Y-m-d", strtotime(date("Y-m-d", strtotime($StaringDate)) . " - 1 day"));

	if ($card_id) {

		$sql = "SELECT period_type, period_type_other, date_expired FROM mi_card WHERE card_id='".$card_id."'";
		$get_card = $oDB->Query($sql);
		$card = $get_card->FetchRow(DBI_ASSOC);

		if ($card['period_type']==1) { # SPECIFIC
			
			echo DateOnly($card['date_expired']);

		} else if ($card['period_type']==2) { # MONTH
			
			echo date("d M Y", strtotime(date("Y-m-d", strtotime($StaringDate)) . " + ".$card['period_type_other']." month"));

		} else if ($card['period_type']==3) { # YEAR
			
			echo date("d M Y", strtotime(date("Y-m-d", strtotime($StaringDate)) . " + ".$card['period_type_other']." year"));

		} else if ($card['period_type']==4) { # LIFF TIME
			
			echo "Member Life Time";

		} else {

			echo "- - -";
		}
	}

	exit;
}

else if($TASK == 'Get_BrandVat'){

	$brand_id = $_REQUEST['brand_id'];
	$type = $_REQUEST['type'];

	$html = "";

	if ($brand_id) {

		$sql_vat = "SELECT tax_type,tax_vat FROM mi_brand WHERE brand_id='".$brand_id."'";
		$brand_vat = $oDB->Query($sql_vat);
		$vat = $brand_vat->FetchRow(DBI_ASSOC);

		if ($vat['tax_type']==1) {

			$html = '<div class="adj_row">
                        <label for="vat_type" class="lable-form">VAT. ('.$vat['tax_vat'].'%) <span class="text-rq">*</span></label>
                        <select id="vat_type" class="form-control text-md" name="vat_type" required autofocus>
                            <option value="">Please Select ..</option>';

            if ($type == 1) {

            	$html .= '<option value="1" selected="selected">Include VAT.</option>
                            <option value="2">Not Include VAT.</option>';

            } elseif ($type == 2) {

            	$html .= '<option value="1">Include VAT.</option>
                            <option value="2" selected="selected">Not Include VAT.</option>';

            } else {

            	$html .= '<option value="1">Include VAT.</option>
                            <option value="2">Not Include VAT.</option>';
            }
            
            $html .= '	</select>
                     </div>';
		}
	}

	echo $html;	

	exit;
}

else if($TASK == 'Get_TargetData'){

	$brand_id = $_REQUEST['brand_id'];
	$view_target = $_REQUEST['view_target'];
	$id = $_REQUEST['target_id'];

	$html = "";

	if ($brand_id!="" && $view_target) {

		$html = '<hr><center>';
		$sql_target = '';

		if ($view_target == 'promotion_view') { # PROMOTION

			if ($brand_id == 'All') { $where_brand = ''; } 
			else { $where_brand = 'AND hilight_coupon.bran_BrandID = "'.$brand_id.'"'; }

			$sql_target = 'SELECT hilight_coupon.coup_Name AS name, 
								hilight_coupon.coup_CouponID AS id,
								hilight_coupon.coup_Image AS image,
								hilight_coupon.coup_ImagePath AS image_path,
								hilight_coupon.coup_Type AS type,
								hilight_coupon.coup_StartDate AS start_date,
								hilight_coupon.coup_EndDate AS end_date,
								mi_brand.name AS brand_name
							FROM hilight_coupon
							LEFT JOIN mi_brand
							ON hilight_coupon.bran_BrandID = mi_brand.brand_id
							WHERE hilight_coupon.coup_Deleted = ""
							'.$where_brand.'
							AND mi_brand.flag_del = ""
							ORDER BY name';

		} elseif ($view_target == 'checkin_view') { # CHECKIN

			if ($brand_id == 'All') { $where_brand = ''; } 
			else { $where_brand = 'AND mi_card.brand_id = "'.$brand_id.'"'; }

			$sql_target = 'SELECT mi_card.name AS name, 
									mi_card.card_id AS id,
									mi_card.image AS image,
									mi_card.path_image AS image_path,
									mi_card.member_fee AS member_fee,
									mi_card.flag_multiple AS multiple,
									mi_brand.name AS brand_name
							FROM mi_card
							LEFT JOIN mi_brand
							ON mi_card.brand_id = mi_brand.brand_id
							WHERE mi_card.flag_del = "0"
							'.$where_brand.'
							AND mi_brand.flag_del = ""
							ORDER BY name';

		} elseif ($view_target == 'privilege_view') { # PRIVILEGE

			if ($brand_id == 'All') { 

				$where_priv = ''; 
				$where_coup = ''; 
				$where_acti = ''; 

			} else { 

				$where_priv = 'AND privilege.bran_BrandID = "'.$brand_id.'"'; 
				$where_coup = 'AND coupon.bran_BrandID = "'.$brand_id.'"'; 
				$where_acti = 'AND activity.bran_BrandID = "'.$brand_id.'"'; 
			}

			$sql_target = 'SELECT privilege.priv_Name AS name, 
								privilege.priv_PrivilegeID AS id,
								privilege.priv_Image AS image,
								privilege.priv_ImagePath AS image_path,
								mi_brand.name AS brand_name,
								"Privilege" AS type,
								"p" AS type_code
								FROM privilege
								LEFT JOIN mi_brand
								ON privilege.bran_BrandID = mi_brand.brand_id
								WHERE privilege.priv_Deleted = ""
								'.$where_priv.'
								AND mi_brand.flag_del = ""

							UNION 

							SELECT coupon.coup_Name AS name, 
								coupon.coup_CouponID AS id,
								coupon.coup_Image AS image,
								coupon.coup_ImagePath AS image_path,
								mi_brand.name AS brand_name,
								"Coupon" AS type,
								"c" AS type_code
								FROM coupon
								LEFT JOIN mi_brand
								ON coupon.bran_BrandID = mi_brand.brand_id
								WHERE coupon.coup_Deleted = ""
								'.$where_coup.'
								AND mi_brand.flag_del = ""
								AND coupon.coup_Birthday = ""
								
							UNION 

							SELECT coupon.coup_Name AS name, 
								coupon.coup_CouponID AS id,
								coupon.coup_Image AS image,
								coupon.coup_ImagePath AS image_path,
								mi_brand.name AS brand_name,
								"Birthday Coupon" AS type,
								"b" AS type_code
								FROM coupon
								LEFT JOIN mi_brand
								ON coupon.bran_BrandID = mi_brand.brand_id
								WHERE coupon.coup_Deleted = ""
								'.$where_coup.'
								AND mi_brand.flag_del = ""
								AND coupon.coup_Birthday = "T"
								
							UNION 

							SELECT activity.acti_Name AS name, 
								activity.acti_ActivityID AS id,
								activity.acti_Image AS image,
								activity.acti_ImagePath AS image_path,
								mi_brand.name AS brand_name,
								"Activity" AS type,
								"a" AS type_code
								FROM activity
								LEFT JOIN mi_brand
								ON activity.bran_BrandID = mi_brand.brand_id
								WHERE activity.acti_Deleted = ""
								'.$where_acti.'
								AND mi_brand.flag_del = ""

							ORDER BY
								CASE
								WHEN type = "Privilege" THEN 1
								WHEN type = "Coupon" THEN 2
								WHEN type = "Birthday Coupon" THEN 3
								WHEN type = "Activity" THEN 4
								END, brand_name, name';

		} elseif ($view_target == 'redeem_view') { # REDEEM

			if ($brand_id == 'All') { $where_brand = ''; } 
			else { $where_brand = 'AND mi_brand.brand_id = "'.$brand_id.'"'; }

			$sql_target = 'SELECT reward.rewa_Name AS name, 
									reward.rewa_RewardID AS id,
									IF(reward.card_CardID=0,
										CONCAT("<img src=\'../../upload/",reward.rewa_ImagePath,reward.rewa_Image,"\' height=\'80px\' class=\'image_border\'>"),
										CONCAT("<img src=\'../../upload/",mi_card.path_image,mi_card.image,"\' height=\'80px\' class=\'image_border img-rounded\'>")
									) AS image,
									reward.rewa_Limit AS reward_limit,
									reward.rewa_Qty AS qty,
									reward.rewa_Type AS type,
									mi_brand.name AS brand_name
								FROM reward
								LEFT JOIN mi_brand
								ON reward.bran_BrandID = mi_brand.brand_id
								LEFT JOIN mi_card
								ON reward.card_CardID = mi_card.card_id
								WHERE reward.rewa_Deleted = ""
								'.$where_brand.'
								AND mi_brand.flag_del = ""
								ORDER BY name';
		}

		if ($sql_target) {

			$check_target = $oDB->QueryOne($sql_target);

			if ($view_target == 'promotion_view') {

				$html .= '<label style="font-size:16px"><u>Select Promotion</u></label><br><br>
                			<input type="text" id="myInput" class="form-control text-md" onkeyup="myFunction()" placeholder="Search"><br>
							<table class="table table-striped table-bordered" style="width:720px;margin:0">
								<thead>
									<tr class="th_table">
										<th width="56px">Select</th>
										<th width="200px">Promotion</th>
										<th colspan="2">Detail</th>
									</tr>
								</thead>
							</table>
							<div style="display:inline-block;margin:0;white-space:0;height:320px;overflow:auto;width:720px;">
							<table class="table table-striped table-bordered" style="width:720px">
								<tbody>';

				$get_target = $oDB->Query($sql_target);
				while($target = $get_target->FetchRow(DBI_ASSOC)){

					$check = '';

					if ($id!='undefined') {
						
						$sql_check = 'SELECT tavi_Deleted, tavi_TargetViewID 
										FROM target_view 
										WHERE tali_TargetListID="'.$id.'"
										AND tavi_ID="'.$target['id'].'"
										AND tavi_Type="Promotion"';
						$check = $oDB->Query($sql_check);
						$check_data = $check->FetchRow(DBI_ASSOC);

						if ($check_data['tavi_Deleted']=='' && $check_data['tavi_TargetViewID']) { $status = 'checked'; }
						else { $status = ''; }
					
					} else { $status = ''; }

					$html .= '<tr>
								<td style="text-align:center" width="56px"><input type="checkbox" name="'.$target['id'].'" value="1" '.$status.'></td>
								<td style="text-align:center" width="200px"><img src="../../upload/'.$target['image_path'].$target['image'].'" height="80px" class="image_border"></td>
								<td style="text-align:right" width="120px">
									Brand<br>
									Promotion<br>
									Type<br>
									Start-End Date</td>
								<td>
									'.$target['brand_name'].'<br>
									'.$target['name'].'<br>
									'.$target['type'].'<br>
									'.DateOnly($target['start_date']).' - '.DateOnly($target['end_date']).'</td>
							</tr>';
				}

				$html .= '		</tbody>
							</table>
							</div>';

			} elseif ($view_target == 'privilege_view') {

				$html .= '<label style="font-size:16px"><u>Select Privilege</u></label><br><br>
                			<input type="text" id="myInput" class="form-control text-md" onkeyup="myFunction()" placeholder="Search"><br>
							<table class="table table-striped table-bordered" style="width:720px;margin:0">
								<thead>
									<tr class="th_table">
										<th width="56px">Select</th>
										<th width="200px">Privilege</th>
										<th colspan="2">Detail</th>
									</tr>
								</thead>
							</table>
							<div style="display:inline-block;margin:0;white-space:0;height:320px;overflow:auto;width:720px;">
							<table class="table table-striped table-bordered" style="width:720px">
								<tbody>';

				$get_target = $oDB->Query($sql_target);
				while($target = $get_target->FetchRow(DBI_ASSOC)){

					if ($target['type_code'] == 'p') { $type = 'Privilege'; }
					elseif ($target['type_code'] == 'c') { $type = 'Coupon'; }
					elseif ($target['type_code'] == 'b') { $type = 'Birthday'; }
					elseif ($target['type_code'] == 'a') { $type = 'Activity'; }

					$check = '';

					if ($id!='undefined') {
						
						$sql_check = 'SELECT tavi_Deleted, tavi_TargetViewID 
										FROM target_view 
										WHERE tali_TargetListID="'.$id.'"
										AND tavi_ID="'.$target['id'].'"
										AND tavi_Type="'.$type.'"';
						$check = $oDB->Query($sql_check);
						$check_data = $check->FetchRow(DBI_ASSOC);

						if ($check_data['tavi_Deleted']=='' && $check_data['tavi_TargetViewID']) { $status = 'checked'; }
						else { $status = ''; }
					
					} else { $status = ''; }

					$html .= '<tr>
								<td style="text-align:center" width="56px"><input type="checkbox" name="'.$target['type_code'].$target['id'].'" value="1" '.$status.'></td>
								<td style="text-align:center" width="200px"><img src="../../upload/'.$target['image_path'].$target['image'].'" height="80px" class="image_border"></td>
								<td style="text-align:right" width="120px">
									Brand<br>
									Privilege<br>
									Type</td>
								<td>
									'.$target['brand_name'].'<br>
									'.$target['name'].'<br>
									'.$target['type'].'</td>
							</tr>';
				}

				$html .= '		</tbody>
							</table>
						</div>';

			} elseif ($view_target == 'checkin_view') {

				$html .= '<label style="font-size:16px"><u>Select Card</u></label><br><br>
                			<input type="text" id="myInput" class="form-control text-md" onkeyup="myFunction()" placeholder="Search"><br>
							<table class="table table-striped table-bordered" style="width:720px;margin:0">
								<thead>
									<tr class="th_table">
										<th width="56px">Select</th>
										<th width="200px">Card</th>
										<th colspan="2">Detail</th>
									</tr>
								</thead>
							</table>
							<div style="display:inline-block;margin:0;white-space:0;height:320px;overflow:auto;width:720px;">
							<table class="table table-striped table-bordered" style="width:720px">
								<tbody>';

				$get_target = $oDB->Query($sql_target);
				while($target = $get_target->FetchRow(DBI_ASSOC)){

					$check = '';

					if ($id!='undefined') {
						
						$sql_check = 'SELECT tavi_Deleted, tavi_TargetViewID 
										FROM target_view 
										WHERE tali_TargetListID="'.$id.'"
										AND tavi_ID="'.$target['id'].'"
										AND tavi_Type="Card"';
						$check = $oDB->Query($sql_check);
						$check_data = $check->FetchRow(DBI_ASSOC);

						if ($check_data['tavi_Deleted']=='' && $check_data['tavi_TargetViewID']) { $status = 'checked'; }
						else { $status = ''; }
					
					} else { $status = ''; }

					$html .= '<tr>
								<td style="text-align:center" width="56px"><input type="checkbox" name="'.$target['id'].'" value="1" '.$status.'></td>
								<td style="text-align:center" width="200px"><img src="../../upload/'.$target['image_path'].$target['image'].'" height="80px" class="image_border img-rounded"></td>
								<td style="text-align:right" width="120px">
									Brand<br>
									Card<br>
									Member Fee<br>
									Multiple</td>
								<td>
									'.$target['brand_name'].'<br>
									'.$target['name'].'<br>
									'.number_format($target['member_fee'],2).' ฿<br>
									'.$target['multiple'].'</td>
							</tr>';
				}

				$html .= '		</tbody>
							</table>
						</div>';

			} elseif ($view_target == 'redeem_view') {

				$html .= '<label style="font-size:16px"><u>Select Reward</u></label><br><br>
                			<input type="text" id="myInput" class="form-control text-md" onkeyup="myFunction()" placeholder="Search"><br>
							<table class="table table-striped table-bordered" style="width:720px;margin:0">
								<thead>
									<tr class="th_table">
										<th width="56px">Select</th>
										<th width="200px">Reward</th>
										<th colspan="2">Detail</th>
									</tr>
								</thead>
							</table>
							<div style="display:inline-block;margin:0;white-space:0;height:320px;overflow:auto;width:720px;">
							<table id="myTable" class="table table-striped table-bordered" style="width:720px">
								<tbody>';

				$get_target = $oDB->Query($sql_target);
				while($target = $get_target->FetchRow(DBI_ASSOC)){

					$check = '';

					if ($id!='undefined') {
						
						$sql_check = 'SELECT tavi_Deleted, tavi_TargetViewID 
										FROM target_view 
										WHERE tali_TargetListID="'.$id.'"
										AND tavi_ID="'.$target['id'].'"
										AND tavi_Type="Reward"';
						$check = $oDB->Query($sql_check);
						$check_data = $check->FetchRow(DBI_ASSOC);

						if ($check_data['tavi_Deleted']=='' && $check_data['tavi_TargetViewID']) { $status = 'checked'; }
						else { $status = ''; }
					
					} else { $status = ''; }

					if ($target["reward_limit"]=='T') { $target['qty'] = number_format($target['qty']); }
					else { $target['qty'] = "Unlimit"; }

					$html .= '<tr>
								<td style="text-align:center" width="56px"><input type="checkbox" name="'.$target['id'].'" value="1" '.$status.'></td>
								<td style="text-align:center" width="200px">'.$target['image'].'</td>
								<td style="text-align:right" width="120px">
									Brand<br>
									Reward<br>
									Type<br>
									Qty</td>
								<td>
									'.$target['brand_name'].'<br>
									'.$target['name'].'<br>
									'.$target['type'].'<br>
									'.$target['qty'].'</td>
							</tr>';
				}

				$html .= '		</tbody>
							</table>
						</div>';
			}

			$html .= '</center>';

		} elseif ($view_target == 'member_profile_view') {

		    # GENDER

			$member_gender = '';

			if ($id!='undefined') {
						
				$sql_check = 'SELECT tavi_ID 
								FROM target_view 
								WHERE tali_TargetListID="'.$id.'"
								AND tavi_Type="Gender"';
				$member_gender = $oDB->QueryOne($sql_check);	
			}

			# AGE

			$member_age = '';

			if ($id!='undefined') {
						
				$sql_check = 'SELECT tavi_ID 
								FROM target_view 
								WHERE tali_TargetListID="'.$id.'"
								AND tavi_Type="Age"';
				$member_age = $oDB->QueryOne($sql_check);	
			}

	        $age_basic = $member_age;
	        $token = strtok($age_basic,",");
	     	$basic_age1 = $token;
	     	$token = strtok (",");
	     	$basic_age2 = $token;

			$html = '<hr><center><label style="font-size:16px"><u>Member Profile</u></label></center><br>
		                <div class="adj_row">
		                    <label for="target_gender" class="lable-form">Gender <span class="text-rq">*</span></label>
		                    <select id="target_gender" class="form-control text-md" name="target_gender" required autofocus>
		                    	<option value="">Please Select ..</option>';

			if ($member_gender == "All") { $select = 'selected'; }
			else { $select = ''; }

		    $html .= '<option value="All" '.$select.'>All</option>';

	        $query = "SELECT mata_MasterTargetID,mata_NameEn FROM master_target WHERE mafi_MasterFieldID='5'";

			$result = mysql_query($query) or die(mysql_error()."[".$query."]");

			while ($row = mysql_fetch_array($result)) {

				if ($row['mata_NameEn'] == $member_gender) { $select = 'selected'; }
				else { $select = ''; }

	    		$html .= "<option value=".$row['mata_NameEn']." ".$select.">".$row['mata_NameEn']."</option>";
			}

		    $html .= '      </select>
		                </div>
		                <div class="adj_row">
		                    <label for="target_gander" class="lable-form">Age <span class="text-rq">*</span></label>
		                    <span class="form-inline">
		                    <select id="age_basic1" name="age_basic1" class="form-control" onchange="age_basic();" required autofocus>
		                    	<option value="All">All</option>';

	        $query = "SELECT mata_MasterTargetID,mata_NameEn FROM master_target WHERE mafi_MasterFieldID='6'";

			$result = mysql_query($query) or die(mysql_error()."[".$query."]");

			while ($row = mysql_fetch_array($result)) {

				if ($row['mata_MasterTargetID']==$basic_age1) { $select='selected="selected"';}
				else{ $select='';}

	    		$html .= "<option value='".$row['mata_MasterTargetID']."' ".$select.">".$row['mata_NameEn']."</option>";
			}
	                                          
	        $html .= '</select>
	        			&nbsp;-&nbsp;
	                    <span id="age_target_data" class="fontBlack">';

	        if ($basic_age1!="") {

	            $html .= 	$basic_age1.'&nbsp;-&nbsp;
	                        <span id="age_target_data" class="fontBlack">
	                            <select id="age_basic2" name="age_basic2" class="form-control">';

		        $query = "SELECT mata_MasterTargetID,mata_NameEn FROM master_target WHERE mafi_MasterFieldID='6'";

				$result = mysql_query($query) or die(mysql_error()."[".$query."]");

		        while ($row = mysql_fetch_array($result)) {

					if ($row['mata_MasterTargetID']==$basic_age2) { $select='selected="selected"';}
					else{ $select='';}

		    		$html .= "<option value='".$row['mata_MasterTargetID']."' ".$select.">".$row['mata_NameEn']."</option>";
				}
		                                           
		        $html .= '</select>';
		    }
		                                           
		    $html .= '</span>&nbsp; Age Restriction
		                </span></div>';

		} else {

			$html .= '<label>No Data to Select</label>';
		}

		if ($_SESSION['role_action']['push_notification']['add'] || $_SESSION['role_action']['push_notification']['edit']) {

			$html .= '<br>
						<div class="clear_all">
							<button class="btn btn-success btn_hide" type="submit">SUBMIT</button>
							&nbsp;&nbsp;&nbsp;
							<button class="btn btn-warning btn_hide" type="reset" onclick="window.location.href=\'target_page.php\'">CANCEL</button>
							<input type="hidden" id="act" name="act" value="save" />
						</div>
					<br>';
		}
	}

	echo $html;	

	exit;
}

else if($TASK == 'Get_PushNotification'){

	$brand_id = $_REQUEST['brand_id'];
	$type_id = trim_txt($_REQUEST['type_id']);
	$noti_id = $_REQUEST['noti_id'];

	$html = "";

	if ($brand_id != "" && $type_id != "") {

		$html = '<select id="puno_PushNotificationID" class="form-control text-md" name="puno_PushNotificationID" required autofocus>
                    <option value="">Please Select ..</option>';

		$sql_noti = "SELECT puno_PushNotificationID, puno_Header 
						FROM push_notification 
						WHERE bran_BrandID='".$brand_id."'
						AND puno_Type='".$type_id."'
						ORDER BY puno_Header";
		$get_noti = $oDB->Query($sql_noti);

		while ($noti = $get_noti->FetchRow(DBI_ASSOC)) {

			if ($noti_id == $noti['puno_PushNotificationID']) { $select = 'selected'; }
			else { $select = ''; }

			$html .= '<option value="'.$noti['puno_PushNotificationID'].'" '.$select.'>'.$noti['puno_Header'].'</option>';
		}

		$html .= '</select>';

	} else {

		$html = '<select id="puno_PushNotificationID" class="form-control text-md" name="puno_PushNotificationID" required autofocus>
                    <option value="">Please Select ..</option>
                    <span id="push_notification_select"></span>
                </select>';
	}

	echo $html;	

	exit;
}

else if($TASK == 'Get_TargetList'){

	$brand_id = $_REQUEST['brand_id'];
	$target_id = $_REQUEST['target_id'];

	$html = "";

	if ($brand_id != "") {

		$html = '<select id="tali_TargetListID" class="form-control text-md" name="tali_TargetListID" required autofocus>
                    <option value="">Please Select ..</option>';

		$sql_target = "SELECT tali_TargetListID, tali_Name 
						FROM target_list 
						WHERE bran_BrandID='".$brand_id."'
						ORDER BY tali_Name";
		$get_target = $oDB->Query($sql_target);

		while ($target = $get_target->FetchRow(DBI_ASSOC)) {

			if ($target_id == $target['tali_TargetListID']) { $select = 'selected'; }
			else { $select = ''; }

			$html .= '<option value="'.$target['tali_TargetListID'].'" '.$select.'>'.$target['tali_Name'].'</option>';
		}

		$html .= '</select>';

	} else {

		$html = '<select id="tali_TargetListID" class="form-control text-md" name="tali_TargetListID" required autofocus>
                    <option value="">Please Select ..</option>
                    <span id="push_notification_select"></span>
                </select>';
	}

	echo $html;	

	exit;
}

else if($TASK == 'Get_Promotion'){

	$brand_id = $_REQUEST['brand_id'];
	$coup_id = $_REQUEST['coup_id'];

	$html = '<select id="hico_HilightCouponID" class="form-control text-md" name="hico_HilightCouponID" required autofocus>
				<option value="">Please Select ..</option>';

	if ($brand_id != "") {
		$sql_coupon = "SELECT coup_CouponID, coup_Name 
						FROM hilight_coupon 
						WHERE bran_BrandID='".$brand_id."'
						ORDER BY coup_Name";
		$get_coupon = $oDB->Query($sql_coupon);

		while ($coupon = $get_coupon->FetchRow(DBI_ASSOC)) {

			if ($coup_id == $coupon['coup_CouponID']) { $select = 'selected'; }
			else { $select = ''; }

			$html .= '<option value="'.$coupon['coup_CouponID'].'" '.$select.'>'.$coupon['coup_Name'].'</option>';
		}
	}

	$html .= '</select>';

	echo $html;	

	exit;
}

else if($TASK == 'Get_MainCategory'){

	$type = $_REQUEST['type'];
	$main_category = $_REQUEST['main_category'];

	$html = '';

	if ($type != "Reward" && $type != "General") {

		$html = '<div class="adj_row" id="main_data">
					<label class="lable-form">Main Brand Category <span class="text-rq">*</span></label>
					<select id="vaca_MainCategoryBrandID" class="form-control text-md" name="vaca_MainCategoryBrandID[]" multiple required autofocus>';

		$sql_main = "SELECT main_category_brand_id AS id, name_en FROM mi_main_category_brand";
		$get_main = $oDB->Query($sql_main);

		$category = array();

		if ($main_category) {

			$token = strtok($main_category , ",");
			$i = 0;

			while ($token !== false) {

			    $category[$i] = $token;
			    $token = strtok(",");
			    $i++;
			}
		}

		while ($main = $get_main->FetchRow(DBI_ASSOC)) {

			$select = "";
			foreach ($category as $category_id) {

				if ($category_id == $main['id']) { $select = 'selected'; }
			}

			$html .= '<option value="'.$main['id'].'" '.$select.'>'.$main['name_en'].'</option>';
		}

		$html .= '</select></div>';
	}

	echo $html;	

	exit;
}

?>