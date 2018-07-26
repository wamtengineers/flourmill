<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_POST["account_edit"])){
	extract($_POST);
	$err="";
	if(empty($title))
		$err="Fields with (*) are Mandatory.<br />";
	if( numrows( doquery( "select id from account where title = '".slash( $title )."' and id <> '".$id."'", $dblink ) ) > 0 ){
		$err="Account with same name already exists.<br />";
	}
	if($err==""){
		$sql="Update account set `title`='".slash($title)."', `account_type_id`='".slash($account_type_id)."', `description`='".slash($description)."' where id='".$id."'";
		doquery($sql,$dblink);
		unset($_SESSION["account_manage"]["edit"]);
		header('Location: account_manage.php?tab=list&msg='.url_encode("Sucessfully Updated"));
		die;
	}
	else{
		foreach($_POST as $key=>$value)
			$_SESSION["account_manage"]["edit"][$key]=$value;
		header("Location: account_manage.php?tab=edit&err=".url_encode($err)."&id=$id");
		die;
	}
}
/*----------------------------------------------------------------------------------*/
if(isset($_GET["id"]) && $_GET["id"]!=""){
	$rs=doquery("select * from account where id='".slash($_GET["id"])."'",$dblink);
	if(numrows($rs)>0){
		$r=dofetch($rs);
		foreach($r as $key=>$value)
			$$key=htmlspecialchars(unslash($value));
		if(isset($_SESSION["account_manage"]["edit"]))
			extract($_SESSION["account_manage"]["edit"]);
	}
	else{
		header("Location: account_manage.php?tab=list");
		die;
	}
}
else{
	header("Location: account_manage.php?tab=list");
	die;
}