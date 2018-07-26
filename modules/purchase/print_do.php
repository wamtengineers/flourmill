<?php
if(!defined("APP_START")) die("No Direct Access");
$rs = doquery( $sql, $dblink );
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
    border-collapse:  collapse;
	max-width:1200px;
	margin:0 auto;
}
</style>
<table width="100%" cellspacing="0" cellpadding="0">
<?php
$total = array();
$brands = array();
$items = doquery( "select * from items where product_type=1 order by sortorder", $dblink );
if( numrows( $items ) > 0 ) {
	while( $item = dofetch( $items ) ) {
		$brands[] = $item;
	}	
}
?>
<tr class="head">
	<th colspan="<?php echo count( $brands )+8?>">
    	<?php echo get_config( 'fees_chalan_header' )?>
    	<h2>Purchase Lists</h2>
        <p>
        	<?php
			if( !empty( $q ) ){
				echo " Supplier: ".$q;
			}
			if( !empty( $date_from ) || !empty( $date_to ) ){
				echo "<br />Date";
			}
			if( !empty( $date_from ) ){
				echo " from ".$date_from;
			}
			if( !empty( $date_to ) ){
				echo " to ".$date_to;
			}
			?>
        </p>
    </th>
</tr>
<tr>
	<th align="center" width="2%" rowspan="2">S.NO</th>
	<th align="center" width="3%" rowspan="2">ID</th>
    <th width="15%" rowspan="2">Date</th>
    <th width="15%" rowspan="2">Supplier</th>
    <th width="10%" rowspan="2">Invoice#</th>
    <th width="10%" rowspan="2">Quantity</th>
    <th width="10%" rowspan="2">Invoice Total</th>
    <th width="10%" rowspan="2">Payment</th>
    <th width="25%" colspan="<?php echo count($brands)?>">Items</th>
</tr>
<tr>
	<?php
    foreach( $brands as $brand ) {
		?>
		<td><?php echo unslash( $brand[ "code" ] )?></td>
		<?php
		$total[ "item".$brand[ "id" ] ] = 0;
	}
	?>
</tr>
<?php
if( numrows( $rs ) > 0 ) {
	$sn = 1;
	$total[ "quantity" ] = 0;
	$total[ "amount" ] = 0;
	$total[ "payment" ] = 0;
	while( $r = dofetch( $rs ) ) {
		?>
		<tr>
        	<td align="center"><?php echo $sn++?></td>
            <td align="center"><?php echo unslash( $r[ "id" ] )?></td>
            <td><?php echo datetime_convert($r["date"]); ?></td>
            <td><?php echo unslash($r["supplier_name"]); ?></td>
            <td align="right"><?php echo unslash($r["invoice_number"]); ?></td>
            <td align="right"><?php echo unslash($r["total_items"]); ?></td>
            <td align="right"><?php echo curr_format(unslash($r["net_price"])); ?></td>
            <td align="right"><?php echo curr_format(unslash($r["payment_amount"])); ?></td>
            <?php
            foreach( $brands as $brand ) {
				$item=doquery("select * from purchase_items where purchase_id = '".$r["id"]."' and item_id='".$brand[ "id" ]."'", $dblink);
                if(numrows( $item ) > 0){
					$item=dofetch($item)
					?>
					<td align="right"><?php echo curr_format(unslash($item["quantity"])); ?></td>
					<?php
					$total[ "item".$brand[ "id" ] ] += $item["quantity"];
				}
				else{
					?>
					<td align="right">0.00</td>
					<?php
				}
			}
			$total[ "quantity" ] += $r["total_items"];
			$total[ "amount" ] += $r["net_price"];
			$total[ "payment" ] += $r["payment_amount"];
			?>
        </tr>
		<?php
	}
	?>
	<tr>
    	<th colspan="5" align="right">Total</th>
        <th align="right"><?php echo curr_format( $total[ "quantity" ] )?></th>
        <th align="right"><?php echo curr_format( $total[ "amount" ] )?></th>
        <th align="right"><?php echo curr_format( $total[ "payment" ] )?></th>
        <?php
        foreach( $brands as $brand ) {
			?>
			<th align="right"><?php echo curr_format( $total[ "item".$brand[ "id" ] ] )?></th>
			<?php
		}
		?>
    </tr>
	<?php
}
?>
</table>
<?php
die;