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

if($date != ""){
	$extra.=" and datetime_added BETWEEN '".date('Y-m-d',strtotime(date_dbconvert($date)))." 00:00:00' AND '".date('Y-m-d',strtotime(date_dbconvert($date)))." 23:59:59'";
}

$order_by = "datetime_added";
$order = "desc";
$orderby = $order_by." ".$order;
?>
<div class="page-header">
	<h1 class="title">Reports</h1>
  	<ol class="breadcrumb">
    	<li class="active">Sales report</li>
  	</ol>
  	<div class="right">
    	<div class="btn-group" role="group" aria-label="..."> 
        	<a id="topstats" class="btn btn-light" href="#"><i class="fa fa-search"></i></a> 
        </div>
  	</div>
</div>
<ul class="topstats clearfix search_filter"<?php if($is_search) echo ' style="display: block"';?>>
	<li class="col-xs-12 col-lg-12 col-sm-12">
        <div>
        	<form class="form-horizontal" action="" method="get">
                <span class="col-sm-1 text-to">Date</span>
                <div class="col-sm-2">
                    <input type="text" title="Enter Date From" name="date" id="date" placeholder="" class="form-control date-picker"  value="<?php echo $date?>" >
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
    	<thead>
            <tr>
                <th width="5%" class="text-center">S.no</th>
                <th>Date</th>
                <th>Customer Name</th>
                <th class="text-right">Total Items</th>
                <th class="text-right" >Price</th>
    
                <th class="text-right">Net Price</th>
            </tr>
            <tr class="head">
                <th colspan="3" class="text-right">Total</th>
                <?php
					$sql="select (sum(quantity), sum(unit_price), sum(total_price) from sales_items where sales_id = sales.id) from sales where 1 $extra and status=1 order by $orderby";
					$total=dofetch(doquery($sql, $dblink));
				?>
                <th class="text-right"><?php echo $total[ "sum(quantity)" ]?></th>
                <th class="text-right">Rs. <?php echo curr_format($total[ "sum(unit_price)" ])?></th>
     
                <th class="text-right" >Rs. <?php echo curr_format($total[ "sum(total_price)" ])?></th>
            </tr>
    	</thead>
    	<tbody>
			<?php 
            $sql="select * from sales where 1 $extra order by $orderby";
            $rs=show_page($rows, $pageNum, $sql);
            if(numrows($rs)>0){
                $sn=1;
                while($r=dofetch($rs)){             
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $sn;?></td>
                        
                        <td><?php echo datetime_convert($r["datetime_added"]); ?></td>
                        <td><?php echo empty($r["customer_name"])?"":unslash($r["customer_name"]); ?></td>
                        <td class="text-right"><?php echo unslash($r["total_items"]); ?></td>
                        <td class="text-right">Rs. <?php echo curr_format(unslash($r["total_price"])); ?></td>
                        <td class="text-right">Rs. <?php echo curr_format(unslash($r["discount"])); ?></td>
                        <td class="text-right">Rs. <?php echo curr_format(unslash($r["net_price"])); ?></td>
                    </tr>
                    <?php 
                    $sn++;
                }
            }
            else{	
                ?>
                <tr>
                    <td colspan="8"  class="no-record">No Result Found</td>
                </tr>
                <?php
            }
            ?>
    	</tbody>
  	</table>
</div>
