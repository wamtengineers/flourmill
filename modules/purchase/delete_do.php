<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_GET["id"]) && !empty($_GET["id"])){
	$id=slash($_GET["id"]);
	$purchase = doquery( "select * from purchase where id = '".$id."' ", $dblink );
	if( numrows( $purchase ) > 0 ) {
		$purchase = dofetch( $purchase );
		doquery("delete from purchase_items where purchase_id='".$id."'",$dblink);
		if( $purchase[ "transaction_id" ] > 0 ) {
			doquery( "delete from transaction where id = '".$purchase[ "transaction_id" ]."'", $dblink );
		}
		if( $purchase[ "brokery_id" ] > 0 ) {
			doquery( "delete from expense where id = '".$purchase[ "brokery_id" ]."'", $dblink );
		}
		if( $purchase[ "fare_transaction_id" ] > 0 ) {
			doquery( "delete from ".($purchase[ "cnf" ]==0?"expense":"transaction")." where id = '".$purchase[ "fare_transaction_id" ]."'", $dblink );
		}
		doquery("delete from purchase where id='".$id."'",$dblink);
	}
	header("Location: purchase_manage.php?msg=".url_encode( "Record deleted successfully." ));
	die;
}