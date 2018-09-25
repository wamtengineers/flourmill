<?php
if(!defined("APP_START")) die("No Direct Access");

?>
<div class="page-header">
	<h1 class="title">Manage Sales</h1>
  	<ol class="breadcrumb">
    	<li class="active">Sales and billing</li>
  	</ol>
  	<div class="right">
    	<div class="btn-group" role="group" aria-label="..."> 
        	<a href="sales_manage.php?tab=addedit" class="btn btn-light editproject">Add New Record</a> 
            <a id="topstats" class="btn btn-light" href="#"><i class="fa fa-search"></i></a> 
            <a class="btn print-btn" href="sales_manage.php?tab=report"><i class="fa fa-print" aria-hidden="true"></i></a>  
        </div>
  	</div>
</div>
<ul class="topstats clearfix search_filter" style="display:block;">
	<li class="col-xs-12 col-lg-12 col-sm-12">
        <div>
        	<form class="form-horizontal" action="" method="get">
                <div class="col-sm-1">
                    <input type="text" placeholder="Enter Date From" name="date_from" id="date_from" class="form-control datepicker"  value="<?php echo $date_from?>" >
                </div>
                <div class="col-sm-1">
                    <input type="text" placeholder="Enter Date To" name="date_to" id="date_to" class="form-control datepicker" value="<?php echo $date_to?>" >
                </div>
                <div class="col-sm-2">
                  	<select name="account_id" class="searchbox">
                    	<option value="">Select Customer</option>
                        <?php
                        $rs = doquery( "select * from account order by id", $dblink );
						if( numrows( $rs ) > 0 ) {
							while( $r = dofetch( $rs ) ) {
								?>
								<option value="<?php echo $r[ "id" ]?>"<?php echo $account_id == $r[ "id" ]?' selected':''?>><?php echo unslash( $r[ "title" ] )?></option>
								<?php
							}
						}
						?>
                    </select>
                </div>
                <div class="col-sm-2">
                	<select name="status[]" id="status" class="searchbox" multiple>
                    	<option value="1"<?php echo in_array( "1", $status)? " selected":"";?>>Dispatched</option>
                    	<option value="2"<?php echo in_array( "2", $status)? " selected":"";?>>Delivering</option>
                        <option value="3"<?php echo in_array( "3", $status)? " selected":"";?>>Delivered</option>
                        <option value="4"<?php echo in_array( "4", $status)? " selected":"";?>>On Hold</option>
                        <option value="0"<?php echo in_array( "5", $status)? " selected":"";?>>Cancelled</option>
                    </select>
                </div>
                <div class="col-sm-2">
                	<select name="transaction_id" id="transaction_id">
                    	<option value="">Credit/Cash<?php echo $transaction_id?></option>
                        <option value="1"<?php echo ($transaction_id=="1")? " selected":"";?>>Cash Sale</option>
                        <option value="2"<?php echo ($transaction_id=="2")? " selected":"";?>>Credit Sale</option>
                    </select>
                </div>
                <div class="col-sm-2">
                  <input type="text" title="Enter String" value="<?php echo $q;?>" name="q" id="search" class="form-control" placeholder="Search Items" >  
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
    	<thead>
            <tr>
                <th width="5%" class="text-center">S.no</th>
                <th class="text-center" width="5%"><div class="checkbox checkbox-primary">
                    <input type="checkbox" id="select_all" value="0" title="Select All Records">
                    <label for="select_all"></label></div></th>
                <th>
                	<a href="sales_manage.php?order_by=datetime_added&order=<?php echo $order=="asc"?"desc":"asc"?>" class="sorting">
                        Date
                        <?php
                            if( $order_by == "datetime_added" ) {
                                ?>
                                <span class="sort-icon">
                                    <i class="fa fa-angle-<?php echo $order=="asc"?"up":"down"?>" data-hover_in="<?php echo $order=="asc"?"down":"up"?>" data-hover_out="<?php echo $order=="desc"?"down":"up"?>" aria-hidden="true"></i>
                                </span>
                                <?php
                            }
                            ?>
 					</a>
                </th>
                <th width="10%">Token Number</th>
                <th>Customer Name</th>
                <th>Items</th>
                <th class="text-right">Packing</th>
                <th class="text-right">Quantity</th>
                <th class="text-right">Rate</th>
                <th class="text-right">
                	<a href="sales_manage.php?order_by=total_price&order=<?php echo $order=="asc"?"desc":"asc"?>" class="sorting">
                		Total Amount
                        <?php
                            if( $order_by == "total_price" ) {
                                ?>
                                <span class="sort-icon">
                                    <i class="fa fa-angle-<?php echo $order=="asc"?"up":"down"?>" data-hover_in="<?php echo $order=="asc"?"down":"up"?>" data-hover_out="<?php echo $order=="desc"?"down":"up"?>" aria-hidden="true"></i>
                                </span>
                                <?php
                            }
                            ?>
                    </a>
                </th>
                <th class="text-right">Grand Total</th>
        		<th class="text-right">Total Weight</th>
                <th class="text-center">Status</th>
                <th class="text-center">Actions</th>
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
                        <td><?php echo datetime_convert($r["datetime_added"]); ?></td>
                        <td><?php echo $r[ "id" ]//get_token_number( $r ); ?></td>
                        <td><?php echo unslash( $r[ "customer" ] ); ?></td>
                        <td>
                        	<?php 
								$items = doquery("select a.*, b.title from sales_items a left join items b on a.item_id = b.id where sales_id = '".$r["id"]."'", $dblink);
								 while($item=dofetch($items)){
									echo unslash($item["title"])." <br>";
								 }
							?>
                        </td>
                        <td class="text-right">
							<?php 
                                $packing = doquery("select a.* from sales_items a left join items b on a.item_id = b.id where sales_id = '".$r["id"]."'", $dblink);
                                 while($pack=dofetch($packing)){
                                    echo $pack["packing"]." <br>";
                                 }
                            ?>
                        </td>
                        <td class="text-right">
                        	<?php 
								$quantity = doquery("select quantity-less_weight as item_quantity from sales_items where sales_id = '".$r["id"]."'", $dblink);
								 while($qty=dofetch($quantity)){
									echo $qty["item_quantity"]." <br>";
								 }
							?>
                        </td>
                        <td class="text-right">
							<?php 
                                $rates = doquery("select unit_price from sales_items where sales_id = '".$r["id"]."'", $dblink);
                                 while($rate=dofetch($rates)){
                                    echo number_format(abs($rate["unit_price"]), 2, '.',',')." <br>";
                                 }
                            ?>
                        </td>
                        <td class="text-right">
                        	<?php 
								$items_price = doquery("select total_price from sales_items where sales_id = '".$r["id"]."'", $dblink);
								 while($item_price=dofetch($items_price)){
									echo curr_format($item_price["total_price"])." <br>";
								 }
							?>
                        </td>                        
                        <td class="text-right"><?php echo curr_format($r["amount"]); ?></td>    
                        <td class="text-right"><?php echo curr_format($r["total_items"]); ?></td>                     
                        <td class="text-center">
                        	<?php
							if($r["status"]==0){
								?>
								<span class="order-status cancel">Cancelled</span>
								<?php
							}
							elseif($r["status"]==1){
								?>
								<span class="order-status dispatch">Dispatched</span>
								<?php
							}
							elseif($r["status"]==2){
								?>
								<span class="order-status deliver">Delivering</span>
								<?php
							}
							elseif($r["status"]==3){
								?>
								<span class="order-status delivered">Delivered</span>
								<?php
							}
							elseif($r["status"]==3){
								?>
								<span class="order-status onhold">On Hold</span>
								<?php
							}
							?>
                            
                        </td>
                        <td class="text-center">
                            <a href="sales_manage.php?tab=addedit&id=<?php echo $r['id'];?>"><img title="Edit Record" alt="Edit" src="images/edit.png"></a>&nbsp;&nbsp;
                            <a href="sales_manage.php?tab=print&id=<?php echo $r['id'];?>"><img title="Print Record" alt="Print" src="images/view.png"></a>&nbsp;&nbsp;
                            <a onclick="return confirm('Are you sure you want to delete')" href="sales_manage.php?id=<?php echo $r['id'];?>&amp;tab=delete"><img title="Delete Record" alt="Delete" src="images/delete.png"></a>
                        </td>
                    </tr>
                    <?php 
                    $sn++;
                }
                ?>
                <tr>
                    <td colspan="8" class="actions">
                        <select name="bulk_action" id="bulk_action" title="Choose Action">
                            <option value="null">Bulk Action</option>
                            <option value="delete">Delete</option>
                            <option value="statuson">Set Status Dispatched</option>
                            <option value="statusof">Set Status Cancelled</option>
                            <option value="statusrec">Set Status Delivering</option>
                        </select>
                        <input type="button" name="apply" value="Apply" id="apply_bulk_action" class="btn btn-light" title="Apply Action"  />
                    </td>
                    <td colspan="6" class="paging" title="Paging" align="right"><?php echo pages_list($rows, "sales", $sql, $pageNum)?></td>
                </tr>
                <?php	
            }
            else{	
                ?>
                <tr>
                    <td colspan="14"  class="no-record">No Result Found</td>
                </tr>
                <?php
            }
            ?>
    	</tbody>
  	</table>
</div>

