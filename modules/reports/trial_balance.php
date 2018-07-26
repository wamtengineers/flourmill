<?php
if(!defined("APP_START")) die("No Direct Access");
$extra='';
$is_search=true;
if(isset($_GET["date_from"])){
	$date_from=slash($_GET["date_from"]);
	$_SESSION["reports"]["trial_balance"]["date_from"]=$date_from;
}

if(isset($_SESSION["reports"]["trial_balance"]["date_from"]))
	$date_from=$_SESSION["reports"]["trial_balance"]["date_from"];
else
	$date_from=date("01/m/Y");

if($date_from != ""){
	$extra.=" and datetime_added>='".date('Y-m-d',strtotime(date_dbconvert($date_from)))." 00:00:00'";
}
if(isset($_GET["date_to"])){
	$date_to=slash($_GET["date_to"]);
	$_SESSION["reports"]["trial_balance"]["date_to"]=$date_to;
}

if(isset($_SESSION["reports"]["trial_balance"]["date_to"]))
	$date_to=$_SESSION["reports"]["trial_balance"]["date_to"];
else
	$date_to=date("d/m/Y");

if($date_to != ""){
	$extra.=" and datetime_added<='".date('Y-m-d',strtotime(date_dbconvert($date_to)))." 23:59:59'";
}
if( empty( $extra ) ) {
	$extra = ' and 1=0 ';
}
?>
<div class="page-header">
	<h1 class="title">Reports</h1>
  	<ol class="breadcrumb">
    	<li class="active">Trial Balance Report</li>
  	</ol>
  	<div class="right">
    	<div class="btn-group" role="group" aria-label="..."> 
        	<a id="topstats" class="btn btn-light" href="#"><i class="fa fa-search"></i></a> 
            <a class="btn print-btn" href="report_manage.php?tab=trial_balance_print"><i class="fa fa-print" aria-hidden="true"></i></a>  
        </div>
  	</div>
</div>
<ul class="topstats clearfix search_filter"<?php if($is_search) echo ' style="display: block"';?>>
	<li class="col-xs-12 col-lg-12 col-sm-12">
        <div class="">
        	<form class="form-horizontal" action="" method="get">
                <input type="hidden" name="tab" value="income" />
                <span class="col-sm-1">Date From</span>
                <div class="col-sm-2">
                    <input type="text" title="Enter Date From" name="date_from" id="date_from" placeholder="" class="form-control date-picker"  value="<?php echo $date_from?>">
                </div>
                <span class="col-sm-1">Date To</span>
                <div class="col-sm-2">
                    <input type="text" title="Enter Date To" name="date_to" id="date_to" placeholder="" class="form-control date-picker"  value="<?php echo $date_to?>">
                </div>
                
                <div class="col-sm-2 text-left">
                    <input type="button" class="btn btn-danger btn-l reset_search" value="Reset" alt="Reset Record" title="Reset Record" />
                    <input type="submit" class="btn btn-default btn-l" value="Search" alt="Search Record" title="Search Record" />
                </div>
          	</form>
        </div>
  	</li>
