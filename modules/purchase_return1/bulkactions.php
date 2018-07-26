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
				$purchase_return = doquery( "select * from purchase_return where id = '".$id[$i]."' ", $dblink );
				if( numrows( $purchase_return ) > 0 ) {
					$purchase_return = dofetch( $purchase_return );
					doquery("delete from purchase_return_items where purchase_return_id='".$id[$i]."'",$dblink);
					if( $purchase_return[ "transaction_id" ] > 0 ) {
						doquery( "delete from transaction where id = '".$purchase_return[ "transaction_id" ]."'", $dblink );
					}
					doquery("delete from purchase_return where id='".$id[$i]."'",$dblink);
				}
				$i++;
			}
			header("Location: purchase_return_manage.php?tab=list&msg=".url_encode("Records Deleted."));
			die;
		}
		if($bulk_action=="statuson"){
			$i=0;
			while($i<count($id)){
				$rec = doquery( "select * from purchase_return where id='".$id[$i]."'", $dblink );
				if( numrows( $rec ) > 0 ) {
					$rec = dofetch( $rec );
					if( $rec[ "transaction_id" ] > 0 ) {
						doquery( "update transaction set status=1 where id = '".$rec[ "transaction_id" ]."'", $dblink );
					}
				}
				doquery("update purchase_return set status=1 where id='".$id[$i]."'",$dblink);
				$i++;
			}
			header("Location: purchase_return_manage.php?tab=list&msg=".url_encode("Records Status On."));
			die;
		}
		if($bulk_action=="statusof"){
			$i=0;
			while($i<count($id)){
				$rec = doquery( "select * from purchase_return where id='".$id[$i]."'", $dblink );
				if( numrows( $rec ) > 0 ) {
					$rec = dofetch( $rec );
					if( $rec[ "transaction_id" ] > 0 ) {
						doquery( "update transaction set status=1 where id = '".$rec[ "transaction_id" ]."'", $dblink );
					}
				}
				doquery("update purchase_return set status=0 where id='".$id[$i]."'",$dblink);
				$i++;
			}
			header("Location: purchase_return_manage.php?tab=list&msg=".url_encode("Records Status Off."));
			die;
		}
	}
	else{
		header("Location: purchase_return_manage.php?tab=list&err=".url_encode($err));
		die;					
	}
}
else{
	header("Location: index.php");
	die;	
}