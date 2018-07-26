<?php
if(!defined("APP_START")) die("No Direct Access");
$rs = doquery( $sql, $dblink );
	$total_items = $total_price = $payment_amount = 0;
	
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
</head>
<body>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr class="head">
        <th colspan="14">
            <h1><?php echo get_config( 'site_title' )?></h1>
            <h2>PURCHASE LIST</h2>
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
                    echo " Customer: ". get_field($account_id, "account","title");
                }
                ?>
            </p>
        </th>
    </tr>
    <tr>
        <th width="2%" style="text-align:center">S#</th>
        <th width="5%">Date</th>
        <th width="5%">Bill No.</th>
        <th width="12%">Customer Name</th>
        <th width="10%">Items</th>
        <th width="7%">Packing</th>
        <th width="7%" style="text-align:right;">Total KG</th>
        <th width="7%" style="text-align:right;">Less</th>
        <th width="7%" style="text-align:right;">Net KG</th>
        <th width="7%" style="text-align:right;">Rate</th>
        <th width="7%" style="text-align:right;">Total Price</th>
        <th width="7%" style="text-align:right;">Fare of V.</th>
        <th width="7%" style="text-align:right;">Brokery</th>
        <th width="7%" style="text-align:right;">Balance</th>
    </tr>
	<?php
    if(numrows($rs)>0){
        $sn=1;
		$fov_total = 0;
		$brokery_total = 0;
		$balance_total = 0;
        while($r=dofetch($rs)){
            $total_items += $r["total_items"];
            $total_price += $r["total_price"];
            $payment_amount += $r["amount"];
			$fov = 0;
			$brokery = 0;
			$balance = $r["total_price"];
            ?>
            <tr>
                <td style="text-align:center"><?php echo $sn++?></td>
                <td style="text-align:left;"><?php echo date("d M", strtotime($r["datetime_added"])); ?></td>
                <td style="text-align:left;"><?php echo unslash($r["bill_no"]); ?></td>
                <td style="text-align:left;"><?php echo get_field($r["account_id"], "account","title");?></td>
                <td><?php echo $r[ "items" ];?></td>
                <td><?php echo curr_format( $r[ "packing" ]);?></td>
                <td style="text-align:right;"><?php echo curr_format($r["total_items"]); ?></td>
                <td style="text-align:right;"><?php echo curr_format($r["less_weight_item"]); ?></td>
                <td style="text-align:right;"><?php echo curr_format($r["total_items"]-$r["less_weight_item"]); ?></td>
                <td style="text-align:right;"><?php echo $r["unit_price"]; ?></td>
                <td style="text-align:right;"><?php echo curr_format(unslash($r["total_price"])); ?></td>
                <td style="text-align:right;"><?php
                	if( !empty( $r[ "fare_transaction_id" ] ) ) {
						if( $r[ "cnf" ] == 1 ) {
							$t = doquery( "select * from transaction where id = '".$r[ "fare_transaction_id" ]."'", $dblink );
							if( numrows( $t ) > 0 ) {
								$t = dofetch( $t );
								$fov = $t[ "amount" ];
								$balance -= $fov;
							}
						}
						else{
							$t = doquery( "select * from expense where id = '".$r[ "fare_transaction_id" ]."'", $dblink );
							if( numrows( $t ) > 0 ) {
								$t = dofetch( $t );
								$fov = $t[ "amount" ];
							}
						}
					}
					if( !empty( $fov ) ) {
						echo curr_format( $fov );
						$fov_total += $fov;
					}
					else{
						echo '--';
					}
				?></td> 
                <td style="text-align:right;"><?php
                	if( !empty( $r[ "brokery_id" ] ) ) {
						$t = doquery( "select * from transaction where id = '".$r[ "brokery_id" ]."'", $dblink );
						if( numrows( $t ) > 0 ) {
							$t = dofetch( $t );
							$brokery = $t[ "amount" ];
							$balance -= $brokery;
						}
					}
					if( !empty( $brokery ) ) {
						echo curr_format( $brokery );
						$brokery_total += $brokery;
					}
					else{
						echo '--';
					}
				?></td> 
                <td style="text-align:right;"><?php echo curr_format( $balance ); $balance_total += $balance; ?></td>
            </tr>
            <?php
        }
    }
    ?>
    <tr>
        <th colspan="6" style="text-align:right;">Total</th>
        <th style="text-align:right;"><?php echo curr_format( $total_items );?></th>
        <th style="text-align:right;"><?php echo curr_format($total_price);?></th>
        <th style="text-align:right;"><?php echo curr_format($fov_total);?></th>
        <th style="text-align:right;"><?php echo curr_format($brokery_total);?></th>
        <th style="text-align:right;"><?php echo curr_format($balance_total);?></th>
        <th></th>
        <th></th>
        <th></th>
    </tr>
</table>
</div>
</body>
</html>
<?php
die;
//}