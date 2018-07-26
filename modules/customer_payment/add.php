<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_SESSION["customer_payment_manage"]["add"])){
	extract($_SESSION["customer_payment_manage"]["add"]);	
}
else{
	$customer_id="";
	$datetime_added=date("d/m/Y H:i A");
	$amount="";
	$account_id="";
	$details="";
	$type="";
}
?>
<div class="page-header">
	<h1 class="title">Add New Customer Payment</h1>
  	<ol class="breadcrumb">
    	<li class="active">Manage Customer Payment</li>
  	</ol>
  	<div class="right">
    	<div class="btn-group" role="group" aria-label="..."> <a href="customer_payment_manage.php" class="btn btn-light editproject">Back to List</a> </div>
  	</div>
</div>
<form action="customer_payment_manage.php?tab=add" method="post" enctype="multipart/form-data" name="frmAdd" class="form-horizontal form-horizontal-left">
	<?php
    	$i=0;
  	?>
  	<div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label class="form-label" for="customer_id">Customer Name <span class="manadatory">*</span></label>
            </div>
            <div class="col-sm-10">
                <select name="customer_id" title="Choose Option">
                    <option value="0">Select Customer</option>
                    <?php
                    $res=doquery("select * from customer where status=1 order by id desc", $dblink);
                    if(numrows($res)>0){
                        while($rec=dofetch($res)){
                        ?>
                        <option value="<?php echo $rec["id"]?>"<?php echo($customer_id==$rec["id"])?"selected":"";?>><?php echo unslash($rec["customer_name"]); ?></option>
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
                <label class="form-label" for="datetime">Datetime</label>
            </div>
            <div class="col-sm-10">
                <input type="text" title="Enter datetime" value="<?php echo $datetime_added; ?>" name="datetime_added" id="datetime_added" class="form-control date-timepicker" >
            </div>
        </div>
  	</div>
    <div class="form-group">
        <div class="row">
            <div class="col-sm-2 control-label">
                <label for="type" class="form-label">Type </label>
            </div>
            <div class="col-sm-10">
                <select name="type" id="type" title="Choose Option">
                    <option value="">Select Type</option>
                    <?php
                    foreach ($customer_type as $key=>$value) {
                        ?>
                        <option value="<?php echo $key?>"<?php echo $key==$type?' selected="selected"':""?>><?php echo $value ?></option>
                        <?php
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>
  	<div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label class="form-label" for="amount">Amount <span class="manadatory">*</span></label>
            </div>
            <div class="col-sm-10">
                <input type="text" title="Enter amount" value="<?php echo $amount; ?>" name="amount" id="amount" class="form-control" >
            </div>
        </div>
  	</div>
    <div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label class="form-label" for="account_id">Paid By </label>
            </div>
            <div class="col-sm-10">
                <select name="account_id" title="Choose Option">
                    <option value="">Select Account</option>
                    <?php
                    $res=doquery("select * from account where status=1 order by title", $dblink);
                    if(numrows($res)>0){
                        while($rec=dofetch($res)){
                        ?>
                        <option value="<?php echo $rec["id"]?>"<?php echo($account_id==$rec["id"])?"selected":"";?>><?php echo unslash($rec["title"]); ?></option>
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
            	<label class="form-label" for="details">Details </label>
            </div>
            <div class="col-sm-10">
                 <textarea title="Enter Details" value="" name="details" id="details" class="form-control"><?php echo $details; ?></textarea>
            </div>
        </div>
    </div>
  	<div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label for="company" class="form-label"></label>
            </div>
            <div class="col-sm-10">
                <input type="submit" value="SUBMIT" class="btn btn-default btn-l" name="customer_payment_add" title="Submit Record" />
            </div>
        </div>
  	</div>
</form>