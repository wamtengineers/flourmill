<?php
if(!defined("APP_START")) die("No Direct Access");
$extra='';
$is_search=true;
if(isset($_GET["date"])){
	$date=slash($_GET["date"]);
	$_SESSION["reports"]["daily"]["date"]=$date;
}
if(isset($_SESSION["reports"]["daily"]["date"]))
	$date=$_SESSION["reports"]["daily"]["date"];
else
	$date=date("d/m/Y");

$order_by = "datetime_added";
$order = "desc";
if( isset($_GET["order_by"]) ){
	$_SESSION["reports"]["daily"]["order_by"]=slash($_GET["order_by"]);
}
if( isset( $_SESSION["reports"]["daily"]["order_by"] ) ){
	$order_by = $_SESSION["reports"]["daily"]["order_by"];
}
if( isset($_GET["order"]) ){
	$_SESSION["reports"]["daily"]["order"]=slash($_GET["order"]);
}
if( isset( $_SESSION["reports"]["daily"]["order"] ) ){
	$order = $_SESSION["reports"]["daily"]["order"];
}
$orderby = $order_by." ".$order;
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
    font-size:  12px;
}
table th, table td{
	padding:3px 5px !important;
}
table {
    border-collapse:  collapse;
	width:100%;
}
.head{ display:none;}
/*@media print{
	body{ overflow:hidden;}
	#top,.page-header,#footer{
		display:none;
	}
	.topstats{ display:none !important;}
	.table-responsive, table{ display:block;}
	.content{ padding:0;}
	#footer.bottom_round_corners{ display:none;}
	.head{ display:block;}
}*/
</style>
<div class="page-header">
	<h1 class="title">Reports</h1>
  	<ol class="breadcrumb">
    	<li class="active">Daily Report</li>
  	</ol>
  	<div class="right">
    	<div class="btn-group" role="group" aria-label="..."> 
        	<a id="topstats" class="btn btn-light" href="#"><i class="fa fa-search"></i></a> 
            <a class="btn print-btn" href="report_manage.php?tab=daily_print"><i class="fa fa-print" aria-hidden="true"></i></a>  
        </div>
  	</div>
</div>
<ul class="topstats clearfix search_filter"<?php if($is_search) echo ' style="display: block"';?>>
	<li class="col-xs-12 col-lg-12 col-sm-12">
        <div>
        	<form class="form-horizontal" action="" method="get">
                <span class="col-sm-1 text-to">Date</span>
                <div class="col-sm-2">
                	<input type="text" title="Enter Date From" name="date" id="date" placeholder="" class="form-control date-picker"  value="<?php echo $date?>">
                </div>
                <div class="col-sm-3 text-left">
                    <input type="button" class="btn btn-danger btn-l reset_search" value="Reset" alt="Reset Record" title="Reset Record" />
                    <input type="submit" class="btn btn-default btn-l" value="Search" alt="Search Record" title="Search Record" />
                </div>
          	</form>
        </div>
  	</li>
