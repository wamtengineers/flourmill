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
			$rs = doquery( "select * from sales_return where id='".$id."'", $dblink );
			if( numrows( $rs ) > 0 ) {
				$r = dofetch( $rs );
				$addedit = array(
					"id" => $r[ "id" ],
					"datetime_added" => datetime_convert( $r[ "datetime_added" ] ),
					"account_id" => $r[ "account_id" ],
					"status" => $r[ "status" ],
					"bill_no" => $r[ "bill_no" ],
					"less_weight" => $r[ "less_weight" ],
					"discount" => $r[ "discount" ],
					"brokery_id" => 0,
					"broker_id" => '0',
					"broker_amount" => 0,
					"cnf" => $r[ "cnf" ],
					"fare_transaction_id" => 0,
					"fare_of_vehicle" => 0,
					"fare_of_vehicle_payment_account_id" => '0',
					"transaction_id" => 0,
					"payment_account_id" => "",
					"payment_amount" => 0,
					
				);
				if( !empty( $r[ "brokery_id" ] ) ) {
					$expense = doquery( "select * from transaction where id = '".$r[ "brokery_id" ]."'", $dblink );
					if( numrows( $expense ) > 0 ) {
						$expense = dofetch( $expense );
						$addedit[ "brokery_id" ] = $expense[ "id" ];
						$addedit[ "broker_id" ] = $expense[ "account_id" ];
						$addedit[ "broker_amount" ] = $expense[ "amount" ];
					}
				}
				if( !empty( $r[ "fare_transaction_id" ] ) ) {
					if( $r[ "cnf" ] == 0 ) {
						$expense = doquery( "select * from expense where id = '".$r[ "fare_transaction_id" ]."'", $dblink );
						if( numrows( $expense ) > 0 ) {
							$expense = dofetch( $expense );
							$addedit[ "fare_transaction_id" ] = $expense[ "id" ];
							$addedit[ "fare_of_vehicle_payment_account_id" ] = $expense[ "account_id" ];
							$addedit[ "fare_of_vehicle" ] = $expense[ "amount" ];
						}
					}
					else{
						$transaction = doquery( "select * from transaction where id = '".$r[ "fare_transaction_id" ]."'", $dblink );
						if( numrows( $transaction ) > 0 ) {
							$transaction = dofetch( $transaction );
							$addedit[ "fare_transaction_id" ] = $transaction[ "id" ];
							$addedit[ "fare_of_vehicle_payment_account_id" ] = $transaction[ "reference_id" ];
							$addedit[ "fare_of_vehicle" ] = $transaction[ "amount" ];
						}
					}
				}
				if( !empty( $r[ "transaction_id" ] ) ) {
					$transaction = doquery( "select * from transaction where id = '".$r[ "transaction_id" ]."'", $dblink );
					if( numrows( $transaction ) > 0 ) {
						$transaction = dofetch( $transaction );
						$addedit[ "transaction_id" ] = $transaction[ "id" ];
						$addedit[ "payment_account_id" ] = $transaction[ "reference_id" ];
						$addedit[ "payment_amount" ] = $transaction[ "amount" ];
					}
				}
                $items = array();
				$rs = doquery( "select * from sales_return_items where sales_return_id='".$id."' order by id", $dblink );
				if( numrows( $rs ) > 0 ) {
					while( $r = dofetch( $rs ) ) {
						$items[] = array(
							"id" => $r["id"],
							"sales_return_id" => $r[ "sales_return_id" ],
							"item_id" => $r["item_id"],
							"packing" => $r["packing"],
							"quantity" => $r[ "quantity" ],
							"less_weight" => $r[ "less_weight" ],
							"unit_price" => $r[ "unit_price" ],
							"rate" => $r[ "rate" ],
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
					$check = dofetch( doquery( "select cnf, fare_transaction_id from sales_return where id = '".$addedit->id."'", $dblink ));
					if( $addedit->cnf!=$check[ "cnf" ] ){
						if( $check[ "cnf" ]==0 ){
							doquery( "delete from expense where id = '".$check[ "fare_transaction_id" ]."'", $dblink );
						}
						else{
							doquery( "delete from transaction where id = '".$check[ "fare_transaction_id" ]."'", $dblink );
						}
					}
					doquery( "update sales_return set `datetime_added`='".slash(datetime_dbconvert(unslash($addedit->datetime_added)))."', bill_no='".slash( $addedit->bill_no )."', `account_id`='".slash($addedit->account_id)."', `less_weight`='".slash($addedit->less_weight)."', `discount`='".slash($addedit->discount)."', `status`='".slash($addedit->status)."', `cnf`='".slash($addedit->cnf)."' where id='".$addedit->id."'", $dblink );
					$addedit_id = $addedit->id;
				}
				else {
					doquery( "insert into sales_return (datetime_added, bill_no, account_id, less_weight, discount, status, cnf, added_by) VALUES ('".slash(datetime_dbconvert($addedit->datetime_added))."', '".slash($addedit->bill_no)."', '".slash($addedit->account_id)."', '".slash($addedit->less_weight)."', '".slash($addedit->discount)."', '".slash($addedit->status)."', '".slash($addedit->cnf)."', '".$_SESSION[ "logged_in_admin" ][ "id" ]."')", $dblink );
					$addedit_id = inserted_id();
				}
				if( !empty( $addedit->broker_id ) && !empty( $addedit->broker_amount ) ) {
					$update = false;
					if( !empty( $addedit->brokery_id ) ) {
						$transaction = doquery( "select id from transaction where id='".$addedit->brokery_id."'", $dblink );
						if( numrows( $transaction ) > 0 ) {
							$update = true;
						}
					}
					if( $update ) {
						doquery( "update transaction set datetime_added='".slash(datetime_dbconvert($addedit->datetime_added))."', account_id = '".slash( $addedit->account_id )."', reference_id = '".slash( $addedit->broker_id )."', amount = '".slash( $addedit->broker_amount )."' where id = '".$addedit->brokery_id."'", $dblink );
					}
					else {
						doquery( "insert into transaction(datetime_added, account_id, reference_id, amount, details, added_by) values( '".slash(datetime_dbconvert($addedit->datetime_added))."', '".$addedit->account_id."', '".slash( $addedit->broker_id )."', '".slash( $addedit->broker_amount )."', 'Brokery against Sales Return #".$addedit_id."', '".$_SESSION[ "logged_in_admin" ][ "id" ]."' )", $dblink );
						$addedit->brokery_id = inserted_id();
					}
				}
				else{
					if( !empty( $addedit->brokery_id ) ) {
						doquery( "delete from transaction where id = '".$addedit->brokery_id."'", $dblink );
						$addedit->brokery_id = 0;
					}
				}
				doquery( "update sales_return set brokery_id = '".$addedit->brokery_id."' where id='".$addedit_id."'", $dblink );
				if( !empty( $addedit->fare_of_vehicle_payment_account_id ) && !empty( $addedit->fare_of_vehicle ) ) {
					if( $addedit->cnf==0 ){
						$update = false;
						if( !empty( $addedit->fare_transaction_id ) ) {
							$expense = doquery( "select id from expense where id='".$addedit->fare_transaction_id."'", $dblink );
							if( numrows( $expense ) > 0 ) {
								$update = true;
							}
						}
						if( $update ) {
							doquery( "update expense set datetime_added='".slash(datetime_dbconvert($addedit->datetime_added))."', account_id = '".slash( $addedit->fare_of_vehicle_payment_account_id )."', amount = '".slash( $addedit->fare_of_vehicle )."' where id = '".$addedit->fare_transaction_id."'", $dblink );
						}
						else {
							doquery( "insert into expense(datetime_added, expense_category_id, account_id, amount, details, added_by) values( '".slash(datetime_dbconvert($addedit->datetime_added))."', '".get_config( "fare_category_id" )."', '".slash( $addedit->fare_of_vehicle_payment_account_id )."', '".slash( $addedit->fare_of_vehicle )."', 'Fare of Vehicle against Sales Return #".$addedit_id."', '".$_SESSION[ "logged_in_admin" ][ "id" ]."' )", $dblink );
							$addedit->fare_transaction_id = inserted_id();
						}
					}
					else{
						$update = false;
						if( !empty( $addedit->fare_transaction_id ) ) {
							$transaction = doquery( "select id from transaction where id='".$addedit->fare_transaction_id."'", $dblink );
							if( numrows( $transaction ) > 0 ) {
								$update = true;
							}
						}
						if( $update ) {
							doquery( "update transaction set datetime_added='".slash(datetime_dbconvert($addedit->datetime_added))."', account_id = '".slash( $addedit->account_id )."', amount = '".slash( $addedit->fare_of_vehicle )."', reference_id = '".slash( $addedit->fare_of_vehicle_payment_account_id )."' where id = '".$addedit->fare_transaction_id."'", $dblink );
						}
						else {
							doquery( "insert into transaction(datetime_added, account_id, amount, reference_id, details, added_by) values( '".slash(datetime_dbconvert($addedit->datetime_added))."', '".slash( $addedit->account_id )."', '".slash( $addedit->fare_of_vehicle )."', '".slash( $addedit->fare_of_vehicle_payment_account_id )."', 'Fare of Vehicle against Sales Return #".$addedit_id."', '".$_SESSION[ "logged_in_admin" ][ "id" ]."' )", $dblink );
							$addedit->fare_transaction_id = inserted_id();
						}
					}
				}
				else{
					if( !empty( $addedit->fare_transaction_id ) ) {
						if( $addedit->cnf==1 ){
							doquery( "delete from expense where id = '".$addedit->fare_transaction_id."'", $dblink );
						}
						else{
							doquery( "delete from transaction where id = '".$addedit->fare_transaction_id."'", $dblink );
						}
						$addedit->fare_transaction_id = 0;
						
					}
				}
				doquery( "update sales_return set fare_transaction_id = '".$addedit->fare_transaction_id."' where id='".$addedit_id."'", $dblink );
				if( !empty( $addedit->payment_account_id ) && !empty( $addedit->payment_amount ) ) {
					$update = false;
					if( !empty( $addedit->transaction_id ) ) {
						$transaction = doquery( "select id from transaction where id='".$addedit->transaction_id."'", $dblink );
						if( numrows( $transaction ) > 0 ) {
							$update = true;
						}
					}
					if( $update ) {
						doquery( "update transaction set datetime_added='".slash(datetime_dbconvert($addedit->datetime_added))."', account_id = '".slash( $addedit->account_id )."', amount = '".slash( $addedit->payment_amount )."', reference_id = '".slash( $addedit->payment_account_id )."' where id = '".$addedit->transaction_id."'", $dblink );
					}
					else {
						doquery( "insert into transaction(account_id, datetime_added, amount, reference_id, details, added_by) values( '".slash( $addedit->account_id )."', NOW(), '".slash( $addedit->payment_amount )."', '".slash( $addedit->payment_account_id )."', 'Payment against Sales Return #".$addedit_id."', '".$_SESSION[ "logged_in_admin" ][ "id" ]."' )", $dblink );
						$addedit->transaction_id = inserted_id();
					}
				}
				else{
					if( !empty( $addedit->transaction_id ) ) {
						doquery( "delete from transaction where id = '".$addedit->transaction_id."'", $dblink );
						$addedit->transaction_id = 0;
					}
				}
				doquery( "update sales_return set transaction_id = '".$addedit->transaction_id."' where id='".$addedit_id."'", $dblink );
				$item_ids = array();
				foreach( $addedit->items as $item ) {
					if( !empty( $item->id ) ) { 
						doquery( "update sales_return_items set `item_id`='".slash( $item->item_id )."', `packing`='".slash( $item->packing )."', `unit_price`='".$item->unit_price."', `rate`='".$item->rate."', `quantity`='".$item->quantity."', `less_weight`='".$item->less_weight."', `total_price`='".$item->total."' where id='".$item->id."'", $dblink );
						$item_ids[] = $item->id;
					}
					else {						
						doquery( "insert into sales_return_items ( sales_return_id, item_id, packing, unit_price,  rate, quantity, less_weight, total_price ) values( '".$addedit_id."', '".$item->item_id."', '".$item->packing."', '".$item->unit_price."', '".$item->rate."', '".$item->quantity."', '".$item->less_weight."', '".$item->total."' )", $dblink );
						$item->id = inserted_id();
						$item_ids[] = $item->id;
						$quantity = $item->quantity;
					}
				}
				
				if( !empty( $addedit->id ) && count( $item_ids ) > 0 ) {
					$rs = doquery( "select * from sales_return_items where sales_return_id='".$addedit_id."' and id not in( ".implode( ",", $item_ids )." )", $dblink );
					
					doquery( "delete from sales_return_items where sales_return_id='".$addedit_id."' and id not in( ".implode( ",", $item_ids )." )", $dblink );
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