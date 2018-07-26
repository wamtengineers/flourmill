<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_SESSION["items_variations_manage"]["add"])){
	extract($_SESSION["items_variations_manage"]["add"]);	
}
else{
	$quantity="";
	$price="";
	$cost_price="";
}
?>
<div class="page-header">
	<h1 class="title">Add New Items Variations</h1>
  	<ol class="breadcrumb">
    	<li class="active">Manage Item Variations</li>
  	</ol>
  	<div class="right">
    	<div class="btn-group" role="group" aria-label="..."> <a href="items_variations_manage.php" class="btn btn-light editproject">Back to List</a> </div>
  	</div>
</div>
<form action="items_variations_manage.php?tab=add" method="post" enctype="multipart/form-data" name="frmAdd"  onSubmit="return checkFields();" class="form-horizontal form-horizontal-left">
	<?php
    	$i=0;
  	?>
    <?php
    $attributes = doquery( "select * from items_attributes where item_id = '".$parent_item[ "id" ]."'", $dblink );
	if( numrows( $attributes ) > 0 ) {
		while( $attribute = dofetch( $attributes ) ) {
			?>
			<div class="form-group">
                <div class="row">
                    <div class="col-sm-2 control-label">
                        <label class="form-label" for="attribute_<?php echo $attribute[ "id" ]?>"><?php echo unslash( $attribute[ "name" ] )?> <span class="manadatory">*</span></label>
                    </div>
                    <div class="col-sm-10">
                        <select name="items_attribute[<?php echo $attribute[ "id" ]?>]" id="attribute_<?php echo $attribute[ "id" ]?>" class="form-control">
                        	<option value="0"<?php echo ( isset( $items_attribute[ $attribute[ "id" ] ] ) && $items_attribute[ $attribute[ "id" ] ] == 0 )? ' selected="selected"' : ""?>>Any</option>
                            <?php
                            $options = explode( "\n", unslash( $attribute[ "values" ] ) );
							foreach( $options as $option ) {
								$option = trim( $option );
								if( !empty( $option ) ) {
									?>
									<option value="<?php echo $option?>"<?php echo ( isset( $items_attribute[ $attribute[ "id" ] ] ) && $items_attribute[ $attribute[ "id" ] ] == $option )? ' selected="selected"' : ""?>><?php echo $option?></option>
									<?php
								}
							}
							?>
                        </select>
                    </div>
                </div>
            </div>
			<?php
		}
	}
	?>
  	<div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label class="form-label" for="quantity">Qauntity <span class="manadatory">*</span></label>
            </div>
            <div class="col-sm-10">
                <input type="text" title="Enter name" value="<?php echo $quantity; ?>" name="quantity" id="quantity" class="form-control">
            </div>
        </div>
  	</div>
  	<div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label class="form-label" for="price">Price</label>
            </div>
            <div class="col-sm-10">
                <input type="text" title="Enter price" value="<?php echo $price; ?>" name="price" id="price" class="form-control">
            </div>
        </div>
  	</div>
    <div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label class="form-label" for="cost_price">Cost Price</label>
            </div>
            <div class="col-sm-10">
                <input type="text" title="Enter price" value="<?php echo $cost_price; ?>" name="cost_price" id="cost_price" class="form-control">
            </div>
        </div>
  	</div>
  	<div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label for="company" class="form-label"></label>
            </div>
            <div class="col-sm-10">
                <input type="submit" value="SUBMIT" class="btn btn-default btn-l" name="items_variations_add" title="Submit Record" />
            </div>
        </div>
  	</div>
</form>