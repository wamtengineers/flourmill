<?php
if(!defined("APP_START")) die("No Direct Access");
$rs = doquery( $sql, $dblink );
$amount = 0;
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
	<th colspan="7">
    	<h1><?php echo get_config( 'site_title' )?></h1>
    	<h2>Expense List</h2>
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
			if( !empty( $expense_category_id ) ){
				echo " Expense Category: ".get_field($expense_category_id, "expense_category","title")."<br>";
			}
			if( !empty( $account_id ) ){
				echo " Payment Account: ".get_field($account_id, "account","title");
			}
			?>
        </p>
    </th>
</tr>
<tr>
    <th width="5%" align="center">S.no</th>
    <th width="15%">Date/Time</th>
    <th width="15%">Expense Category</th>
    <th width="15%">Payment Account</th>
    <th width="10%">Amount</th>
    <th width="20%">Details</th>
    <th width="10%">Added By</th>
</tr>
<?php
if( numrows( $rs ) > 0 ) {
	$sn = 1;
	while( $r = dofetch( $rs ) ) {
		$amount += $r["amount"];
		?>
		<tr>
        	<td align="center"><?php echo $sn++?></td>
           	<td><?php echo datetime_convert($r["datetime_added"]); ?></td>
            <td><?php echo get_field( unslash($r["expense_category_id"]), "expense_category", "title" ); ?></td>
            <td><?php echo get_field( unslash($r["account_id"]), "account", "title" ); ?></td>
            <td align="right"><?php echo curr_format(unslash($r["amount"])); ?></td>
            <td><?php echo unslash($r["details"]); ?></td>
            <td><?php echo get_field( unslash($r["added_by"]), "admin", "username" ); ?></td>
        </tr>
		<?php
	}
}
?>
<tr>
    <th colspan="4" style="text-align:right;">Total</th>
    <th style="text-align:right;"><?php echo curr_format($amount);?></th>
    <th style="text-align:right;"></th>
    <th style="text-align:right;"></th>
</tr>
</table>
<?php
die;