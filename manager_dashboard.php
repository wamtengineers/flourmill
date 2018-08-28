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
   	<div ng-app="manager" ng-controller="managerController" id="managerController">
        <div class="page-header">
            <h1 class="title">{{ current_tab==0?'Overview':(current_tab==1?'Sales':(current_tab==2?'Purchase':'Cashbook')) }}</h1>
            <ol class="breadcrumb">
                <li class="active">
                	Manager Dashboard
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
                    	
                        
                    </div>
                    <div class="item-selector" ng-show="current_tab==1">
                    	<div id="total-sale" ng-repeat="module in ['sales']">
                            <h2 class="total-heading">{{ overview_tab[module]==0?'All':(overview_tab[module]==1?'Dispatched':(overview_tab[module]==2?'Delivering':'Cancelled')) }} Orders - {{ module }}
                            	<div class="innertabs">
                                	<div class="innertab wct_tab_overview" ng-click="overview_tab[module]=0"><i class="fa fa-th-list"></i> All</div>
                                    <div class="innertab wct_tab_sales" ng-click="overview_tab[module]=1"><i class="fa fa-check-square-o"></i> <span ng-if="module=='sales'">Dispatched</span></div>
                                    <div class="innertab wct_tab_cashbook" ng-click="overview_tab[module]=4" ng-if="module=='sales'"><i class="fa fa-truck"></i> Delivered</div>
                                    <div class="innertab wct_tab_purchase" ng-click="overview_tab[module]=2"><i class="fa fa-database"></i> <span ng-if="module=='sales'">Delivering</span></div>
                                    <div class="innertab wct_tab_orange" ng-click="overview_tab[module]=5"><i class="fa fa-question"></i> On Hold</div>
                                    <div class="innertab wct_tab_danger" ng-click="overview_tab[module]=3"><i class="fa fa-close"></i> Cancelled</div>
                                </div>
                            </h2>
                            <div class="wct_innertabs_selected" ng-class="[{'wct_tab_overview': overview_tab[module]==0}, {'wct_tab_sales': overview_tab[module]==1}, {'wct_tab_danger': overview_tab[module]==3}, {'wct_tab_cashbook': overview_tab[module]==4}, {'wct_tab_orange': overview_tab[module]==5}]"></div>
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
                                        	<span class="order-status" ng-if="module=='sales'" ng-class="[{'wct_tab_danger': order.status==0}, {'wct_tab_sales': order.status==1}, {'wct_tab_cashbook': order.status==3}, {'wct_tab_orange': order.status==4}]">{{ order.status==0?'Cancelled':(order.status==1?'Dispatched':(order.status==2?'Delivering':(order.status==3?'Delivered':(order.status==4?'On Hold':'Cancelled')))) }}</span>
                                        	<span class="order-status" ng-if="module=='purchase'" ng-class="[{'wct_tab_danger': order.status==0}, {'wct_tab_sales': order.status==1}]">{{ order.status==0?'Cancelled':(order.status==1?'Received':(order.status==2?'Receiving':'Cancelled')) }}</span>
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
                    <div class="item-selector" ng-show="current_tab==2">
                    	<div id="total-sale" ng-repeat="module in ['purchase']">
                            <h2 class="total-heading">{{ overview_tab[module]==0?'All':(overview_tab[module]==1?'Dispatched':(overview_tab[module]==2?'Delivering':'Cancelled')) }} Orders - {{ module }}
                            	<div class="innertabs">
                                	<div class="innertab wct_tab_overview" ng-click="overview_tab[module]=0"><i class="fa fa-th-list"></i> All</div>
                                    <div class="innertab wct_tab_sales" ng-click="overview_tab[module]=1"><i class="fa fa-check-square-o"></i><span ng-if="module=='purchase'">Received</span></div>
                                    <div class="innertab wct_tab_cashbook" ng-click="overview_tab[module]=4" ng-if="module=='sales'"><i class="fa fa-truck"></i> Delivered</div>
                                    <div class="innertab wct_tab_purchase" ng-click="overview_tab[module]=2"><i class="fa fa-database"></i><span ng-if="module=='purchase'">Receiving</span></div>
                                    <div class="innertab wct_tab_orange" ng-click="overview_tab[module]=5"><i class="fa fa-question"></i> On Hold</div>
                                    <div class="innertab wct_tab_danger" ng-click="overview_tab[module]=3"><i class="fa fa-close"></i> Cancelled</div>
                                </div>
                            </h2>
                            <div class="wct_innertabs_selected" ng-class="[{'wct_tab_overview': overview_tab[module]==0}, {'wct_tab_purchase': overview_tab[module]==2}, {'wct_tab_danger': overview_tab[module]==3}, {'wct_tab_cashbook': overview_tab[module]==4}, {'wct_tab_orange': overview_tab[module]==5}]"></div>
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
                                        	<span class="order-status" ng-if="module=='sales'" ng-class="[{'wct_tab_danger': order.status==0}, {'wct_tab_purchase': order.status==2}, {'wct_tab_cashbook': order.status==3}, {'wct_tab_orange': order.status==4}]">{{ order.status==0?'Cancelled':(order.status==1?'Dispatched':(order.status==2?'Delivering':(order.status==3?'Delivered':(order.status==4?'On Hold':'Cancelled')))) }}</span>
                                        	<span class="order-status" ng-if="module=='purchase'" ng-class="[{'wct_tab_danger': order.status==0}, {'wct_tab_purchase': order.status==2}]">{{ order.status==0?'Cancelled':(order.status==1?'Received':(order.status==2?'Receiving':'Cancelled')) }}</span>
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
                    <div class="item-selector" ng-show="current_tab==3">
                    	<div id="item-row" class="cashbook">
                            <div class="row">
                            	<div class="col-md-6">
                                    <div id="total-expense" class="expense-form">
                                        <table width="100%" class="table table-hover list">
                                            <thead>
                                                <tr>
                                                    <th colspan="4" class="bg-info padding-8">Transaction</th>
                                                </tr>
                                                <tr class="head">
                                                    <th width="25%">Destination Account</th>
                                                    <th width="25%">Source Account</th>
                                                    <th width="15%" class="text-right">Amount</th>
                                                    <th width="30%">Details</th>
                                                </tr>
                                            </thead>
                                            <tr ng-repeat="transaction in transactions">
                                                <td>{{ get_field( transaction.account_id, accounts, "title" ) }}</td>
                                                <td>{{ get_field( transaction.reference_id, accounts, "title" ) }}</td>
                                                <td class="text-right">{{ transaction.amount }}</td>
                                                <td style="position:relative">{{ transaction.details }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div id="total-expense" class="expense-form">
                                        <table width="100%" class="table table-hover list">
                                            <thead>
                                                <tr>
                                                    <th colspan="4" class="bg-info padding-8">Expense</th>
                                                </tr>
                                                <tr class="head">
                                                    <th width="20%">Expense Category</th>
                                                    <th width="20%">Source Account</th>
                                                    <th width="15%" class="text-right">Amount</th>
                                                    <th width="30%">Details</th>
                                                </tr>
                                            </thead>
                                            <tr ng-repeat="expense in expenses">
                                                <td>{{ get_field( expense.expense_category_id, expense_categories, "title" ) }}</td>
                                                <td>{{ get_field( expense.account_id, accounts, "title" ) }}</td>
                                                <td class="text-right">{{ expense.amount }}</td>
                                                <td style="position:relative">{{ expense.details }}</td>
                                            </tr>
                                        </table>
                                    </div>
                               	</div>
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
        </div>
    </div>
</div>

<?php include("include/footer.php");?>