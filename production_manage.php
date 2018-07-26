<?php
include("include/db.php");
include("include/utility.php");
include("include/session.php");
include("include/paging.php");
define("APP_START", 1);
$filename = 'production_manage.php';
include("include/admin_type_access.php");
$tab_array=array("list", "add", "edit", "status", "delete", "bulk_action");
if(isset($_REQUEST["tab"]) && in_array($_REQUEST["tab"], $tab_array)){
	$tab=$_REQUEST["tab"];
}
else{
	$tab="list";
}

switch($tab){
	case 'add':
		include("modules/production/add_do.php");
	break;
	case 'edit':
		include("modules/production/edit_do.php");
	break;
	case 'delete':
		include("modules/production/delete_do.php");
	break;
	case 'status':
		include("modules/production/status_do.php");
	break;
	case 'bulk_action':
		include("modules/production/bulkactions.php");
	break;
}
?>
<?php include("include/header.php");?>
  <div class="container-widget row">
    <div class="col-md-12">
      <?php
	  
		switch($tab){
			case 'list':
				include("modules/production/list.php");
			break;
			case 'add':
				include("modules/production/add.php");
			break;
			case 'edit':
				include("modules/production/edit.php");
			break;
		}
      ?>
    </div>
  </div>
</div>
<?php include("include/footer.php");?>