<?php
if(!defined("APP_START")) die("No Direct Access");
$extra='';
$is_search=false;
if(isset($_GET["datetime"])){
	$datetime=slash($_GET["datetime"]);
	$_SESSION["production_manage"]["datetime"]=$datetime;
}
if(isset($_SESSION["production_manage"]["datetime"]))
	$datetime=$_SESSION["production_manage"]["datetime"];
else
	$datetime="";
if(!empty($datetime)){
	$extra.="and datetime_added>='".date("Y/m/d H:i:s", strtotime(date_dbconvert($datetime)))."'  and datetime_added<'".date("Y/m/d H:i:s", strtotime(date_dbconvert($datetime))+3600*24)."'";
	$is_search=true;
}
?>
<div class="page-header">
	<h1 class="title">Manage Production</h1>
  	<ol class="breadcrumb">
    	<li class="active">All the Administrators who can use the manage item</li>
  	</ol>
  	<div class="right">
    	<div class="btn-group" role="group" aria-label="..."> 
        	<a href="production_manage.php?tab=add" class="btn btn-light editproject">Add New Record</a> 
            <a id="topstats" class="btn btn-light" href="#"><i class="fa fa-search"></i></a>
    	</div> 
    </div> 
</div> 
<ul class="topstats clearfix search_filter"<?php if($is_search) echo ' style="display: block"';?>>
	<li class="col-xs-12 col-lg-12 col-sm-12">
        <div>
        	<form class="form-horizontal" action="" method="get">    	
                <div class="col-sm-3 col-xs-8">
                  <input type="text" title="Enter String" value="<?php echo $datetime;?>" name="datetime" id="search" class="form-control date-picker" >  
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
                <th width="10%" class="text-center">S.no</th>
                <th class="text-center" width="10%"><div class="checkbox checkbox-primary">
                    <input type="checkbox" id="select_all" value="0" title="Select All Records">
                    <label for="select_all"></label></div></th>
                <th width="20%">DateTime</th>
                <th width="30%">Items</th>
                <th class="text-center" width="10%">Status</th>
                <th class="text-center" width="10%">Actions</th>
            </tr>
    	</thead>
    	<tbody>
			<?php
            $sql="select * from production where 1 $extra";
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
                        <td><?php echo datetime_convert($r["datetime_added"]); ?></td>
                        <td>
                        	<?php 
								$items=doquery("select * from production_items where production_id='".$r["id"]."'",$dblink);
								while($item=dofetch($items)){
									echo unslash($item["quantity"])." x ".get_field($item["item_id"], "items","title")."<br>";
								}
							?>
                        </td>
                        <td class="text-center"><a href="production_manage.php?id=<?php echo $r['id'];?>&tab=status&s=<?php echo ($r["status"]==0)?1:0;?>">
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
                        </a></td>
                        <td align="center">
                            <a href="production_manage.php?tab=edit&id=<?php echo $r['id'];?>"><img title="Edit Record" alt="Edit" src="images/edit.png"></a>&nbsp;&nbsp;
                            <a onclick="return confirm('Are you sure you want to delete')" href="production_manage.php?id=<?php echo $r['id'];?>&amp;tab=delete"><img title="Delete Record" alt="Delete" src="images/delete.png"></a>
                        </td>
                    </tr>
                    <?php 
                    $sn++;
                }
                ?>
                <tr>
                    <td colspan="3" class="actions">
                        <select name="bulk_action" id="bulk_action" title="Choose Action">
                            <option value="null">Bulk Action</option>
                            <option value="delete">Delete</option>
                            <option value="statuson">Set Status On</option>
                            <option value="statusof">Set Status Off</option>
                        </select>
                        <input type="button" name="apply" value="Apply" id="apply_bulk_action" class="btn btn-light" title="Apply Action"  />
                    </td>
                    <td colspan="3" class="paging" title="Paging" align="right"><?php echo pages_list($rows, "production", $sql, $pageNum)?></td>
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
  	</table>
</div>
