<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_POST["items_attributes_edit"])){
	extract($_POST);
	$err="";
	if(empty($name))
		$err="Fields with (*) are Mandatory.<br />";
	if($err==""){
		$sql="Update items_attributes set `name`='".slash($name)."',`values`='".slash($values)."'"." where id='".$id."'";
		doquery($sql,$dblink);
		unset($_SESSION["items_attributes_manage"]["edit"]);
		header('Location: items_attributes_manage.php?tab=list&msg='.url_encode("Sucessfully Updated"));
		die;
	}
	else{
		foreach($_POST as $key=>$value)
			$_SESSION["items_attributes_manage"]["edit"][$key]=$value;
		header('Location: items_attributes_manage.php?tab=edit&err='.url_encode($err)."&id=$id");
		die;
	}
}
/*----------------------------------------------------------------------------------*/
if(isset($_GET["id"]) && $_GET["id"]!=""){
	$rs=doquery("select * from items_attributes where id='".slash($_GET["id"])."' and item_id='".$parent_item_id."'",$dblink);
	if(numrows($rs)>0){
		$r=dofetch($rs);
		foreach($r as $key=>$value)
			$$key=htmlspecialchars(unslash($value));
		if(isset($_SESSION["items_attributes_manage"]["edit"]))
			extract($_SESSION["items_attributes_manage"]["edit"]);
	}
	else{
		header('Location: items_attributes_manage.php?tab=list');
		die;
	}
}
else{
	header('Location: items_attributes_manage.php?tab=list');
	die;
}