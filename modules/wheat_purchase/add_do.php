<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_POST["wheat_purchase_add"])){
	extract($_POST);
	$err="";
	if(empty($datetime_added) || empty($supplier_id))
		$err="Fields with (*) are Mandatory.<br />";
	if($err==""){
		$sql="INSERT INTO wheat_purchase (datetime_added, supplier_id, broker_name, vehicle_number, gross_weight, wheat_price, deduction_weight, net_weight, brokery, carrage_expenses, market_committe, kata_paisa) VALUES ('".slash(datetime_dbconvert($datetime_added))."', '".slash($supplier_id)."', '".slash($broker_name)."', '".slash($vehicle_number)."','".slash($gross_weight)."','".slash($wheat_price)."','".slash($deduction_weight)."','".slash($net_weight)."','".slash($brokery)."','".slash($carrage_expenses)."','".slash($market_committe)."','".slash($kata_paisa)."')";
		doquery($sql,$dblink);
		unset($_SESSION["wheat_purchase_manage"]["add"]);
		header('Location: wheat_purchase_manage.php?tab=list&msg='.url_encode("Sucessfully Added"));
		die;
	}
	else{
		foreach($_POST as $key=>$value)
			$_SESSION["wheat_purchase_manage"]["add"][$key]=$value;
		header('Location: wheat_purchase_manage.php?tab=add&err='.url_encode($err));
		die;
	}
}