</ul>
<div class="panel-body table-responsive">
	<table class="table table-hover list">
    	<tr class="head">
        	<th colspan="11" width="100%">
                    <?php
                    if( !empty( $date ) ){
                        echo " Date ".$date;
                    }
                    ?>
            </th>
        </tr>
        <tr>
            <th colspan="12">CASH SALES</th>
        </tr>
        <tr>
            <th width="5%" style="text-align:center">S#</th>
            <th width="15%">Date/Time</th>
            <th width="10%">Token Number</th>
            <th width="15%">Customer Name</th>
            <th width="10%">Items</th>
            <th width="8%" style="text-align:right;">Packing</th>
            <th width="8%" style="text-align:right;">Quantity</th>
            <th width="10%" style="text-align:right;">Rate</th>
            <th width="10%" style="text-align:right;">Total Amount</th>
            <th width="10%" style="text-align:right;">Grand Total</th>
            <th width="10%" style="text-align:right;">Total Weight</th>
            <th style="text-align:center;">Status</th>
        </tr>
		<?php
		/*$sales=doquery("select * from (select a.*, b.title, amount, (select sum((quantity-less_weight)*if(rate=0,packing,1)) from sales_items where sales_id = a.id)-less_weight as total_items, (select group_concat(concat(quantity, ' &times ', packing, 'KG ', title) SEPARATOR '<br>') from sales_items left join items on sales_items.item_id = items.id where sales_id = a.id) as items, (select sum(total_price) from sales_items where sales_id = a.id)-discount as total_price from sales a left join account b on a.account_id = b.id left join transaction c on a.transaction_id = c.id ) as temp_table where account_id='".get_config("dailysale_customer_id")."' and datetime_added BETWEEN '".date('Y-m-d',strtotime(date_dbconvert($date)))." 00:00:00' AND '".date('Y-m-d',strtotime(date_dbconvert($date)))." 23:59:59' order by datetime_added asc", $dblink);*/
		$sql = "SELECT a.*, d.title as customer, e.amount, sum((b.quantity-b.less_weight)*if(b.rate=0,b.packing,1))-a.less_weight as total_items, group_concat(concat(b.quantity, ' × ', b.packing, 'KG ', c.title) SEPARATOR '<br>') as items, sum(b.total_price)-a.discount as total_price, b.unit_price FROM `sales` a left join sales_items b on a.id = b.sales_id left join items c on b.item_id = c.id left join account d on a.account_id = d.id left join transaction e on a.transaction_id = e.id where a.account_id='".get_config("dailysale_customer_id")."' and a.datetime_added BETWEEN '".date('Y-m-d',strtotime(date_dbconvert($date)))." 00:00:00' AND '".date('Y-m-d',strtotime(date_dbconvert($date)))." 23:59:59' group by a.id order by a.datetime_added asc";
		$sales = doquery($sql, $dblink);
		$total_items = $total_price = $payment_amount = 0;
        if(numrows($sales)>0){
            $sn=1;
            while($sale=dofetch($sales)){
				$total_items += $sale["total_items"];
				$total_price += $sale["total_price"];
				$payment_amount += $sale["amount"];
        		?>
                <tr>
                    <td style="text-align:center"><?php echo $sn++?></td>
                    <td style="text-align:left;"><?php echo date_convert($sale["datetime_added"]); ?></td>
                    <td><?php echo get_token_number( $sale ); ?></td>
                    <td style="text-align:left;"><?php echo get_field($sale["account_id"], "account","title");?></td>
                    <td>
                        <?php 
                            $items = doquery("select a.*, b.title from sales_items a left join items b on a.item_id = b.id where sales_id = '".$sale["id"]."'", $dblink);
                             while($item=dofetch($items)){
                                echo unslash($item["title"])." <br>";
                             }
                        ?>
                    </td>
                    <td style="text-align:right;">
                    	<?php 
                            $packing = doquery("select a.* from sales_items a left join items b on a.item_id = b.id where sales_id = '".$sale["id"]."'", $dblink);
                             while($pack=dofetch($packing)){
                                echo round($pack["packing"],2)." <br>";
                             }
                        ?>
                    </td>
                    <td style="text-align:right;">
                    	<?php 
                            $quantity = doquery("select quantity-less_weight as item_quantity from sales_items where sales_id = '".$sale["id"]."'", $dblink);
                             while($qty=dofetch($quantity)){
                                echo round($qty["item_quantity"],2)." <br>";
                             }
                        ?>
                    </td>
                    <td style="text-align:right;">
                    	<?php 
                            $rates = doquery("select unit_price from sales_items where sales_id = '".$sale["id"]."'", $dblink);
                             while($rate=dofetch($rates)){
                                echo round($rate["unit_price"],2)." <br>";
                             }
                        ?>
                    </td>
                    <td style="text-align:right;">
                    	<?php 
                            $items_price = doquery("select total_price from sales_items where sales_id = '".$sale["id"]."'", $dblink);
                             while($item_price=dofetch($items_price)){
                                echo round($item_price["total_price"],2)." <br>";
                             }
                        ?>
                    </td>
                    <td style="text-align:right;"><?php echo round($sale["amount"],2); ?></td> 
                    <td style="text-align:right;"><?php echo round($sale["total_items"],2); ?></td>
                    <td class="text-center">
                        <?php
                        if($sale["status"]==0){
                            ?>
                            <span class="order-status cancel">Cancelled</span>
                            <?php
                        }
                        elseif($sale["status"]==1){
                            ?>
                            <span class="order-status dispatch">Dispatched</span>
                            <?php
                        }
                        elseif($sale["status"]==2){
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
        <tr>
            <th colspan="12">CREDIT SALES</th>
        </tr>
        <tr>
            <th width="5%" style="text-align:center">S#</th>
            <th width="15%">Date/Time</th>
            <th width="10%">Token Number</th>
            <th width="15%">Customer Name</th>
            <th width="10%">Items</th>
            <th width="8%" style="text-align:right;">Packing</th>
            <th width="8%" style="text-align:right;">Quantity</th>
            <th width="10%" style="text-align:right;">Rate</th>
            <th width="10%" style="text-align:right;">Total Amount</th>
            <th width="10%" style="text-align:right;">Grand Total</th>
            <th width="10%" style="text-align:right;">Total Weight</th>
            <th style="text-align:center;">Status</th>
        </tr>
		<?php
		$sql="select * from (select a.*, b.title, amount, (select sum((quantity-less_weight)*if(rate=0,packing,1)) from sales_items where sales_id = a.id)-less_weight as total_items, (select group_concat(concat(quantity, ' &times ', packing, 'KG ', title) SEPARATOR '<br>') from sales_items left join items on sales_items.item_id = items.id where sales_id = a.id) as items, (select sum(total_price) from sales_items where sales_id = a.id)-discount as total_price from sales a left join account b on a.account_id = b.id left join transaction c on a.transaction_id = c.id ) as temp_table where account_id<>'".get_config("dailysale_customer_id")."' and datetime_added BETWEEN '".date('Y-m-d',strtotime(date_dbconvert($date)))." 00:00:00' AND '".date('Y-m-d',strtotime(date_dbconvert($date)))." 23:59:59' order by datetime_added asc";
		$sql = "SELECT a.*, d.title as customer, e.amount, sum((b.quantity-b.less_weight)*if(b.rate=0,b.packing,1))-a.less_weight as total_items, group_concat(concat(b.quantity, ' × ', b.packing, 'KG ', c.title) SEPARATOR '<br>') as items, sum(b.total_price)-a.discount as total_price, b.unit_price FROM `sales` a left join sales_items b on a.id = b.sales_id left join items c on b.item_id = c.id left join account d on a.account_id = d.id left join transaction e on a.transaction_id = e.id where a.account_id<>'".get_config("dailysale_customer_id")."' and a.datetime_added BETWEEN '".date('Y-m-d',strtotime(date_dbconvert($date)))." 00:00:00' AND '".date('Y-m-d',strtotime(date_dbconvert($date)))." 23:59:59' group by a.id order by a.datetime_added asc";
		$sales=doquery($sql, $dblink);
		$total_items = $total_price = $payment_amount = 0;
        if(numrows($sales)>0){
            $sn=1;
            while($sale=dofetch($sales)){
				$total_items += $sale["total_items"];
				$total_price += $sale["total_price"];
				$payment_amount += $sale["amount"];
        		?>
                <tr>
                    <td style="text-align:center"><?php echo $sn++?></td>
                    <td style="text-align:left;"><?php echo date_convert($sale["datetime_added"]); ?></td>
                    <td><?php echo get_token_number( $sale ); ?></td>
                    <td style="text-align:left;"><?php echo get_field($sale["account_id"], "account","title");?></td>
                    <td>
                        <?php 
                            $items = doquery("select a.*, b.title from sales_items a left join items b on a.item_id = b.id where sales_id = '".$sale["id"]."'", $dblink);
                             while($item=dofetch($items)){
                                echo unslash($item["title"])." <br>";
                             }
                        ?>
                    </td>
                    <td style="text-align:right;">
                    	<?php 
                            $packing = doquery("select a.* from sales_items a left join items b on a.item_id = b.id where sales_id = '".$sale["id"]."'", $dblink);
                             while($pack=dofetch($packing)){
                                echo $pack["packing"]." <br>";
                             }
                        ?>
                    </td>
                    <td style="text-align:right;">
                    	<?php 
                            $quantity = doquery("select quantity-less_weight as item_quantity from sales_items where sales_id = '".$sale["id"]."'", $dblink);
                             while($qty=dofetch($quantity)){
                                echo round($qty["item_quantity"],2)." <br>";
                             }
                        ?>
                    </td>
                    <td style="text-align:right;">
                    	<?php 
                            $rates = doquery("select unit_price from sales_items where sales_id = '".$sale["id"]."'", $dblink);
                             while($rate=dofetch($rates)){
                                echo round($rate["unit_price"], 2)." <br>";
                             }
                        ?>
                    </td>
                    <td style="text-align:right;">
                    	<?php 
                            $items_price = doquery("select total_price from sales_items where sales_id = '".$sale["id"]."'", $dblink);
                             while($item_price=dofetch($items_price)){
                                echo round($item_price["total_price"],2)." <br>";
                             }
                        ?>
                    </td>
                    <td style="text-align:right;"><?php echo round($sale["amount"],2); ?></td> 
                    <td style="text-align:right;"><?php echo round($sale["total_items"],2); ?></td> 
                    <td class="text-center">
                        <?php
                        if($sale["status"]==0){
                            ?>
                            <span class="order-status cancel">Cancelled</span>
                            <?php
                        }
                        elseif($sale["status"]==1){
                            ?>
                            <span class="order-status dispatch">Dispatched</span>
                            <?php
                        }
                        elseif($sale["status"]==2){
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
    <table class="table table-hover list">
		<tr>
            <th colspan="14">PURCHASE</th>
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
		$purchases=doquery("select a.*, b.packing, b.unit_price, b.quantity as total_items, b.total_price as total_price, c.title as items, b.less_weight as less_weight_item, amount from purchase a inner join purchase_items b on a.id = b.purchase_id left join items c on b.item_id = c.id left join transaction d on a.transaction_id = d.id where 1 and a.datetime_added BETWEEN '".date('Y-m-d',strtotime(date_dbconvert($date)))." 00:00:00' AND '".date('Y-m-d',strtotime(date_dbconvert($date)))." 23:59:59' order by a.datetime_added desc", $dblink);
		$purchase_total_items = $purchase_total_price = $purchase_payment_amount = 0;
		$fov_total = 0;
            $brokery_total = 0;
            $balance_total = 0;
            $total_less_weight_item = 0;
            $rate_total = 0;
        if(numrows($purchases)>0){
            $sn=1;
            
            while($purchase=dofetch($purchases)){
                $purchase_total_items += $purchase["total_items"];
                $purchase_total_price += $purchase["total_price"];
                $total_less_weight_item += $purchase["less_weight_item"];
                $rate_total += $purchase["unit_price"];
                $purchase_payment_amount += $purchase["amount"];
                $fov = 0;
                $brokery = 0;
                $balance = $purchase["total_price"];
                ?>
                <tr>
                    <td style="text-align:center"><?php echo $sn++?></td>
                    <td style="text-align:left;"><?php echo date("d M", strtotime($purchase["datetime_added"])); ?></td>
                    <td style="text-align:left;"><?php echo unslash($purchase["bill_no"]); ?></td>
                    <td style="text-align:left;"><?php echo get_field($purchase["account_id"], "account","title");?></td>
                    <td><?php echo $purchase[ "items" ];?></td>
                    <td><?php echo curr_format( $purchase[ "packing" ]);?></td>
                    <td style="text-align:right;"><?php echo curr_format($purchase["total_items"]); ?></td>
                    <td style="text-align:right;"><?php echo curr_format($purchase["less_weight_item"]); ?></td>
                    <td style="text-align:right;"><?php echo curr_format($purchase["total_items"]-$purchase["less_weight_item"]); ?></td>
                    <td style="text-align:right;"><?php echo $purchase["unit_price"]; ?></td>
                    <td style="text-align:right;"><?php echo curr_format(unslash($purchase["total_price"])); ?></td>
                    <td style="text-align:right;"><?php
                        if( !empty( $purchase[ "fare_transaction_id" ] ) ) {
                            if( $purchase[ "cnf" ] == 1 ) {
                                $t = doquery( "select * from transaction where id = '".$purchase[ "fare_transaction_id" ]."'", $dblink );
                                if( numrows( $t ) > 0 ) {
                                    $t = dofetch( $t );
                                    $fov = $t[ "amount" ];
                                    $balance -= $fov;
                                }
                            }
                            else{
                                $t = doquery( "select * from expense where id = '".$purchase[ "fare_transaction_id" ]."'", $dblink );
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
                        if( !empty( $purchase[ "brokery_id" ] ) ) {
                            $t = doquery( "select * from transaction where id = '".$purchase[ "brokery_id" ]."'", $dblink );
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
            <th style="text-align:right;"><?php echo curr_format( $purchase_total_items );?></th>
            <th style="text-align:right;"><?php echo curr_format( $total_less_weight_item );?></th>
            <th style="text-align:right;"><?php echo curr_format( $purchase_total_items-$total_less_weight_item );?></th>
            <th style="text-align:right;"><?php echo curr_format($rate_total);?></th>
            <th style="text-align:right;"><?php echo curr_format($purchase_total_price);?></th>
            <th style="text-align:right;"><?php echo curr_format($fov_total);?></th>
            <th style="text-align:right;"><?php echo curr_format($brokery_total);?></th>
            <th style="text-align:right;"><?php echo curr_format($balance_total);?></th>
        </tr>
	</table>
    <table class="table table-hover list">
        <tr>
            <th colspan="7">Transaction</th>
        </tr>
        <tr>
            <th width="5%" class="text-center">S#</th>
            <th>
                	<a href="report_manage.php?tab=daily&order_by=datetime_added&order=<?php echo $order=="asc"?"desc":"asc"?>" class="sorting">
                    	Date
                        <?php
						if( $order_by == "datetime_added" ) {
							?>
							<span class="sort-icon">
								<i class="fa fa-angle-<?php echo $order=="asc"?"up":"down"?>" data-hover_in="<?php echo $order=="asc"?"down":"up"?>" data-hover_out="<?php echo $order=="desc"?"down":"up"?>" aria-hidden="true"></i>
							</span>
							<?php
						}
						?>
                  	</a>
                </th>
            <th width="20%">Account</th>
            <th width="30%">Details</th>
            <th class="text-right" width="10%">Debit</th>
            <th class="text-right" width="10%">Credit</th>
            <th class="text-right" >Balance</th>
        </tr>
        <?php
		$total_sale = dofetch(doquery("select sum(amount), b.title as title, c.title as details  from transaction a left join account b on a.reference_id = b.id left join account c on a.account_id = c.id where datetime_added BETWEEN '".date('Y-m-d',strtotime(date_dbconvert($date)))." 00:00:00' AND '".date('Y-m-d',strtotime(date_dbconvert($date)))." 23:59:59' and reference_id = '".get_config("dailysale_customer_id")."'", $dblink));
		$sn=1;
		$balance = get_account_balance( date_dbconvert($date) );
		?>
        <tr>
            <td colspan="2"></td>
            <td><?php echo $order == 'desc'?'Closing':'Opening'?> Balance</td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-right"><?php echo curr_format( $balance )?></td>
        </tr>
		<tr>
            <td class="text-center"><?php echo $sn++;?></td>
            <td><?php echo $date; ?></td>
            <td><?php echo unslash($total_sale["title"]); ?></td>
            <td><?php echo unslash($total_sale["details"]); ?></td>
            <td class="text-right"><?php echo curr_format($total_sale["sum(amount)"]); $balance+=$total_sale["sum(amount)"]; ?></td>
            <td class="text-right">0</td>
            <td class="text-right"><?php echo curr_format( $balance )?></td>
        </tr>
		<?php
		$transactions = array();
		//$transactions[] = "select 3 as position, a.datetime_added, b.title, if(details='', concat( 'Transfer to account ', title ), details) as details, 0 as debit, amount as credit from transaction a left join account b on a.reference_id = b.id where a.status=1 and a.reference_id <> '".get_config("dailysale_customer_id")."' and a.account_id='".get_config('drawbox_id')."'";
		//$transactions[] = "select 4 as position, a.datetime_added, b.title, if(details='', concat( 'Transfer to account ', title ), details) as details, 0 as debit, amount as credit from transaction a left join account b on a.reference_id = b.id where a.status=1 and a.reference_id <> '".get_config("dailysale_customer_id")."' and a.account_id!='".get_config('drawbox_id')."'";
		
		$transactions[] = "select 1 as position, a.datetime_added, b.title,  if(details='', concat( 'Transfer from account ', title ), details) as details, amount as debit, 0 as credit from transaction a left join account b on a.reference_id = b.id where a.status=1 and a.reference_id <> '".get_config("dailysale_customer_id")."' and a.account_id='".get_config('drawbox_id')."'";
		$transactions[] = "select 2 as position, a.datetime_added, b.title,  if(details='', concat( 'Transfer from account ', c.title ), details) as details, amount as debit, 0 as credit from transaction a left join account b on a.account_id = b.id left join account c on a.reference_id = c.id where a.status=1 and a.reference_id <> '".get_config("dailysale_customer_id")."' and a.reference_id <> '".get_config("drawbox_id")."' and a.account_id<>'".get_config('drawbox_id')."'";
		$transactions[] = "select 4 as position, a.datetime_added, c.title,  if(details='', concat( 'Transfer from account ', b.title ), details) as details, 0 as debit, amount as credit from transaction a left join account b on a.account_id = b.id left join account c on a.reference_id = c.id where a.status=1 and a.reference_id <> '".get_config("dailysale_customer_id")."' and a.reference_id <> '".get_config("drawbox_id")."' and a.account_id<>'".get_config('drawbox_id')."'";
		$transactions[] = "select 3 as position, a.datetime_added, b.title,  if(details='', concat( 'Transfer from account ', title ), details) as details, 0 as debit, amount as credit from transaction a left join account b on a.account_id = b.id where a.status=1 and a.reference_id='".get_config('drawbox_id')."'";
		//$transactions[] = "select 5 as position, a.datetime_added, b.title, if(details='', concat( 'Transfer to account ', title ), details) as details, 0 as debit, amount as credit from transaction a left join account b on a.reference_id = b.id where a.status=1 and a.reference_id <> '".get_config("dailysale_customer_id")."' and a.account_id!='".get_config('drawbox_id')."'";
		//$transactions[] = "select 2 as position, a.datetime_added, concat('Expense: ', b.title ), details, amount as debit, 0 as credit from expense a left join expense_category b on a.expense_category_id = b.id where a.status=1";
		
		$transactions[] = "select 5 as position, a.datetime_added, concat('Expense: ', c.title ), details, 0 as debit, amount as credit from expense a left join account b on a.account_id = b.id left join expense_category c on a.expense_category_id = c.id where a.status=1 and a.account_id='".get_config('drawbox_id')."'";
		
		$transactions="(".implode( ' union ', $transactions ).") as total_records";
		
		$sql = "select * from ".$transactions." where 1 and datetime_added BETWEEN '".date('Y-m-d',strtotime(date_dbconvert($date)))." 00:00:00' AND '".date('Y-m-d',strtotime(date_dbconvert($date)))." 23:59:59' order by position, datetime_added desc";
		$rs=doquery($sql, $dblink);
		if(numrows($rs)>0){
			$sn=1;
			while($r=dofetch($rs)){       
                ?>
                <tr>
                    <td class="text-center"><?php echo $sn;?></td>
                    <td><?php echo datetime_convert($r["datetime_added"]); ?></td>
                    <td><?php echo unslash($r["title"]); ?></td>
                    <td><?php echo unslash($r["details"]); ?></td>
                    <td class="text-right"><?php echo curr_format($r["debit"]); ?></td>
                    <td class="text-right"><?php echo curr_format($r["credit"]); ?></td>
                    <td class="text-right"><?php if($order == 'asc'){$balance += ($r["debit"]-$r["credit"])*($order == 'desc'?'-1':1);} echo curr_format( $balance ); if($order == 'desc'){$balance += ($r["debit"]-$r["credit"])*($order == 'desc'?'-1':1);} ?></td>
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
                <td class="text-right"><?php echo curr_format( $balance )?></td>
            </tr>
            <?php	
            }
            else{	
                ?>
                <tr>
                    <td colspan="7"  class="no-record">No Result Found</td>
                </tr>
                <?php
            }
            ?>
	</table>
    <?php
    /*$transactions = array();
	//$transactions[] = "select 3 as position, a.datetime_added, b.title, if(details='', concat( 'Transfer to account ', title ), details) as details, 0 as debit, amount as credit from transaction a left join account b on a.reference_id = b.id where a.status=1 and a.reference_id <> '".get_config("dailysale_customer_id")."'";	
	$transactions[] = "select 1 as position, a.datetime_added, b.title,  if(details='', concat( 'Transfer from account ', title ), details) as details, amount as debit, 0 as credit from transaction a left join account b on a.account_id = b.id where a.status=1 and a.reference_id <> '".get_config("dailysale_customer_id")."' and a.account_id='".get_config('drawbox_id')."'";
	
	//$transactions[] = "select 2 as position, a.datetime_added, concat('Expense: ', b.title ), details, amount as debit, 0 as credit from expense a left join expense_category b on a.expense_category_id = b.id where a.status=1";
	
	//$transactions[] = "select 4 as position, a.datetime_added, concat('Expense: ', b.title ), details, 0 as debit, amount as credit from expense a left join account b on a.account_id = b.id where a.status=1";
	
	$transactions="(".implode( ' union ', $transactions ).") as total_records";
	
	$sql = "select * from ".$transactions." where 1 and datetime_added BETWEEN '".date('Y-m-d',strtotime(date_dbconvert($date)))." 00:00:00' AND '".date('Y-m-d',strtotime(date_dbconvert($date)))." 23:59:59' order by position, datetime_added desc";
	$balance = dofetch( doquery( "select sum(debit)-sum(credit) as balance from ".$transactions." where 1 $extra and datetime_added < '".date('Y-m-d',strtotime(date_dbconvert($date)))." 00:00:00'", $dblink ) );
	if( $order == 'desc' ) {
		$balance = get_account_balance( date_dbconvert($date)." 23:59:59" );
	}
	else{
		$balance = get_account_balance( date_dbconvert($date) );
	}
	?>
    <table class="table table-hover list">
    	<thead>
            <tr>
                <th width="5%" class="text-center">S.no</th>
                <th>
                	<a href="report_manage.php?tab=daily&order_by=datetime_added&order=<?php echo $order=="asc"?"desc":"asc"?>" class="sorting">
                    	Date
                        <?php
						if( $order_by == "datetime_added" ) {
							?>
							<span class="sort-icon">
								<i class="fa fa-angle-<?php echo $order=="asc"?"up":"down"?>" data-hover_in="<?php echo $order=="asc"?"down":"up"?>" data-hover_out="<?php echo $order=="desc"?"down":"up"?>" aria-hidden="true"></i>
							</span>
							<?php
						}
						?>
                  	</a>
                </th>
                <th>Details</th>
                <th class="text-right">Debit</th>
                <th class="text-right" >Credit</th>
                <th class="text-right" >Balance</th>
            </tr>
    	</thead>
    	<tbody>
			<?php 
            $rs=doquery($sql, $dblink);
            if(numrows($rs)>0){
                $sn=1;
				?>
				<tr>
                	<td colspan="2"></td>
                    <td><?php echo $order == 'desc'?'Closing':'Opening'?> Balance</td>
                    <td></td>
                    <td></td>
                    <td class="text-right"><?php echo curr_format( $balance )?></td>
                </tr>
				<?php
				while($r=dofetch($rs)){             
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $sn;?></td>
                        <td><?php echo datetime_convert($r["datetime_added"]); ?></td>
                        <td><?php echo unslash($r["details"]); ?></td>
                        <td class="text-right"><?php echo curr_format($r["debit"]); ?></td>
                        <td class="text-right"><?php echo curr_format($r["credit"]); ?></td>
                        <td class="text-right"><?php if($order == 'asc'){$balance += ($r["debit"]-$r["credit"])*($order == 'desc'?'-1':1);} echo curr_format( $balance ); if($order == 'desc'){$balance += ($r["debit"]-$r["credit"])*($order == 'desc'?'-1':1);} ?></td>
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
                    <td class="text-right"><?php echo curr_format( $balance )?></td>
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
            ?>
    	</tbody>
  	</table><?php */?>
</div>

