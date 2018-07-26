<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_SESSION["packing_manage"]["add"])){
	extract($_SESSION["packing_manage"]["add"]);	
}
else{
	$title="";
	$total_units="";
}
?>
<div class="page-header">
	<h1 class="title">Add New Packing</h1>
  	<ol class="breadcrumb">
    	<li class="active">Manage Packing</li>
  	</ol>
  	<div class="right">
    	<div class="btn-group" role="group" aria-label="..."> <a href="packing_manage.php" class="btn btn-light editproject">Back to List</a> </div>
  	</div>
</div>
<form action="packing_manage.php?tab=add" method="post" enctype="multipart/form-data" name="frmAdd"  onSubmit="return checkFields();" class="form-horizontal form-horizontal-left">
	<?php
    	$i=0;
  	?>
  	<div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label class="form-label" for="title">Title <span class="manadatory">*</span></label>
            </div>
            <div class="col-sm-10">
                <input type="text" title="Enter Title" value="<?php echo $title; ?>" name="title" id="title" class="form-control" >
            </div>
        </div>
  	</div>
    <div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label class="form-label" for="total_units">Total Units</label>
            </div>
            <div class="col-sm-10">
                <input type="text" title="Enter Unit" value="<?php echo $total_units; ?>" name="total_units" id="total_units" class="form-control" >
            </div>
        </div>
  	</div>
  	<div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label for="company" class="form-label"></label>
            </div>
            <div class="col-sm-10">
                <input type="submit" value="SUBMIT" class="btn btn-default btn-l" name="packing_add" title="Submit Record" />
            </div>
        </div>
  	</div>
</form>