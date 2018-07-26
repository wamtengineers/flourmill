<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_GET["id"]) && !empty($_GET["id"])){
	doquery("delete from wheat_purchase_return where id='".slash($_GET["id"])."'",$dblink);
	header("Location: wheat_purchase_return_manage.php");
	die;
}