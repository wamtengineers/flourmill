<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_POST["items_variations_edit"])){
	extract($_POST);
	$err="";
	if(empty($quantity))
		$err="Fields with (*) are Mandatory.<br />";
	if($err==""){
		$sql="Update items_variations set `quantity`='".slash($quantity)."',`price`='".slash($price)."',`cost_price`='".slash($cost_price)."'"." where id='".$id."'";
		doquery($sql,$dblink);
		doquery( "delete from items_variations_attributes where items_variations_id = '".$id."' ", $dblink );
		$attributes = doquery( "select * from items_attributes where item_id = '".$parent_item[ "id" ]."'", $dblink );
		if( numrows( $attributes ) > 0 ) {
			while( $attribute = dofetch( $attributes ) ) {
				if( isset( $items_attribute[ $attribute[ "id" ] ] ) ){
					doquery( "insert into items_variations_attributes values( '".$id."', '".$attribute[ "id" ]."', '".slash( $items_attribute[ $attribute[ "id" ] ] )."' )", $dblink );
				}
			}
		}
		update_stock( $parent_item_id );
		unset($_SESSION["items_variations_manage"]["edit"]);
		header('Location: items_variations_manage.php?tab=list&msg='.url_encode("Sucessfully Updated"));
		die;
	}
	else{
		foreach($_POST as $key=>$value)
			$_SESSION["items_variations_manage"]["edit"][$key]=$value;
		header('Location: items_variations_manage.php?tab=edit&err='.url_encode($err)."&id=$id");
		die;
	}
}
/*----------------------------------------------------------------------------------*/
if(isset($_GET["id"]) && $_GET["id"]!=""){
	$rs=doquery("select * from items_variations where id='".slash($_GET["id"])."' and item_id='".$parent_item_id."'",$dblink);
	if(numrows($rs)>0){
		$r=dofetch($rs);
		foreach($r as $key=>$value)
			$$key=htmlspecialchars(unslash($value));
		$items_attribute = array();
		$items_variations_attributes = doquery( "select * from items_variations_attributes where items_variations_id = '".$id."'", $dblink );
		if( numrows( $items_variations_attributes ) > 0 ) {
			while( $items_variations_attribute = dofetch( $items_variations_attributes ) ) {
				$items_attribute[ $items_variations_attribute[ "items_attributes_id" ] ] = unslash( $items_variations_attribute[ 'value' ] );
			}
		}
		if(isset($_SESSION["items_variations_manage"]["edit"]))
			extract($_SESSION["items_variations_manage"]["edit"]);
	}
	else{
		header('Location: items_variations_manage.php?tab=list');
		die;
	}
}
else{
	header('Location: items_variations_manage.php?tab=list');
	die;
}