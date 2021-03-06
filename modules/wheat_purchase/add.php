<?php
if(!defined("APP_START")) die("No Direct Access");
if(isset($_SESSION["wheat_purchase_manage"]["add"])){
	extract($_SESSION["wheat_purchase_manage"]["add"]);	
}
else{
	$datetime_added=date("d/m/Y H:i A");
	$supplier_id="";
	$broker_name="";
	$vehicle_number="";
	$gross_weight="";
	$wheat_price="";
	$deduction_weight="";
	$net_weight="";
	$brokery="";
	$carrage_expenses="";
	$market_committe="";
	$kata_paisa="";
}
?>
<div class="page-header">
	<h1 class="title">Add New Wheat Purchase</h1>
  	<ol class="breadcrumb">
    	<li class="active">Manage Wheat Purchase</li>
  	</ol>
  	<div class="right">
    	<div class="btn-group" role="group" aria-label="..."> <a href="wheat_purchase_manage.php" class="btn btn-light editproject">Back to List</a> </div>
  	</div>
</div>
<form class="form-horizontal form-horizontal-left" role="form" action="wheat_purchase_manage.php?tab=add" method="post" enctype="multipart/form-data" name="frmAdd">
    <?php
        $i=0;
    ?>
    <div class="form-group">
        <div class="row">
        	 <div class="col-sm-2 control-label">
            	<label class="form-label" for="datetime_added">Datetime <span class="manadatory">*</span></label>
            </div>
            <div class="col-sm-10">
                <input type="text" title="Enter Date" value="<?php echo $datetime_added; ?>" name="datetime_added" id="datetime_added" class="form-control date-timepicker" />
            </div>
        </div>
    </div>
    <div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label class="form-label" for="supplier_id">Party Name </label>
            </div>
            <div class="col-sm-10">
                <select name="supplier_id" title="Choose Option">
                    <option value="0">Select Party Name</option>
                    <?php
                    $res=doquery("select * from supplier where status=1 order by supplier_name", $dblink);
                    if(numrows($res)>0){
                        while($rec=dofetch($res)){
                        ?>
                        <option value="<?php echo $rec["id"]?>"<?php echo($supplier_id==$rec["id"])?"selected":"";?>><?php echo unslash($rec["supplier_name"]); ?></option>
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
            	<label class="form-label" for="broker_name">Broker Name</label>
            </div>
            <div class="col-sm-10">
                <input type="text" title="Enter Broker Name" value="<?php echo $broker_name; ?>" name="broker_name" id="broker_name" class="form-control" />
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
        	<div class="col-sm-2 control-label">
            	<label class="form-label" for="vehicle_number">Vehicle Number</label>
            </div>
            <div class="col-sm-10">
                <input type="text" title="Enter Vehicle Number" value="<?php echo $vehicle_number; ?>" name="vehicle_number" id="vehicle_number" class="form-control" />
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
        	<div class="col-sm-2 control-label">
            	<label class="form-label" for="gross_weight">Gross Weight</label>
            </div>
            <div class="col-sm-10">
                <input type="text" title="Enter Gross Weight" value="<?php echo $gross_weight; ?>" name="gross_weight" id="gross_weight" class="form-control" />
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
        	<div class="col-sm-2 control-label">
            	<label class="form-label" for="wheat_price">Wheat Price</label>
            </div>
            <div class="col-sm-10">
                <input type="text" title="Enter Wheat Price" value="<?php echo $wheat_price; ?>" name="wheat_price" id="wheat_price" class="form-control" />
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
        	<div class="col-sm-2 control-label">
            	<label class="form-label" for="deduction_weight">Deduction Weight</label>
            </div>
            <div class="col-sm-10">
                <input type="text" title="Enter Deduction Weight" value="<?php echo $deduction_weight; ?>" name="deduction_weight" id="deduction_weight" class="form-control" />
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
        	<div class="col-sm-2 control-label">
            	<label class="form-label" for="net_weight">Net Weight</label>
            </div>
            <div class="col-sm-10">
                <input type="text" title="Enter Net Weight" value="<?php echo $net_weight; ?>" name="net_weight" id="net_weight" class="form-control" />
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
        	<div class="col-sm-2 control-label">
            	<label class="form-label" for="brokery">Brokery</label>
            </div>
            <div class="col-sm-10">
                <input type="text" title="Enter Brokery" value="<?php echo $brokery; ?>" name="brokery" id="brokery" class="form-control" />
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
        	<div class="col-sm-2 control-label">
            	<label class="form-label" for="carrage_expenses">Carrage Expenses</label>
            </div>
            <div class="col-sm-10">
                <input type="text" title="Enter Carrage Expenses" value="<?php echo $carrage_expenses; ?>" name="carrage_expenses" id="carrage_expenses" class="form-control" />
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
        	<div class="col-sm-2 control-label">
            	<label class="form-label" for="market_committe">Market Committe</label>
            </div>
            <div class="col-sm-10">
                <input type="text" title="Enter Market Committe" value="<?php echo $market_committe; ?>" name="market_committe" id="market_committe" class="form-control" />
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
        	<div class="col-sm-2 control-label">
            	<label class="form-label" for="kata_paisa">Kata Paisa</label>
            </div>
            <div class="col-sm-10">
                <input type="text" title="Enter Kata Paisa" value="<?php echo $kata_paisa; ?>" name="kata_paisa" id="kata_paisa" class="form-control" />
            </div>
        </div>
    </div>
    <div class="form-group">
    	<div class="row">
            <div class="col-sm-2 control-label">
                <label for="company" class="form-label"></label>
            </div>
            <div class="col-sm-10">
                <input type="submit" value="SUBMIT" class="btn btn-default btn-l" name="wheat_purchase_add" title="Submit Record" />
            </div>
        </div>
  	</div>  
</form>