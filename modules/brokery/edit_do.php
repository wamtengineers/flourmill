<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_POST["brokery_edit"])){
	extract($_POST);
	$err="";
	if(empty($item_id))
		$err="Fields with (*) are Mandatory.<br />";
	if($err==""){
		$sql="Update brokery set `item_id`='".slash($item_id)."', `packing`='".slash($packing)."', `amount`='".slash($amount)."'"." where id='".$id."'";
		doquery($sql,$dblink);
		unset($_SESSION["brokery_manage"]["edit"]);
		header('Location: brokery_manage.php?tab=list&msg='.url_encode("Sucessfully Updated"));
		die;
	}
	else{
		foreach($_POST as $key=>$value)
			$_SESSION["brokery_manage"]["edit"][$key]=$value;
		header('Location: brokery_manage.php?tab=edit&err='.url_encode($err)."&id=$id");
		die;
	}
}
/*----------------------------------------------------------------------------------*/
if(isset($_GET["id"]) && $_GET["id"]!=""){
	$rs=doquery("select * from brokery where id='".slash($_GET["id"])."' and account_id='".$parent_account_id."'",$dblink);
	if(numrows($rs)>0){
		$r=dofetch($rs);
		foreach($r as $key=>$value)
			$$key=htmlspecialchars(unslash($value));
		if(isset($_SESSION["brokery_manage"]["edit"]))
			extract($_SESSION["brokery_manage"]["edit"]);
	}
	else{
		header('Location: brokery_manage.php?tab=list');
		die;
	}
}
else{
	header('Location: brokery_manage.php?tab=list');
	die;
}