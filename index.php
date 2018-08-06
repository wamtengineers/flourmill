<?php 
include("include/db.php");
include("include/utility.php");
include("include/session.php");
define("APP_START", 1);
if( isset( $_GET[ "close" ] ) ) {
	doquery( "insert into closing_activity values('', NOW(), '".$_SESSION[ "logged_in_admin" ][ "id" ]."')", $dblink );
	header( "Location: index.php" );
	die;
}
include("modules/dashboard/ajax.php");
$page="index";
?>
<?php include("include/header.php");?>		
   	<div ng-app="pos" ng-controller="posController" id="posController">
        <div class="page-header">
            <h1 class="title">{{ current_tab==0?'Overview':(current_tab==1?'Sales':(current_tab==2?'Purchase':'Cashbook')) }}</h1>
            <ol class="breadcrumb">
                <li class="active">
                	<label>Revalidate Number</label>
                    <input ng-model="sale_revalidate.sales_id" type="text" style="vertical-align:top">
                    <button type="submit" ng-click="save_revalidate_sale()" class="btn btn-default btn-l">Submit</button>
                </li>
            </ol>
            <div class="wct_tabs_container">
                <div class="wct_tab_overview" ng-click="show_tab( 0 )"><span><i class="fa fa-tachometer"></i>Overview</span></div>
                <div class="wct_tab_sales" ng-click="show_tab( 1 )"><span><i class="fa fa-shopping-cart"></i>Sales</span></div>
                <div class="wct_tab_purchase" ng-click="show_tab( 2 )"><span><i class="fa fa-truck"></i>Purchase</span></div>
                <div class="wct_tab_cashbook" ng-click="show_tab( 3 )"><span><i class="fa fa-money"></i>Cashbook</span></div>
            </div>
        </div>
        <div id="item-row" ng-class="{'padding-bottom': new_order.items.length>0}">
            <div class="wct_tabs_selected" ng-class="[{'wct_tab_overview': current_tab==0}, {'wct_tab_sales': current_tab==1}, {'wct_tab_purchase': current_tab==2}, {'wct_tab_cashbook': current_tab==3}]"></div>
            <div ng-if="errors.length > 0" class="errors">
                <div ng-repeat="error in errors" class="alert alert-danger">{{error}}</div>
            </div>
            <div class="row clearfix">
                <div class="col-md-12">
                	<div class="item-selector" ng-show="current_tab==0">
                    	<div id="total-sale" ng-repeat="module in ['sales', 'purchase']">
                            <h2 class="total-heading">{{ overview_tab[module]==0?'All':(overview_tab[module]==1?'Dispatched':(overview_tab[module]==2?'Delivering':'Cancelled')) }} Orders - {{ module }}
                            	<div class="innertabs">
                                	<div class="innertab wct_tab_overview" ng-click="overview_tab[module]=0"><i class="fa fa-th-list"></i> All</div>
                                    <div class="innertab wct_tab_sales" ng-click="overview_tab[module]=1"><i class="fa fa-check-square-o"></i> <span ng-if="module=='sales'">Dispatched</span><span ng-if="module=='purchase'">Received</span></div>
                                    <div class="innertab wct_tab_cashbook" ng-click="overview_tab[module]=4" ng-if="module=='sales'"><i class="fa fa-truck"></i> Delivered</div>
                                    <div class="innertab wct_tab_purchase" ng-click="overview_tab[module]=2"><i class="fa fa-database"></i> <span ng-if="module=='sales'">Delivering</span><span ng-if="module=='purchase'">Receiving</span></div>
                                    <div class="innertab wct_tab_orange" ng-click="overview_tab[module]=5"><i class="fa fa-question"></i> On Hold</div>
                                    <div class="innertab wct_tab_danger" ng-click="overview_tab[module]=3"><i class="fa fa-close"></i> Cancelled</div>
                                </div>
                            </h2>
                            <div class="wct_innertabs_selected" ng-class="[{'wct_tab_overview': overview_tab[module]==0}, {'wct_tab_sales': overview_tab[module]==1}, {'wct_tab_purchase': overview_tab[module]==2}, {'wct_tab_danger': overview_tab[module]==3}, {'wct_tab_cashbook': overview_tab[module]==4}, {'wct_tab_orange': overview_tab[module]==5}]"></div>
                            <div id="cart" class="panel-body table-responsive" style="max-height: 435px;">
                                <table width="100%" class="table table-hover list">
                                    <thead>
                                        <tr>
                                            <th width="5%">Token No.</th>
                                            <th width="5%">Time</th>
                                            <th width="12%">Account</th>
                                            <th width="25%">Items</th>
                                            <th width="8%" class="text-right">Total KG<br>{{ total_kg( get_orders( module ) )|currency:"":0 }}Kg</th>
                                            <th width="8%" class="text-right">Total Price<br>{{ total_price( get_orders( module ) )|currency:"Rs.":0 }}</th>
                                            <th width="8%" class="text-right">Payment<br>{{ sum( get_orders( module ), 'payment_amount'  )|currency:'Rs. ':0 }}</th>
                                            <th width="12%" class="text-right">Payment Account</th>
                                            <th width="8%" class="text-center">Status</th>
                                            <th width="10%" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tr ng-repeat="order in get_orders( module )" ng-class="[{'alert-danger': order.status==0}, {'alert-warning': order.status==2}, {'alert-info': order.status==1}]">
                                        <td class="text-center">{{ order.token_number }}</td>
                                        <td>{{ order.datetime_added }}</td>
                                        <td>{{ get_field( order.account_id, accounts, "title" ) }}</td>
                                        <td>
                                            <ul>
                                                <li ng-repeat="item in order.items">{{ item.title }} {{ item.packing }}KG x {{ item.quantity }} Packs (Rate: <span ng-click="rate_update( order.id )">{{ item.unit_price|currency:"Rs.":2 }}</span>)</li>
                                            </ul>
                                        </td>
                                        <td class="text-right">{{ total_kg( order )|currency:'':0 }} Kg</td>
                                        <td class="text-right">{{ sum( order.items, 'total_price' ) - order.discount |currency:'':0 }}</td>
                                        <td class="text-right">{{ order.payment_amount|currency:'':0 }}</td>
                                        <td>{{ get_field( order.payment_account_id, accounts, "title" ) }}</td>
                                        <td class="text-center">
                                        	<span class="order-status" ng-if="module=='sales'" ng-class="[{'wct_tab_danger': order.status==0}, {'wct_tab_sales': order.status==1}, {'wct_tab_purchase': order.status==2}, {'wct_tab_cashbook': order.status==3}, {'wct_tab_orange': order.status==4}]">{{ order.status==0?'Cancelled':(order.status==1?'Dispatched':(order.status==2?'Delivering':(order.status==3?'Delivered':(order.status==4?'On Hold':'Cancelled')))) }}</span>
                                        	<span class="order-status" ng-if="module=='purchase'" ng-class="[{'wct_tab_danger': order.status==0}, {'wct_tab_sales': order.status==1}, {'wct_tab_purchase': order.status==2}]">{{ order.status==0?'Cancelled':(order.status==1?'Received':(order.status==2?'Receiving':'Cancelled')) }}</span>
                                        </td>
                                        <td class="text-center">
                                        	<a href="" title="Hold Order" class="cancel-order" ng-click="set_status(order.id, 4, module)" ng-if="order.status==2 || order.status==3"><i class="fa fa-close" aria-hidden="true"></i></a>
                                            <?php if( $_SESSION[ "logged_in_admin" ][ "admin_type_id" ] == 1 ){?><a href="" title="Print" ng-click="print_receipt(order.id)" ng-if="module=='sales' && order.status != 0"><i class="fa fa-print" aria-hidden="true"></i></a><?php } ?>
                                        </td>
                                    </tr>
                                    <tr ng-show="get_orders( module ).length == 0" class="alert-danger">
                                    	<td colspan="10">No records found.</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div id="total-sale">
                            <h2 class="total-heading">Sales Revalidate</h2>
                            <div id="cart" class="panel-body table-responsive" style="max-height: 435px;">
                                <table width="100%" class="table table-hover list">
                                    <thead>
                                        <tr>
                                            <th width="5%">Token No.</th>
                                            <th width="5%">Time</th>
                                            <th width="12%">Account</th>
                                            <th width="25%">Items</th>
                                            <th width="8%" class="text-right">Total KG</th>
                                            <th width="8%" class="text-right">Total Price<br></th>
                                            <th width="8%" class="text-right">Payment<br></th>
                                            <th width="12%" class="text-right">Payment Account</th>
                                            <th width="8%" class="text-center">Status</th>
                                            <th width="10%" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tr ng-repeat="sale_revalidate in sales_revalidate">
                                        <td class="text-center">{{ sale_revalidate.token_number }}</td>
                                        <td>{{ sale_revalidate.datetime_added }}</td>
                                        <td>{{ get_field( sale_revalidate.account_id, accounts, "title" ) }}</td>
                                        <td>
                                            <ul>
                                                <li ng-repeat="item in sales_revalidate.items">{{ item.title }} {{ item.packing }}KG x {{ item.quantity }} Packs (Rate: <span ng-click="rate_update( sale_revalidate.id )">{{ item.unit_price|currency:"Rs.":2 }}</span>)</li>
                                            </ul>
                                        </td>
                                        <td class="text-right">{{ total_kg_revalidate( sale_revalidate )|currency:'':0 }} Kg</td>
                                        <td class="text-right">{{ sum_revalidate( sale_revalidate ) - sale_revalidate.discount |currency:'':0 }}</td>
                                        <td class="text-right">{{ sale_revalidate.payment_amount|currency:'':0 }}</td>
                                        <td>{{ get_field( sale_revalidate.payment_account_id, accounts, "title" ) }}</td>
                                        <td class="text-center">
                                        	
                                        </td>
                                        <td class="text-center">
                                        	<a href="" title="Hold Order" class="cancel-order" ng-click="set_status(order.id, 4, module)" ng-if="order.status==2 || order.status==3"><i class="fa fa-close" aria-hidden="true"></i></a>
                                            <?php if( $_SESSION[ "logged_in_admin" ][ "admin_type_id" ] == 1 ){?><a href="" title="Print" ng-click="print_receipt(order.id)" ng-if="module=='sales' && order.status != 0"><i class="fa fa-print" aria-hidden="true"></i></a><?php } ?>
                                        </td>
                                    </tr>
                                    <tr ng-show="get_orders( module ).length == 0" class="alert-danger">
                                    	<td colspan="10">No records found.</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="item-selector" ng-show="current_tab==1 || current_tab==2">
                    	<div class="row" ng-show="new_order.transaction_type==''">
                            <div class="col-md-2">
                            	<div class="item" ng-click="new_order.transaction_type=1">
                                    <div class="item-img">
                                        <div class="img-placeholder"><span>Cash {{ current_tab==1?"Sale":"Purchase" }}</span></div>
                                  	</div>
                               	</div>
                            </div>
                            <div class="col-md-2">
                            	<div class="item" ng-click="new_order.transaction_type=2; new_order.account_id='0'">
                                    <div class="item-img">
                                        <div class="img-placeholder"><span>Credit {{ current_tab==1?"Sale":"Purchase" }}</span></div>
                                  	</div>
                               	</div>
                            </div>                        
                       	</div>
                        <div id="tabs" class="c-tabs no-js items-wrap clearfix credit-account-selector" ng-show="new_order.transaction_type==2 && new_order.account_id=='0'">
                        	<h1 style="margin:0">Select Account</h1>
                            <select ng-model="new_order.account_id" class="order-select-box" chosen>
                                <option value="0">Select Account</option>
                                <optgroup ng-repeat="account_type in account_types" label="{{ account_type.title }}">
                                    <option ng-repeat="account in accounts|filter:{account_type_id: account_type.id}:1" value="{{account.id}}">{{account.title}}</option>
                                </optgroup>
                            </select>
                       	</div>
                    	<div id="tabs" class="c-tabs no-js items-wrap clearfix" ng-show="new_order.transaction_type!='' && new_order.account_id!='0'">
                        	<h1 style="margin:0">{{ new_order.transaction_type==1?"Cash":"Credit" }} {{ current_tab==1?"Sale":"Purchase" }} <span ng-if="new_order.transaction_type==2"> - {{ get_field( new_order.account_id, accounts, 'title' ) }}</span></h1>
                            <button class="btn btn-primary" ng-click="new_order.transaction_type=''" style="margin-bottom:20px;">
                            	Back
                            </button>
                            <div class="c-tab is-active clearfix" ng-show="new_order.transaction_item===''">
                            	<div class="row">
                                    <div ng-repeat="item in items">
                                    	<div class="col-md-2">
                                            <div class="item" ng-click="new_order.transaction_item=$index">
                                                <div class="item-img">
                                                    <div class="img-placeholder"><span>{{ item.title }}</span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                               	</div>
                            </div>
                            <div ng-repeat="item in items" class="c-tab is-active clearfix" ng-show="new_order.transaction_item===$index">
                                <h3>{{item.title}}
                                	<button class="btn btn-primary" ng-click="new_order.transaction_item=''" style="float: right">
                                        Back
                                    </button>
                                </h3>
                                <div class="row">
                                    <div ng-repeat="packing in item.packing">
                                        <div class="col-md-2" ng-class="{'active': order_item(item.id, packing.packing)}">
                                            <div class="item">
                                                <div class="item-img">
                                                    <div class="img-placeholder"><span>{{ packing.title }}</span></div>
                                                    <div class="item-img-hover">
                                                        <form>
                                                            <h3>
                                                                <span class="dec" ng-click="order_item_remove(item.id, packing.packing)">-</span>
                                                                <input value="{{ order_item(item.id, packing.packing) }}" ng-change="order_item_add(item.id, packing.packing, 0, user_input[item.id][$index][0])" ng-model="user_input[item.id][$index][0]" type="text" ng-num-pad data-numpad="nmpd1">
                                                                <span class="inc" ng-click="order_item_add(item.id, packing.packing)">+</span>
                                                            </h3>
                                                        </form>
                                                    </div>
                                                </div>    
                                                <div class="item-text">
                                                    <h2>{{ item.title }} <a ng-show="order_item(item.id, packing.packing) > 0" class="add_another" ng-click="add_another( item.id, packing.packing )">Add Another</a></h2>
                                                </div>    
                                            </div>
                                        </div>
                                        <div ng-repeat="more_item in more_items|filter:{item_id: item.id, packing: packing.packing}" class="col-md-2" ng-class="{'active': order_item(item.id, packing.packing, $index+1)}">
                                        	<div class="item">
                                                <div class="item-img">
                                                    <div class="img-placeholder"><span>{{ packing.title }}</span></div>
                                                    <div class="item-img-hover">
                                                        <form>
                                                            <h3>
                                                                <span class="dec" ng-click="order_item_remove(item.id, packing.packing, $index+1)">-</span>
                                                                <input value="{{ order_item(item.id, packing.packing, $index+1) }}" ng-change="order_item_add(item.id, packing.packing, $index+1, user_input[item.id][$parent.$index][$index+1])" ng-model="user_input[item.id][$parent.$index][$index+1]" type="text" ng-num-pad>
                                                                <span class="inc" ng-click="order_item_add(item.id, packing.packing, $index+1)">+</span>
                                                            </h3>
                                                        </form>
                                                    </div>
                                                </div>    
                                                <div class="item-text">
                                                    <h2>{{ item.title }} <a ng-show="order_item(item.id, packing.packing, $index) > 0" class="add_another" ng-click="add_another( item.id, packing.packing )">Add Another</a></h2>
                                                </div>    
                                            </div>                                        	
                                        </div>
                                   	</div>
                                    <div class="col-md-2" ng-class="{'active': order_item(item.id, custom_packing[item.id][0], 0)}">
                                        <div class="item">
                                            <div class="item-img">
                                                <div class="img-placeholder"><span>Custom</span></div>
                                                <div class="item-img-hover">
                                                    <form>
                                                        <h3>
                                                            <span class="dec" ng-click="order_item_remove(item.id, custom_packing[item.id][0])">-</span>
                                                            <input ng-model="custom_packing[item.id][0]" type="text" placeholder="Packing" class="packing-input" ng-num-pad>
                                                            <input value="{{ order_item(item.id, custom_packing[item.id][0], 0) }}" ng-change="order_item_add(item.id, custom_packing[item.id][0], 0, user_input[item.id]['custom'][0])" ng-model="user_input[item.id]['custom'][0]" type="text" ng-num-pad>
                                                            <span class="inc" ng-click="order_item_add(item.id, custom_packing[item.id][0])">+</span>
                                                        </h3>
                                                    </form>
                                                </div>
                                            </div>    
                                            <div class="item-text">
                                                <h2>{{ item.title }} <a ng-show="order_item(item.id, custom_packing[item.id][0], 0) > 0" class="add_another" ng-click="add_another( item.id, 'custom' )">Add Another</a></h2>
                                            </div>    
                                        </div>
                                    </div>
                                    <div ng-repeat="more_item in more_items|filter:{item_id: item.id, packing: 'custom'}" class="col-md-2" ng-class="{'active': order_item(item.id, custom_packing[item.id][$index+1], $index+1)}">
                                        <div class="item">
                                            <div class="item-img">
                                                <div class="img-placeholder"><span>Custom</span></div>
                                                <div class="item-img-hover">
                                                    <form>
                                                        <h3>
                                                            <span class="dec" ng-click="order_item_remove(item.id, custom_packing[item.id][$index+1])">-</span>
                                                            <input ng-model="custom_packing[item.id][$index+1]" type="text" placeholder="Packing" class="packing-input" ng-num-pad>
                                                            <input value="{{ order_item(item.id, packing.packing, $index+1) }}" ng-change="order_item_add(item.id, custom_packing[item.id][$index+1], $index+1, user_input[item.id]['custom'][$index+1])" ng-model="user_input[item.id]['custom'][$index+1]" type="text" ng-num-pad>
                                                            <span class="inc" ng-click="order_item_add(item.id, custom_packing[item.id][$index+1], $index+1)">+</span>
                                                        </h3>
                                                    </form>
                                                </div>
                                            </div>    
                                            <div class="item-text">
                                                <h2>{{ item.title }} <a ng-show="order_item(item.id, custom_packing[item.id][$index+1]) > 0" class="add_another" ng-click="add_another( item.id, 'custom' )">Add Another</a></h2>
                                            </div>    
                                        </div>                                        	
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="item-selector" ng-show="current_tab==3">
                    	<div id="item-row" class="cashbook">
                            <div class="row clearfix">
                                <div class="col-md-6">
                                    <div id="total-expense" class="expense-form">
                                        <table width="100%" class="table table-hover list">
                                            <thead>
                                                <tr>
                                                    <th colspan="5" class="bg-info padding-8">Transaction</th>
                                                </tr>
                                                <tr class="head">
                                                    <th width="20%">Destination Account</th>
                                                    <th width="20%">Source Account</th>
                                                    <th width="15%" class="text-right">Amount</th>
                                                    <th width="30%" colspan="2">Details</th>
                                                </tr>
                                            </thead>
                                            <tr>
                                                <!--<td class="text-center icon-row"><input type="text" ng-model="transaction.datetime_added" class="date-timepicker angular-datetimepicker" style="width:100%;"></td>-->
                                                <td>
                                                    <select style="font-size: 12px; color:#000" ng-model="transaction.account_id" chosen>
                                                        <option value="0">Select Account</option>
                                                        <optgroup ng-repeat="account_type in account_types" label="{{ account_type.title }}">
                                                            <option ng-repeat="account in accounts|filter:{account_type_id: account_type.id}:1" value="{{account.id}}">{{account.title}}</option>
                                                        </optgroup>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select style="font-size: 12px; color:#000" ng-model="transaction.reference_id" chosen>
                                                        <option value="0">Select Account</option>
                                                        <optgroup ng-repeat="account_type in account_types" label="{{ account_type.title }}">
                                                            <option ng-repeat="account in accounts|filter:{account_type_id: account_type.id}:1" value="{{account.id}}">{{account.title}}</option>
                                                        </optgroup>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" id="amount" class="form-control text-right" placeholder="Amount" ng-model="transaction.amount">
                                                </td>
                                                <td class="text-right" style="position:relative">
                                                    <textarea class="form-control" placeholder="Details" ng-model="transaction.details"></textarea>
                                                </td>
                                                <td><input type="button" class="btn btn-default btn-l" value="Save" ng-click="add_transaction()"></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div id="total-expense" class="expense-form">
                                        <table width="100%" class="table table-hover list">
                                            <thead>
                                                <tr>
                                                    <th colspan="5" class="bg-info padding-8">Expense</th>
                                                </tr>
                                                <tr class="head">
                                                    <th width="20%">Expense Category</th>
                                                    <th width="20%">Source Account</th>
                                                    <th width="15%" class="text-right">Amount</th>
                                                    <th width="30%" colspan="2">Details</th>
                                                </tr>
                                            </thead>
                                            <tr>
                                                <!--<td class="text-center icon-row"><input type="text" ng-model="expense.datetime_added" class="date-timepicker angular-datetimepicker" data-controllerid="posController" style="width:100%;"></td>-->
                                                <td>
                                                    <select style="font-size: 12px; color:#000" ng-model="expense.expense_category_id" chosen>
                                                        <option value="0">Select Category</option>
                                                        <option ng-repeat="category in expense_categories" value="{{category.id}}">{{category.title}}</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select style="font-size: 12px; color:#000" ng-model="expense.account_id" chosen>
                                                        <option value="0">Select Account</option>
                                                        <optgroup ng-repeat="account_type in account_types" label="{{ account_type.title }}">
                                                            <option ng-repeat="account in accounts|filter:{account_type_id: account_type.id}:1" value="{{account.id}}">{{account.title}}</option>
                                                        </optgroup>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" id="amount" class="form-control text-right" placeholder="Amount" ng-model="expense.amount">
                                                </td>
                                                <td class="text-right" style="position:relative">
                                                    <textarea class="form-control" placeholder="Details" ng-model="expense.details"></textarea>
                                                </td>
                                                <td><input type="button" class="btn btn-default btn-l" value="Save" ng-click="add_expense()"></td>
                                            </tr>
                                        </table>
                                    </div>
                               	</div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div id="total-expense" class="expense-form">
                                        <table width="100%" class="table table-hover list" ng-init="cnt=1;">
                                            <thead>
                                                <tr>
                                                    <th colspan="7" class="bg-info padding-8">Cashbook</th>
                                                </tr>
                                                <tr class="head">
                                                    <th width="3%" class="text-right">SN</th>
                                                    <th width="12%">Date/Time</th>
                                                    <th width="25%">Account</th>
                                                    <th width="30%">Details</th>
                                                    <th width="15%" class="text-right">Debit</th>
                                                    <th width="15%" class="text-right">Credit</th>
                                                </tr>
                                            </thead>
                                            <tr>
                                            	<td class="text-right">{{ cnt+1 }}</td>
                                                <td>{{ dt }}</td>
                                                <td>Sales</td>
                                                <td>--</td>
                                                <td class="text-right">--</td>
                                                <td class="text-right">{{ total_price( sales_orders )-total_price( sales_orders|filter:{status: 0}:1 )|currency:"":0 }}</td>
                                            </tr>
                                            <tr ng-repeat="account in debit_accounts.sales" ng-init="cnt=cnt+1">
                                            	<td class="text-right">{{ $index+cnt }}</td>
                                                <td>{{ dt }}</td>
                                                <td>{{ get_field( account.id, accounts, 'title' ) }}</td>
                                                <td>Sales</td>
                                                <td class="text-right">{{ account.total|currency:"":0 }}</td>
                                                <td class="text-right">--</td>
                                            </tr>
                                            <tr ng-repeat="account in credit_accounts.sales" ng-init="cnt=cnt+1">
                                            	<td class="text-right">{{ $index+cnt }}</td>
                                                <td>{{ dt }}</td>
                                                <td>{{ get_field( account.id, accounts, 'title' ) }}</td>
                                                <td>Sales</td>
                                                <td class="text-right">{{ account.total|currency:"":0 }}</td>
                                                <td class="text-right">--</td>
                                                <!--<td class="text-right">{{ petty_cash.balance+total_price( sales_orders )-total_price( sales_orders|filter:{status: 0}:1 ) - sum_dynamic( credit_accounts.sales, $index, 'total' ) + sum_dynamic( credit_accounts.sales, $index, 'payment' )|currency:"":0 }}</td>-->
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="nav-bar bg-info color5-bg" ng-if="new_order.items" ng-show="new_order.items.length>0" style="border-top: solid 10px rgb(119,169,47);padding-top: 18px;">
                <div class="col-md-1 order">
                    <h2>{{ new_order.transaction_type==1?'Cash':'Credit'}} {{ current_tab==1?'Sale':'Purchase'}} Order</h2>
                </div>
                <div class="col-md-5 items-margin">
                    <strong>Items Name</strong>
                    <ul>
                        <li>
                        	<table class="order-item-table">
                            	<tr>
                                	<th width="17%">Item</th>
                                    <th width="15%">Packing</th>
                                    <th width="12%">Quantity</th>
                                    <th width="12%">Less Wt</th>
                                    <th width="10%">Net Wt</th>
                                    <th width="10%">Rate</th>
                                    <th width="14%">Price</th>
                                </tr>
                                <tr ng-repeat="item in new_order.items">
                                	<td>{{ get_field( item.item_id, items, 'title' ) }}</td>
                                    <td><input type="text" ng-num-pad ng-model="new_order.items[ $index ].packing" style="width:68%" /> Kg</td>
                                    <td>
                                    	<input type="text" ng-num-pad ng-model="new_order.items[ $index ].quantity" />
                                    </td>
                                    <td>
                                    	<input type="text" ng-num-pad ng-model="new_order.items[ $index ].less_weight" />
                                    </td>
                                    <td>
                                    	<strong style="background: #fff;height: 26px;color: #000;font-size: 14px;font-weight: normal;border-radius: 3px;padding-left: 10px;line-height: 26px;">{{new_order.items[ $index ].quantity-new_order.items[ $index ].less_weight}}</strong>
                                    </td>
                                    <td>
                                    	<select cclass="order-select-box" chosen ng-model="new_order.items[ $index ].rate">
                                        	<option value="0">Box</option>
                                            <option value="1">Kg</option>
                                      	</select>
                                    </td>
                                    <td><input type="text" ng-num-pad ng-model="new_order.items[ $index ].unit_price" /></td>
                                </tr>
                            </table>
                       	</li>
                    </ul>
                </div>
                <div class="col-md-2 text-left total-item">
                    <div class="total-col">
                        <strong>Less Weight</strong>
                        <ul>
                            <li><input type="text" class="order-input-box" ng-num-pad ng-model="new_order.less_weight" /></li>
                        </ul>
                    </div>
                    <div class="total-col">
                        <strong>Discount</strong>
                        <ul>
                            <li><input type="text" name="discount" class="order-input-box" ng-num-pad ng-model="new_order.discount"/></li>
                        </ul>
                    </div>
                    <div class="total-col">
                        <strong>Total Price</strong>
                        <ul>
                            <li class="order-input-box">{{ order_total()|currency:'Rs. ':0 }}</li>
                        </ul>
                    </div>
                    <div class="total-col" style="display:none">
                        <strong>Payment <i class="fa fa-refresh" ng-click="new_order.payment_amount=order_total()"></i></strong>
                        <ul>
                            <li><input type="text" name="payment_amount" class="order-input-box" ng-num-pad ng-model="new_order.payment_amount"/></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-2 text-left total-item">
                	<div class="total-col">
                        <strong>Account</strong>
                        <ul>
                            <li style="text-align:left">
                                <select ng-model="new_order.account_id" class="order-select-box" chosen>
                                    <option value="0">Select Account</option>
                                    <optgroup ng-repeat="account_type in account_types" label="{{ account_type.title }}">
                                        <option ng-repeat="account in accounts|filter:{account_type_id: account_type.id}:1" value="{{account.id}}">{{account.title}}</option>
                                    </optgroup>
                                </select>
                            </li>
                        </ul>
                    </div>
                    <div class="total-col" style="display:none">
                        <strong>Payment Account</strong>
                        <ul>
                            <li style="text-align:left">
                                <select ng-model="new_order.payment_account_id" class="order-select-box" chosen>
                                    <option value="0">Select Account</option>
                                    <optgroup ng-repeat="account_type in account_types" label="{{ account_type.title }}">
                                        <option ng-repeat="account in accounts|filter:{account_type_id: account_type.id}:1" value="{{account.id}}">{{account.title}}</option>
                                    </optgroup>
                                </select>
                            </li>
                        </ul>
                    </div>
                    <div class="total-col">
                        <strong style="display:inline-block; vertical-align:top">CNF</strong>
                        <ul style="display:inline-block; padding-top:0;">
                            <li>
                            	<div class="checkbox checkbox-primary">
                                    <input type="checkbox" id="cnf" ng-model="new_order.cnf" title="Select">
                                    <label for="cnf"></label>
                                </div>
                            	
                            </li>
                        </ul>
                    </div>
                    <div class="total-col">
                        <strong>Fare of Vehicle</strong>
                        <ul>
                            <li><input type="text" name="fare_of_vehicle" class="order-input-box" ng-model="new_order.fare_of_vehicle" ng-num-pad /></li>
                        </ul>
                    </div>
                    <div class="total-col">
                        <strong>Fare Payment Account</strong>
                        <ul>
                            <li style="text-align:left">
                                <select ng-model="new_order.fare_of_vehicle_payment_account_id" class="order-select-box" chosen>
                                    <option value="0">Select Account</option>
                                    <optgroup ng-repeat="account_type in account_types" label="{{ account_type.title }}">
                                        <option ng-repeat="account in accounts|filter:{account_type_id: account_type.id}:1" value="{{account.id}}">{{account.title}}</option>
                                    </optgroup>
                                </select>
                            </li>
                        </ul>
                    </div>
              	</div>
                <div class="col-md-2 text-left">
                    <strong>Broker</strong>
                    <ul>
                        <li style="text-align:left">
                            <select ng-model="new_order.broker_account_id" class="order-select-box" chosen>
                                <option value="0">Select Account</option>
                                <optgroup ng-repeat="account_type in account_types" label="{{ account_type.title }}">
                                    <option ng-repeat="account in accounts|filter:{account_type_id: account_type.id}:1" value="{{account.id}}">{{account.title}}</option>
                                </optgroup>
                            </select>
                        </li>
                    </ul>
                    <strong>Brokery Amount</strong>
                    <ul>
                        <li style="text-align:left"><input type="text" name="broker_amount" class="order-input-box" ng-model="new_order.broker_amount" ng-num-pad /></li>
                    </ul>
                    <a href="" class="cart-button" ng-click="save_order()">Place Order</a>
                </div>
            </div>            
        </div>
    </div>
</div>

<?php include("include/footer.php");?>