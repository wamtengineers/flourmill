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
@media print{
	body{ overflow:hidden;}
	#top,.page-header,#footer{
		display:none;
	}
	.topstats{ display:none !important;}
	.table-responsive, table{ display:block;}
	.content{ padding:0;}
	#footer.bottom_round_corners{ display:none;}
	.head{ display:block;}
}
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
        	<th colspan="10" width="100%">
                    <?php
                    if( !empty( $date ) ){
                        echo " Date ".$date;
                    }
                    ?>
            </th>
        </tr>
        <tr>
            <th colspan="10">SALES</th>
        </tr>
        <tr>
            <th width="5%" style="text-align:center">S#</th>
            <th width="15%">Date/Time</th>
            <th width="10%">Token Number</th>
            <th width="15%">Customer Name</th>
            <th width="15%">Items</th>
            <th width="8%">Packing</th>
            <th width="10%" style="text-align:right;">Total Items</th>
            <th width="10%" style="text-align:right;">Total Price</th>
            <th width="10%" style="text-align:right;">Payment Amount</th>
            <th style="text-align:center;">Status</th>
        </tr>
		<?php
		$sales=doquery("select * from (select a.*, b.title, amount, (select sum((quantity-less_weight)*if(rate=0,packing,1)) from sales_items where sales_id = a.id)-less_weight as total_items, (select group_concat(concat(quantity, ' &times ', packing, 'KG ', title) SEPARATOR '<br>') from sales_items left join items on sales_items.item_id = items.id where sales_id = a.id) as items, (select sum(total_price) from sales_items where sales_id = a.id)-discount as total_price from sales a left join account b on a.account_id = b.id left join transaction c on a.transaction_id = c.id ) as temp_table where 1 and datetime_added BETWEEN '".date('Y-m-d',strtotime(date_dbconvert($date)))." 00:00:00' AND '".date('Y-m-d',strtotime(date_dbconvert($date)))." 23:59:59' order by datetime_added desc", $dblink);
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
                    <td style="text-align:left;"><?php echo datetime_convert($sale["datetime_added"]); ?></td>
                    <td><?php echo get_token_number( $sale ); ?></td>
                    <td style="text-align:left;"><?php echo get_field($sale["account_id"], "account","title");?></td>
                    <td>
                        <?php echo $sale[ "items" ];?>
                    </td>
                    <td style="text-align:right;">
                        <?php 
                            $packing = doquery("select * from sales_items where sales_id = '".$sale["id"]."'", $dblink);
                             while($pack=dofetch($packing)){
                                echo curr_format($pack["packing"]). " KG". " , ";
                             }
                        ?>
                    </td>
                    <td style="text-align:right;"><?php echo curr_format($sale["total_items"]); ?></td>
                    <td style="text-align:right;"><?php echo curr_format($sale["total_price"]); ?></td>
                    <td style="text-align:right;"><?php echo curr_format($sale["amount"]); ?></td> 
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
            <th colspan="6" style="text-align:right;">Total</th>
            <th style="text-align:right;"><?php echo curr_format($total_items);?></th>
            <th style="text-align:right;"><?php echo curr_format($total_price);?></th>
            <th style="text-align:right;"><?php echo curr_format($payment_amount);?></th>
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
            <th width="15%">Date</th>
            <th width="20%">Account</th>
            <th width="30%">Details</th>
            <th class="text-right" width="10%">Debit</th>
            <th class="text-right" width="10%">Credit</th>
        </tr>
        <?php
		$transactions = array();
		$transactions[] = "select 3 as position, a.datetime_added, b.title, if(details='', concat( 'Transfer to account ', title ), details) as details, 0 as debit, amount as credit from transaction a left join account b on a.reference_id = b.id where a.status=1";
		
		$transactions[] = "select 1 as position, a.datetime_added, b.title,  if(details='', concat( 'Transfer from account ', title ), details) as details, amount as debit, 0 as credit from transaction a left join account b on a.account_id = b.id where a.status=1";
		
		$transactions[] = "select 2 as position, a.datetime_added, concat('Expense: ', b.title ), details, amount as debit, 0 as credit from expense a left join expense_category b on a.expense_category_id = b.id where a.status=1";
		
		$transactions[] = "select 4 as position, a.datetime_added, concat('Expense: ', b.title ), details, 0 as debit, amount as credit from expense a left join account b on a.account_id = b.id where a.status=1";
		
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
                </tr>
                <?php
				$sn++;
            }
        }
        ?>
	</table>
</div>