</ul>
<div class="panel-body table-responsive">
	<table class="table table-hover list">
    	<?php
		$sql="select (select sum(total_price) from sales_items where sales_id = sales.id)-discount as total from sales where status = 1 $extra";
		$sale_total=dofetch(doquery($sql, $dblink));
		$sql="select (select sum(total_price) from sales_return_items where sales_return_id = sales_return.id)-discount as total from sales_return where status = 1 $extra";
		$sale_return_total=dofetch(doquery($sql, $dblink));
		$sql="select sum(wheat_price) as total from wheat_purchase where status = 1 $extra";
		$wheat_purchase_total=dofetch(doquery($sql, $dblink));
		$sql="select sum(wheat_price) as total from wheat_purchase_return where status = 1 $extra";
		$wheat_purchase_return_total=dofetch(doquery($sql, $dblink));
		?>
        <tr class="head">
            <th class="text-right">Sale from <?php echo $date_from?> to <?php echo $date_to?></th>
            <th class="text-right" ><?php echo curr_format($sale_total[ "total" ])?></th>
        </tr>
        <tr class="head">
            <th class="text-right">Sale Return <?php echo $date_from?> to <?php echo $date_to?></th>
            <th class="text-right" ><?php echo curr_format(-$sale_return_total[ "total" ])?></th>
        </tr>
        <tr class="head">
            <th class="text-right">Wheat Purchase from <?php echo $date_from?> to <?php echo $date_to?></th>
            <th class="text-right" ><?php echo curr_format($wheat_purchase_total[ "total" ])?></th>
        </tr>
        <tr class="head">
            <th class="text-right">Wheat Purchase Return from <?php echo $date_from?> to <?php echo $date_to?></th>
            <th class="text-right" ><?php echo curr_format(-$wheat_purchase_return_total[ "total" ])?></th>
        </tr>
        <?php
		$supplier_total = 0;
        $rs = doquery( "select supplier_name, sum(amount) as total from supplier_payment a left join supplier b on a.supplier_id = b.id where a.status=1 $extra group by supplier_id", $dblink );
		if( numrows( $rs ) > 0 ) {
			?>
            <?php
				$supplier_payment=dofetch(doquery("select sum(amount) as total from supplier_payment where status=1 $extra", $dblink));
				$supplier_total += $supplier_payment[ "total" ];
			?>
            <tr class="head bg-info">
                <th class="text-right">Total Supplier Payment</th>
                <th class="text-right" ><?php echo curr_format($supplier_total)?></th>
            </tr>
            <?php
			while( $r = dofetch( $rs ) ) {
				if( $r[ "total" ] > 0 ){
					?>
                    <tr class="head">
                        <th class="text-right"><?php echo empty($r["supplier_name"])?"Unknown Supplier":unslash($r["supplier_name"]); ?></th>
                        <th class="text-right" ><?php echo curr_format($r[ "total" ])?></th>
                    </tr>	
                    <?php
				}
			}
		}
		?>
        <?php
		$customer_total = 0;
        $rs = doquery( "select customer_name, sum(amount) as total from customer_payment a left join customer b on a.customer_id = b.id where a.status=1 $extra group by customer_id", $dblink );
		if( numrows( $rs ) > 0 ) {
			?>
            <?php
				$customer_payment=dofetch(doquery("select sum(amount) as total from customer_payment where status=1 $extra", $dblink));
				$customer_total += $customer_payment[ "total" ];
			?>
            <tr class="head bg-info">
                <th class="text-right">Total Customer Payment</th>
                <th class="text-right" ><?php echo curr_format($customer_total)?></th>
            </tr>
            <?php
			while( $r = dofetch( $rs ) ) {
				if( $r[ "total" ] > 0 ){
					?>
                    <tr class="head">
                        <th class="text-right"><?php echo empty($r["customer_name"])?"Unknown Customer":unslash($r["customer_name"]); ?></th>
                        <th class="text-right" ><?php echo curr_format($r[ "total" ])?></th>
                    </tr>
                    <?php
					
				}
			}
		}
		?>
        <?php
		$expense_total = 0;
        $rs = doquery( "select title, sum(amount) as total from expense a left join expense_category b on a.expense_category_id = b.id where a.status=1 $extra group by expense_category_id", $dblink );
		if( numrows( $rs ) > 0 ) {
			?>
            <?php
				$expense=dofetch(doquery("select sum(amount) as total from expense where status=1 $extra", $dblink));
				$expense_total += $expense[ "total" ];
			?>
            <tr class="head bg-info">
                <th class="text-right">Total Expense</th>
                <th class="text-right" ><?php echo curr_format($expense_total)?></th>
            </tr>
            <?php
			while( $r = dofetch( $rs ) ) {
				if( $r[ "total" ] > 0 ){
					?>
                    <tr class="head">
                        <th class="text-right"><?php echo unslash( $r[ "title" ] )?></th>
                        <th class="text-right" ><?php echo curr_format($r[ "total" ])?></th>
                    </tr>	
                    <?php
				}
			}
		}
		?>
  	</table>
</div>
