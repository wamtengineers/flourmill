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
        <th colspan="9">
            <h1><?php echo get_config( 'site_title' )?></h1>
            <h2>PURCHASE RETURN Lists</h2>
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
        <th width="5%" style="text-align:center">S#</th>
        <th width="15%">Date</th>
        <th width="15%">Customer Name</th>
        <th width="15%">Items</th>
        <th width="10%" style="text-align:right;">Total Items</th>
        <th width="10%" style="text-align:right;">Total Price</th>
        <th width="10%" style="text-align:right;">Payment Amount</th>
    </tr>
            <?php
            if(numrows($rs)>0){
                $sn=1;
                while($r=dofetch($rs)){
					$total_items += $r["total_items"];
					$total_price += $r["total_price"];
					$payment_amount += $r["amount"];
                    ?>
                    <tr>
                    	<td style="text-align:center"><?php echo $sn++?></td>
                        <td style="text-align:left;"><?php echo datetime_convert($r["datetime_added"]); ?></td>
                        <td style="text-align:left;"><?php echo get_field($r["customer_id"], "customer","customer_name");?></td>
                        <td>
                        	<?php echo $r[ "items" ];?>
                        </td>
                        <td style="text-align:right;"><?php echo unslash($r["total_items"]); ?></td>
                        <td style="text-align:right;"><?php echo curr_format(unslash($r["total_price"])); ?></td>
                        <td style="text-align:right;"><?php echo curr_format(unslash($r["amount"])); ?></td> 
                    </tr>
                    <?php
                }
            }
            ?>
            <tr>
            	<th colspan="4" style="text-align:right;">Total</th>
                <th style="text-align:right;"><?php echo $total_items;?></th>
                <th style="text-align:right;"><?php echo curr_format($total_price);?></th>
                <th style="text-align:right;"><?php echo curr_format($payment_amount);?></th>
            </tr>
        </table>
</div>
</body>
</html>
<?php
die;
//}