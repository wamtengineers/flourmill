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
<div ng-app="addedit" ng-controller="addeditController" id="addeditController">
    <div style="display:none">{{addedit_id=<?php echo $id?>}}</div>
    <div class="page-header">
        <h1 class="title">{{get_action()}} {{get_title()}}</h1>
        <ol class="breadcrumb">
            <li class="active">Manage {{get_title()}}</li>
        </ol>
        <div class="right">
            <div class="btn-group" role="group" aria-label="..."> <a href="javascript:window.location=$manage_url" class="btn btn-light editproject">Back to List</a> </div>
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
            	<input ng-model="addedit.datetime_added" data-controllerid="addeditController" class="form-control date-timepicker angular-datetimepicker" />
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-sm-2 control-label">
                <label class="form-label" for="account_id">Account </label>
            </div>
            <div class="col-sm-10">
                <select ng-model="addedit.account_id" class="order-select-box" chosen>
                    <option value="0">Select Account</option>
                    <optgroup ng-repeat="account_type in account_types" label="{{ account_type.title }}">
                        <option ng-repeat="account in accounts|filter:{account_type_id: account_type.id}:1" value="{{account.id}}">{{account.title}}</option>
                    </optgroup>
                </select>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-sm-2 control-label">
                <label class="form-label" for="date">Bill No.</label>
            </div>
            <div class="col-sm-10">
            	<input ng-model="addedit.bill_no" class="form-control" />
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-sm-2 control-label">
                <label class="form-label" for="status">Status</label>
            </div>
            <div class="col-sm-10">
                <select class="margin-btm-5" ng-model="addedit.status" chosen>
                    <option value="2"><?php echo $manage_url == 'purchase_manage.php' || $manage_url == 'sales_return_manage.php'?'Received':'Delivering'?></option>
                   	<option value="1"><?php echo $manage_url == 'purchase_manage.php' || $manage_url == 'sales_return_manage.php'?'Arrived':'Dispatched'?></option>
                    <option value="3">Delivered</option>
                    <option value="4">On Hold</option>
                    <option value="0">Cancelled</option>
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
                                <th width="5%" class="text-center">S.no</th>
                                <th width="20%">Items</th>
                                <th width="10%">Rate</th>
                                <th class="text-right" width="8%">Packing</th>
                                <th class="text-right" width="8%">Weight</th>
                                <th class="text-right" width="10%">Less Weight</th>
                                <th class="text-right" width="10%">Net Weight</th>
                                <th class="text-right" width="8%">Unit Price</th>
                                <th class="text-right" width="10%">Total Price</th>
                                <th class="text-center" width="12%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="item in addedit.items">
                                <td class="text-center serial_number">{{ $index+1 }}</td>
                                <td>
                                    <select title="Choose Option" ng-model="addedit.items[$index].item_id" chosen>
                                        <option value="">Select Items</option>
                                        <option ng-repeat="item in items" value="{{ item.id }}">{{ item.title }}</option>
                                    </select>
                                </td>
                                <td>
                                    <select title="Choose Rate" ng-model="addedit.items[$index].rate" ng-change="update_total( $index )" chosen>
                                        <option value="0">Per Bag</option>
                                        <option value="1">Per KG</option>
                                    </select>
                                </td>
                                <td class="text-right"><input type="text" ng-change="update_total( $index )" ng-model="addedit.items[$index].packing" /></td>
                                <td class="text-right"><input type="text" ng-change="update_total( $index )" ng-model="addedit.items[$index].quantity" /></td>
                                <td class="text-right"><input ng-show="addedit.items[$index].rate==1" type="text" ng-change="update_total( $index )" ng-model="addedit.items[$index].less_weight" /></td>
                                <td class="text-right"><span ng-show="addedit.items[$index].rate==1">{{ addedit.items[$index].quantity-addedit.items[$index].less_weight|currency:'':2 }}</span></td>
                                <td class="text-right"><input type="text" ng-change="update_total( $index )" ng-model="addedit.items[$index].unit_price" /></td>
                                <td class="text-right">{{ addedit.items[$index].total|currency:'Rs. ':0 }}</td>                        
                                <td class="text-center"><a href="" ng-click="add( $index )">Add</a> - <a href="" ng-click="remove( $index )">Delete</a></td>
                            </tr>
                            <tr>
                                <th colspan="8" class="text-right">Total KG</th>
                                <th class="text-right">{{ total_items()|currency:'':0 }}</th>
                                <th class="text-right">&nbsp;</th>
                            </tr>
                            <tr>
                                <th colspan="8" class="text-right">Less Weight</th>
                                <th class="text-right"><input type="text" style="text-align:right" ng-model="addedit.less_weight" /></th>
                                <th class="text-right">&nbsp;</th>
                            </tr>
                            <tr>
                                <th colspan="8" class="text-right">Net Total KG</th>
                                <th class="text-right">{{ total_items()-addedit.less_weight|currency:'':0 }}</th>
                                <th class="text-right">&nbsp;</th>
                            </tr>
                            <tr>
                                <th colspan="8" class="text-right">Total</th>
                                <th class="text-right">{{ grand_total()|currency:'':0 }}</th>
                                <th class="text-right">&nbsp;</th>
                            </tr>
                            
                            <tr>
                                <th colspan="8" class="text-right">Discount</th>
                                <th class="text-right"><input type="text" id="discount" style="text-align:right" ng-model="addedit.discount" ng-change='update_net_total()' /></th>
                                <th class="text-right">&nbsp;</th>
                            </tr>
                            <tr>
                                <th colspan="8" class="text-right">Net Total</th>
                                <th class="text-right">{{ grand_total()-addedit.discount|currency:'':0 }}</th>
                                <th class="text-right">&nbsp;</th>
                            </tr>
                            <tr>
                            	<td colspan="11" style="padding:0">
                                	<table class="table table-hover list" style="margin-bottom:0">
                                    	<td width="33.33%">
                                        	<table class="table table-hover list">
                                                <tr>
                                                    <th>Broker Account</th>
                                                    <td><select ng-model="addedit.broker_id" class="order-select-box" chosen>
                                                        <option value="0">Select Account</option>
                                                        <optgroup ng-repeat="account_type in account_types" label="{{ account_type.title }}">
                                                            <option ng-repeat="account in accounts|filter:{account_type_id: account_type.id}:1" value="{{account.id}}">{{account.title}}</option>
                                                        </optgroup>
                                        			</select></td>
                                                </tr>
                                                <tr>
                                                    <th>Broker Amount</th>
                                                    <td><input type="text" style="text-align:right; width:100%" ng-model="addedit.broker_amount" /></td>
                                                </tr>
                                          	</table>
                                       	</td>
                                        <td width="33.33%">
                                        	<table class="table table-hover list">
                                                <tr>
                                                    <th>CNF</th>
                                                    <td><select class="margin-btm-5" ng-model="addedit.cnf" chosen>
                                                        	<option value="0">No</option>
                                                        	<option value="1">Yes</option>
                                                   	</select></td>
                                                </tr>
                                                <tr>
                                                    <th>Fare of Vehicle</th>
                                                    <td><input type="text" style="text-align:right; width:100%" ng-model="addedit.fare_of_vehicle" /></td>
                                                </tr>
                                                <tr>
                                                    <th>Fare of Vehicle Payment Account</th>
                                                    <td><select ng-model="addedit.fare_of_vehicle_payment_account_id" class="order-select-box" chosen>
                                                        <option value="0">Select Account</option>
                                                        <optgroup ng-repeat="account_type in account_types" label="{{ account_type.title }}">
                                                            <option ng-repeat="account in accounts|filter:{account_type_id: account_type.id}:1" value="{{account.id}}">{{account.title}}</option>
                                                        </optgroup>
                                        			</select></td>
                                                </tr>
                                          	</table>
                                       	</td>
                                        <td width="33.33%">
                                        	<table class="table table-hover list">
                                                <tr>
                                                    <th>Payment Account</th>
                                                    <td><select class="margin-btm-5" ng-model="addedit.payment_account_id" chosen>
                                                        <option value="0">Select Account</option>
                                                        <optgroup ng-repeat="account_type in account_types" label="{{ account_type.title }}">
                                                            <option ng-repeat="account in accounts|filter:{account_type_id: account_type.id}:1" value="{{account.id}}">{{account.title}}</option>
                                                        </optgroup>
                                   					</select></td>
                                                </tr>
                                                <tr>
                                                    <th>Payment Amount <i class="fa fa-refresh" ng-click="addedit.payment_amount=grand_total()-addedit.discount"></i></th>
                                                    <td><input type="text" style="text-align:right; width: 100%" ng-model="addedit.payment_amount" /></td>
                                                </tr>
                                          	</table>
                                       	</td>
                                    </table>
                                </td>
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
                <button type="submit" ng-disabled="processing" class="btn btn-default btn-l" ng-click="save_addedit()" title="Submit Record"><i class="fa fa-spin fa-gear" ng-show="processing"></i> SUBMIT</button>
            </div>
        </div>
    </div>
    </div>
</div>