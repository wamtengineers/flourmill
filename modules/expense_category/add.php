<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_SESSION["expense_category_manage"]["add"])){
	extract($_SESSION["expense_category_manage"]["add"]);	
}
else{
	$title="";
}
?>
<div class="page-header">
	<h1 class="title">Add New Expense Category</h1>
  	<ol class="breadcrumb">
    	<li class="active">Manage Expense Category</li>
  	</ol>
  	<div class="right">
    	<div class="btn-group" role="group" aria-label="..."> <a href="expense_category_manage.php" class="btn btn-light editproject">Back to List</a> </div>
  	</div>
</div>
<form class="form-horizontal form-horizontal-left" role="form" action="expense_category_manage.php?tab=add" method="post" enctype="multipart/form-data" name="frmAdd"  onSubmit="return checkFields();">
    <?php
        $i=0;
    ?>
    <div class="form-group">
        <div class="row">
        	<div class="col-sm-2 control-label">
            	<label class="form-label" for="title">Title </label>
            </div>
            <div class="col-sm-10">
                <input type="text" title="Enter Title" value="<?php echo $title; ?>" name="title" id="title" class="form-control" />
            </div>
        </div>
    </div>
    <div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label for="company" class="form-label"></label>
            </div>
            <div class="col-sm-10">
                <input type="submit" value="SUBMIT" class="btn btn-default btn-l" name="expense_category_add" title="Submit Record" />
            </div>
        </div>
  	</div>  
</form>