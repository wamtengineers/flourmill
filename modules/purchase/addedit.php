<?php
if(!defined("APP_START")) die("No Direct Access");
if( isset( $_GET[ "id" ] ) ) {
	$id = slash( $_GET[ "id" ] );
}
else {
	$id = 0;
}
?>
<div ng-app="purchase" ng-controller="purchaseController" id="purchaseController">
    <div style="display:none">{{purchase_id=<?php echo $id?>}}</div>
    <div class="page-header">
        <h1 class="title">{{get_action()}} Purchase</h1>
        <ol class="breadcrumb">
            <li class="active">Manage Purchase</li>
        </ol>
        <div class="right">
            <div class="btn-group" role="group" aria-label="..."> <a href="purchase_manage.php" class="btn btn-light editproject">Back to List</a> </div>
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
            	<input ng-model="purchase.datetime_added" data-controllerid="purchaseController" class="form-control date-timepicker angular-datetimepicker" />
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-sm-2 control-label">
                <label class="form-label" for="supplier_id">Supplier Name <span class="manadatory">*</span></label>
            </div>
            <div class="col-sm-10">
                <select class="margin-btm-5" ng-model="purchase.supplier_id">
                    <option value="">Select Supplier</option>
                   	<option ng-repeat="supplier in suppliers" value="{{ supplier.id }}">{{ supplier.name }}</option>
                </select>
            </div>
        </div>
    </div>
    <div class="form-group assets-list">
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
                                <th class="text-right" width="10%">Unit Price</th>
                                <th class="text-right" width="10%">Qty</th>
                                <th class="text-right" width="10%">Total Price</th>
                                <th class="text-center" width="10%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="item in purchase.items">
                                <td class="text-center serial_number">{{ $index+1 }}</td>
                                <td>
                                    <select title="Choose Option" ng-model="purchase.items[ $index ].item_id" chosen>
                                        <option value="">Select Item</option>
                                        <option ng-repeat="item in items" value="{{ item.id }}">{{ item.category }} - {{ item.title }}</option>
                                    </select>
                                </td>
                                <td class="text-right"><input type="text" ng-model="purchase.items[ $index ].unit_price" ng-change="update_total( $index )" /></td>
                                <td class="text-right">
                                	<input type="text" ng-change="update_total( $index )" ng-model="purchase.items[ $index ].quantity" />
                                </td>
                                <td class="text-right"><input type="text" ng-model="purchase.items[ $index ].total" ng-change="calc_unit_price( $index )" /></td>                        
                                <td class="text-center"><a href="" ng-click="add( $index )">Add</a> - <a href="" ng-click="remove( $index )">Delete</a></td>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-right">Total Items</th>
                                <th class="text-right">{{ total_items()|currency:'':0 }}</th>
                                <th class="text-right">&nbsp;</th>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-right">Grand Total</th>
                                <th class="text-right">{{ grand_total()|currency:'':0 }}</th>
                                <th class="text-right">&nbsp;</th>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-right">Discount</th>
                                <th class="text-right"><input type="text" style="text-align:right" ng-model="purchase.discount" ng-change='update_net_total()' /></th>
                                <th class="text-right">&nbsp;</th>
                            </tr>
                            <tr>
                                <th colspan="4" class="text-right">Net Total</th>
                                <th class="text-right">{{ grand_total()-purchase.discount|currency:'':0 }}</th>
                                <th class="text-right">&nbsp;</th>
                            </tr>
                            <tr>
                                <th class="text-right" colspan="4"><label>Payment Account </label></th>
                                <th class="text-left" colspan="2">
                                    <select class="margin-btm-5" ng-model="purchase.payment_account_id" chosen>
                                        <option value="">Select Account</option>
                                        <option ng-repeat="account in accounts" value="{{account.id}}">{{account.title}}</option>
                                    </select>
                                </th>
                            </tr>
                            <tr>
                                <th class="text-right" colspan="4">Payment Amount <i class="fa fa-calculator" style="cursor:pointer" ng-click="update_payment_amount()"></i></th>
                                <th class="text-right"><input type="text" style="text-align:right" ng-model="purchase.payment_amount" /></th>
                                <th class="text-right">&nbsp;</th>
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
                <button type="submit" ng-disabled="processing" class="btn btn-default btn-l" ng-click="save_purchase()" title="Submit Record"><i class="fa fa-spin fa-gear" ng-show="processing"></i> SUBMIT</button>
            </div>
        </div>
    </div>
    </div>
</div>