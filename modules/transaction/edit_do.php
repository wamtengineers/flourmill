<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_POST["transaction_edit"])){
	extract($_POST);
	$err="";
	if($account_id == "" || $reference_id == "")
		$err="Fields with (*) are Mandatory.<br />";
	if($err==""){
		$sql="Update transaction set `account_id`='".slash($account_id)."',`reference_id`='".slash($reference_id)."',`datetime_added`='".slash(datetime_dbconvert($datetime_added))."',`amount`='".slash($amount)."',`details`='".slash($details)."'"." where id='".$id."'";
		doquery($sql,$dblink);
		unset($_SESSION["transaction_manage"]["edit"]);
		header('Location: transaction_manage.php?tab=list&msg='.url_encode("Sucessfully Updated"));
		die;
	}
	else{
		foreach($_POST as $key=>$value)
			$_SESSION["transaction_manage"]["edit"][$key]=$value;
		header("Location: transaction_manage.php?tab=edit&err=".url_encode($err)."&id=$id");
		die;
	}
}
/*----------------------------------------------------------------------------------*/
if(isset($_GET["id"]) && $_GET["id"]!=""){
	$rs=doquery("select * from transaction where id='".slash($_GET["id"])."'",$dblink);
	if(numrows($rs)>0){
		$r=dofetch($rs);
		foreach($r as $key=>$value)
			$$key=htmlspecialchars(unslash($value));
			$datetime_added=datetime_convert($datetime_added);
		if(isset($_SESSION["transaction_manage"]["edit"]))
			extract($_SESSION["transaction_manage"]["edit"]);
	}
	else{
		header("Location: transaction_manage.php?tab=list");
		die;
	}
}
else{
	header("Location: transaction_manage.php?tab=list");
	die;
}