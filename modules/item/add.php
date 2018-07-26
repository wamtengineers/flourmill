<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_SESSION["items_manage"]["add"])){
	extract($_SESSION["items_manage"]["add"]);	
}
else{
	$item_category_id="";
	$title="";
	$sortorder="";
	$packing_ids=array();
}
?>
<div class="page-header">
	<h1 class="title">Add New Item</h1>
  	<ol class="breadcrumb">
    	<li class="active">Manage Item</li>
  	</ol>
  	<div class="right">
    	<div class="btn-group" role="group" aria-label="..."> <a href="items_manage.php" class="btn btn-light editproject">Back to List</a> </div>
  	</div>
</div>
<form action="items_manage.php?tab=add" method="post" enctype="multipart/form-data" name="frmAdd"  onSubmit="return checkFields();" class="form-horizontal form-horizontal-left">
	<?php
    	$i=0;
  	?>
    <div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label class="form-label" for="item_category_id">Category</label>
            </div>
            <div class="col-sm-10">
            	<select name="item_category_id" id="item_category_id" title="Choose Option">
                <option value="0">Select Item Category</option>
				 	<?php
                  	$res=doquery("Select * from item_category order by sortorder",$dblink);
                  	if(numrows($res)>0){
						while($rec=dofetch($res)){
						   	?>
						 	<option value="<?php echo $rec["id"]?>"<?php echo($item_category_id==$rec["id"])?"selected":"";?>><?php echo unslash($rec["title"]); ?></option>
							<?php			
					   	}			
                  	}
                  	?>
                </select>
            </div>
        </div>
  	</div>
    
  	<div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label class="form-label" for="title">Title <span class="manadatory">*</span></label>
            </div>
            <div class="col-sm-10">
                <input type="text" title="Enter Name" value="<?php echo $title; ?>" name="title" id="title" class="form-control" >
            </div>
        </div>
  	</div>
    
    <div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label class="form-label" for="sortorder">Sortorder</label>
            </div>
            <div class="col-sm-10">
                <input type="text" title="Enter Sortorder" value="<?php echo $sortorder; ?>" name="sortorder" id="sortorder" class="form-control" >
            </div>
        </div>
  	</div>
    <div class="form-group">
        <div class="row">
        	<div class="col-sm-2 control-label">
            	<label class="form-label" for="packing_id">Packing</label>
            </div>
            <div class="col-sm-10">
                <select name="packing_ids[]" id="packing_id" multiple="multiple" class="select_multiple" title="Choose Option">
                    <option value="0">Select Packing</option>
                    <?php
                    $res=doquery("select * from packing order by id",$dblink);
                    if(numrows($res)>0){
                        while($rec=dofetch($res)){
                        ?>
                        <option value="<?php echo $rec["id"]?>"<?php echo($packing_ids==$rec["id"])?"selected":"selected";?>><?php echo unslash($rec["title"])?></option>
                        <?php			
                        }			
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>
  	<div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label for="company" class="form-label"></label>
            </div>
            <div class="col-sm-10">
                <input type="submit" value="SUBMIT" class="btn btn-default btn-l" name="items_add" title="Submit Record" />
            </div>
        </div>
  	</div>
</form>