<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_GET["id"]) && !empty($_GET["id"])){
	$id=slash($_GET["id"]);
	doquery("delete from unit where id='".slash($_GET["id"])."'",$dblink);
	header("Location: unit_manage.php");
	die;
}