<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_POST["items_attributes_add"])){
	extract($_POST);
	$err="";
	if(empty($name))
		$err="Fields with (*) are Mandatory.<br />";
	if($err==""){
		$sql="INSERT INTO items_attributes (item_id, name, `values`) VALUES ('".slash($parent_item_id)."', '".slash($name)."', '".slash($values)."')";
		doquery($sql,$dblink);
		unset($_SESSION["items_attributes_manage"]["add"]);
		header('Location: items_attributes_manage.php?tab=list&msg='.url_encode("Sucessfully Added"));
		die;
	}
	else{
		foreach($_POST as $key=>$value)
			$_SESSION["items_attributes_manage"]["add"][$key]=$value;
		header('Location: items_attributes_manage.php?tab=add&err='.url_encode($err));
		die;
	}
}