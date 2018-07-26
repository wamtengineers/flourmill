<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_GET["id"]) && !empty($_GET["id"])){
	doquery("delete from brokery where id='".slash($_GET["id"])."' and account_id='".$parent_account_id."'",$dblink);
	header("Location: brokery_manage.php?tab=list&msg=".url_encode("Record Deleted."));
	die;
}