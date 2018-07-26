<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_POST["items_variations_add"])){
	extract($_POST);
	$err="";
	if( empty($quantity) || empty($price) ) {
		$err="Fields with (*) are Mandatory.<br />";
	}
	if($err==""){
		$sql="INSERT INTO items_variations (item_id, quantity, price, cost_price) VALUES ('".slash($parent_item_id)."', '".slash($quantity)."', '".slash($price)."', '".slash($cost_price)."')";
		doquery($sql,$dblink);
		$id = inserted_id();
		$attributes = doquery( "select * from items_attributes where item_id = '".$parent_item[ "id" ]."'", $dblink );
		if( numrows( $attributes ) > 0 ) {
			while( $attribute = dofetch( $attributes ) ) {
				if( isset( $items_attribute[ $attribute[ "id" ] ] ) ){
					doquery( "insert into items_variations_attributes values( '".$id."', '".$attribute[ "id" ]."', '".slash( $items_attribute[ $attribute[ "id" ] ] )."' )", $dblink );
				}
			}
		}
		update_stock( $parent_item_id );
		unset($_SESSION["items_variations_manage"]["add"]);
		header('Location: items_variations_manage.php?tab=list&msg='.url_encode("Sucessfully Added"));
		die;
	}
	else{
		foreach($_POST as $key=>$value)
			$_SESSION["items_variations_manage"]["add"][$key]=$value;
		header('Location: items_variations_manage.php?tab=add&err='.url_encode($err));
		die;
	}
}