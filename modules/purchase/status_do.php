<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_GET["id"]) && !empty($_GET["id"])){
	$status = slash($_GET["s"]);
	$id=slash($_GET["id"]);
	$rec = doquery( "select * from purchase where id='".$id."'", $dblink );
	if( numrows( $rec ) > 0 ) {
		$rec = dofetch( $rec );
		if( $rec[ "transaction_id" ] > 0 ) {
			doquery( "update transaction set status='".$status."' where id = '".$rec[ "transaction_id" ]."'", $dblink );
		}
		if( $rec[ "brokery_id" ] > 0 ) {
			doquery( "update expense set status='".$status."' where id = '".$rec[ "brokery_id" ]."'", $dblink );
		}
		if( $rec[ "fare_transaction_id" ] > 0 ) {
			doquery( "update ".($rec[ "cnf" ]==0?"expense":"transaction")." set status='".$status."' where id = '".$rec[ "fare_transaction_id" ]."'", $dblink );
		}
	}
	doquery("update purchase set status='".$status."' where id='".$id."'",$dblink);
	header("Location: purchase_manage.php");
	die;
}