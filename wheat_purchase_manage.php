<?php
include("include/db.php");
include("include/utility.php");
include("include/session.php");
include("include/paging.php");
define("APP_START", 1);
$filename = 'wheat_purchase_manage.php';
include("include/admin_type_access.php");
$tab_array=array("list", "add", "edit", "status", "delete", "bulk_action", "print");
if(isset($_REQUEST["tab"]) && in_array($_REQUEST["tab"], $tab_array)){
	$tab=$_REQUEST["tab"];
}
else{
	$tab="list";
}
$q="";
$extra='';
$is_search=false;
if(isset($_GET["supplier_id"])){
	$supplier_id=slash($_GET["supplier_id"]);
	$_SESSION["wheat_purchase"]["list"]["supplier_id"]=$supplier_id;
}
if(isset($_SESSION["wheat_purchase"]["list"]["supplier_id"]))
	$supplier_id=$_SESSION["wheat_purchase"]["list"]["supplier_id"];
else
	$supplier_id="";
if($supplier_id!=""){
	$extra.=" and supplier_id='".$supplier_id."'";
	$is_search=true;
}
if(isset($_GET["date_from"])){
	$date_from=slash($_GET["date_from"]);
	$_SESSION["wheat_purchase"]["list"]["date_from"]=$date_from;
}
if(isset($_SESSION["wheat_purchase"]["list"]["date_from"]))
	$date_from=$_SESSION["wheat_purchase"]["list"]["date_from"];
else
	$date_from="";
if($date_from != ""){
	$extra.=" and datetime_added>='".datetime_dbconvert($date_from)."'";
	$is_search=true;
}
if(isset($_GET["date_to"])){
	$date_to=slash($_GET["date_to"]);
	$_SESSION["wheat_purchase"]["list"]["date_to"]=$date_to;
}
if(isset($_SESSION["wheat_purchase"]["list"]["date_to"]))
	$date_to=$_SESSION["wheat_purchase"]["list"]["date_to"];
else
	$date_to="";
if($date_to != ""){
	$extra.=" and datetime_added<'".datetime_dbconvert($date_to)."'";
	$is_search=true;
}
$sql="select * from wheat_purchase where 1 $extra order by datetime_added desc";
switch($tab){
	case 'add':
		include("modules/wheat_purchase/add_do.php");
	break;
	case 'edit':
		include("modules/wheat_purchase/edit_do.php");
	break;
	case 'delete':
		include("modules/wheat_purchase/delete_do.php");
	break;
	case 'status':
		include("modules/wheat_purchase/status_do.php");
	break;
	case 'bulk_action':
		include("modules/wheat_purchase/bulkactions.php");
	break;
	case 'print':
		include("modules/wheat_purchase/print_do.php");
	break;
}
?>
<?php include("include/header.php");?>
	<div class="container-widget row">
    	<div class="col-md-12">
		  <?php
            switch($tab){
                case 'list':
                    include("modules/wheat_purchase/list.php");
                break;
                case 'add':
                    include("modules/wheat_purchase/add.php");
                break;
                case 'edit':
                    include("modules/wheat_purchase/edit.php");
                break;
            }
          ?>
    	</div>
  	</div>
</div>
<?php include("include/footer.php");?>