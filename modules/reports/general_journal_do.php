<?php
if(!defined("APP_START")) die("No Direct Access");
$extra='';
$is_search=true;
if(isset($_GET["date_from"])){
	$date_from=slash($_GET["date_from"]);
	$_SESSION["reports"]["general_journal"]["date_from"]=$date_from;
}
if(isset($_SESSION["reports"]["general_journal"]["date_from"]))
	$date_from=$_SESSION["reports"]["general_journal"]["date_from"];
else
	$date_from=date("d/m/Y");
if(isset($_GET["date_to"])){
	$date_to=slash($_GET["date_to"]);
	$_SESSION["reports"]["general_journal"]["date_to"]=$date_to;
}
if(isset($_SESSION["reports"]["general_journal"]["date_to"]))
	$date_to=$_SESSION["reports"]["general_journal"]["date_to"];
else
	$date_to=date("d/m/Y");
if(isset($_GET["account_id"])){
	$account_id=slash($_GET["account_id"]);
	$_SESSION["reports"]["general_journal"]["account_id"]=$account_id;
}
if(isset($_SESSION["reports"]["general_journal"]["account_id"]))
	$account_id=$_SESSION["reports"]["general_journal"]["account_id"];
else
	$account_id="";
$extra.=" and datetime_added BETWEEN '".date('Y-m-d',strtotime(date_dbconvert($date_from)))." 00:00:00' AND '".date('Y-m-d',strtotime(date_dbconvert($date_to)))." 23:59:59'";
if( !empty( $account_id ) ) {
	$account = dofetch( doquery( "select * from account where id='".$account_id."'", $dblink ) );
}

$order_by = "datetime_added";
$order = "desc";
if( isset($_GET["order_by"]) ){
	$_SESSION["reports"]["general_journal"]["order_by"]=slash($_GET["order_by"]);
}
if( isset( $_SESSION["reports"]["general_journal"]["order_by"] ) ){
	$order_by = $_SESSION["reports"]["general_journal"]["order_by"];
}
if( isset($_GET["order"]) ){
	$_SESSION["reports"]["general_journal"]["order"]=slash($_GET["order"]);
}
if( isset( $_SESSION["reports"]["general_journal"]["order"] ) ){
	$order = $_SESSION["reports"]["general_journal"]["order"];
}
$orderby = $order_by." ".$order;
$main_sql = array();
$main_sql[] = "select datetime_added, a.id, 0 as type, concat( 'Sales #', ' ', a.id) as details, sum(c.total_price)-a.discount as debit, 0 as credit from sales a left join account b on a.account_id=b.id left join sales_items c on a.id=c.sales_id where a.status <> 0 and account_id='".$account_id."' group by a.id";

$main_sql[] = "select datetime_added, a.id, 1 as type, concat( 'Sales Return #', ' ', a.id) as details, 0 as debit, sum(c.total_price)-a.discount as credit from sales_return a left join account b on a.account_id=b.id left join sales_return_items c on a.id=c.sales_return_id where a.status <> 0 and account_id='".$account_id."'";

$main_sql[] = "select datetime_added, a.id, 2 as type, concat( 'Purchase #', ' ', a.id) as details, 0 as debit, c.total_price-a.discount as credit from purchase a left join account b on a.account_id=b.id left join purchase_items c on a.id=c.purchase_id where a.status <> 0 and account_id='".$account_id."'";

$main_sql[] = "select datetime_added, a.id, 3 as type, concat( 'Purchase Return #', ' ', a.id) as details, c.total_price-a.discount as debit, 0 as credit from purchase_return a left join account b on a.account_id=b.id left join purchase_return_items c on a.id=c.purchase_return_id where a.status <> 0 and account_id='".$account_id."'";

$main_sql[] = "select datetime_added, a.id, 4 as type, if(details='', concat( 'Transfer to account ', title ), concat(title, ': ', details)) as details, amount as debit, amount as credit from transaction a left join account b on a.account_id=b.id where a.status = 1 and account_id = reference_id and reference_id='".$account_id."'";

$main_sql[] = "select datetime_added, a.id, 5 as type, if(details='', concat( 'Transfer from account ', title ), details) as details, amount as debit, 0 as credit from transaction a left join account b on a.reference_id=b.id where a.status = 1 and account_id != reference_id and account_id='".$account_id."'";

$main_sql[] = "select datetime_added, a.id, 6 as type, if(details='', concat( 'Transfer to account ', title ), concat(title, ': ', details)) as details, 0 as debit, amount as credit from transaction a left join account b on a.account_id=b.id where a.status = 1 and account_id != reference_id and reference_id='".$account_id."'";

$main_sql[] = "select datetime_added, a.id, 7 as type, if(details='', concat( 'Expense: ', title ), concat(title,': ', details)) as details, 0 as debit, amount as credit from expense a left join expense_category b on a.expense_category_id=b.id where a.status = 1 and account_id='".$account_id."'";

$main_sql="(".implode( ' union ', $main_sql ).") as total_records";
$sql = "select * from ".$main_sql." where 1 $extra order by $orderby";

//$balance = dofetch( doquery( "select sum(debit)-sum(credit) as balance from ".$main_sql." where datetime_added < '".date('Y-m-d',strtotime(date_dbconvert($date_from)))." 00:00:00'", $dblink ) );
if( $order == 'desc' ) {
	$balance = get_account_balance( $account_id, date_dbconvert($date_to)." 23:59:59" );
}
else{
	$balance = get_account_balance( $account_id, date_dbconvert($date_from) );
}
