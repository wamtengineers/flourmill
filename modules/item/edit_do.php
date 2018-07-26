<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_POST["items_edit"])){
	extract($_POST);
	$err="";
	if(empty($title))
		$err="Fields with (*) are Mandatory.<br />";
	if($err==""){
		$sql="Update items set `item_category_id`='".slash($item_category_id)."',`title`='".slash($title)."',`sortorder`='".slash($sortorder)."' where id='".$id."'";
		doquery($sql,$dblink);
		doquery("delete from items_packing_sizes where item_id='".$id."'", $dblink);
		if( isset( $packing_ids ) && count( $packing_ids ) > 0 ) {
			foreach( $packing_ids as $packing_id ) {
				doquery( "insert into items_packing_sizes(item_id, packing_id) values( '".$id."', '".$packing_id."' )", $dblink );
			}
		}
		unset($_SESSION["items_manage"]["edit"]);
		header('Location: items_manage.php?tab=list&msg='.url_encode("Sucessfully Updated"));
		die;
	}
	else{
		foreach($_POST as $key=>$value)
			$_SESSION["items_manage"]["edit"][$key]=$value;
		header("Location: items_manage.php?tab=edit&err=".url_encode($err)."&id=$id");
		die;
	}
}
/*----------------------------------------------------------------------------------*/
if(isset($_GET["id"]) && $_GET["id"]!=""){
	$rs=doquery("select * from items where id='".slash($_GET["id"])."'",$dblink);
	if(numrows($rs)>0){
		$r=dofetch($rs);
		foreach($r as $key=>$value)
			$$key=htmlspecialchars(unslash($value));
			$packing_ids = array();
			$packing="select * from items_packing_sizes where item_id='".$id."'";
			$rs1 = doquery( $packing, $dblink );
			if( numrows( $rs1 ) > 0 ) {
				while( $r1 = dofetch( $rs1 ) ) {
					$packing_ids[] = $r1[ "packing_id" ];
				}
			}
		if(isset($_SESSION["items_manage"]["edit"]))
			extract($_SESSION["items_manage"]["edit"]);
	}
	else{
		header("Location: items_manage.php?tab=list");
		die;
	}
}
else{
	header("Location: items_manage.php?tab=list");
	die;
}