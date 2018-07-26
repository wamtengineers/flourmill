<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_POST["purchase_add"])){
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
		$sql="INSERT INTO purchase (date, supplier_name, phone, address, supplier_id) VALUES ('".slash(datetime_dbconvert($date))."', '".slash($supplier_name)."', '".slash($phone)."', '".slash($address)."', '".slash($supplier_id)."')";
		doquery($sql,$dblink);
		$purchase_id=inserted_id();
		$grand_total_price=$quantity=0;	
		foreach($items as $item){
			$quantity+=$item["quantity"];
			$grand_total_price+=$item["total_price"];
			doquery("insert into purchase_items(purchase_id, item_category_id, item_name, purchase_price, sale_price, quantity, total_price) values('".$purchase_id."', '".$item["item_category_id"]."', '".slash($item["item_name"])."', '".$item["purchase_price"]."', '".$item["sale_price"]."', '".$item["quantity"]."', '".$item["total_price"]."')", $dblink);
		}
		doquery("update purchase set total_items=".$quantity.", discount='".$discount."', total_price='".$grand_total_price."', net_price='".($grand_total_price-$discount)."' where id='".$purchase_id."'", $dblink);
		unset($_SESSION["purchase_manage"]["add"]);
		header('Location: purchase_manage.php?tab=list&msg='.url_encode("Sucessfully Added"));
		die;
	}
	else{
		foreach($_POST as $key=>$value)
			$_SESSION["purchase_manage"]["add"][$key]=$value;
		header('Location: purchase_manage.php?tab=add&err='.url_encode($err));
		die;
	}
}