<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_GET["id"]) && !empty($_GET["id"])){
	$id=slash($_GET["id"]);
	$purchase_return = doquery( "select * from purchase_return where id = '".$id."' ", $dblink );
	if( numrows( $purchase_return ) > 0 ) {
		$purchase_return = dofetch( $purchase_return );
		doquery("delete from purchase_return_items where purchase_return_id='".$id."'",$dblink);
		if( $purchase_return[ "transaction_id" ] > 0 ) {
			doquery( "delete from transaction where id = '".$purchase_return[ "transaction_id" ]."'", $dblink );
		}
		if( $purchase_return[ "brokery_id" ] > 0 ) {
			doquery( "delete from expense where id = '".$purchase_return[ "brokery_id" ]."'", $dblink );
		}
		if( $purchase_return[ "fare_transaction_id" ] > 0 ) {
			doquery( "delete from ".($purchase_return[ "cnf" ]==1?"expense":"transaction")." where id = '".$purchase_return[ "fare_transaction_id" ]."'", $dblink );
		}
		doquery("delete from purchase_return where id='".$id."'",$dblink);
	}
	header("Location: purchase_return_manage.php?msg=".url_encode( "Record deleted successfully." ));
	die;
}