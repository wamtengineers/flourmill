<?php
if(!defined("APP_START")) die("No Direct Access");
$q="";
$extra='';
$is_search=false;
if(isset($_GET["date_from"])){
	$date_from=slash($_GET["date_from"]);
	$_SESSION["wheat_purchase_return"]["list"]["date_from"]=$date_from;
}
if(isset($_SESSION["wheat_purchase_return"]["list"]["date_from"]))
	$date_from=$_SESSION["wheat_purchase_return"]["list"]["date_from"];
else
	$date_from="";
if($date_from != ""){
	$extra.=" and datetime_added>='".datetime_dbconvert($date_from)."'";
	$is_search=true;
}
if(isset($_GET["date_to"])){
	$date_to=slash($_GET["date_to"]);
	$_SESSION["wheat_purchase_return"]["list"]["date_to"]=$date_to;
}
if(isset($_SESSION["wheat_purchase_return"]["list"]["date_to"]))
	$date_to=$_SESSION["wheat_purchase_return"]["list"]["date_to"];
else
	$date_to="";
if($date_to != ""){
	$extra.=" and datetime_added<'".datetime_dbconvert($date_to)."'";
	$is_search=true;
}
?>
<div class="page-header">
	<h1 class="title">Manage Wheat Purchase Return</h1>
  	<ol class="breadcrumb">
    	<li class="active">All the administrators who can use the manage item</li>
  	</ol>
  	<div class="right">
    	<div class="btn-group" role="group" aria-label="..."> 
        	<a href="wheat_purchase_return_manage.php?tab=add" class="btn btn-light editproject">Add New Record</a> 
            <a id="topstats" class="btn btn-light" href="#"><i class="fa fa-search"></i></a> 
    	</div> 
    </div> 
</div>
<ul class="topstats clearfix search_filter"<?php if($is_search) echo ' style="display: block"';?>>
    <li class="col-xs-12 col-lg-12 col-sm-12">
    	<div>
        	<form class="form-horizontal" action="" method="get">
                <div class="col-sm-2">
                    <input type="text" title="Enter Date From" name="date_from" id="date_from" placeholder="Date From" class="form-control date-timepicker"  value="<?php echo $date_from?>" >
                </div>
                <div class="col-sm-2">
                    <input type="text" title="Enter Date To" name="date_to" id="date_to" placeholder="Date To" class="form-control date-timepicker" value="<?php echo $date_to?>" >
                </div>
                <div class="col-sm-3 col-xs-2 text-left">
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
                <th width="5%" class="text-center">S.No</th>
                <th class="text-center" width="5%"><div class="checkbox checkbox-primary">
                    <input type="checkbox" id="select_all" value="0" title="Select All Records">
                    <label for="select_all"></label></div></th>
                <th width="20%">Date</th>
                <th width="15%">Party Name</th>
                <th width="15%">Broker Name</th>
                <th width="10%">Vahicle Number</th>
                <th width="10%" class="text-right">Wheat Price</th>
                <th width="10%" class="text-center">Status</th>
                <th width="10%" class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $sql="select * from wheat_purchase_return where 1 $extra";
            $rs=show_page($rows, $pageNum, $sql);
            if(numrows($rs)>0){
                $sn=1;
                while($r=dofetch($rs)){             
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $sn;?></td>
                        <td class="text-center"><div class="checkbox margin-t-0 checkbox-primary">
                            <input type="checkbox" name="id[]" id="<?php echo "rec_".$sn?>"  value="<?php echo $r["id"]?>" title="Select Record" />
                            <label for="<?php echo "rec_".$sn?>"></label></div>
                        </td>
                        <td><?php echo date_convert($r["datetime_added"]); ?></td>
                        <td><?php echo get_field( unslash($r["supplier_id"]), "supplier", "supplier_name" ); ?></td>
                        <td><?php echo unslash($r["broker_name"]); ?></td>
                        <td><?php echo unslash($r["vehicle_number"]); ?></td>
                        <td class="text-right"><?php echo curr_format(unslash($r["wheat_price"])); ?></td>
                        <td class="text-center">
                            <a href="wheat_purchase_return_manage.php?id=<?php echo $r['id'];?>&tab=status&s=<?php echo ($r["status"]==0)?1:0;?>">
                                <?php
                                if($r["status"]==0){
                                    ?>
                                    <img src="images/offstatus.png" alt="Off" title="Set Status On">
                                    <?php
                                }
                                else{
                                    ?>
                                    <img src="images/onstatus.png" alt="On" title="Set Status Off">
                                    <?php
                                }
                                ?>
                            </a>
                        </td>
                        <td class="text-center">
                            	<a href="wheat_purchase_return_manage.php?tab=edit&id=<?php echo $r['id'];?>"><img title="Edit Record" alt="Edit" src="images/edit.png"></a>&nbsp;&nbsp;
                            	<a onclick="return confirm('Are you sure you want to delete')" href="wheat_purchase_return_manage.php?id=<?php echo $r['id'];?>&amp;tab=delete"><img title="Delete Record" alt="Delete" src="images/delete.png"></a>
                        </td>
                    </tr>  
                    <?php 
                    $sn++;
                }
                ?>
                <tr>
                    <td colspan="5" class="actions">
                        <select name="bulk_action" class="" id="bulk_action" title="Choose Action">
                            <option value="null">Bulk Action</option>
                            <option value="delete">Delete</option>
                            <option value="statuson">Set Status On</option>
                            <option value="statusof">Set Status Off</option>
                        </select>
                        <input type="button" name="apply" value="Apply" id="apply_bulk_action" class="btn btn-light" title="Apply Action"  />
                    </td>
                    <td colspan="4" class="paging" title="Paging" align="right"><?php echo pages_list($rows, "wheat_purchase_return", $sql, $pageNum)?></td>
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
        </tbody>
     </table>
</div>