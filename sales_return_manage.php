<?php
include("include/db.php");
include("include/utility.php");
include("include/session.php");
include("include/paging.php");
define("APP_START", 1);
$filename = 'sales_return_manage.php';
include("include/admin_type_access.php");
$tab_array=array("list", "status", "delete", "bulk_action", "report","addedit");
if(isset($_REQUEST["tab"]) && in_array($_REQUEST["tab"], $tab_array)){
	$tab=$_REQUEST["tab"];
}
else{
	$tab="list";
}
$q="";
$extra='';
if(isset($_GET["date_from"])){
	$date_from=slash($_GET["date_from"]);
	$_SESSION["sales_return"]["list"]["date_from"]=$date_from;
}
if(isset($_SESSION["sales_return"]["list"]["date_from"]))
	$date_from=$_SESSION["sales_return"]["list"]["date_from"];
else
	$date_from="";
if($date_from != ""){
	$extra.=" and a.datetime_added > '".date('Y-m-d',strtotime(date_dbconvert($date_from)))." 00:00:00'";
	$is_search=true;
}
if(isset($_GET["date_to"])){
	$date_to=slash($_GET["date_to"]);
	$_SESSION["sales_return"]["list"]["date_to"]=$date_to;
}
if(isset($_SESSION["sales_return"]["list"]["date_to"]))
	$date_to=$_SESSION["sales_return"]["list"]["date_to"];
else
	$date_to="";
if($date_to != ""){
	$extra.=" and a.datetime_added<'".date('Y-m-d',strtotime(date_dbconvert($date_to)))." 23:59:59'";
	$is_search=true;
}
if(isset($_GET["account_id"])){
	$account_id=slash($_GET["account_id"]);
	$_SESSION["sales_return"]["list"]["account_id"]=$account_id;
}
if(isset($_SESSION["sales_return"]["list"]["account_id"]))
	$account_id=$_SESSION["sales_return"]["list"]["account_id"];
else
	$account_id="";
if(!empty($account_id)){
	$extra.=" and a.account_id = '".$account_id."'";
	$is_search=true;
}
if(isset($_GET["status"])){
	$_SESSION["sales_return"]["list"]["status"]=$_GET[ "status" ];
}
if(isset($_SESSION["sales_return"]["list"]["status"])){
	$status=$_SESSION["sales_return"]["list"]["status"];
}
else{
	$status=array();
}	
if(count( $status ) > 0){
	$sts = array();
	foreach( $status as $st )
		$sts[] = "a.status='".$st."'";
	$extra .= "and (".implode( " or ", $sts ).")";
	$is_search=true;
}
if(isset($_GET["transaction_id"])){
	$_SESSION["sales_return"]["list"]["transaction_id"]=$_GET[ "transaction_id" ];
}
if(isset($_SESSION["sales_return"]["list"]["transaction_id"])){
	$transaction_id=$_SESSION["sales_return"]["list"]["transaction_id"];
}
else{
	$transaction_id="";
}	
if(!empty($transaction_id)){
	if( $transaction_id==1 ){
		$extra.=" and transaction_id <> 0";
	}
	else{
		$extra.=" and transaction_id = 0";
	}
	$is_search=true;
}
if(isset($_GET["q"])){
	$q=slash($_GET["q"]);
	$_SESSION["sales_return"]["list"]["q"]=$q;
}
if(isset($_SESSION["sales_return"]["list"]["q"]))
	$q=$_SESSION["sales_return"]["list"]["q"];
else
	$q="";
if(!empty($q)){
	$extra.=" and (c.title like '%".$q."%' or a.id like '%".$q."%')";
	$is_search=true;
}
$order_by = "a.datetime_added";
$order = "desc";
if( isset($_GET["order_by"]) ){
	$_SESSION["sales_return"]["list"]["order_by"]=slash($_GET["order_by"]);
}
if( isset( $_SESSION["sales_return"]["list"]["order_by"] ) ){
	$order_by = $_SESSION["sales_return"]["list"]["order_by"];
}
if( isset($_GET["order"]) ){
	$_SESSION["sales_return"]["list"]["order"]=slash($_GET["order"]);
}
if( isset( $_SESSION["sales_return"]["list"]["order"] ) ){
	$order = $_SESSION["sales_return"]["list"]["order"];
}
$orderby = $order_by." ".$order;
$sql = "SELECT a.*, d.title as customer, e.amount, sum((b.quantity-b.less_weight)*if(b.rate=0,b.packing,1))-a.less_weight as total_items, group_concat(concat(b.quantity, ' × ', b.packing, 'KG ', c.title) SEPARATOR '<br>') as items, sum(b.total_price)-a.discount as total_price, b.unit_price FROM `sales_return` a left join sales_return_items b on a.id = b.sales_return_id left join items c on b.item_id = c.id left join account d on a.account_id = d.id left join transaction e on a.transaction_id = e.id where 1 $extra group by a.id order by $orderby";
switch($tab){
	case 'addedit':
		include("modules/sales_return/addedit_do.php");
	break;
	case 'delete':
		include("modules/sales_return/delete_do.php");
	break;
	case 'status':
		include("modules/sales_return/status_do.php");
	break;
	case 'bulk_action':
		include("modules/sales_return/bulkactions.php");
	break;
	case 'report':
		include("modules/sales_return/report.php");
		die;
	break;
}
?>
<?php include("include/header.php");?>
<?php
$manage_url = 'sales_return_manage.php';
?>
<script type="text/javascript">
	var $manage_url='<?php echo $manage_url?>';
</script>
  <div class="container-widget row">
    <div class="col-md-12">
      <?php
		switch($tab){
			case 'addedit':
				include("modules/common/addedit.php");
			break;
			case 'list':
				include("modules/sales_return/list.php");
			break;
		}
      ?>
    </div>
  </div>
</div>
<?php include("include/footer.php");?>