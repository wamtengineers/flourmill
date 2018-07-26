<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_GET["id"]) && !empty($_GET["id"])){
	$expense=dofetch(doquery("select * from expense where id='".slash($_GET["id"])."'", $dblink));
?>
<style>
h1, h2, h3, p {
    margin: 0 0 10px;
}
.voucher{
	max-width:960px;
	margin:0 auto;
}
body {
    margin:  0;
    font-family:  Arial;
    font-size:  14px;
}
.head th, .head td{ border:0;}
th, td {
    border: solid 1px #000;
    padding: 10px 10px;
    font-size: 13px;
	vertical-align:top;
}
table {
    border-collapse:  collapse;
	max-width:1200px;
	margin:0 auto;
}
.clear:after { 
  content: "";
  clear: both;
  display: table;
}
.voucher_head{
	text-align:center;
}
.voucher_head h1{
	font-size: 20px;
	text-transform: uppercase;
	margin: 10px 0;
}
.voucher_head h2{
	font-size: 20px;
	text-transform: uppercase;
	margin-bottom:30px;
}
.voucher_detail p{
	font-size:16px;
	border-bottom: 1px solid;
	padding-bottom: 5px;
}
.detail_left{
	float:left;
}
.detail_right{
	float:right;
}
.voucher_detail {
    margin: 10px 0;
}
.signature{
	margin-top:80px;
}
.signature p{
	font-size:20px;
}
.signature ul{
	margin:0;
	padding:0;
}
.signature li{
	width:33%;
	display:inline-block;
	font-weight:700;
}
.signature td{ border: none;
padding: 0;
height: 100px;
vertical-align: bottom;}
</style>
<div class="voucher">
<div class="voucher_head">
	<h1><?php echo get_config( 'site_title' )?></h1>
    <h2>Expense Voucher</h2>
</div>
<!--<div class="voucher_detail clear">
	<div class="detail_left">
    	<p></p>
        <p></p>
    </div>
    <div class="detail_right">
    	<p>DATE: <?php echo datetime_convert($expense["datetime_added"]); ?></p>
    </div>
</div>-->
<table width="100%" cellspacing="0" cellpadding="0">
<thead>
<tr>
	<th align="left">VOUCHER NO : <?php echo $expense["id"] ?></th>
    <th colspan="2" align="left">DATE : <?php echo datetime_convert($expense["datetime_added"]); ?></th>
	<th align="left" style="text-transform:uppercase">CREDIT ACCOUNT : <?php echo get_field( unslash($expense["account_id"]), "account", "title" ); ?></th>
    
</tr>
<tr>
    <th width="15%" align="center">S.no</th>
    <th width="20%" align="left">Expense Head</th>
    <th width="15%" align="right">Amount</th>
    <th width="50%" align="left">Details</th>
</tr>
</thead>
<tbody>
	<tr>
		<td align="center">1</td>
		<td><?php echo get_field( unslash($expense["expense_category_id"]), "expense_category", "title" ); ?></td>
		<td align="right"><?php echo curr_format(unslash($expense["amount"])); ?></td>
		<td><?php echo unslash($expense["details"]); ?></td>
	</tr>
</tbody>
</table>
<div class="signature">
	<ul>
    	<li>Prepared By:</li>
        <li style="text-align:center;">Authorised By:</li>
        <li style="text-align:right">Received By:</li>
    </ul>
</div>
</div>
<?php
die;
}