<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_POST["purchase_return_edit"])){
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
		$sql="Update purchase_return set `date`='".slash(datetime_dbconvert(unslash($date)))."',`supplier_name`='".slash($supplier_name)."',`phone`='".slash($phone)."',`address`='".slash($address)."',`supplier_id`='".slash($supplier_id)."' where id='".$id."'";
		doquery($sql,$dblink);
		$grand_total_price=$quantity=0;
		foreach($items_array as $item_id=>$item){
			$r=dofetch(doquery("select unit_price from items where id='".slash($item_id)."'", $dblink));
			$total_price=($item["unit_price"])*$item["quantity"];
			$grand_total_price-=$total_price;
			$quantity-=$item["quantity"];
			$prev=doquery("select id, quantity from purchase_return_items where purchase_id='".$id."' and item_id='".$item_id."'", $dblink);
			if(numrows($prev)){
				$prev=dofetch($prev);
				doquery("update purchase_return_items set `unit_price`='".$item["unit_price"]."', `quantity`='".$item["quantity"]."', `total_price`='".$total_price."' where id='".$prev["id"]."'", $dblink);
				doquery("update items set quantity=quantity-".($item["quantity"]-$prev["quantity"])." where id='".slash($item_id)."'", $dblink);
			}
			else{
				doquery("insert into purchase_return_items(purchase_id, item_id, unit_price, quantity, total_price) values('".$id."', '".$item_id."', '".$item["unit_price"]."','".$item["quantity"]."', '".$total_price."')", $dblink);
				doquery("update items set quantity=quantity-".$item["quantity"]." where id='".slash($item_id)."'", $dblink);	
			}
		}
		doquery("update purchase_return set total_items=".$quantity.", discount='".$discount."', total_price='".$grand_total_price."', net_price='".($grand_total_price-$discount)."' where id='".$id."'", $dblink);
		$items = doquery( "select * from purchase_return_items where purchase_id='".$id."' and item_id not in (".implode($items, ",").")", $dblink );
		if( numrows( $items ) > 0 ) {
			while( $item = dofetch( $items ) ) {
				doquery("update items set quantity=quantity-".$item["quantity"]." where id='".slash($item[ "item_id" ])."'", $dblink);
				doquery( "delete from purchase_return_items where id='".$item[ "id" ]."'", $dblink );
			}
		}
		$purchase = dofetch( doquery( "select * from purchase where id = '".$id."' ", $dblink ) );
		if( $supplier_payment_account_id > 0 ) {
			if( $purchase[ "supplier_payment_id" ] == 0 ) {
				doquery( "insert into transaction( account_id, type, datetime_added, amount, details) values('".$supplier_payment_account_id."', 0, NOW(), '".$payment_amount."', 'Payment against Purchase ID: #".$id."')", $dblink );
				$transaction_id = inserted_id();
				doquery( "insert into supplier_payment(supplier_id, datetime, amount, transaction_id) values('".slash( $supplier_id )."', NOW(), '".$payment_amount."', '".$transaction_id."')", $dblink );
				$supplier_payment_id = inserted_id();
				doquery( "update purchase_return set supplier_payment_id = '".$supplier_payment_id."' where id ='".$id."'", $dblink);
				doquery( "update transaction set reference_id = '".$supplier_payment_id."' where id ='".$transaction_id."'", $dblink);
			}
			else {
				$supplier_payment = doquery( "select * from supplier_payment where id = '".$purchase[ "supplier_payment_id" ]."'", $dblink );
				if( numrows( $supplier_payment ) > 0 ) {
					$supplier_payment = dofetch( $supplier_payment );
					doquery( "update transaction set account_id = '".$supplier_payment_account_id."', amount = '".$payment_amount."' where id = '".$supplier_payment[ "transaction_id" ]."'", $dblink );
					doquery( "update supplier_payment set amount = '".$payment_amount."' where id = '".$supplier_payment[ "transaction_id" ]."'", $dblink );
				}
			}
		}
		else {
			if( $purchase[ "supplier_payment_id" ] > 0 ) {
				$supplier_payment = doquery( "select * from supplier_payment where id = '".$purchase[ "supplier_payment_id" ]."'", $dblink );
				if( numrows( $supplier_payment ) > 0 ) {
					$supplier_payment = dofetch( $supplier_payment );
					doquery( "delete from transaction where id = '".$supplier_payment[ "transaction_id" ]."'", $dblink );
				}
				doquery( "delete from supplier_payment where id = '".$purchase[ "supplier_payment_id" ]."'", $dblink );
				doquery( "update purchase_return set supplier_payment_id = 0, payment_amount = 0 where id = '".$id."'", $dblink );
			}
		}
		unset($_SESSION["purchase_return_manage"]["edit"]);
		header('Location: purchase_return_manage.php?tab=list&msg='.url_encode("Sucessfully Updated"));
		die;
	}
	else{
		foreach($_POST as $key=>$value)
			$_SESSION["purchase_return_manage"]["edit"][$key]=$value;
		header('Location: purchase_return_manage.php?tab=edit&err='.url_encode($err));
		die;
	}
}
/*----------------------------------------------------------------------------------*/
if(isset($_GET["id"]) && $_GET["id"]!=""){
	$rs=doquery("select * from purchase_return where id='".slash($_GET["id"])."'",$dblink);
	if(numrows($rs)>0){
		$r=dofetch($rs);
		foreach($r as $key=>$value)
			$$key=htmlspecialchars(unslash($value));
		$date=datetime_convert($date);
		$items=$unit_price=$quantity=array();
		$rs=doquery("select * from purchase_return_items where purchase_id='".$id."' order by id", $dblink);
		if(numrows($rs)){
			while($r=dofetch($rs)){
				$items[]=$r["item_id"];
				$unit_price[]=$r["unit_price"];
				$quantity[]=$r["quantity"];
			}
		}
		$supplier_payment_account_id = 0;
		if( $supplier_payment_id > 0 ) {
			$supplier_payment = doquery( "select account_id from supplier_payment a inner join transaction b on a.transaction_id = b.id where a.id = '".$supplier_payment_id."'", $dblink );
			if( numrows( $supplier_payment ) > 0 ) {
				$supplier_payment = dofetch( $supplier_payment );
				$supplier_payment_account_id = $supplier_payment[ "account_id" ];
			}
		}
		if(isset($_SESSION["purchase_return_manage"]["edit"]))
			extract($_SESSION["purchase_return_manage"]["edit"]);
	}
	else{
		header("Location: purchase_return_manage.php?tab=list");
		die;
	}
}
else{
	header("Location: purchase_return_manage.php?tab=list");
	die;
}