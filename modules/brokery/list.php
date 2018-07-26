<?php
if(!defined("APP_START")) die("No Direct Access");
$q="";
$extra=" and account_id = '".$parent_account_id."'";
$is_search=false;
if(isset($_GET["item_id"])){
	$item_id=slash($_GET["item_id"]);
	$_SESSION["brokery"]["list"]["item_id"]=$item_id;
}
if(isset($_SESSION["brokery"]["list"]["item_id"]))
	$item_id=$_SESSION["brokery"]["list"]["item_id"];
else
	$item_id="";
if($item_id!=""){
	$extra.=" and item_id='".$item_id."'";
	$is_search=true;
}
$sql = "select * from brokery where 1".$extra." order by id";
?>
<div class="page-header">
	<h1 class="title">Brokery Account</h1>
  	<ol class="breadcrumb">
    	<li class="active">Manage Brokery</li>
  	</ol>
  	<div class="right">
    	<div class="btn-group" role="group" aria-label="..."> 
        	<a href="brokery_manage.php?tab=add" class="btn btn-light editproject">Add New Record</a> <a id="topstats" class="btn btn-light" href="#"><i class="fa fa-search"></i></a>
        </div>
  	</div>
</div>
<ul class="topstats clearfix search_filter"<?php if($is_search) echo ' style="display: block"';?>>
	<li class="col-xs-12 col-lg-12 col-sm-12">
        <div>
        	<form class="form-horizontal" action="" method="get">
                <div class="col-sm-3">
                	<select name="item_id" id="item_id" class="form-control">
                        <option value=""<?php echo ($item_id=="")? " selected":"";?>>Select Item</option>
                        <?php
                            $res=doquery("select * from items order by id",$dblink);
                            if(numrows($res)>=0){
                                while($rec=dofetch($res)){
                                ?>
                                <option value="<?php echo $rec["id"]?>" <?php echo($item_id==$rec["id"])?"selected":"";?>><?php echo unslash($rec["title"])?></option>
                                <?php
                                }
                            }	
                        ?>
                    </select>
                </div>
                <div class="col-sm-1 col-xs-2">
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
                <th class="text-center" width="5%"><div class="checkbox checkbox-primary">
                    <input type="checkbox" id="select_all" value="0" title="Select All Records">
                    <label for="select_all"></label></div></th>
                <th width="25%">Item</th>
                <th width="10%">Packing</th>
                <th width="10%" class="text-right">Amount</th>
                <th class="text-center" width="5%">Status</th>
                <th class="text-center" width="10%">Actions</th>
            </tr>
    	</thead>
    	<tbody>
			<?php 
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
                        <td><?php echo get_field( unslash($r["item_id"]), "items", "title" );?></td>
                        <td><?php echo  unslash( $r[ "packing" ]  );?></td>
                        <td class="text-right"><?php echo curr_format(unslash($r["amount"])); ?></td>
                        <td class="text-center"><a href="brokery_manage.php?id=<?php echo $r['id'];?>&tab=status&s=<?php echo ($r["status"]==0)?1:0;?>">
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
                            <a href="brokery_manage.php?tab=edit&id=<?php echo $r['id'];?>"><img title="Edit Record" alt="Edit" src="images/edit.png"></a>&nbsp;&nbsp;
                            <a onclick="return confirm('Are you sure you want to delete')" href="brokery_manage.php?id=<?php echo $r['id'];?>&amp;tab=delete"><img title="Delete Record" alt="Delete" src="images/delete.png"></a>
                        </td>
                    </tr>
                    <?php 
                    $sn++;
                }
                ?>
                <tr>
                    <td colspan="4" class="actions">
                        <select name="bulk_action" id="bulk_action" title="Choose Action">
                            <option value="null">Bulk Action</option>
                            <option value="delete">Delete</option>
                        </select>
                        <input type="button" name="apply" value="Apply" id="apply_bulk_action" class="btn btn-light" title="Apply Action"  />
                    </td>
                    <td colspan="3" class="paging" title="Paging" align="right"><?php echo pages_list($rows, "brokery", $sql, $pageNum)?></td>
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
    	</tbody>
  	</table>
</div>