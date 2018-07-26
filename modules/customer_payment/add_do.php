<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_POST["customer_payment_add"])){
	extract($_POST);
	$err="";
	if(empty($customer_id) || empty($amount))
		$err="Fields with (*) are Mandatory.<br />";
	if($err==""){
		$sql="INSERT INTO customer_payment (customer_id, datetime_added, type, amount, account_id, details) VALUES ('".slash($customer_id)."','".slash(datetime_dbconvert($datetime_added))."','".slash($type)."','".slash($amount)."','".slash($account_id)."','".slash($details)."')";
		doquery($sql,$dblink);
		unset($_SESSION["customer_payment_manage"]["add"]);
		header('Location: customer_payment_manage.php?tab=list&msg='.url_encode("Sucessfully Added"));
		die;
	}
	else{
		foreach($_POST as $key=>$value)
			$_SESSION["customer_payment_manage"]["add"][$key]=$value;
		header('Location: customer_payment_manage.php?tab=add&err='.url_encode($err));
		die;
	}
}