<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_POST["expense_edit"])){
	extract($_POST);
	$err="";
	if(empty($datetime_added) || empty($amount))
		$err="Fields with (*) are Mandatory.<br />";
	if($err==""){
		$sql="Update expense set `datetime_added`='".slash(datetime_dbconvert($datetime_added))."',`expense_category_id`='".slash($expense_category_id)."',`account_id`='".slash($account_id)."',`details`='".slash($details)."',`amount`='".slash($amount)."'"." where id='".$id."'";
		doquery($sql,$dblink);
		unset($_SESSION["expense_manage"]["edit"]);
		header('Location: expense_manage.php?tab=list&msg='.url_encode("Sucessfully Updated"));
		die;
	}
	else{
		foreach($_POST as $key=>$value)
			$_SESSION["expense_manage"]["edit"][$key]=$value;
		header("Location: expense_manage.php?tab=edit&err=".url_encode($err)."&id=$id");
		die;
	}
}
/*----------------------------------------------------------------------------------*/
if(isset($_GET["id"]) && $_GET["id"]!=""){
	$rs=doquery("select * from expense where id='".slash($_GET["id"])."'",$dblink);
	if(numrows($rs)>0){
		$r=dofetch($rs);
		foreach($r as $key=>$value)
			$$key=htmlspecialchars(unslash($value));
			$datetime_added=datetime_convert($datetime_added);
		if(isset($_SESSION["expense_manage"]["edit"]))
			extract($_SESSION["expense_manage"]["edit"]);
	}
	else{
		header("Location: expense_manage.php?tab=list");
		die;
	}
}
else{
	header("Location: expense_manage.php?tab=list");
	die;
}