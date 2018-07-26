<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_POST["packing_add"])){
	extract($_POST);
	$err="";
	if(empty($title))
		$err="Fields with (*) are Mandatory.<br />";
	if($err==""){
		$sql="INSERT INTO packing (title, total_units) VALUES ('".slash($title)."', '".slash($total_units)."')";
		doquery($sql,$dblink);
		unset($_SESSION["packing_manage"]["add"]);
		header('Location: packing_manage.php?tab=list&msg='.url_encode("Sucessfully Added"));
		die;
	}
	else{
		foreach($_POST as $key=>$value)
			$_SESSION["packing_manage"]["add"][$key]=$value;
		header('Location: packing_manage.php?tab=add&err='.url_encode($err));
		die;
	}
}