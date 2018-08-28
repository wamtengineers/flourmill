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
table table th, table table td{
	padding:5px;
}
table {
    border-collapse:  collapse;
	width:100%;
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
    	<thead>
            <tr>
                <th width="5%" style="text-align:center">S#</th>
                <th width="15%">Date</th>
                <th width="10%">Token Number</th>
                <th width="15%">Customer Name</th>
                <th width="15%">Items</th>
                <th width="8%">Packing</th>
                <th width="10%" style="text-align:right;">Total Items</th>
                <th width="10%" style="text-align:right;">Total Price</th>
                <th width="10%" style="text-align:right;">Payment Amount</th>
                <th style="text-align:center">Status</th>
            </tr>
    	</thead>
    	<tbody>
        	<tr>
                <td style="text-align:center"></td>
                <td style="text-align:left;"></td>
                <td></td>
                <td style="text-align:left;"></td>
                <td></td>
                <td style="text-align:right;">
                </td>
                <td style="text-align:right;"></td>
                <td style="text-align:right;"></td>
                <td style="text-align:right;"></td> 
                <td class="text-center">
                    
                </td>
            </tr>
    	</tbody>
  	</table>
</div>

