<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_GET["id"]) && !empty($_GET["id"])){
	$id=slash($_GET["id"]);
	doquery("delete from packing where id='".slash($_GET["id"])."'",$dblink);
	header("Location: packing_manage.php");
	die;
}