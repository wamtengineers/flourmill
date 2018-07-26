<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_POST["wheat_purchase_edit"])){
	extract($_POST);
	$err="";
	if(empty($datetime_added) || empty($supplier_id))
		$err="Fields with (*) are Mandatory.<br />";
	if($err==""){
		$sql="Update wheat_purchase set `datetime_added`='".slash(datetime_dbconvert($datetime_added))."',`supplier_id`='".slash($supplier_id)."',`broker_name`='".slash($broker_name)."',`vehicle_number`='".slash($vehicle_number)."',`gross_weight`='".slash($gross_weight)."',`wheat_price`='".slash($wheat_price)."',`deduction_weight`='".slash($deduction_weight)."',`net_weight`='".slash($net_weight)."',`brokery`='".slash($brokery)."',`carrage_expenses`='".slash($carrage_expenses)."',`market_committe`='".slash($market_committe)."',`kata_paisa`='".slash($kata_paisa)."'"." where id='".$id."'";
		doquery($sql,$dblink);
		unset($_SESSION["wheat_purchase_manage"]["edit"]);
		header('Location: wheat_purchase_manage.php?tab=list&msg='.url_encode("Sucessfully Updated"));
		die;
	}
	else{
		foreach($_POST as $key=>$value)
			$_SESSION["wheat_purchase_manage"]["edit"][$key]=$value;
		header("Location: wheat_purchase_manage.php?tab=edit&err=".url_encode($err)."&id=$id");
		die;
	}
}
/*----------------------------------------------------------------------------------*/
if(isset($_GET["id"]) && $_GET["id"]!=""){
	$rs=doquery("select * from wheat_purchase where id='".slash($_GET["id"])."'",$dblink);
	if(numrows($rs)>0){
		$r=dofetch($rs);
		foreach($r as $key=>$value)
			$$key=htmlspecialchars(unslash($value));
			$datetime_added = date_convert($datetime_added);
		if(isset($_SESSION["wheat_purchase_manage"]["edit"]))
			extract($_SESSION["wheat_purchase_manage"]["edit"]);
	}
	else{
		header("Location: wheat_purchase_manage.php?tab=list");
		die;
	}
}
else{
	header("Location: wheat_purchase_manage.php?tab=list");
	die;
}