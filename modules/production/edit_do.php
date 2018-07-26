<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_POST["production_edit"])){
	extract($_POST);
	$err="";
	if(empty($datetime_added))
		$err="Fields with (*) are Mandatory.<br />";
	if($err==""){
		$sql="Update production set `datetime_added`='".slash(datetime_dbconvert(unslash($datetime_added)))."' where id='".$id."'";
		doquery($sql,$dblink);
		$production_item=doquery("select * from production_items where production_id='".slash($_GET["id"])."'",$dblink);
		if(numrows($production_item)){
			while($production=dofetch($production_item)){
				$quantity=$production["quantity"];
				doquery("update items set quantity=quantity-".$quantity." where id='".slash($production[ "item_id" ])."'", $dblink);
			}
		}
		doquery("delete from production_items where production_id='".$id."'", $dblink);
		foreach( $quantities as $item_id => $quantity ) {
			if( !empty( $quantity ) ){
				doquery("insert into production_items(production_id, item_id, quantity) values('".$id."', '".$item_id."', '".$quantity."')", $dblink);
				doquery("update items set quantity=quantity+".$quantity." where id='".slash($item_id)."'", $dblink);
			}
		}
		unset($_SESSION["production_manage"]["edit"]);
		header('Location: production_manage.php?tab=list&msg='.url_encode("Sucessfully Updated"));
		die;
	}
	else{
		foreach($_POST as $key=>$value)
			$_SESSION["production_manage"]["edit"][$key]=$value;
		header("Location: production_manage.php?tab=edit&err=".url_encode($err)."&id=".$id);
		die;
	}
}
/*----------------------------------------------------------------------------------*/
if(isset($_GET["id"]) && $_GET["id"]!=""){
	$rs=doquery("select * from production where id='".slash($_GET["id"])."'",$dblink);
	if(numrows($rs)>0){
		$r=dofetch($rs);
		foreach($r as $key=>$value)
			$$key=htmlspecialchars(unslash($value));
			$datetime_added=datetime_convert($datetime_added);
		$rs=doquery("select * from production_items where production_id='".slash($_GET["id"])."'",$dblink);
		if(numrows($rs)>0){
			while($r=dofetch($rs)){
				$quantities[ $r[ "item_id" ] ] = $r[ "quantity" ];
			}
		}
		if(isset($_SESSION["production_manage"]["edit"]))
			extract($_SESSION["production_manage"]["edit"]);
	}
	else{
		header("Location: production_manage.php?tab=list");
		die;
	}
}
else{
	header("Location: production_manage.php?tab=list");
	die;
}