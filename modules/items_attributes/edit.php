<?php
if(!defined("APP_START")) die("No Direct Access");
?>
<div class="page-header">
	<h1 class="title">Edit Items Attributes</h1>
  	<ol class="breadcrumb">
    	<li class="active">Manage Items Attributes</li>
  	</ol>
  	<div class="right">
    	<div class="btn-group" role="group" aria-label="..."> <a href="items_attributes_manage.php" class="btn btn-light editproject">Back to List</a> </div>
  	</div>
</div>
<form action="items_attributes_manage.php?tab=edit" method="post" enctype="multipart/form-data" name="frmAdd"  class="form-horizontal form-horizontal-left">
	<input type="hidden" name="id" value="<?php echo $id;?>">
    <div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label class="form-label" for="name">Name <span class="manadatory">*</span></label>
            </div>
            <div class="col-sm-10">
                <input type="text" title="Enter name" value="<?php echo $name; ?>" name="name" id="name" class="form-control">
            </div>
        </div>
  	</div>
  	<div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label class="form-label" for="values">Values</label>
            </div>
            <div class="col-sm-10">
                <textarea name="values" id="values" class="form-control"><?php echo $values; ?></textarea>
            </div>
        </div>
  	</div>
  	<div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label for="company" class="form-label"></label>
            </div>
            <div class="col-sm-10">
                <input type="submit" value="UPDATE" class="btn btn-default btn-l" name="items_attributes_edit" title="Update Record" />
            </div>
        </div>
  	</div>
</form>