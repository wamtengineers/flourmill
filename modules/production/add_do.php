<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_POST["production_add"])){
	extract($_POST);
	$err="";
	if(empty($datetime_added))
		$err="Fields with (*) are Mandatory.<br />";
	if($err==""){
		$sql="INSERT INTO production (datetime_added, added_by) VALUES ('".slash(datetime_dbconvert($datetime_added))."','".$_SESSION["logged_in_admin"]["id"]."')";
		doquery($sql,$dblink);
		$production_id=inserted_id();
		foreach( $quantities as $item_id => $quantity ) {
			if( !empty( $quantity ) ){
				doquery("insert into production_items(production_id, item_id, quantity) values('".$production_id."', '".$item_id."', '".$quantity."')", $dblink);
				doquery("update items set quantity=quantity+".$quantity." where id='".slash($item_id)."'", $dblink);
			}
		}
		unset($_SESSION["production_manage"]["add"]);
		header('Location: production_manage.php?tab=list&msg='.url_encode("Sucessfully Added"));
		die;
	}
	else{
		foreach($_POST as $key=>$value)
			$_SESSION["production_manage"]["add"][$key]=$value;
		header('Location: production_manage.php?tab=add&err='.url_encode($err));
		die;
	}
}