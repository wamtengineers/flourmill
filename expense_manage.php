<?php
include("include/db.php");
include("include/utility.php");
include("include/session.php");
include("include/paging.php");
define("APP_START", 1);
$filename = 'expense_manage.php';
include("include/admin_type_access.php");
$tab_array=array("list", "add", "edit", "status", "delete", "bulk_action","voucher", "print");
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
	$_SESSION["expense"]["list"]["date_from"]=$date_from;
}
if(isset($_SESSION["expense"]["list"]["date_from"]))
	$date_from=$_SESSION["expense"]["list"]["date_from"];
else
	$date_from="";
if($date_from != ""){
	$extra.=" and datetime_added>='".datetime_dbconvert($date_from)."'";
	$is_search=true;
}
if(isset($_GET["date_to"])){
	$date_to=slash($_GET["date_to"]);
	$_SESSION["expense"]["list"]["date_to"]=$date_to;
}
if(isset($_SESSION["expense"]["list"]["date_to"]))
	$date_to=$_SESSION["expense"]["list"]["date_to"];
else
	$date_to="";
if($date_to != ""){
	$extra.=" and datetime_added<'".datetime_dbconvert($date_to)."'";
	$is_search=true;
}
if(isset($_GET["expense_category_id"])){
	$expense_category_id=slash($_GET["expense_category_id"]);
	$_SESSION["expense"]["list"]["expense_category_id"]=$expense_category_id;
}
if(isset($_SESSION["expense"]["list"]["expense_category_id"]))
	$expense_category_id=$_SESSION["expense"]["list"]["expense_category_id"];
else
	$expense_category_id="";
if($expense_category_id!=""){
	$extra.=" and expense_category_id='".$expense_category_id."'";
	$is_search=true;
}
if(isset($_GET["account_id"])){
	$account_id=slash($_GET["account_id"]);
	$_SESSION["expense"]["list"]["account_id"]=$account_id;
}
if(isset($_SESSION["expense"]["list"]["account_id"]))
	$account_id=$_SESSION["expense"]["list"]["account_id"];
else
	$account_id="";
if($account_id!=""){
	$extra.=" and account_id='".$account_id."'";
	$is_search=true;
}
$sql="select * from expense where 1 $extra order by datetime_added desc";
switch($tab){
	case 'add':
		include("modules/expense/add_do.php");
	break;
	case 'edit':
		include("modules/expense/edit_do.php");
	break;
	case 'delete':
		include("modules/expense/delete_do.php");
	break;
	case 'status':
		include("modules/expense/status_do.php");
	break;
	case 'bulk_action':
		include("modules/expense/bulkactions.php");
	break;
	case 'voucher':
		include("modules/expense/voucher.php");
	break;
	case 'print':
		include("modules/expense/print.php");
	break;
}
?>
<?php include("include/header.php");?>
	<div class="container-widget row">
    	<div class="col-md-12">
		  <?php
            switch($tab){
                case 'list':
                    include("modules/expense/list.php");
                break;
                case 'add':
                    include("modules/expense/add.php");
                break;
                case 'edit':
                    include("modules/expense/edit.php");
                break;
            }
          ?>
    	</div>
  	</div>
</div>
<?php include("include/footer.php");?>