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
        <th colspan="2">
            <h1><?php echo get_config( 'site_title' )?></h1>
            <h2>Trial Balance Report</h2>
            <p>
                <?php
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
        <tr>
            <th align="right">Sale from <?php echo $date_from?> to <?php echo $date_to?></th>
            <th align="right"><?php echo curr_format($sale_total[ "total" ])?></th>
        </tr>
        <tr>
            <th align="right">Sale Return <?php echo $date_from?> to <?php echo $date_to?></th>
            <th align="right"><?php echo curr_format(-$sale_return_total[ "total" ])?></th>
        </tr>
        <tr>
            <th align="right">Wheat Purchase from <?php echo $date_from?> to <?php echo $date_to?></th>
            <th align="right"><?php echo curr_format($wheat_purchase_total[ "total" ])?></th>
        </tr>
        <tr>
            <th align="right">Wheat Purchase Return from <?php echo $date_from?> to <?php echo $date_to?></th>
            <th align="right"><?php echo curr_format(-$wheat_purchase_return_total[ "total" ])?></th>
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
            <tr>
                <th align="right">Total Supplier Payment</th>
                <th align="right"><?php echo curr_format($supplier_total)?></th>
            </tr>
            <?php
			while( $r = dofetch( $rs ) ) {
				if( $r[ "total" ] > 0 ){
					?>
                    <tr>
                        <th align="right"><?php echo empty($r["supplier_name"])?"Unknown Supplier":unslash($r["supplier_name"]); ?></th>
                        <th align="right"><?php echo curr_format($r[ "total" ])?></th>
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
            <tr>
                <th align="right">Total Customer Payment</th>
                <th align="right"><?php echo curr_format($customer_total)?></th>
            </tr>
            <?php
			while( $r = dofetch( $rs ) ) {
				if( $r[ "total" ] > 0 ){
					?>
                    <tr>
                        <th align="right"><?php echo empty($r["customer_name"])?"Unknown Customer":unslash($r["customer_name"]); ?></th>
                        <th align="right"><?php echo curr_format($r[ "total" ])?></th>
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
            <tr>
                <th align="right">Total Expense</th>
                <th align="right"><?php echo curr_format($expense_total)?></th>
            </tr>
            <?php
			while( $r = dofetch( $rs ) ) {
				if( $r[ "total" ] > 0 ){
					?>
                    <tr>
                        <th align="right"><?php echo unslash( $r[ "title" ] )?></th>
                        <th align="right"><?php echo curr_format($r[ "total" ])?></th>
                    </tr>	
                    <?php
				}
			}
		}
		?>	
</table>
<?php
die;
