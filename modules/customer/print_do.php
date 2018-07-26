<?php
if(!defined("APP_START")) die("No Direct Access");
$extra='';
$is_search=false;
if(isset($_GET["id"])){
	$id=slash($_GET["id"]);
}
else{
	$id= '';
}
if(isset($_GET["date_from"])){
	$date_from=slash($_GET["date_from"]);
	$_SESSION["customer"]["report"]["date_from"]=$date_from;
}
if(isset($_SESSION["customer"]["report"]["date_from"]))
	$date_from=$_SESSION["customer"]["report"]["date_from"];
else
	$date_from=date( "01/m/Y h:i A" );
	$is_search=true;
if(isset($_GET["date_to"])){
	$date_to=slash($_GET["date_to"]);
	$_SESSION["customer"]["report"]["date_to"]=$date_to;
}
if(isset($_SESSION["customer"]["report"]["date_to"]))
	$date_to=$_SESSION["customer"]["report"]["date_to"];
else
	$date_to=date( "d/m/Y h:i A" );
	$is_search=true;
if($id){
	$extra.=" and id='".$id."'";
	$customers=doquery("select * from customer where 1 $extra",$dblink);
	if(numrows($customers)>0){
		$customer=dofetch($customers);
	}
	else {
		return;
	}
}
?>
<style>
h1, h2, h3, p {
    margin: 0 0 10px;
}

body {
    margin:  0;
    font-family:  Arial;
    font-size:  11px;
}
.head th, .head td{ border:0;}
th, td {
    border: solid 1px #000;
    padding: 5px 5px;
    font-size: 11px;
	vertical-align:top;
}
table table th, table table td{
	padding:3px;
}
table {
    border-collapse:collapse;
	max-width:1200px;
	margin:0 auto;
}
</style>
<table width="100%" cellspacing="0" cellpadding="0">
<tr class="head">
	<th colspan="9">
    	<h1><?php echo get_config( 'site_title' )?></h1>
    	<h2>Customer Ledger</h2>
        <p>
        	<?php
			echo "List of";
			if( !empty( $date_from ) || !empty( $date_to ) ){
				echo "<br />Date";
			}
			if( !empty( $date_from ) ){
				echo " from ".$date_from;
			}
			if( !empty( $date_to ) ){
				echo " to ".$date_to."<br>";
			}
            if( !empty( $id ) ){
				echo " Customer: ".get_field($id, "customer","customer_name");
			}
			?>
        </p>
    </th>
</tr>
<tr>
    <th width="5%" align="center">S.no</th>
    <th>Date</th>
    <th>Transaction</th> 
    <th>Details</th>                
    <th align="right">Amount</th>
    <th align="right">Balance</th>
</tr>
<?php 
if( !empty( $id ) ){
	$balance = get_customer_balance( $customer[ "id" ], datetime_dbconvert( $date_to ) );
	$sn=1;
	?>
	<tr>
		<td align="center"><?php echo $sn++;?></td>
		<td><?php echo $date_to; ?></td>
		<td>Closing Balance</td>
		<td align="right">--</td>
        <td align="right">--</td>
		<td align="right"><?php echo curr_format($balance); ?></td>
	</tr>
	<?php
	$sql="select concat( 'Sale #', id) as transaction, datetime_added, (select sum(total_price) from sales_items where sales_id = sales.id)-discount as amount from sales where customer_id = '".$customer[ "id" ]."' and datetime_added >='".datetime_dbconvert( $date_from )."' and datetime_added <='".datetime_dbconvert( $date_to )."' union select concat( 'Sale Return #', id) as transaction, datetime_added, discount-(select sum(total_price) from sales_return_items where sales_return_id = sales_return.id) as amount from sales_return where customer_id = '".$customer["id"]."' and datetime_added >='".datetime_dbconvert( $date_from )."' and datetime_added <='".datetime_dbconvert( $date_to )."' union select details, datetime_added as datetime_added, -amount from customer_payment where customer_id = '".$customer[ "id" ]."' and datetime_added >='".datetime_dbconvert( $date_from )."' and datetime_added <='".datetime_dbconvert( $date_to )."' order by datetime_added desc";
	$rs=doquery($sql,$dblink);
	if(numrows($rs)>0){
		while($r=dofetch($rs)){
			?>
			<tr>
				<td align="center"><?php echo $sn;?></td>
				<td><?php echo datetime_convert($r["datetime_added"]); ?></td>
				<td><?php echo unslash($r["transaction"]); ?></td>
                <td></td>
				<td align="right"><?php echo curr_format($r["amount"]); ?></td>
				<td align="right"><?php echo curr_format($balance); ?></td>
			</tr>
			<?php 
			$sn++;
			$balance = $balance - $r["amount"];
		}
		?>
		<tr>
			<td align="center"><?php echo $sn++;?></td>
			<td><?php echo $date_from; ?></td>
			<td>Opening Balance</td>
			<td align="right">--</td>
            <td align="right">--</td>
			<td align="right"><?php echo curr_format($balance); ?></td>
		</tr>
		<?php
	}
	else{	
		?>
		<tr>
			<td colspan="6"  class="no-record">No Result Found</td>
		</tr>
		<?php
	}
}
else {
	?>
	<tr>
		<td colspan="6"  class="no-record">Select Customer from above dropdown</td>
	</tr>
	<?php
}
?>
</table>
<?php
die;