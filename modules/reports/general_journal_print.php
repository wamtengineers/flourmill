<?php
if(!defined("APP_START")) die("No Direct Access");
include("general_journal_do.php");
$rs = doquery( $sql, $dblink );
$debit_total=$credit_total=0;
?>
<style>
h1, h2, h3, p {
    margin: 0 0 10px;
}
h1,h2{
	font-size:14px;
	margin:5px 0;
}
body {
    margin:  0;
    font-family:  Arial;
    font-size:  10px;
}
.head th, .head td{ border:0;}
th, td {
    border: solid 1px #000;
    padding: 2px 5px;
    font-size: 10px;
	vertical-align:top;
}
table table th, table table td{
	padding:2px;
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
            <h2>General Journal Report</h2>
            <p>
                <?php
                if( !empty( $date_from ) || !empty( $date_to ) ){
                    echo "<br />Date";
                }
                if( !empty( $date_from ) ){
                    echo " from ".$date_from;
                }
                if( !empty( $date_to ) ){
                    echo " to ".$date_to."<br>";
                }
                if( !empty( $account_id ) ){
                    echo " Account: ".get_field($account_id, "account","title");
                }
                ?>
            </p>
        </th>
    </tr>
    <tr>
        <th width="5%" align="center">S.no</th>
        <th>Date</th>
        <th>Details</th>
        <th>Items</th>
        <th align="right">Bags</th>
        <th align="right">Rate</th>
        <th align="right">Debit</th>
        <th align="right">Credit</th>
        <th align="right">Balance</th>
    </tr>
    <tbody>
		<?php
		if( numrows( $rs ) > 0 ) {
		$sn = 1;
		?>
		<tr>
            <td colspan="2"></td>
            <td><?php echo $order == 'desc'?'Closing':'Opening'?> Balance</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td align="right"><?php echo curr_format( $balance )?></td>
        </tr>
		<?php
        	while($r=dofetch($rs)){  
			$debit_total += $r["debit"];
			$credit_total += $r["credit"];           
				?>
				<tr>
					<td class="text-center"><?php echo $sn;?></td>
					<td><?php echo datetime_convert($r["datetime_added"]); ?></td>
					<td><?php echo unslash($r["details"]); ?></td>
                    <?php
						if($r["type"]==0){
							$sales = doquery("SELECT a.*, group_concat(concat(b.quantity) SEPARATOR '<br>') as quantity, ' x ',  group_concat(concat(c.title, b.packing) SEPARATOR '<br>') as items, group_concat(concat(b.unit_price)SEPARATOR '<br>') as item_price FROM `sales` a left join sales_items b on a.id = b.sales_id left join items c on b.item_id = c.id where a.id='".$r["id"]."' ",$dblink);
							$sale=dofetch($sales);
							?>
                            <td><?php echo unslash($sale[ "items" ]);?></td>
                            <td align="right"><?php echo $sale[ "quantity" ];?></td>
                            <td align="right"><?php echo $sale[ "item_price" ];?></td>
                        	<?php
						}
						elseif($r["type"]==1){
							$sales_return = dofetch(doquery("SELECT a.*, b.quantity, b.packing, b.unit_price, c.title FROM `sales_return` a left join sales_return_items b on b.sales_return_id = a.id left join items c on b.item_id = c.id where a.id = '".$r["id"]."'",$dblink));
							?>
                            <td><?php echo unslash($sales_return[ "title" ])."-".curr_format($sales_return[ "packing" ])."Kg";?></td>
                            <td align="right"><?php echo $sales_return[ "quantity" ];?></td>
                            <td align="right"><?php echo curr_format($sales_return[ "unit_price" ]);?></td>
                        	<?php
						}               
						elseif($r["type"]==2){
							$purchase = dofetch(doquery("SELECT a.*, b.quantity, b.packing, b.unit_price, c.title FROM `purchase` a left join purchase_items b on b.purchase_id = a.id left join items c on b.item_id = c.id where a.id = '".$r["id"]."'",$dblink));
							?>
                            <td><?php echo unslash($purchase[ "title" ])."-".curr_format($purchase[ "packing" ])."Kg";?></td>
                            <td align="right"><?php echo $purchase[ "quantity" ];?></td>
                            <td align="right"><?php echo curr_format($purchase[ "unit_price" ]);?></td>
                        	<?php
						}
						elseif($r["type"]==3){
							$purchase_return = dofetch(doquery("SELECT a.*, b.quantity, b.packing, b.unit_price, c.title FROM `purchase_return` a left join purchase_return_items b on b.purchase_return_id = a.id left join items c on b.item_id = c.id where a.id = '".$r["id"]."'",$dblink));
							?>
                        	<td><?php echo unslash($purchase[ "title" ])."-".curr_format($purchase[ "packing" ])."Kg";?></td>
                            <td align="right"><?php echo $purchase[ "quantity" ];?></td>
                            <td align="right"><?php echo curr_format($purchase[ "unit_price" ]);?></td>
                       		<?php
						}
						else{
							?>
                            <td></td>
                            <td></td>
                            <td></td>
                        	<?php
						}
						?>
					<td align="right"><?php echo curr_format($r["debit"]); ?></td>
					<td align="right"><?php echo curr_format($r["credit"]); ?></td>
					<td align="right"><?php if($order == 'asc'){$balance += ($r["debit"]-$r["credit"])*($order == 'desc'?'-1':1);} echo curr_format( $balance ); if($order == 'desc'){$balance += ($r["debit"]-$r["credit"])*($order == 'desc'?'-1':1);} ?></td>
				</tr>
				<?php 
                    $sn++;
                }
				?>
				<tr>
                	<td colspan="2"></td>
                    <td><?php echo $order != 'desc'?'Closing':'Opening'?> Balance</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td align="right"><?php echo curr_format( $balance )?></td>
                </tr>
                <?php	
            }
            else{	
                ?>
                <tr>
                    <td colspan="9"  class="no-record">No Result Found</td>
                </tr>
                <?php
            }
            ?>
            <tr>
            	<th colspan="6" align="right">Total</th>
                <th align="right"><?php echo curr_format($debit_total);?></th>
                <th align="right"><?php echo curr_format($credit_total);?></th>
                <th align="right"></th>
            </tr>
    	</tbody>
  	</table>
</div>
<?php
die;