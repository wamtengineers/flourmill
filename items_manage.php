<?php
include("include/db.php");
include("include/utility.php");
include("include/session.php");
include("include/paging.php");
define("APP_START", 1);
$filename = 'items_manage.php';
include("include/admin_type_access.php");
$tab_array=array("list", "add", "edit", "status", "delete", "bulk_action", "report");
if(isset($_REQUEST["tab"]) && in_array($_REQUEST["tab"], $tab_array)){
	$tab=$_REQUEST["tab"];
}
else{
	$tab="list";
}
$q="";
$extra='';
$is_search=false;
if(isset($_GET["q"])){
	$q=slash($_GET["q"]);
	$_SESSION["items"]["list"]["q"]=$q;
}
if(isset($_SESSION["items"]["list"]["q"]))
	$q=$_SESSION["items"]["list"]["q"];
else
	$q="";
if(!empty($q)){
	$extra.=" and title like '%".$q."%'";
	$is_search=true;
}

if(isset($_GET["category"])){
	$category=slash($_GET["category"]);
	$_SESSION["items"]["list"]["category"]=$category;
}
if(isset($_SESSION["items"]["list"]["category"]))
	$category=$_SESSION["items"]["list"]["category"];
else
	$category="";

if($category!=""){
	$extra.=" and item_category_id='".$category."'";
	$is_search=true;
}


$order_by = "sortorder";
$order = "asc";
if( isset($_GET["order_by"]) ){
	$_SESSION["items"]["list"]["order_by"]=slash($_GET["order_by"]);
}
if( isset( $_SESSION["items"]["list"]["order_by"] ) ){
	$order_by = $_SESSION["items"]["list"]["order_by"];
}
if( isset($_GET["order"]) ){
	$_SESSION["items"]["list"]["order"]=slash($_GET["order"]);
}
if( isset( $_SESSION["items"]["list"]["order"] ) ){
	$order = $_SESSION["items"]["list"]["order"];
}
$orderby = $order_by." ".$order;
$sql="select * from items where 1 $extra order by $orderby";
switch($tab){
	case 'add':
		include("modules/item/add_do.php");
	break;
	case 'edit':
		include("modules/item/edit_do.php");
	break;
	case 'delete':
		include("modules/item/delete_do.php");
	break;
	case 'status':
		include("modules/item/status_do.php");
	break;
	case 'bulk_action':
		include("modules/item/bulkactions.php");
	break;
	case 'report':
		include("modules/item/report.php");
		die;
	break;
}
?>
<?php include("include/header.php");?>
  <div class="container-widget row">
    <div class="col-md-12">
      <?php
	  
		switch($tab){
			case 'list':
				include("modules/item/list.php");
			break;
			case 'add':
				include("modules/item/add.php");
			break;
			case 'edit':
				include("modules/item/edit.php");
			break;
		}
      ?>
    </div>
  </div>
</div>
<?php include("include/footer.php");?>