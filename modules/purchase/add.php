<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_SESSION["purchase_manage"]["add"])){
	extract($_SESSION["purchase_manage"]["add"]);	
}
else{
	$supplier_id="";
	$supplier_name="";
	$item_category_id="";
	$phone="";
	$address="";
	$date=date("d/m/Y H:i A");
	$items=array(array(
		"item_name" => "",
		"item_category_id" => "",
		"purchase_price" => "",
		"sales_price" => "",
		"quantity" => "",
		"total_price" => ""
	));
	$discount = 0;
}
?>
<div class="page-header">
	<h1 class="title">Add New Purchase</h1>
  	<ol class="breadcrumb">
    	<li class="active">Manage Purchase</li>
  	</ol>
  	<div class="right">
    	<div class="btn-group" role="group" aria-label="..."> <a href="purchase_manage.php" class="btn btn-light editproject">Back to List</a> </div>
  	</div>
</div>
<form action="purchase_manage.php?tab=add" method="post" enctype="multipart/form-data" name="frmAdd"  onSubmit="return checkFields();" class="form-horizontal form-horizontal-left">
	<?php
    	$i=0;
  	?>
  	<div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label class="form-label" for="date">Date <span class="manadatory">*</span></label>
            </div>
            <div class="col-sm-10">
                <input type="text" title="Enter Name" value="<?php echo $date; ?>" name="date" id="date" class="form-control date-timepicker" >
            </div>
        </div>
  	</div>
    <div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label class="form-label" for="supplier_id">Supplier Name <span class="manadatory">*</span></label>
            </div>
            <div class="col-sm-10">
                <select name="supplier_id" id="supplier_id" class="margin-btm-5">
                	<option value="">Select Supplier</option>
                    <?php
                    $rs = doquery( "select * from supplier where status=1 order by id", $dblink );
					if( numrows( $rs ) > 0 ) {
						while( $r = dofetch( $rs ) ) {
							?>
							<option value="<?php echo $r[ "id" ]?>" data-supplier_name="<?php echo htmlspecialchars(unslash($r[ "supplier_name" ]))?>" data-phone="<?php echo htmlspecialchars(unslash($r[ "phone" ]))?>" data-address="<?php echo htmlspecialchars(unslash($r[ "address" ]))?>"><?php echo $r[ "id" ]?> - <?php echo unslash($r[ "supplier_name" ])?></option>
							<?php
						}
					}
					?>
                </select>
                <input type="text" title="Enter Name" value="<?php echo $supplier_name; ?>" name="supplier_name" id="supplier_name" class="form-control" >
            </div>
        </div>
  	</div>
    <div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label class="form-label" for="phone">Phone <span class="manadatory">*</span></label>
            </div>
            <div class="col-sm-10">
                <input type="text" title="Enter Contact Number" value="<?php echo $phone; ?>" name="phone" id="phone" class="form-control" >
            </div>
        </div>
  	</div>
    <div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label class="form-label" for="address">Address <span class="manadatory">*</span></label>
            </div>
            <div class="col-sm-10">
                <input type="text" title="Enter Address" value="<?php echo $address; ?>" name="address" id="address" class="form-control" >
            </div>
        </div>
  	</div>
    
    <div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label class="form-label">Items <span class="manadatory">*</span></label>
            </div>
            <div class="col-sm-10">
                <div class="panel-body table-responsive">
                    <table class="table table-hover list">
                        <thead>
                            <tr>
                                <th width="5%" class="text-center">S.no</th>
                                <th width="15%">Item Category</th>
                                <th width="15%">Item Name</th>
                                <th class="text-right" width="15%">Purchase Price</th>
                                <th class="text-right" width="10%">Sale Price</th>
                                <th class="text-right" width="10%">Total Items</th>
                                <th class="text-right" width="10%">Total Price</th>
                                <th class="text-center" width="10%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $sn=0;
                            if(count($items)>0){
                                foreach($items as $item){
                                    ?>
                                    <tr class="purchase_item">
                                        <td class="text-center serial_number"><?php echo $sn+1;?></td>
                                        <td>
                                        	<select class="item_select" name="items[<?php echo $sn?>][item_category_id]" id="item_category_id<?php echo $sn?>" title="Choose Option">
                                                <option value="0">Select Item Category</option>
                                                 <?php
                                                  $res=doquery("Select * from item_category where parent_id='0' order by title",$dblink);
                                                  if(numrows($res)>0){
                                                       while($rec=dofetch($res)){
                                                           $res2=doquery("Select * from item_category where parent_id='".$rec[ "id" ]."' order by title",$dblink);
                                                          if(numrows($res2)>0){
                                                               while($rec2=dofetch($res2)){ ?>
                                                                <optgroup label="<?php echo unslash($rec["title"])." &gt; ".unslash($rec2["title"]); ?>">
                                                                    <?php
                                                                    $subCat=doquery("Select * from item_category where parent_id='".$rec2["id"]."'",$dblink);
                                                                    if(numrows($subCat)>0){
                                                                        while($subCatName=dofetch($subCat)){
                                                                             ?>
                                                                             <option value="<?php echo $subCatName["id"]?>"<?php echo($item_category_id==$subCatName["id"])?"selected":"";?>><?php echo unslash($subCatName["title"]); ?></option>
                                                                            <?php 
                                                                        }
                                                                    }
                                                                    ?> 
                                                                </optgroup>
                                                                <?php			
                                                               }
                                                          }
                                                       }			
                                                  }
                                                  ?>
                                        	</select>
                                        </td>
                                        <td>
                                        	<input type="text" name="items[<?php echo $sn?>][item_name]" id="item_name<?php echo $sn?>" value="" />
                                        </td>
                                        <td class="text-right"><input type="text" class="purchase_price" name="items[<?php echo $sn?>][purchase_price]" id="purchase_price<?php echo $sn?>" value="<?php echo $item["purchase_price"]?>" /></td>
                                        <td class="text-right"><input type="text" class="sale_price" name="items[<?php echo $sn?>][sale_price]" id="sale_price<?php echo $sn?>" value="" /></td>
                                        <td class="text-right"><input type="number" class="quantity" name="items[<?php echo $sn?>][quantity]" id="quantity<?php echo $sn?>" value="<?php echo $quantity[$sn-1]?>" /></td>
                                        <td class="text-right"><input type="text" class="total_price" name="items[<?php echo $sn?>][total_price]"  id="total_price<?php echo $sn?>" value="" /></td>                        
                                        <td class="text-center"><a href="#" data-id="<?php echo $sn?>" class="add_list_item" data-container_class="purchase_item">Add</a> - <a href="#" data-id="<?php echo $sn?>" class="delete_list_item" data-container_class="purchase_item">Delete</a></td>
                                    </tr>
                                    <?php 
                                    $sn++;
                                }
                            }
                            ?>
                            <tr>
                                <th colspan="6" class="text-right">Total Items</th>
                                <th class="text-right grand_total_item"></th>
                                <th class="text-right">&nbsp;</th>
                            </tr>
                            <tr>
                                <th colspan="6" class="text-right">Discount</th>
                                <th class="text-right"><input type="number" class="discount" name="discount" id="discount" value="<?php echo $discount?>" style="text-align:right" data-container_class="purchase_item" /></th>
                                <th class="text-right">&nbsp;</th>
                            </tr>
                            <tr>
                                <th colspan="6" class="text-right">Total Price</th>
                                <th class="text-right grand_total_price"></th>
                                <th class="text-right">&nbsp;</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
  	<div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label for="company" class="form-label"></label>
            </div>
            <div class="col-sm-10">
                <input type="submit" value="SUBMIT" class="btn btn-default btn-l" name="purchase_add" title="Submit Record" />
            </div>
        </div>
  	</div>
</form>