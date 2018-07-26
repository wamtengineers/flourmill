<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_GET["id"]) && !empty($_GET["id"])){
	$status = slash($_GET["s"]);
	$id=slash($_GET["id"]);
	$rec = doquery( "select * from sales_return where id='".$id."'", $dblink );
	if( numrows( $rec ) > 0 ) {
		$rec = dofetch( $rec );
		if( $rec[ "transaction_id" ] > 0 ) {
			doquery( "update transaction set status='".$status."' where id = '".$rec[ "transaction_id" ]."'", $dblink );
		}
	}
	doquery("update sales_return set status='".$status."' where id='".$id."'",$dblink);
	header("Location: sales_return_manage.php");
	die;
}