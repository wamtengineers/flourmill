<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_GET["id"]) && !empty($_GET["id"])){
	$id=slash($_GET["id"]);
	$sales = doquery( "select * from sales where id = '".$id."' ", $dblink );
	if( numrows( $sales ) > 0 ) {
		$sales = dofetch( $sales );
		doquery("delete from sales_items where sales_id='".$id."'",$dblink);
		if( $sales[ "transaction_id" ] > 0 ) {
			doquery( "delete from transaction where id = '".$sales[ "transaction_id" ]."'", $dblink );
		}
		if( $sales[ "brokery_id" ] > 0 ) {
			doquery( "delete from expense where id = '".$sales[ "brokery_id" ]."'", $dblink );
		}
		if( $sales[ "fare_transaction_id" ] > 0 ) {
			doquery( "delete from ".($sales[ "cnf" ]==1?"expense":"transaction")." where id = '".$sales[ "fare_transaction_id" ]."'", $dblink );
		}
		doquery("delete from sales where id='".$id."'",$dblink);
	}
	header("Location: sales_manage.php?msg=".url_encode( "Record deleted successfully." ));
	die;
}