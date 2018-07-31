<?php
if(!defined("APP_START")) die("No Direct Access");
if( count( $_POST ) > 0 ) {
	$response = array();
	extract( $_POST );
	if( !isset( $date ) ) {
		if( isset( $_SESSION[ "pos" ][ "date" ] ) ) {
			$date =  $_SESSION[ "pos" ][ "date" ];
		}
		else{
			$date = date( "d/m/Y" );
		}
	}
	$date = date_dbconvert( $date )." 00:00:00";
	$from = get_last_closing_dt( $date );
	$to = get_next_closing_dt( $date );
	if( isset( $action ) ) {
		switch( $action ) {
			case "get_dt":
				if( !isset( $_SESSION[ "pos" ][ "date" ] ) ) {
					$_SESSION[ "pos" ][ "date" ] = date( "d/m/Y" );
				}
				$response = $_SESSION[ "pos" ][ "date" ];
			break;
			case "set_dt":
				$_SESSION[ "pos" ][ "date" ] = $_POST[ "date" ];
			break;
			case "get_products":
				$rs = doquery( "select * from items where status = 1 order by sortorder", $dblink );
				if( numrows( $rs ) > 0 ) {
					while( $r = dofetch( $rs ) ) {
						$packing = array();
						$rs2 = doquery( "select a.* from packing a inner join items_packing_sizes b on a.id = b.packing_id where item_id = '".$r[ "id" ]."' order by total_units", $dblink );
						if( numrows( $rs2 ) > 0 ) {
							while( $r2 = dofetch( $rs2 ) ) {
								$packing[] = array(
									"title" => unslash( $r2[ "title" ] ),
									"packing" => (float)$r2[ "total_units" ]
								);
							}
						}
						$response[] = array(
							"id" => $r[ "id" ],
							"title" => unslash( $r[ "title" ] ),
							"packing" => $packing
						);
					}
				}
			break;
			case "get_orders":
				$rs = doquery( "select a.id from ".$module." a left join admin b on a.added_by = b.id where datetime_added>'".$from."' and datetime_added<='".$to."' order by datetime_added desc, id desc", $dblink );
				$orders = array();
				if( numrows( $rs ) > 0 ) {
					while( $r = dofetch( $rs ) ) {
						$orders[] = get_order( $r[ "id" ], $module);
					}
				}
				$response = $orders;
			break;
			case "save_order":
				$err = array();
				$order= json_decode($order);
				if( isset( $order->items ) && is_array( $order->items ) ) {
					if( !isset( $order->account_id ) || empty( $order->account_id ) ) {
						$err[] = "Select Account";
					}
					if( ( !isset( $order->payment_account_id ) || empty( $order->payment_account_id ) ) && $order->payment_amount > 0 ) {
						$err[] = "Select Payment Account";
					}
					if( ( !isset( $order->broker_account_id ) || empty( $order->broker_account_id ) ) && $order->broker_amount > 0 ) {
						$err[] = "Select Broker Account";
					}
					if( ( !isset( $order->fare_of_vehicle_payment_account_id ) || empty( $order->fare_of_vehicle_payment_account_id ) ) && $order->fare_of_vehicle > 0 ) {
						$err[] = "Select Fare Payment Account";
					}
					if( count( $order->items ) > 0 ) {
						if( count( $err ) == 0 ) {
							if( $type == 1 ){
								$module = "sales";
								$account_id = $order->payment_account_id;
								$reference_id = $order->account_id;
							}
							else if( $type == 2 ){
								$module = "purchase";
								$account_id = $order->account_id;
								$reference_id = $order->payment_account_id;
							}
							doquery( "insert into ".$module."( account_id, datetime_added, less_weight, discount, cnf, added_by ) values('".$order->account_id."', NOW(), '".$order->less_weight."', '".$order->discount."', '".$order->cnf."', '".$_SESSION[ "logged_in_admin" ][ "id" ]."')", $dblink );
							$order_id = inserted_id();
							foreach( $order->items as $item ) {
								$total_price = ($item->quantity-(int)$item->less_weight) * $item->unit_price;
								doquery("insert into ".$module."_items(".$module."_id, item_id, packing, unit_price, rate, quantity, less_weight, total_price) values('".$order_id."', '".$item->item_id."', '".$item->packing."', '".$item->unit_price."', '".$item->rate."', '".$item->quantity."', '".$item->less_weight."', '".$total_price."')", $dblink);
							}
							if( !empty( $order->payment_amount ) ) {
								doquery( "insert into transaction( account_id, reference_id, datetime_added, amount, details, added_by ) values( '".$account_id."', '".$reference_id."', NOW(), '".$order->payment_amount."', 'Payment against ".$module." order #".$order_id."', '".$_SESSION[ "logged_in_admin" ][ "id" ]."' )", $dblink );
								$transaction_id = inserted_id();
								doquery( "update ".$module." set transaction_id = '".$transaction_id."' where id = '".$order_id."'", $dblink );
							}
							if(!empty( $order->broker_account_id ) ) {
								if( $module == 'sales' ) {
									doquery( "insert into expense( datetime_added, expense_category_id, account_id, amount, details, added_by ) values( NOW(), '".get_config( "brokery_category_id" )."', '".slash( $order->broker_account_id )."', '".slash( $order->broker_amount )."', 'Brokery against ".$module." order #".$order_id."', '".$_SESSION[ "logged_in_admin" ][ "id" ]."' )", $dblink );
									$brokery_id = inserted_id();
								}
								else{
									doquery( "insert into transaction( datetime_added, account_id, reference_id, amount, details, added_by ) values( NOW(), '".slash( $order->account_id )."', '".slash( $order->broker_account_id )."', '".slash( $order->broker_amount )."', 'Brokery against ".$module." order #".$order_id."', '".$_SESSION[ "logged_in_admin" ][ "id" ]."' )", $dblink );
									$brokery_id = inserted_id();
								}
								doquery( "update ".$module." set brokery_id = '".$brokery_id."' where id = '".$order_id."'", $dblink );
							}
							if( !empty( $order->fare_of_vehicle_payment_account_id ) ) {
								if( $order->cnf==1 && $module=='sales' ){
									doquery( "insert into expense( datetime_added, expense_category_id, account_id, amount, details, added_by ) values( NOW(), '".get_config( "fare_category_id" )."', '".slash( $order->fare_of_vehicle_payment_account_id )."', '".slash( $order->fare_of_vehicle )."', 'Fare of Vehicle against ".$module." #".$order_id."', '".$_SESSION[ "logged_in_admin" ][ "id" ]."' )", $dblink );
									$fare_transaction_id = inserted_id();
								}
								else{
									doquery( "insert into transaction( datetime_added, account_id, reference_id, amount, details, added_by ) values( NOW(), '".slash( $order->account_id )."', '".slash( $order->fare_of_vehicle_payment_account_id )."', '".slash( $order->fare_of_vehicle )."', 'Fare of Vehicle against ".$module." #".$order_id."', '".$_SESSION[ "logged_in_admin" ][ "id" ]."' )", $dblink );
									$fare_transaction_id = inserted_id();
								}
								doquery( "update ".$module." set fare_transaction_id = '".$fare_transaction_id."' where id = '".$order_id."'", $dblink );
							}
							$order = get_order( $order_id, $module );
						}
					}
					else {
						$err[] = "Blank order";
					}
				}
				else{
					$err[] = "Invalid data.";
				}
				if( count( $err ) > 0 ) {
					$response = array(
						"status" => 0,
						"message" => implode("\n",$err)
					);
				}
				else{
					$response = array(
						"status" => 1,
						"order" => $order
					);
				}
			break;
			case "update_order":
				doquery( "update ".$module." set status = '".$status."' where id = '".$id."'", $dblink );
				$update_order = dofetch(doquery( "select * from ".$module." where id = '".$id."'", $dblink ));
				if( $update_order[ "transaction_id" ] > 0 ) {
					doquery( "update transaction set status = '".$status."' where id = '".$update_order["transaction_id"]."'", $dblink );
				}
				if( $update_order[ "brokery_id" ] > 0 ) {
					doquery( "update ".($update_order[ "cnf" ]==1?"expense":"transaction")." set status='".$status."' where id = '".$update_order[ "brokery_id" ]."'", $dblink );
				}
				if( $update_order[ "fare_transaction_id" ] > 0 ) {
					doquery( "update ".($update_order[ "cnf" ]==1?"expense":"transaction")." set status='".$status."' where id = '".$update_order[ "fare_transaction_id" ]."'", $dblink );
				}
			break;
			case "get_accounts":
				$rs = doquery( "select a.*, b.title as account_type from account a left join account_type b on a.account_type_id = b.id where a.status=1 and b.status = 1 order by b.sortorder, a.title", $dblink );
				$drawbox_id = get_config( 'drawbox_id' );
				$dailysale_customer_id = get_config( 'dailysale_customer_id' );
				$dailypurchase_supplier_id = get_config( 'dailypurchase_supplier_id' );
				$accounts = array();
				if( numrows( $rs ) > 0 ) {
					while( $r = dofetch( $rs ) ) {
						$accounts[] = array(
							"id" => $r[ "id" ],
							"account_type_id" => $r[ "account_type_id" ],
							"title" => unslash($r[ "title" ]),
							"is_petty_cash" => $r[ "id" ]==$drawbox_id?1:0,
							"is_daily_sale" => $r[ "id" ]==$dailysale_customer_id?1:0,
							"is_daily_purchase" => $r[ "id" ]==$dailypurchase_supplier_id?1:0,
							"balance" => get_account_balance( $r[ "id" ], $from )
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
			case "get_expense_category":
				$rs = doquery( "select * from expense_category where status=1 order by title", $dblink );
				$expense_categories = array();
				if( numrows( $rs ) > 0 ) {
					while( $r = dofetch( $rs ) ) {
						$expense_categories[] = array(
							"id" => $r[ "id" ],
							"title" => unslash($r[ "title" ]),
						);
					}
				}
				$response = $expense_categories;
			break;
			case "get_expense":
				$rs = doquery( "select * from expense where datetime_added>'".$from."' and datetime_added<='".$to."' and status = 1 order by datetime_added desc, id desc", $dblink );
				$expense = array();
				if( numrows( $rs ) > 0 ) {
					while( $r = dofetch( $rs ) ) {
						$expense[] = array(
							"id" => $r[ "id" ],
							"account_id" => $r[ "account_id" ],
							"expense_category_id" => $r[ "expense_category_id" ],
							"datetime_added" => date("h:i A", strtotime($r[ "datetime_added" ])),
							"amount" => $r[ "amount" ],
							"details" => unslash( $r[ "details" ] )
						);
					}
				}
				$response = $expense;
			break;
			case "add_expense":
				$expense = json_decode( $expense );
				if( !empty( $expense->expense_category_id ) && !empty( $expense->account_id ) && !empty( $expense->amount ) ) {
					doquery("insert into expense(datetime_added, expense_category_id, details, amount, account_id, added_by) values(NOW(), '".slash($expense->expense_category_id)."', '".slash($expense->details)."', '".slash($expense->amount)."', '".slash($expense->account_id)."', '".$_SESSION["logged_in_admin"]["id"]."')", $dblink);
					$id = inserted_id();
					$r = dofetch(doquery("select * from expense where id ='".$id."'", $dblink));
					$expense = array(
						"id" => $r[ "id" ],
						"datetime_added" => date("h:i A", strtotime($r[ "datetime_added" ])),
						"expense_category_id" => get_field( unslash($r["expense_category_id"]), "expense_category", "title" ),
						"details" => unslash($r[ "details" ]),
						"amount" => unslash($r[ "amount" ]),
						"account_id" => $r["account_id"],
					);
					$response = array(
						"status" => 1,
						"expense" => $expense
					);
				}
				else{
					$response = array(
						"status" => 0,
						"message" => "Enter Category, Account and Amount"
					);
				}				
			break;
			case "get_transactions":
				$rs = doquery( "select * from transaction where datetime_added>'".$from."' and datetime_added<='".$to."' and status = 1 order by datetime_added desc, id desc", $dblink );
				$transaction = array();
				if( numrows( $rs ) > 0 ) {
					while( $r = dofetch( $rs ) ) {
						$transaction[] = array(
							"id" => $r[ "id" ],
							"account_id" => $r[ "account_id" ],
							"reference_id" => $r[ "reference_id" ],
							"datetime_added" => date("h:i A", strtotime($r[ "datetime_added" ])),
							"amount" => $r[ "amount" ],
							"details" => unslash( $r[ "details" ] )
						);
					}
				}
				$response = $transaction;
			break;
			case "add_transaction":
				$transaction = json_decode( $transaction );
				if( !empty( $transaction->account_id ) && !empty( $transaction->reference_id ) && !empty( $transaction->amount ) ) {
					doquery("insert into transaction (datetime_added, account_id, reference_id, details, amount, added_by) values(NOW(), '".slash($transaction->account_id)."', '".slash($transaction->reference_id)."', '".slash($transaction->details)."', '".slash($transaction->amount)."', '".$_SESSION["logged_in_admin"]["id"]."')", $dblink);
					$id = inserted_id();
					$r = dofetch(doquery("select * from transaction where id ='".$id."'", $dblink));
					$transaction = array(
						"id" => $r[ "id" ],
						"datetime_added" => date("h:i A", strtotime($r[ "datetime_added" ])),
						"reference_id" => $r["reference_id"],
						"details" => unslash($r[ "details" ]),
						"amount" => unslash($r[ "amount" ]),
						"account_id" => $r["account_id"],
					);
					$response = array(
						"status" => 1,
						"transaction" => $transaction
					);
				}
				else{
					$response = array(
						"status" => 0,
						"message" => "Enter Account and Amount"
					);
				}				
			break;
		}
	}
	echo json_encode( $response );
	die;
}
if( isset($_GET[ "tab" ]) && in_array( $_GET[ "tab" ], array("print_receipt")) ) {
	switch( $_GET[ "tab" ] ) {
		case "print_receipt":
			include("modules/sales/print.php");
			die;
		break;
	}
}
