<?php
if(!defined("APP_START")) die("No Direct Access");
$rs = doquery( $sql, $dblink );
$total_amount = 0;
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
<tr class="head">
	<th colspan="9">
    	<h1><?php echo get_config( 'site_title' )?></h1>
    	<h2>Wheat Purchase List</h2>
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
			if( !empty( $supplier_id ) ){
				echo " Party Name: ".get_field($supplier_id, "supplier","supplier_name");
			}
			?>
        </p>
    </th>
</tr>
<tr>
    <th width="5%" align="center">S.no</th>
    <th width="20%">Datetime</th>
    <th width="15%">Party Name</th>
    <th width="15%">Broker Name</th>
    <th width="10%">Vahicle Number</th>
    <th width="10%" align="right">Wheat Price</th>
</tr>
<?php
if( numrows( $rs ) > 0 ) {
	$sn = 1;
	while( $r = dofetch( $rs ) ) {
		$total_amount += $r["wheat_price"];
		?>
		<tr>
        	<td align="center"><?php echo $sn++?></td>
           	<td><?php echo datetime_convert($r["datetime_added"]); ?></td>
            <td><?php echo get_field( unslash($r["supplier_id"]), "supplier", "supplier_name" ); ?></td>
            <td><?php echo unslash($r["broker_name"]); ?></td>
            <td><?php echo unslash($r["vehicle_number"]); ?></td>
            <td align="right"><?php echo curr_format(unslash($r["wheat_price"])); ?></td>
        </tr>
		<?php
	}
}
?>
<tr>
    <th colspan="5" style="text-align:right;">Total</th>
    <th align="right"><?php echo curr_format($total_amount);?></th>
</tr>
</table>
<?php
die;