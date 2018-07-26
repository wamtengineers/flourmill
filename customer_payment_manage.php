<?php
include("include/db.php");
include("include/utility.php");
include("include/session.php");
include("include/paging.php");
define("APP_START", 1);
$tab_array=array("list", "add", "edit", "status", "delete", "bulk_action", "print", "print_receipt");
if(isset($_REQUEST["tab"]) && in_array($_REQUEST["tab"], $tab_array)){
	$tab=$_REQUEST["tab"];
}
else{
	$tab="list";
}
$q="";
$extra='';
$is_search=false;
if(isset($_GET["date_from"])){
	$date_from=slash($_GET["date_from"]);
	$_SESSION["customer_payment"]["list"]["date_from"]=$date_from;
}
if(isset($_SESSION["customer_payment"]["list"]["date_from"]))
	$date_from=$_SESSION["customer_payment"]["list"]["date_from"];
else
	$date_from="";
if($date_from != ""){
	$extra.=" and datetime_added>='".datetime_dbconvert($date_from)."'";
	$is_search=true;
}
if(isset($_GET["date_to"])){
	$date_to=slash($_GET["date_to"]);
	$_SESSION["customer_payment"]["list"]["date_to"]=$date_to;
}
if(isset($_SESSION["customer_payment"]["list"]["date_to"]))
	$date_to=$_SESSION["customer_payment"]["list"]["date_to"];
else
	$date_to="";
if($date_to != ""){
	$extra.=" and datetime_added<'".datetime_dbconvert($date_to)."'";
	$is_search=true;
}
if(isset($_GET["customer_id"])){
	$customer_id=slash($_GET["customer_id"]);
	$_SESSION["customer_payment"]["list"]["customer_id"]=$customer_id;
}
if(isset($_SESSION["customer_payment"]["list"]["customer_id"]))
	$customer_id=$_SESSION["customer_payment"]["list"]["customer_id"];
else
	$customer_id="";
if($customer_id!=""){
	$extra.=" and customer_id='".$customer_id."'";
	$is_search=true;
}
if(isset($_GET["account_id"])){
	$account_id=slash($_GET["account_id"]);
	$_SESSION["customer_payment"]["list"]["account_id"]=$account_id;
}
if(isset($_SESSION["customer_payment"]["list"]["account_id"]))
	$account_id=$_SESSION["customer_payment"]["list"]["account_id"];
else
	$account_id="";
if($account_id!=""){
	$extra.=" and account_id='".$account_id."'";
	$is_search=true;
}
$order_by = "datetime_added";
$order = "desc";
if( isset($_GET["order_by"]) ){
	$_SESSION["customer_payment"]["list"]["order_by"]=slash($_GET["order_by"]);
}
if( isset( $_SESSION["customer_payment"]["list"]["order_by"] ) ){
	$order_by = $_SESSION["customer_payment"]["list"]["order_by"];
}
if( isset($_GET["order"]) ){
	$_SESSION["customer_payment"]["list"]["order"]=slash($_GET["order"]);
}
if( isset( $_SESSION["customer_payment"]["list"]["order"] ) ){
	$order = $_SESSION["customer_payment"]["list"]["order"];
}
$orderby = $order_by." ".$order;
$sql="select a.*, b.customer_name, b.address from customer_payment a inner join customer b on a.customer_id = b.id where 1 ".$extra." order by $orderby";
switch($tab){
	case 'add':
		include("modules/customer_payment/add_do.php");
	break;
	case 'edit':
		include("modules/customer_payment/edit_do.php");
	break;
	case 'delete':
		include("modules/customer_payment/delete_do.php");
	break;
	case 'status':
		include("modules/customer_payment/status_do.php");
	break;
	case 'bulk_action':
		include("modules/customer_payment/bulkactions.php");
	break;
	case 'print':
		include("modules/customer_payment/print_do.php");
	break;
	case 'print_receipt':
		include("modules/customer_payment/print_receipt.php");
	break;
}
?>
<?php include("include/header.php");?>
  <div class="container-widget row">
    <div class="col-md-12">
      <?php
		switch($tab){
			case 'list':
				include("modules/customer_payment/list.php");
			break;
			case 'add':
				include("modules/customer_payment/add.php");
			break;
			case 'edit':
				include("modules/customer_payment/edit.php");
			break;
		}
      ?>
    </div>
  </div>
</div>
<?php include("include/footer.php");?>