<?php
if(!defined("APP_START")) die("No Direct Access");
?>
<div class="page-header">
	<h1 class="title">Edit Unit</h1>
  	<ol class="breadcrumb">
    	<li class="active">Manage Unit</li>
  	</ol>
  	<div class="right">
    	<div class="btn-group" role="group" aria-label="..."> <a href="unit_manage.php" class="btn btn-light editproject">Back to List</a> </div>
  	</div>
</div>
<form action="unit_manage.php?tab=edit" method="post" enctype="multipart/form-data" name="frmAdd"  class="form-horizontal form-horizontal-left">
	<input type="hidden" name="id" value="<?php echo $id;?>">
    <div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label class="form-label" for="title">Title</label>
            </div>
            <div class="col-sm-10">
                <input type="text" title="Enter Title" value="<?php echo $title; ?>" name="title" id="title" class="form-control" >
            </div>
        </div>
  	</div>
  	<div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label for="company" class="form-label"></label>
            </div>
            <div class="col-sm-10">
                <input type="submit" value="UPDATE" class="btn btn-default btn-l" name="unit_edit" title="Update Record" />
            </div>
        </div>
  	</div>
</form>