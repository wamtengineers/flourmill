<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_SESSION["brokery_manage"]["add"])){
	extract($_SESSION["brokery_manage"]["add"]);	
}
else{
	$item_id="";
	$packing="";
	$amount="";
}
?>
<div class="page-header">
	<h1 class="title">Add New Brokery</h1>
  	<ol class="breadcrumb">
    	<li class="active">Manage Brokery</li>
  	</ol>
  	<div class="right">
    	<div class="btn-group" role="group" aria-label="..."> <a href="brokery_manage.php" class="btn btn-light editproject">Back to List</a> </div>
  	</div>
</div>
<form action="brokery_manage.php?tab=add" method="post" enctype="multipart/form-data" name="frmAdd" class="form-horizontal form-horizontal-left">
	<?php
    	$i=0;
  	?>
    <div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label class="form-label" for="item_id">Items <span class="manadatory">*</span></label>
            </div>
            <div class="col-sm-10">
                <select name="item_id" title="Choose Option">
                    <option value="0">Select Item</option>
                    <?php
                    $res=doquery("select * from items where status=1 order by id", $dblink);
                    if(numrows($res)>0){
                        while($rec=dofetch($res)){
                        ?>
                        <option value="<?php echo $rec["id"]?>"<?php echo($item_id==$rec["id"])?"selected":"";?>><?php echo unslash($rec["title"]); ?></option>
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
                <label class="form-label" for="packing">Packing </label>
            </div>
            <div class="col-sm-10">
                <input type="text" title="Enter Packing" value="<?php echo $packing; ?>" name="packing" id="packing" class="form-control">
            </div>
        </div>
  	</div>
  	<div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label class="form-label" for="amount">Amount</label>
            </div>
            <div class="col-sm-10">
                 <input type="text" title="Enter Amount" value="<?php echo $amount; ?>" name="amount" id="amount" class="form-control">
            </div>
        </div>
  	</div>
  	<div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label for="company" class="form-label"></label>
            </div>
            <div class="col-sm-10">
                <input type="submit" value="SUBMIT" class="btn btn-default btn-l" name="brokery_add" title="Submit Record" />
            </div>
        </div>
  	</div>
</form>