<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_POST["purchase_edit"])){
	extract($_POST);
	$err="";
	if(empty($date) || empty($supplier_name) || count($items)==0)
		$err="Fields with (*) are Mandatory.<br />";
	$i=1;
	foreach($items as $item){
		if( empty( $item[ "item_category_id" ] ) || empty( $item[ "item_name" ] ) || empty( $item[ "purchase_price" ] ) || empty( $item[ "sale_price" ] ) || empty( $item[ "quantity" ] ) || empty( $item[ "total_price" ] )	 ) {
			$err .= 'Item '.$i.' have some empty fields.';
		}
		$i++;
		
	}
	if($err==""){
		$sql="Update purchase set `date`='".slash(datetime_dbconvert(unslash($date)))."',`supplier_name`='".slash($supplier_name)."',`phone`='".slash($phone)."',`address`='".slash($address)."',`supplier_id`='".slash($supplier_id)."' where id='".$id."'";
		doquery($sql,$dblink);
		$grand_total_price=$quantity=0;
		foreach($items as $item){
			$quantity+=$item["quantity"];
			$grand_total_price+=$item["total_price"];
			$prev=doquery("select * from purchase_items where purchase_id='".slash($id)."'", $dblink);
			if(numrows($prev)){
				$prev=dofetch($prev);
				doquery("update purchase_items set `item_category_id`='".slash($item["item_category_id"])."', `item_name`='".slash($item["item_name"])."', `purchase_price`='".slash($item["purchase_price"])."', `sale_price`='".slash($item["sale_price"])."', `quantity`='".slash($item["quantity"])."', `total_price`='".slash($item["total_price"])."' where id='".$prev["id"]."'", $dblink);
			}
			else{
				doquery("insert into purchase_items(purchase_id, item_category_id, item_name, purchase_price, sale_price, quantity, total_price) values('".$id."', '".$item_category_id."', '".slash($item["item_name"])."', '".$item["purchase_price"]."', '".$item["sale_price"]."', '".$item["quantity"]."', '".$total_price."')", $dblink);	
			}
		}
		doquery("update purchase set total_items=".$quantity.", discount='".$discount."', total_price='".$grand_total_price."', net_price='".($grand_total_price-$discount)."' where id='".$id."'", $dblink);
		unset($_SESSION["purchase_manage"]["edit"]);
		header('Location: purchase_manage.php?tab=list&msg='.url_encode("Sucessfully Updated"));
		die;
	}
	else{
		foreach($_POST as $key=>$value)
			$_SESSION["purchase_manage"]["edit"][$key]=$value;
		header('Location: purchase_manage.php?tab=edit&err='.url_encode($err));
		die;
	}
}
/*----------------------------------------------------------------------------------*/
if(isset($_GET["id"]) && $_GET["id"]!=""){
	$rs=doquery("select * from purchase where id='".slash($_GET["id"])."'",$dblink);
	if(numrows($rs)>0){
		$r=dofetch($rs);
		foreach($r as $key=>$value)
			$$key=htmlspecialchars(unslash($value));
		$date=datetime_convert($date);
		$items=array(
			"item_name" => "",
			"item_category_id" => "",
			"purchase_price" => "",
			"sale_price" => "",
			"quantity" => "",
			"total_price" => ""
		);
		$rs=doquery("select * from purchase_items where purchase_id='".$id."'", $dblink);
		if(numrows($rs)){
			while($r=dofetch($rs)){
				$item_name=$r["item_name"];
				$item_category_id=$r["item_category_id"];
				$purchase_price=$r["purchase_price"];
				$sale_price=$r["sale_price"];
				$quantity=$r["quantity"];
				$total_price=$r["total_price"];
			}
		}
		
		if(isset($_SESSION["purchase_manage"]["edit"]))
			extract($_SESSION["purchase_manage"]["edit"]);
	}
	else{
		header("Location: purchase_manage.php?tab=list");
		die;
	}
}
else{
	header("Location: purchase_manage.php?tab=list");
	die;
}