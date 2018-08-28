<?php
include("include/db.php");
include("include/utility.php");
include("include/session.php");
include("include/paging.php");
define("APP_START", 1);
$filename = 'sales_manage.php';
include("include/admin_type_access.php");
$tab_array=array("list", "status", "delete", "bulk_action", "print","report","addedit");
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
	$_SESSION["sales"]["list"]["date_from"]=$date_from;
}
if(isset($_SESSION["sales"]["list"]["date_from"]))
	$date_from=$_SESSION["sales"]["list"]["date_from"];
else
	$date_from="";
if($date_from != ""){
	$extra.=" and datetime_added>='".datetime_dbconvert($date_from)."'";
	$is_search=true;
}
if(isset($_GET["date_to"])){
	$date_to=slash($_GET["date_to"]);
	$_SESSION["sales"]["list"]["date_to"]=$date_to;
}
if(isset($_SESSION["sales"]["list"]["date_to"]))
	$date_to=$_SESSION["sales"]["list"]["date_to"];
else
	$date_to="";
if($date_to != ""){
	$extra.=" and datetime_added<'".datetime_dbconvert($date_to)."'";
	$is_search=true;
}
if(isset($_GET["account_id"])){
	$account_id=slash($_GET["account_id"]);
	$_SESSION["sales"]["list"]["account_id"]=$account_id;
}
if(isset($_SESSION["sales"]["list"]["account_id"]))
	$account_id=$_SESSION["sales"]["list"]["account_id"];
else
	$account_id="";
if(!empty($account_id)){
	$extra.=" and account_id = '".$account_id."'";
	$is_search=true;
}
if(isset($_GET["status"])){
	$_SESSION["sales"]["list"]["status"]=$_GET[ "status" ];
}
if(isset($_SESSION["sales"]["list"]["status"])){
	$status=$_SESSION["sales"]["list"]["status"];
}
else{
	$status=array();
}	
if(count( $status ) > 0){
	$sts = array();
	foreach( $status as $st )
		$sts[] = "status='".$st."'";
	$extra .= "and (".implode( " or ", $sts ).")";
	$is_search=true;
}
if(isset($_GET["transaction_id"])){
	$_SESSION["sales"]["list"]["transaction_id"]=$_GET[ "transaction_id" ];
}
if(isset($_SESSION["sales"]["list"]["transaction_id"])){
	$transaction_id=$_SESSION["sales"]["list"]["transaction_id"];
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
	$_SESSION["sales"]["list"]["q"]=$q;
}
if(isset($_SESSION["sales"]["list"]["q"]))
	$q=$_SESSION["sales"]["list"]["q"];
else
	$q="";
if(!empty($q)){
	$extra.=" and (title like '%".$q."%' or items like '%".$q."%')";
	$is_search=true;
}
$order_by = "datetime_added";
$order = "desc";
if( isset($_GET["order_by"]) ){
	$_SESSION["sales"]["list"]["order_by"]=slash($_GET["order_by"]);
}
if( isset( $_SESSION["sales"]["list"]["order_by"] ) ){
	$order_by = $_SESSION["sales"]["list"]["order_by"];
}
if( isset($_GET["order"]) ){
	$_SESSION["sales"]["list"]["order"]=slash($_GET["order"]);
}
if( isset( $_SESSION["sales"]["list"]["order"] ) ){
	$order = $_SESSION["sales"]["list"]["order"];
}
$orderby = $order_by." ".$order;
$sql="select * from (select a.*, b.title, amount, (select sum((quantity-less_weight)*if(rate=0,packing,1)) from sales_items where sales_id = a.id)-less_weight as total_items, (select group_concat(concat(quantity, ' &times ', packing, 'KG ', title) SEPARATOR '<br>') from sales_items left join items on sales_items.item_id = items.id where sales_id = a.id) as items, (select sum(total_price) from sales_items where sales_id = a.id)-discount as total_price from sales a left join account b on a.account_id = b.id left join transaction c on a.transaction_id = c.id ) as temp_table where 1 $extra order by $orderby";
switch($tab){
	case 'addedit':
		include("modules/sales/addedit_do.php");
	break;
	case 'delete':
		include("modules/sales/delete_do.php");
	break;
	case 'status':
		include("modules/sales/status_do.php");
	break;
	case 'bulk_action':
		include("modules/sales/bulkactions.php");
	break;
	case "print":
		include("modules/sales/print.php");
	break;
	case 'report':
		include("modules/sales/report.php");
		die;
	break;
}
?>
<?php include("include/header.php");?>
<?php
$manage_url = 'sales_manage.php';
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
				include("modules/sales/list.php");
			break;
		}
      ?>
    </div>
  </div>
</div>
<?php if( isset( $_GET[ "print" ]) ){
	?>
	<iframe style="display:none" src="sales_manage.php?tab=print&id=<?php echo $_GET[ "print" ]?>"></iframe>
	<?php
}?> 
<?php include("include/footer.php");?>