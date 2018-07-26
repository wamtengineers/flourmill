<?php
include("include/db.php");
include("include/utility.php");
include("include/session.php");
include("include/paging.php");
define("APP_START", 1);
$filename = 'brokery_manage.php';
if(isset($_GET["parent_id"])){
	$_SESSION["brokery"]["account_id"]=$_GET["parent_id"];
}
if( isset( $_SESSION["brokery"]["account_id"] ) ) {
	$parent_account_id = $_SESSION["brokery"]["account_id"];
}
$tab_array=array("list", "status", "add", "edit", "delete", "bulk_action");
if(isset($_REQUEST["tab"]) && in_array($_REQUEST["tab"], $tab_array)){
	$tab=$_REQUEST["tab"];
}
else{
	$tab="list";
}

switch($tab){
	case 'add':
		include("modules/brokery/add_do.php");
	break;
	case 'edit':
		include("modules/brokery/edit_do.php");
	break;
	case 'delete':
		include("modules/brokery/delete_do.php");
	break;
	case 'status':
		include("modules/brokery/status_do.php");
	break;
	case 'bulk_action':
		include("modules/brokery/bulkactions.php");
	break;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="shortcut icon" href="favi.ico" />
<title><?php echo $site_title?> - Admin Panel</title>
<link type="text/css" rel="stylesheet" href="css/font-awesome.min.css" />
<link type="text/css" rel="stylesheet" href="css/font-awesome.css" />
<link type="text/css" rel="stylesheet"  href="css/bootstrap.css" />
<link rel="stylesheet" href="css/bootstrap-datetimepicker.min.css" />
<link type="text/css" rel="stylesheet"  href="css/awesome-bootstrap-checkbox.css" />
<link href="css/general.css" type="text/css" rel="stylesheet" />
<link type="text/css" rel="stylesheet"  href="css/style.css" />
<link rel="stylesheet" href="css/style.min.css">
<script type="text/javascript" src="js/jquery.js"></script> 
<script type="text/javascript" src='js/tinymce/tinymce.js'></script>
<?php include("js/initialize.php");?>
<script type="text/javascript" src="js/popup.js"></script>
</head>
<body>
<div id="wrapper" class="round_corners">		
		<div class="content" style="padding-top:0">
            <div class="page-header page-header-hidden">
           		<?php
                if(isset($_REQUEST["msg"])){
                	?>
                	<div align="center" class="msg"><?php echo url_decode($_REQUEST["msg"]);?></div>	
                	<?php
                }
            	if(isset($_REQUEST["err"])){
            		?>
            		<div align="center" class="err"><?php echo url_decode($_REQUEST["err"])?></div>	
            		<?php
                }
            	?>
            </div>
            <div class="container-widget row">
                <div class="col-md-12">
                  <?php
                    switch($tab){
                        case 'list':
                            include("modules/brokery/list.php");
                        break;
                        case 'add':
                            include("modules/brokery/add.php");
                        break;
                        case 'edit':
                            include("modules/brokery/edit.php");
                        break;
                    }
                  ?>
                </div>
  			</div>
		</div>
    </div>
<?php include("include/footer.php");?>