<?php
if(!defined("APP_START")) die("No Direct Access");
if( isset( $_GET[ "id" ] ) ) {
	$id = slash( $_GET[ "id" ] );
}
else {
	$id = 0;
}
?>
<style>
.popup-content .page-header{
	display:none !important;
}
.popup-content .form-group .col-sm-2{
	display: block;
	float: none;
	text-align: left;
	width: 100%;
}
.popup-content .form-group .col-sm-10{
	width: 100%;
	float: none;
}
.popup-content .content{
	padding-left: 15px;
	padding-right: 15px;
}
.popup-content .col-sm-offset-2{
	margin-left:0;
}
</style>
<div ng-app="salesreturn" ng-controller="salesreturnController" id="salesreturnController">
    <div style="display:none">{{sales_return_id=<?php echo $id?>}}</div>
    <div class="page-header">
        <h1 class="title">{{get_action()}} Sales Return</h1>
        <ol class="breadcrumb">
            <li class="active">Manage Sales Return</li>
        </ol>
        <div class="right">
            <div class="btn-group" role="group" aria-label="..."> <a href="sales_return_manage.php" class="btn btn-light editproject">Back to List</a> </div>
        </div>
    </div>
	<?php
        $i=0;
    ?>
    <div class="form-horizontal">
    <div class="form-group">
        <div class="row">
            <div class="col-sm-2 control-label">
                <label class="form-label" for="date">Date <span class="manadatory">*</span></label>
            </div>
            <div class="col-sm-10">
            	<input ng-model="sales_return.datetime_added" data-controllerid="salesreturnController" class="form-control date-timepicker angular-datetimepicker" />
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-sm-2 control-label">
                <label class="form-label" for="customer_id">Customer Name </label>
            </div>
            <div class="col-sm-10">
                <select class="margin-btm-5" ng-model="sales_return.customer_id" chosen>
                    <option value="0">Select Customer</option>
                   	<option ng-repeat="customer in customers" value="{{ customer.id }}">{{ customer.name }}</option>
                </select>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-sm-2 control-label">
                <label class="form-label" for="less_weight">Less Weight </label>
            </div>
            <div class="col-sm-10">
            	<input ng-model="sales_return.less_weight" class="form-control" />
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
                                <th width="2%" class="text-center">S.no</th>
                                <th width="25%">Items</th>
                                <th width="10%">Packing</th>
                                <th class="text-right" width="10%">Unit Price</th>
                                <th class="text-right" width="10%">Total Items</th>
                                <th class="text-right" width="10%">Total Price</th>
                                <th class="text-center" width="10%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="item in sales_return.items">
                                <td class="text-center serial_number">{{ $index+1 }}</td>
                                <td>
                                    <select title="Choose Option" ng-model="sales_return.items[$index].item_id" chosen>
                                        <option value="">Select Items</option>
                                        <option ng-repeat="item in items" value="{{ item.id }}">{{ item.category }} - {{ item.title }}</option>
                                    </select>
                                </td>
                                <td class="text-right"><input type="text" ng-change="update_total( $index )" ng-model="sales_return.items[$index].packing" /></td>
                                <td class="text-right"><input type="text" ng-change="update_total( $index )" ng-model="sales_return.items[$index].unit_price" /></td>
                                <td class="text-right"><input type="text" ng-change="update_total( $index )" ng-model="sales_return.items[$index].quantity" /></td>
                                <td class="text-right">{{ sales_return.items[$index].total|currency:'Rs. ':0 }}</td>                        
                                <td class="text-center"><a href="" ng-click="add( $index )">Add</a> - <a href="" ng-click="remove( $index )">Delete</a></td>
                            </tr>
                            <tr>
                                <th colspan="5" class="text-right">Total Items</th>
                                <th class="text-right">{{ total_items()|currency:'':0 }}</th>
                                <th class="text-right">&nbsp;</th>
                            </tr>
                            <tr>
                                <th colspan="5" class="text-right">Total</th>
                                <th class="text-right">{{ grand_total()|currency:'':0 }}</th>
                                <th class="text-right">&nbsp;</th>
                            </tr>
                            <tr>
                                <th colspan="5" class="text-right">Discount</th>
                                <th class="text-right"><input type="text" id="discount" style="text-align:right" ng-model="sales_return.discount" ng-change='update_net_total()' /></th>
                                <th class="text-right">&nbsp;</th>
                            </tr>
                            <tr>
                                <th colspan="5" class="text-right">Net Total</th>
                                <th class="text-right">{{ grand_total()-sales_return.discount|currency:'':0 }}</th>
                                <th class="text-right">&nbsp;</th>
                            </tr>
                            <tr>
                                <th class="text-right" colspan="5"><label>Payment Account </label></th>
                                <th class="text-right" colspan="2">
                                    <select class="margin-btm-5" ng-model="sales_return.payment_account_id">
                                        <option value="">Select Account</option>
                                        <option ng-repeat="account in accounts" value="{{account.id}}">{{account.title}}</option>
                                    </select>
                                </th>
                            </tr>
                            <tr>
                                <th class="text-right" colspan="5">Payment Amount</th>
                                <th class="text-right" colspan="2"><input type="text" style="text-align:right" ng-model="sales_return.payment_amount" /></th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-sm-offset-2 col-sm-10">
            	<div class="alert alert-danger" ng-show="errors.length > 0">
                	<p ng-repeat="error in errors">{{error}}</p>
                </div>
                <button type="submit" ng-disabled="processing" class="btn btn-default btn-l" ng-click="save_sale_return()" title="Submit Record"><i class="fa fa-spin fa-gear" ng-show="processing"></i> SUBMIT</button>
            </div>
        </div>
    </div>
    </div>
</div>