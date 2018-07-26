<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_GET["id"]) && !empty($_GET["id"])){
	doquery("update brokery set status='".slash($_GET["s"])."' where id='".slash($_GET["id"])."' and account_id='".$parent_account_id."'",$dblink);
	header("Location: brokery_manage.php");
	die;
}