<?php
if(!defined("APP_START")) die("No Direct Access");
$q="";
$extra=" and item_id = '".$parent_item_id."'";
$is_search=false;
if(isset($_GET["q"])){
	$q=slash($_GET["q"]);
	$_SESSION["items_variations"]["list"]["q"]=$q;
}
if(isset($_SESSION["items_variations"]["list"]["q"]))
	$q=$_SESSION["items_variations"]["list"]["q"];
else
	$q="";
if(!empty($q)){
	$extra.=" and quantity like '%".$q."%'";
	$is_search=true;
}
$sql = "select * from items_variations where 1".$extra." order by quantity";
?>
<div class="page-header">
	<h1 class="title"><?php echo unslash( $parent_item[ "title" ] )?></h1>
  	<ol class="breadcrumb">
    	<li class="active">Manage Items Variations</li>
  	</ol>
  	<div class="right">
    	<div class="btn-group" role="group" aria-label="..."> 
        	<a href="items_variations_manage.php?tab=add" class="btn btn-light editproject">Add New Record</a> <a id="topstats" class="btn btn-light" href="#"><i class="fa fa-search"></i></a>
        </div>
  	</div>
</div>
<ul class="topstats clearfix search_filter"<?php if($is_search) echo ' style="display: block"';?>>
	<li class="col-xs-12 col-lg-12 col-sm-12">
        <div>
        	<form class="form-horizontal" action="" method="get">
                <div class="col-sm-10 col-xs-8">
                  <input type="text" title="Enter String" value="<?php echo $q;?>" name="q" id="search" class="form-control" >  
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
                <th width="25%">Attributes</th>
                <th width="15%">Quantity</th>
                <th width="15%">Price</th>
                <th width="15%">Cost Price</th>
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
                        <td>
							<?php
                            $attributes = doquery( "select * from items_attributes where item_id = '".$parent_item[ "id" ]."'", $dblink );
                            if( numrows( $attributes ) > 0 ) {
                                while( $attribute = dofetch( $attributes ) ) {
                                	$value = doquery( "select `value` from items_variations_attributes where items_variations_id='".$r[ "id" ]."' and items_attributes_id='".$attribute[ "id" ]."'", $dblink );
									if( numrows( $value ) > 0 ) {
										$value = dofetch( $value );
										echo unslash( $attribute[ "name" ] ).": ".unslash( $value[ "value" ] )."<br />";
									}
								}
                            }
                            ?>
                        </td>
                        <td><?php echo unslash( $r[ "quantity" ] );?></td>
                        <td><?php echo curr_format(unslash($r["price"])); ?></td>
                        <td><?php echo curr_format(unslash($r["cost_price"])); ?></td>
                        <td align="center">
                            <a href="items_variations_manage.php?tab=edit&id=<?php echo $r['id'];?>"><img title="Edit Record" alt="Edit" src="images/edit.png"></a>&nbsp;&nbsp;
                            <a href="items_variations_manage.php?tab=print&id=<?php echo $r['id'];?>" class="barcode_print_button"><img title="Print Label" alt="Print" src="images/view.png"></a>&nbsp;&nbsp;
                            <a onclick="return confirm('Are you sure you want to delete')" href="items_variations_manage.php?id=<?php echo $r['id'];?>&amp;tab=delete"><img title="Delete Record" alt="Delete" src="images/delete.png"></a>
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
                    <td colspan="3" class="paging" title="Paging" align="right"><?php echo pages_list($rows, "items_variations", $sql, $pageNum)?></td>
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