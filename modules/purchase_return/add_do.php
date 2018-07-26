<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_POST["purchase_return_add"])){
	extract($_POST);
	$err="";
	if(empty($date) || empty($supplier_name) || count($items)==0)
		$err="Fields with (*) are Mandatory.<br />";
	$items_array=array();
	$i=0;
	foreach($items as $item){
		if(!empty($item)){
			if(array_key_exists($item, $items_array)){
				$items_array[$item]["quantity"]-=$quantity[$i];
			}
			else{
				$items_array[$item]=array(
					"unit_price" => $unit_price[$i],
					"quantity" => $quantity[$i]
				);
			}
		}
		$i++;
	}
	if($err==""){
		$sql="INSERT INTO purchase_return (date, supplier_name, phone, address, supplier_id) VALUES ('".slash(datetime_dbconvert($date))."', '".slash($supplier_name)."', '".slash($phone)."', '".slash($address)."', '".slash($supplier_id)."')";
		doquery($sql,$dblink);
		$purchase_id=inserted_id();
		$grand_total_price=$quantity=0;	
		foreach($items_array as $item_id=>$item){
			$r=dofetch(doquery("select unit_price from items where id='".slash($item_id)."'", $dblink));
			$total_price=($item["unit_price"])*$item["quantity"];
			$quantity-=$item["quantity"];
			$grand_total_price-=$total_price;
			doquery("insert into purchase_return_items(purchase_id, item_id, unit_price, quantity, total_price) values('".$purchase_id."', '".$item_id."', '".$item["unit_price"]."', '".$item["quantity"]."', '".$total_price."')", $dblink);
			doquery("update items set quantity=quantity-".$item["quantity"]." where id='".slash($item_id)."'", $dblink);
		}
		doquery("update purchase_return set total_items=".$quantity.", discount='".$discount."', total_price='".$grand_total_price."', net_price='".($grand_total_price-$discount)."' where id='".$purchase_id."'", $dblink);
		if( $supplier_payment_account_id > 0 ) {
			doquery( "insert into transaction( account_id, type, datetime_added, amount, details) values('".$supplier_payment_account_id."', 0, NOW(), '".$payment_amount."', 'Payment against Purchase ID: #".$purchase_id."')", $dblink );
			$transaction_id = inserted_id();
			doquery( "insert into supplier_payment(supplier_id, datetime, amount, transaction_id) values('".slash( $supplier_id )."', NOW(), '".$payment_amount."', '".$transaction_id."')", $dblink );
			$supplier_payment_id = inserted_id();
			doquery( "update purchase_return set supplier_payment_id = '".$supplier_payment_id."' where id ='".$purchase_id."'", $dblink);
			doquery( "update transaction set reference_id = '".$supplier_payment_id."' where id ='".$transaction_id."'", $dblink);
		}
		unset($_SESSION["purchase_return_manage"]["add"]);
		header('Location: purchase_return_manage.php?tab=list&msg='.url_encode("Sucessfully Added"));
		die;
	}
	else{
		foreach($_POST as $key=>$value)
			$_SESSION["purchase_return_manage"]["add"][$key]=$value;
		header('Location: purchase_return_manage.php?tab=add&err='.url_encode($err));
		die;
	}
}