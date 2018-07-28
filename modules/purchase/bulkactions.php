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
				$purchase = doquery( "select * from purchase where id = '".$id[$i]."' ", $dblink );
				if( numrows( $purchase ) > 0 ) {
					$purchase = dofetch( $purchase );
					doquery("delete from purchase_items where purchase_id='".$id[$i]."'",$dblink);
					if( $purchase[ "transaction_id" ] > 0 ) {
						doquery( "delete from transaction where id = '".$purchase[ "transaction_id" ]."'", $dblink );
					}
					if( $purchase[ "brokery_id" ] > 0 ) {
						doquery( "delete from transaction where id = '".$purchase[ "brokery_id" ]."'", $dblink );
					}
					if( $purchase[ "fare_transaction_id" ] > 0 ) {
						doquery( "delete from ".($purchase[ "cnf" ]==0?"expense":"transaction")." where id = '".$purchase[ "fare_transaction_id" ]."'", $dblink );
					}
					doquery("delete from purchase where id='".$id[$i]."'",$dblink);
				}
				$i++;
			}
			header("Location: purchase_manage.php?tab=list&msg=".url_encode("Records Deleted."));
			die;
		}
		if($bulk_action=="statuson"){
			$i=0;
			while($i<count($id)){
				$rec = doquery( "select * from purchase where id='".$id[$i]."'", $dblink );
				if( numrows( $rec ) > 0 ) {
					$rec = dofetch( $rec );
					if( $rec[ "transaction_id" ] > 0 ) {
						doquery( "update transaction set status=1 where id = '".$rec[ "transaction_id" ]."'", $dblink );
					}
					if( $rec[ "brokery_id" ] > 0 ) {
						doquery( "update transaction set status=1 where id = '".$rec[ "brokery_id" ]."'", $dblink );
					}
					if( $rec[ "fare_transaction_id" ] > 0 ) {
						doquery( "update ".($rec[ "cnf" ]==0?"expense":"transaction")." set status=1 where id = '".$rec[ "fare_transaction_id" ]."'", $dblink );
					}
				}
				doquery("update purchase set status=1 where id='".$id[$i]."'",$dblink);
				$i++;
			}
			header("Location: purchase_manage.php?tab=list&msg=".url_encode("Records Status Arrived."));
			die;
		}
		if($bulk_action=="statusof"){
			$i=0;
			while($i<count($id)){
				$rec = doquery( "select * from purchase where id='".$id[$i]."'", $dblink );
				if( numrows( $rec ) > 0 ) {
					$rec = dofetch( $rec );
					if( $rec[ "transaction_id" ] > 0 ) {
						doquery( "update transaction set status=0 where id = '".$rec[ "transaction_id" ]."'", $dblink );
					}
					if( $rec[ "brokery_id" ] > 0 ) {
						doquery( "update transaction set status=0 where id = '".$rec[ "brokery_id" ]."'", $dblink );
					}
					if( $rec[ "fare_transaction_id" ] > 0 ) {
						doquery( "update ".($rec[ "cnf" ]==0?"expense":"transaction")." set status=0 where id = '".$rec[ "fare_transaction_id" ]."'", $dblink );
					}
				}
				doquery("update purchase set status=0 where id='".$id[$i]."'",$dblink);
				$i++;
			}
			header("Location: purchase_manage.php?tab=list&msg=".url_encode("Records Status Cancelled."));
			die;
		}
		if($bulk_action=="statusrec"){
			$i=0;
			while($i<count($id)){
				$rec = doquery( "select * from purchase where id='".$id[$i]."'", $dblink );
				if( numrows( $rec ) > 0 ) {
					$rec = dofetch( $rec );
					if( $rec[ "transaction_id" ] > 0 ) {
						doquery( "update transaction set status=1 where id = '".$rec[ "transaction_id" ]."'", $dblink );
					}
					if( $rec[ "brokery_id" ] > 0 ) {
						doquery( "update transaction set status=1 where id = '".$rec[ "brokery_id" ]."'", $dblink );
					}
					if( $rec[ "fare_transaction_id" ] > 0 ) {
						doquery( "update ".($rec[ "cnf" ]==0?"expense":"transaction")." set status=1 where id = '".$rec[ "fare_transaction_id" ]."'", $dblink );
					}
				}
				doquery("update purchase set status=1 where id='".$id[$i]."'",$dblink);
				$i++;
			}
			header("Location: purchase_manage.php?tab=list&msg=".url_encode("Records Status Received."));
			die;
		}
	}
	else{
		header("Location: purchase_manage.php?tab=list&err=".url_encode($err));
		die;					
	}
}
else{
	header("Location: index.php");
	die;	
}