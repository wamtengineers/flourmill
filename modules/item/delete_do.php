<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_GET["id"]) && !empty($_GET["id"])){
	$id=slash($_GET["id"]);
	doquery("delete from items_packing_sizes where item_id='".$_GET["id"]."'",$dblink);
	doquery("delete from items where id='".slash($_GET["id"])."'",$dblink);
	header("Location: items_manage.php");
	die;
}