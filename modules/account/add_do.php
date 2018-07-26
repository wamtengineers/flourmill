<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_POST["account_add"])){
	extract($_POST);
	$err="";
	if(empty($title))
		$err="Fields with (*) are Mandatory.<br />";
	if( numrows( doquery( "select id from account where title = '".slash( $title )."'", $dblink ) ) > 0 ){
		$err="Account with same name already exists.<br />";
	}
	if($err==""){
		$sql="INSERT INTO account (title, account_type_id, description) VALUES ('".slash($title)."', '".slash($account_type_id)."', '".slash($description)."')";
		doquery($sql,$dblink);
		unset($_SESSION["account_manage"]["add"]);
		header('Location: account_manage.php?tab=list&msg='.url_encode("Sucessfully Added"));
		die;
	}
	else{
		foreach($_POST as $key=>$value)
			$_SESSION["account_manage"]["add"][$key]=$value;
		header('Location: account_manage.php?tab=add&err='.url_encode($err));
		die;
	}
}