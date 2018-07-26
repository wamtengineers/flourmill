<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_POST["action"])){
	$response = array();
	switch($_POST["action"]){
		case 'get_datetime':
			$response = datetime_convert( date( "Y-m-d H:i:s" ) );
		break;
		case "get_accounts":
			$rs = doquery( "select a.*, b.title as account_type from account a left join account_type b on a.account_type_id = b.id where a.status=1 and b.status = 1 order by b.sortorder, a.title", $dblink );
			$accounts = array();
			$drawbox_id = get_config( 'drawbox_id' );
			if( numrows( $rs ) > 0 ) {
				while( $r = dofetch( $rs ) ) {
					$accounts[] = array(
						"id" => $r[ "id" ],
						"account_type_id" => $r[ "account_type_id" ],
						"is_petty_cash" => $r[ "id" ]==$drawbox_id?1:0,
						"title" => unslash($r[ "title" ]),
					);
				}
			}
			
			$rs = doquery( "select * from account_type where status=1 order by sortorder", $dblink );
			$account_types = array();
			if( numrows( $rs ) > 0 ) {
				while( $r = dofetch( $rs ) ) {
					$account_types[] = array(
						"id" => $r[ "id" ],
						
						"title" => unslash($r[ "title" ]),
						
					);
				}
			}
			$response = array( 
				"account_types" => $account_types,
				"accounts" => $accounts,
			);
		break;
		case "get_items":
			$rs = doquery( "select * from items where status=1 order by sortorder asc ", $dblink );
			$items = array();
			if( numrows( $rs ) > 0 ) {
				while( $r = dofetch( $rs ) ) {
					$items[] = array(
						"id" => $r[ "id" ],
						"title" => $r[ "title" ]
					);
				}
			}
			$response = $items;
		break;
		case "get_addedit":
			$id = slash( $_POST[ "id" ] );
			$rs = doquery( "select * from purchase_return where id='".$id."'", $dblink );
			if( numrows( $rs ) > 0 ) {
				$r = dofetch( $rs );
				$addedit = array(
					"id" => $r[ "id" ],
					"datetime_added" => datetime_convert( $r[ "datetime_added" ] ),
					"account_id" => $r[ "account_id" ],
					"broker_id" => $r[ "broker_id" ],
					"broker_amount" => $r[ "broker_amount" ],
					"less_weight" => $r[ "less_weight" ],
					"discount" => $r[ "discount" ],
					"status" => $r[ "status" ],
					"cnf" => $r[ "cnf" ],
					"transaction_id" => 0,
					"payment_account_id" => "",
					"payment_amount" => 0,
					
				);
				if( !empty( $r[ "transaction_id" ] ) ) {
					$transaction = doquery( "select * from transaction where id = '".$r[ "transaction_id" ]."'", $dblink );
					if( numrows( $transaction ) > 0 ) {
						$transaction = dofetch( $transaction );
						$addedit[ "transaction_id" ] = $transaction[ "id" ];
						$addedit[ "payment_account_id" ] = $transaction[ "account_id" ];
						$addedit[ "reference_id" ] = $r[ "account_id" ];
						$addedit[ "payment_amount" ] = $transaction[ "amount" ];
					}
				}
                $items = array();
				$rs = doquery( "select * from purchase_return_items where purchase_return_id='".$id."' order by id", $dblink );
				if( numrows( $rs ) > 0 ) {
					while( $r = dofetch( $rs ) ) {
						$items[] = array(
							"id" => $r["id"],
							"purchase_return_id" => $r[ "purchase_return_id" ],
							"item_id" => $r["item_id"],
							"packing" => $r["packing"],
							"quantity" => $r[ "quantity" ],
							"unit_price" => $r[ "unit_price" ],
							"total" => $r[ "total_price" ],
                        );
					}
				}
				$addedit[ "items" ] = $items;
			}
			$response = $addedit;
		break;
		case "save_addedit":
			$err = array();
			$addedit = json_decode( $_POST[ "addedit" ] );
			if( empty( $addedit->datetime_added ) ) {
				$err[] = "Fields with * are mandatory";
				
			}
			if( count( $addedit->items ) == 0 ) {
				$err[] = "Add some items first.";
			}
			else {
				$i=1;
				foreach( $addedit->items as $item ) {
					if(empty( $item->item_id ) ){
						$err[] = (empty( $item->item_id )?"Select Item":"").(empty( $item->item_id ))." at Row#".$i;
					}
					$i++;
				}
			}
			if( count( $err ) == 0 ) {
				if( !empty( $addedit->id ) ) {
					doquery( "update purchase_return set `datetime_added`='".slash(datetime_dbconvert(unslash($addedit->datetime_added)))."', `account_id`='".slash($addedit->account_id)."', `less_weight`='".slash($addedit->less_weight)."', `discount`='".slash($addedit->discount)."', `status`='".slash($addedit->status)."', `broker_id`='".slash($addedit->broker_id)."', `broker_amount`='".slash($addedit->broker_amount)."', `cnf`='".slash($addedit->cnf)."' where id='".$addedit->id."'", $dblink );
					$addedit_id = $addedit->id;
				}
				else {
					doquery( "insert into purchase_return (datetime_added, account_id, less_weight, discount, status, broker_id, broker_amount, cnf, added_by) VALUES ('".slash(datetime_dbconvert($addedit->datetime_added))."', '".slash($addedit->account_id)."', '".slash($addedit->less_weight)."', '".slash($addedit->discount)."', '".slash($addedit->status)."', '".slash($addedit->broker_id)."', '".slash($addedit->broker_amount)."', '".slash($addedit->cnf)."', '".$_SESSION[ "logged_in_admin" ][ "id" ]."')", $dblink );
					$addedit_id = inserted_id();
				}
				if( !empty( $addedit->payment_account_id ) ) {
					$update = false;
					if( !empty( $addedit->transaction_id ) ) {
						$transaction = doquery( "select id from transaction where id='".$addedit->transaction_id."'", $dblink );
						if( numrows( $transaction ) > 0 ) {
							$update = true;
						}
					}
					if( $update ) {
						doquery( "update transaction set account_id = '".slash( $addedit->payment_account_id )."', amount = '".slash( $addedit->payment_amount )."', reference_id = '".slash( $addedit->account_id )."' where id = '".$addedit->transaction_id."'", $dblink );
					}
					else {
						doquery( "insert into transaction(account_id, datetime_added, amount, reference_id, details) values( '".slash( $addedit->payment_account_id )."', NOW(), '".slash( $addedit->payment_amount )."', '".slash( $addedit->account_id )."', 'Payment against Purchase Return #".$addedit_id."' )", $dblink );
						$addedit->transaction_id = inserted_id();
						doquery( "update purchase_return set transaction_id = '".$addedit->transaction_id."' where id='".$addedit_id."'", $dblink );
					}
				}
				$item_ids = array();
				foreach( $addedit->items as $item ) {
					if( !empty( $item->id ) ) {  
						doquery( "update purchase_return_items set `item_id`='".slash( $item->item_id )."', `packing`='".slash( $item->packing )."', `unit_price`='".$item->unit_price."', `quantity`='".$item->quantity."', `total_price`='".$item->total."' where id='".$item->id."'", $dblink );
						$item_ids[] = $item->id;
					}
					else {						
						doquery( "insert into purchase_return_items ( purchase_return_id, item_id, packing, unit_price, quantity, total_price ) values( '".$addedit_id."', '".$item->item_id."', '".$item->packing."', '".$item->unit_price."', '".$item->quantity."', '".$item->total."' )", $dblink );
						$item->id = inserted_id();
						$item_ids[] = $item->id;
						$quantity = $item->quantity;
					}
				}
				
				if( !empty( $addedit->id ) && count( $item_ids ) > 0 ) {
					$rs = doquery( "select * from purchase_return_items where purchase_return_id='".$addedit_id."' and id not in( ".implode( ",", $item_ids )." )", $dblink );
					
					doquery( "delete from purchase_return_items where purchase_return_id='".$addedit_id."' and id not in( ".implode( ",", $item_ids )." )", $dblink );
				}
				$response = array(
					"status" => 1,
					"id" => $addedit_id
				);
			}
			else {
				$response = array(
					"status" => 0,
					"error" => $err
				);
			}
		break;
	}
	echo json_encode( $response );
	die;
}