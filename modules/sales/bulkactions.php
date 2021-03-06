<?php
if(!defined("APP_START")) die("No Direct Access");

if(isset($_GET["action"]) && $_GET["action"]!=""){
	$bulk_action=$_GET["action"];
	$id=explode(",",urldecode($_GET["Ids"]));	
	$err="";
	if($bulk_action=="null"){
		$err.="Select Action. <br>";
	}
	if(!isset($_GET["Ids"]) || $_GET["Ids"]==""){
		$err.="Select Records. <br>";	
	}
	if(empty($err)){
		if($bulk_action=="delete"){
			$i=0;
			while($i<count($id)){
				$sales = doquery( "select * from sales where id = '".$id[$i]."' ", $dblink );
				if( numrows( $sales ) > 0 ) {
					$sales = dofetch( $sales );
					doquery("delete from sales_items where sales_id='".$id[$i]."'",$dblink);
					if( $sales[ "transaction_id" ] > 0 ) {
						doquery( "delete from transaction where id = '".$sales[ "transaction_id" ]."'", $dblink );
					}
					if( $sales[ "brokery_id" ] > 0 ) {
						doquery( "delete from expense where id = '".$sales[ "brokery_id" ]."'", $dblink );
					}
					if( $sales[ "fare_transaction_id" ] > 0 ) {
						doquery( "delete from ".($sales[ "cnf" ]==1?"expense":"transaction")." where id = '".$sales[ "fare_transaction_id" ]."'", $dblink );
					}
					doquery("delete from sales where id='".$id[$i]."'",$dblink);
				}
				$i++;
			}
			header("Location: sales_manage.php?tab=list&msg=".url_encode("Records Deleted."));
			die;
		}
		if($bulk_action=="statuson"){
			$i=0;
			while($i<count($id)){
				$rec = doquery( "select * from sales where id='".$id[$i]."'", $dblink );
				if( numrows( $rec ) > 0 ) {
					$rec = dofetch( $rec );
					if( $rec[ "transaction_id" ] > 0 ) {
						doquery( "update transaction set status=1 where id = '".$rec[ "transaction_id" ]."'", $dblink );
					}
					if( $rec[ "brokery_id" ] > 0 ) {
						doquery( "update expense set status=1 where id = '".$rec[ "brokery_id" ]."'", $dblink );
					}
					if( $rec[ "fare_transaction_id" ] > 0 ) {
						doquery( "update ".($rec[ "cnf" ]==1?"expense":"transaction")." set status=1 where id = '".$rec[ "fare_transaction_id" ]."'", $dblink );
					}
				}
				doquery("update sales set status=1 where id='".$id[$i]."'",$dblink);
				$i++;
			}
			header("Location: sales_manage.php?tab=list&msg=".url_encode("Records Status Dispatched."));
			die;
		}
		if($bulk_action=="statusof"){
			$i=0;
			while($i<count($id)){
				$rec = doquery( "select * from sales where id='".$id[$i]."'", $dblink );
				if( numrows( $rec ) > 0 ) {
					$rec = dofetch( $rec );
					if( $rec[ "transaction_id" ] > 0 ) {
						doquery( "update transaction set status=0 where id = '".$rec[ "transaction_id" ]."'", $dblink );
					}
					if( $rec[ "brokery_id" ] > 0 ) {
						doquery( "update expense set status=0 where id = '".$rec[ "brokery_id" ]."'", $dblink );
					}
					if( $rec[ "fare_transaction_id" ] > 0 ) {
						doquery( "update ".($rec[ "cnf" ]==1?"expense":"transaction")." set status=0 where id = '".$rec[ "fare_transaction_id" ]."'", $dblink );
					}
				}
				doquery("update sales set status=0 where id='".$id[$i]."'",$dblink);
				$i++;
			}
			header("Location: sales_manage.php?tab=list&msg=".url_encode("Records Status Cancelled."));
			die;
		}
		if($bulk_action=="statusrec"){
			$i=0;
			while($i<count($id)){
				$rec = doquery( "select * from sales where id='".$id[$i]."'", $dblink );
				if( numrows( $rec ) > 0 ) {
					$rec = dofetch( $rec );
					if( $rec[ "transaction_id" ] > 0 ) {
						doquery( "update transaction set status=1 where id = '".$rec[ "transaction_id" ]."'", $dblink );
					}
					if( $rec[ "brokery_id" ] > 0 ) {
						doquery( "update expense set status=1 where id = '".$rec[ "brokery_id" ]."'", $dblink );
					}
					if( $rec[ "fare_transaction_id" ] > 0 ) {
						doquery( "update ".($rec[ "cnf" ]==1?"expense":"transaction")." set status=1 where id = '".$rec[ "fare_transaction_id" ]."'", $dblink );
					}
				}
				doquery("update sales set status=1 where id='".$id[$i]."'",$dblink);
				$i++;
			}
			header("Location: sales_manage.php?tab=list&msg=".url_encode("Records Status Delivering."));
			die;
		}
	}
	else{
		header("Location: sales_manage.php?tab=list&err=".url_encode($err));
		die;					
	}
}
else{
	header("Location: index.php");
	die;	
}