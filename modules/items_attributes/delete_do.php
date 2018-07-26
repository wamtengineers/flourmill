<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_GET["id"]) && !empty($_GET["id"])){
	doquery("delete from items_attributes where id='".slash($_GET["id"])."' and item_id='".$parent_item_id."'",$dblink);
	header("Location: items_attributes_manage.php?tab=list&msg=".url_encode("Record Deleted."));
	die;
}