<?php
include("include/db.php");
include("include/utility.php");
include("include/session.php");
include("include/paging.php");
define("APP_START", 1);
$filename = 'purchase_return_manage.php';
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
$is_search=false;
if(isset($_GET["date_from"])){
	$date_from=slash($_GET["date_from"]);
	$_SESSION["purchase_return"]["list"]["date_from"]=$date_from;
}
if(isset($_SESSION["purchase_return"]["list"]["date_from"]))
	$date_from=$_SESSION["purchase_return"]["list"]["date_from"];
else
	$date_from="";
if($date_from != ""){
	$extra.=" and datetime_added>='".datetime_dbconvert($date_from)."'";
	$is_search=true;
}
if(isset($_GET["date_to"])){
	$date_to=slash($_GET["date_to"]);
	$_SESSION["purchase_return"]["list"]["date_to"]=$date_to;
}
if(isset($_SESSION["purchase_return"]["list"]["date_to"]))
	$date_to=$_SESSION["purchase_return"]["list"]["date_to"];
else
	$date_to="";
if($date_to != ""){
	$extra.=" and datetime_added<'".datetime_dbconvert($date_to)."'";
	$is_search=true;
}
if(isset($_GET["account_id"])){
	$account_id=slash($_GET["account_id"]);
	$_SESSION["purchase_return"]["list"]["account_id"]=$account_id;
}
if(isset($_SESSION["purchase_return"]["list"]["account_id"]))
	$account_id=$_SESSION["purchase_return"]["list"]["account_id"];
else
	$account_id="";
if(!empty($account_id)){
	$extra.=" and account_id = '".$account_id."'";
	$is_search=true;
}
if(isset($_GET["q"])){
	$q=slash($_GET["q"]);
	$_SESSION["purchase_return"]["list"]["q"]=$q;
}
if(isset($_SESSION["purchase_return"]["list"]["q"]))
	$q=$_SESSION["purchase_return"]["list"]["q"];
else
	$q="";
if(!empty($q)){
	$extra.=" and (title like '%".$q."%' or items like '%".$q."%')";
	$is_search=true;
}
$order_by = "datetime_added";
$order = "desc";
if( isset($_GET["order_by"]) ){
	$_SESSION["purchase_return"]["list"]["order_by"]=slash($_GET["order_by"]);
}
if( isset( $_SESSION["purchase_return"]["list"]["order_by"] ) ){
	$order_by = $_SESSION["purchase_return"]["list"]["order_by"];
}
if( isset($_GET["order"]) ){
	$_SESSION["purchase_return"]["list"]["order"]=slash($_GET["order"]);
}
if( isset( $_SESSION["purchase_return"]["list"]["order"] ) ){
	$order = $_SESSION["purchase_return"]["list"]["order"];
}
$orderby = $order_by." ".$order;
$sql="select * from (select a.*, b.title, amount, (select sum(quantity*packing) from purchase_return_items where purchase_return_id = a.id) as total_items, (select group_concat(concat(quantity, ' &times ', packing, 'KG ', title) SEPARATOR '<br>') from purchase_return_items left join items on purchase_return_items.item_id = items.id where purchase_return_id = a.id) as items, (select sum(total_price) from purchase_return_items where purchase_return_id = a.id)-discount as total_price from purchase_return a left join account b on a.account_id = b.id left join transaction c on a.transaction_id = c.id ) as temp_table where 1 $extra order by $orderby";
switch($tab){
	case 'addedit':
		include("modules/purchase_return/addedit_do.php");
	break;
	case 'delete':
		include("modules/purchase_return/delete_do.php");
	break;
	case 'status':
		include("modules/purchase_return/status_do.php");
	break;
	case 'bulk_action':
		include("modules/purchase_return/bulkactions.php");
	break;
	case 'report':
		include("modules/purchase_return/report.php");
		die;
	break;
}
?>
<?php include("include/header.php");?>
<script type="text/javascript">
	var $manage_url='purchase_return_manage.php';
</script>
  <div class="container-widget row">
    <div class="col-md-12">
      <?php
		switch($tab){
			case 'addedit':
				include("modules/common/addedit.php");
			break;
			case 'list':
				include("modules/purchase_return/list.php");
			break;
		}
      ?>
    </div>
  </div>
</div>
<?php include("include/footer.php");?>