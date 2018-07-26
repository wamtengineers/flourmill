<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_POST["items_add"])){
	extract($_POST);
	$err="";
	if(empty($title))
		$err="Fields with (*) are Mandatory.<br />";
	if($err==""){
		$sql="INSERT INTO items (item_category_id, title, sortorder) VALUES ('".slash($item_category_id)."','".slash($title)."','".slash($sortorder)."')";
		doquery($sql,$dblink);
		$id = inserted_id();
		if( isset( $packing_ids ) && count( $packing_ids ) > 0 ) {
			foreach( $packing_ids as $packing_id ) {
				doquery( "insert into items_packing_sizes(item_id, packing_id) values( '".$id."', '".$packing_id."' )", $dblink );
			}
		}
		unset($_SESSION["items_manage"]["add"]);
		header('Location: items_manage.php?tab=list&msg='.url_encode("Sucessfully Added"));
		die;
	}
	else{
		foreach($_POST as $key=>$value)
			$_SESSION["items_manage"]["add"][$key]=$value;
		header('Location: items_manage.php?tab=add&err='.url_encode($err));
		die;
	}
}