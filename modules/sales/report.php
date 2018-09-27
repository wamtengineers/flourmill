<?php
if(!defined("APP_START")) die("No Direct Access");
$rs = doquery( $sql, $dblink );
	$total_items = $total_price = $payment_amount = 0;
	
	?>
<style>
h1, h2, h3, p {
    margin: 0 0 5px;
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
    font-size: 11px;
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
</head>
<body>
<table width="100%" cellspacing="0" cellpadding="0">
	<tr class="head">
        <th colspan="12">
            <h1><?php echo get_config( 'site_title' )?></h1>
            <h2>SALES LIST</h2>
            <p>
                <?php
				if( $transaction_id==2 ){
                    echo " Credit Sale";
                }
                if( !empty( $date_from ) || !empty( $date_to ) ){
                    echo "<br />Date";
                }
                if( !empty( $date_from ) ){
                    echo " from ".$date_from;
                }
                if( !empty( $date_to ) ){
                    echo " to ".$date_to."<br>";
                }
				if( !empty( $q ) ){
                    echo " Items: ".$q."<br>";
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
        <th width="10%">Token Number</th>
        <th width="15%">Customer Name</th>
        <th>Items</th>
        <th width="8%" style="text-align:right;">Packing</th>
        <th width="8%" style="text-align:right;">Quantity</th>
        <th width="10%" style="text-align:right;">Rate</th>
        <th width="10%" style="text-align:right;">Total Amount</th>
        <th width="10%" style="text-align:right;">Grand Total</th>
        <th width="10%" style="text-align:right;">Total Weight</th>
        <th style="text-align:center">Status</th>
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
                <td style="text-align:left;"><?php echo date_convert($r["datetime_added"]); ?></td>
                <td><?php echo $r[ "id" ]; ?></td>
                <td style="text-align:left;"><?php echo unslash( $r[ "customer" ] ); ?></td>
                <td>
                    <?php 
						$items = doquery("select a.*, b.title from sales_items a left join items b on a.item_id = b.id where sales_id = '".$r["id"]."'", $dblink);
						 while($item=dofetch($items)){
							echo unslash($item["title"])." <br>";
						 }
					?>
                </td>
                <td style="text-align:right;">
                    <?php 
						$packing = doquery("select a.* from sales_items a left join items b on a.item_id = b.id where sales_id = '".$r["id"]."'", $dblink);
						 while($pack=dofetch($packing)){
							echo $pack["packing"]." <br>";
						 }
					?>
                </td>
                <td style="text-align:right;">
                	<?php 
						$quantity = doquery("select quantity-less_weight as item_quantity from sales_items where sales_id = '".$r["id"]."'", $dblink);
						 while($qty=dofetch($quantity)){
							echo round($qty["item_quantity"],2)." <br>";
						 }
					?>
                </td>
                <td style="text-align:right;">
                	<?php 
						$rates = doquery("select unit_price from sales_items where sales_id = '".$r["id"]."'", $dblink);
						 while($rate=dofetch($rates)){
							echo round($rate["unit_price"],2)." <br>";
						 }
					?>
                </td>
                <td style="text-align:right;">
                	<?php 
						$items_price = doquery("select total_price from sales_items where sales_id = '".$r["id"]."'", $dblink);
						 while($item_price=dofetch($items_price)){
							echo round($item_price["total_price"],2)." <br>";
						 }
					?>
                </td>
                <td style="text-align:right;"><?php echo round($r["amount"],2); ?></td>
                <td style="text-align:right;"><?php echo round($r["total_items"],2); ?></td> 
                <td class="text-center">
					<?php
                    if($r["status"]==0){
                        ?>
                        <span class="order-status cancel">Cancelled</span>
                        <?php
                    }
                    elseif($r["status"]==1){
                        ?>
                        <span class="order-status dispatch">Dispatched</span>
                        <?php
                    }
                    elseif($r["status"]==2){
                        ?>
                        <span class="order-status deliver">Delivering</span>
                        <?php
                    }
                    ?>
                </td>
            </tr>
            <?php
        }
    }
    ?>
    <tr>
        <th colspan="8" style="text-align:right;">Total</th>
        <th style="text-align:right;"><?php echo curr_format($total_price);?></th>
        <th style="text-align:right;"><?php echo curr_format($payment_amount);?></th>
        <th style="text-align:right;"><?php echo curr_format($total_items);?></th>
        <th></th>
    </tr>
</table>
</div>
</body>
</html>
<?php
die;
//}