<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_GET["id"]) && !empty($_GET["id"])){
	$id=slash($_GET["id"]);
	$sales_return = doquery( "select * from sales_return where id = '".$id."' ", $dblink );
	if( numrows( $sales_return ) > 0 ) {
		$sales_return = dofetch( $sales_return );
		doquery("delete from sales_return_items where sales_return_id='".$id."'",$dblink);
		if( $sales_return[ "transaction_id" ] > 0 ) {
			doquery( "delete from transaction where id = '".$sales_return[ "transaction_id" ]."'", $dblink );
		}
		if( $sales_return[ "brokery_id" ] > 0 ) {
			doquery( "delete from expense where id = '".$sales_return[ "brokery_id" ]."'", $dblink );
		}
		if( $sales_return[ "fare_transaction_id" ] > 0 ) {
			doquery( "delete from ".($sales_return[ "cnf" ]==0?"expense":"transaction")." where id = '".$sales_return[ "fare_transaction_id" ]."'", $dblink );
		}
		doquery("delete from sales_return where id='".$id."'",$dblink);
	}
	header("Location: sales_return_manage.php?msg=".url_encode( "Record deleted successfully." ));
	die;
}