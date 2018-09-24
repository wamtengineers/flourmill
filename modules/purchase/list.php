<?php
if(!defined("APP_START")) die("No Direct Access");

?>
<div class="page-header">
	<h1 class="title">Manage Purchase</h1>
  	<ol class="breadcrumb">
    	<li class="active">Purchase and billing</li>
  	</ol>
  	<div class="right">
    	<div class="btn-group" role="group" aria-label="..."> 
        	<a href="purchase_manage.php?tab=addedit" class="btn btn-light editproject">Add New Record</a> 
            <a id="topstats" class="btn btn-light" href="#"><i class="fa fa-search"></i></a> 
            <a class="btn print-btn" href="purchase_manage.php?tab=report"><i class="fa fa-print" aria-hidden="true"></i></a>  
        </div>
  	</div>
</div>
<ul class="topstats clearfix search_filter"<?php if($is_search) echo ' style="display: block"';?>>
	<li class="col-xs-12 col-lg-12 col-sm-12">
        <div>
        	<form class="form-horizontal" action="" method="get">
                <div class="col-sm-2">
                    <input type="text" placeholder="Enter Date From" name="date_from" id="date_from" class="form-control datepicker"  value="<?php echo $date_from?>" >
                </div>
                <div class="col-sm-2">
                    <input type="text" placeholder="Enter Date To" name="date_to" id="date_to" class="form-control datepicker" value="<?php echo $date_to?>" >
                </div>
                <div class="col-sm-2">
                  	<select name="account_id" class="searchbox">
                    	<option value="">Select Account</option>
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
                	<select name="status" id="status" class="custom_select">
                    	<option value="1"<?php echo ($status=="1")? " selected":"";?>>Arrived</option>
                    	<option value="2"<?php echo ($status=="2")? " selected":"";?>>Received</option>
                        
                        <option value="0"<?php echo ($status=="0")? " selected":"";?>>Cancelled</option>
                    </select>
                </div>
                <div class="col-sm-2">
                  <input type="text" title="Enter String" value="<?php echo $q;?>" name="q" id="search" class="form-control" placeholder="Search Customer" >  
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
                <th width="2%" class="text-center">S.no</th>
                <th class="text-center" width="3%"><div class="checkbox checkbox-primary">
                    <input type="checkbox" id="select_all" value="0" title="Select All Records">
                    <label for="select_all"></label></div></th>
                <th width="5%">
                	<a href="purchase_manage.php?order_by=datetime_added&order=<?php echo $order=="asc"?"desc":"asc"?>" class="sorting">
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
                <th width="6%">Bill No.</th>
                <th width="12%">Customer Name</th>
                <th width="10%">Items</th>
                <th width="7%">Packing</th>
                <th width="7%" class="text-right">Total KG</th>
                <th width="7%" class="text-right">Less</th>
        		<th width="7%" class="text-right">Net KG</th>
                <th width="7%" class="text-right">Rate</th>
                <th width="10%" class="text-right">
                	<a href="purchase_manage.php?order_by=total_price&order=<?php echo $order=="asc"?"desc":"asc"?>" class="sorting">
                		Total Price
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
                <th width="8%" style="text-align:right;">Fare of V.</th>
                <th width="7%" style="text-align:right;">Brokery</th>
                <th width="7%" style="text-align:right;">Balance</th>
                <th width="3%" class="text-center">Status</th>
                <th width="5%" class="text-center">Actions</th>
            </tr>
    	</thead>
    	<tbody>
			<?php
            $rs=show_page($rows, $pageNum, $sql);
            if(numrows($rs)>0){
				$fov_total = 0;
				$brokery_total = 0;
				$balance_total = 0;
				$total_items = $total_price = $payment_amount = 0;
                $sn=1;
                while($r=dofetch($rs)){    
					$total_items += $r["total_items"];
					$total_price += $r["total_price"];
					$payment_amount += $r["amount"];
					$fov = 0;
					$brokery = 0;
					$balance = $r["total_price"];         
                    ?>
                    <tr>
                        <td class="text-center"><?php echo $sn;?></td>
                        <td class="text-center"><div class="checkbox margin-t-0 checkbox-primary">
                            <input type="checkbox" name="id[]" id="<?php echo "rec_".$sn?>"  value="<?php echo $r["id"]?>" title="Select Record" />
                            <label for="<?php echo "rec_".$sn?>"></label></div>
                        </td>
                        <td><?php echo date_convert($r["datetime_added"]); ?></td>
                        <td><?php echo unslash($r["bill_no"]); ?></td>
                        <td><?php echo get_field($r["account_id"], "account","title"); ?></td>
                        <td><?php echo $r[ "items" ];?></td>
                        <td><?php echo curr_format( $r[ "packing" ]);?></td>
                        <td class="text-right"><?php echo curr_format($r["total_items"]); ?></td>
                        <td class="text-right"><?php echo curr_format($r["less_weight_item"]); ?></td>
                        <td class="text-right"><?php echo curr_format($r["total_items"]-$r["less_weight_item"]); ?></td>
                        <td class="text-right"><?php echo $r["unit_price"]; ?></td>
                        <td class="text-right"><?php echo curr_format($r["total_price"]); ?></td>
                        <td class="text-right"><?php
                            if( !empty( $r[ "fare_transaction_id" ] ) ) {
                                if( $r[ "cnf" ] == 1 ) {
                                    $t = doquery( "select * from transaction where id = '".$r[ "fare_transaction_id" ]."'", $dblink );
                                    if( numrows( $t ) > 0 ) {
                                        $t = dofetch( $t );
                                        $fov = $t[ "amount" ];
                                        $balance -= $fov;
                                    }
                                }
                                else{
                                    $t = doquery( "select * from expense where id = '".$r[ "fare_transaction_id" ]."'", $dblink );
                                    if( numrows( $t ) > 0 ) {
                                        $t = dofetch( $t );
                                        $fov = $t[ "amount" ];
                                    }
                                }
                            }
                            if( !empty( $fov ) ) {
                                echo curr_format( $fov );
                                $fov_total += $fov;
                            }
                            else{
                                echo '--';
                            }
                        ?></td> 
                        <td class="text-right"><?php
                            if( !empty( $r[ "brokery_id" ] ) ) {
                                $t = doquery( "select * from transaction where id = '".$r[ "brokery_id" ]."'", $dblink );
                                if( numrows( $t ) > 0 ) {
                                    $t = dofetch( $t );
                                    $brokery = $t[ "amount" ];
                                    $balance -= $brokery;
                                }
                            }
                            if( !empty( $brokery ) ) {
                                echo curr_format( $brokery );
                                $brokery_total += $brokery;
                            }
                            else{
                                echo '--';
                            }
                        ?></td> 
                        <td class="text-right"><?php echo curr_format( $balance ); $balance_total += $balance; ?></td>                        
                        <td class="text-center">
                        	<?php
							if($r["status"]==0){
								?>
								<span class="order-status cancel">Cancelled</span>
								<?php
							}
							elseif($r["status"]==1){
								?>
								<span class="order-status dispatch">Arrived</span>
								<?php
							}
							elseif($r["status"]==2){
								?>
								<span class="order-status deliver">Received</span>
								<?php
							}
							?>
                            
                        </td>
                        <td class="text-center">
                            <a href="purchase_manage.php?tab=addedit&id=<?php echo $r['id'];?>"><img title="Edit Record" alt="Edit" src="images/edit.png"></a>&nbsp;&nbsp;
                            <a onclick="return confirm('Are you sure you want to delete')" href="purchase_manage.php?id=<?php echo $r['id'];?>&amp;tab=delete"><img title="Delete Record" alt="Delete" src="images/delete.png"></a>
                        </td>
                    </tr>
                    <?php 
                    $sn++;
                }
                ?>
                <tr>
                    <td colspan="10" class="actions">
                        <select name="bulk_action" id="bulk_action" title="Choose Action">
                            <option value="null">Bulk Action</option>
                            <option value="delete">Delete</option>
                            <option value="statuson">Set Status Arrived</option>
                            <option value="statusof">Set Status Cancelled</option>
                            <option value="statusrec">Set Status Received</option>
                        </select>
                        <input type="button" name="apply" value="Apply" id="apply_bulk_action" class="btn btn-light" title="Apply Action"  />
                    </td>
                    <td colspan="7" class="paging" title="Paging" align="right"><?php echo pages_list($rows, "purchase", $sql, $pageNum)?></td>
                </tr>
                <?php	
            }
            else{	
                ?>
                <tr>
                    <td colspan="17"  class="no-record">No Result Found</td>
                </tr>
                <?php
            }
            ?>
    	</tbody>
  	</table>
</div>

