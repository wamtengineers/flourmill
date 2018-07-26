<?php
if(!defined("APP_START")) die("No Direct Access");
$extra='';
$is_search=true;
if(isset($_GET["date_from"])){
	$date_from=slash($_GET["date_from"]);
	$_SESSION["reports"]["total_report"]["date_from"]=$date_from;
}
if(isset($_SESSION["reports"]["total_report"]["date_from"]))
	$date_from=$_SESSION["reports"]["total_report"]["date_from"];
else
	$date_from="";
if($date_from != ""){
	$extra.=" and date>='".datetime_dbconvert($date_from)."'";
	$is_search=true;
}
if(isset($_GET["date_to"])){
	$date_to=slash($_GET["date_to"]);
	$_SESSION["reports"]["total_report"]["date_to"]=$date_to;
}
if(isset($_SESSION["reports"]["total_report"]["date_to"]))
	$date_to=$_SESSION["reports"]["total_report"]["date_to"];
else
	$date_to="";
if($date_to != ""){
	$extra.=" and date<'".datetime_dbconvert($date_to)."'";
	$is_search=true;
}