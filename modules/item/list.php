<?php
if(!defined("APP_START")) die("No Direct Access");
?>
<div class="page-header">
	<h1 class="title">Manage Items</h1>
  	<ol class="breadcrumb">
    	<li class="active">All the administrators who can use the manage item</li>
  	</ol>
  	<div class="right">
    	<div class="btn-group" role="group" aria-label="..."> 
        	<a href="items_manage.php?tab=add" class="btn btn-light editproject">Add New Record</a> 
            <a id="topstats" class="btn btn-light" href="#"><i class="fa fa-search"></i></a> 
            <a class="btn print-btn" href="items_manage.php?tab=report"><i class="fa fa-print" aria-hidden="true"></i></a>
    	</div> 
    </div> 
</div> 
<ul class="topstats clearfix search_filter"<?php if($is_search) echo ' style="display: block"';?>>
	<li class="col-xs-12 col-lg-12 col-sm-12">
        <div>
        	<form class="form-horizontal" action="" method="get">
                <div class="col-sm-2">
                	<select name="category" id="category" title="Choose Option">
                        <option value="">Select Item Category</option>
                        <?php
                        $res=doquery("Select * from item_category order by sortorder ASC",$dblink);
                        if(numrows($res)>0){
                            while($rec=dofetch($res)){
                            ?>
                            <option value="<?php echo $rec["id"]?>"<?php echo($category==$rec["id"])?"selected":"";?>><?php echo unslash($rec["title"]); ?></option>
                         <?php			
                            }			
                        }
                        ?>
                	</select>
                </div>
                <div class="col-sm-3 col-xs-8">
                  <input type="text" title="Enter String" value="<?php echo $q;?>" name="q" id="search" class="form-control" >  
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
                <th width="3%" class="text-center">S.no</th>
                <th class="text-center" width="3%"><div class="checkbox checkbox-primary">
                    <input type="checkbox" id="select_all" value="0" title="Select All Records">
                    <label for="select_all"></label></div></th>
                <th width="15%">Item Category</th>
                <th width="15%">
                	<a href="items_manage.php?order_by=title&order=<?php echo $order=="asc"?"desc":"asc"?>" class="sorting">
                    	Title
                    	<?php
						if( $order_by == "title" ) {
							?>
							<span class="sort-icon">
                                <i class="fa fa-angle-<?php echo $order=="asc"?"up":"down"?>" data-hover_in="<?php echo $order=="asc"?"down":"up"?>" data-hover_out="<?php echo $order=="desc"?"down":"up"?>" aria-hidden="true"></i>
                            </span>
							<?php
						}
						?>
                    </a>
                </th>
                <th width="20%">Packing</th>
                <th class="text-center" width="5%">Status</th>
                <th class="text-center" width="8%">Actions</th>
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
                        <td><?php if($r["item_category_id"]==0) echo ""; else echo get_field($r["item_category_id"], "item_category");?></td>
                        <td><?php echo unslash($r["title"]); ?></td>
                        <td>
                        	<?php
                            	$packing = array();
								$rs2 =doquery("select title from items_packing_sizes a inner join packing b on a.packing_id=b.id where item_id='".$r["id"]."'", $dblink);
								if( numrows( $rs2 ) > 0 ) {
									while( $r2 = dofetch( $rs2 ) ) {
										$packing[] = $r2[ "title" ];
									}
								}
								echo implode( ", ", $packing );
							?>
                        </td>
                        <td class="text-center"><a href="items_manage.php?id=<?php echo $r['id'];?>&tab=status&s=<?php echo ($r["status"]==0)?1:0;?>">
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
                            <a href="items_manage.php?tab=edit&id=<?php echo $r['id'];?>"><img title="Edit Record" alt="Edit" src="images/edit.png"></a>&nbsp;&nbsp;
                            <a onclick="return confirm('Are you sure you want to delete')" href="items_manage.php?id=<?php echo $r['id'];?>&amp;tab=delete"><img title="Delete Record" alt="Delete" src="images/delete.png"></a>
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
                            <option value="statuson">Set Status On</option>
                            <option value="statusof">Set Status Off</option>
                        </select>
                        <input type="button" name="apply" value="Apply" id="apply_bulk_action" class="btn btn-light" title="Apply Action"  />
                    </td>
                    <td colspan="3" class="paging" title="Paging" align="right"><?php echo pages_list($rows, "items", $sql, $pageNum)?></td>
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
