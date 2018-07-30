<?php 
include("include/db.php");
include("include/utility.php");
include("include/session.php");
define("APP_START", 1);
include("modules/dashboard/ajax.php");
?>
<?php include("include/header.php");?>		
   	<div ng-app="gatepass" ng-controller="gatepassController" id="gatepassController">
        <div class="page-header">
            <h1 class="title">Gatepass</h1>
            <ol class="breadcrumb">
                <li class="active"><input ng-model="dt" data-controllerid="gatepassController" class="form-control datepicker angular-datepicker" /></li>
            </ol>
        </div>
        <div id="item-row">
            <div class="row clearfix">
                <div class="col-md-12">
                    <div class="item-selector">
                    	<div id="total-sale" ng-repeat="module in ['sales']">
                            <h2 class="total-heading">{{ overview_tab[module]==0?'All':(overview_tab[module]==1?'Dispatched':(overview_tab[module]==2?'Delivering':'Cancelled')) }} Orders - {{ module }}
                            	<div class="innertabs">
                                	<div class="innertab wct_tab_overview" ng-click="overview_tab[module]=0"><i class="fa fa-th-list"></i> All</div>
                                    <div class="innertab wct_tab_sales" ng-click="overview_tab[module]=1"><i class="fa fa-check-square-o"></i> <span ng-if="module=='sales'">Dispatched</span></div>
                                    <div class="innertab wct_tab_purchase" ng-click="overview_tab[module]=2"><i class="fa fa-database"></i> <span ng-if="module=='sales'">Delivering</span></div>
                                    <div class="innertab wct_tab_danger" ng-click="overview_tab[module]=3"><i class="fa fa-close"></i> Cancelled</div>
                                </div>
                            </h2>
                            <div class="wct_innertabs_selected" ng-class="[{'wct_tab_overview': overview_tab[module]==0}, {'wct_tab_sales': overview_tab[module]==1}]"></div>
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
                                        	<span class="order-status" ng-if="module=='sales'" ng-class="[{'wct_tab_danger': order.status==0}, {'wct_tab_sales': order.status==1}]">{{ order.status==0?'Cancelled':(order.status==1?'Dispatched':(order.status==2?'Delivering':'Cancelled')) }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class=""><a href="" class="" ng-click="order_detail()" title="Print">View Order</a></div>
                                        </td>
                                    </tr>
                                    <tr ng-show="get_orders( module ).length == 0" class="alert-danger">
                                    	<td colspan="9">No records found.</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div id="order_detail"> 
                        	<div class="head">
                            	<h1>Ghouri Flour Mill</h1>
                            </div>
                            <div class="text-center"><div class="token-number">Token Number: <strong>35</strong></div></div>
                            <div class="barcode_num">
                                <span class="barcode"><img src="barcode.php?text=222&size=30" /></span>
                                <span class="number">222</span>
                            </div>
                            <p>Date/Time: <strong style="float:right">28/07/2018 7.00 PM</strong></p>
                            <table width="100%" cellpadding="0" cellspacing="0" class="table table-hover list">
                                <tr class="bg-success">
                                    <td class="text-center">
                                    	<span class="item-name">Meida</span> &times; <span class="qty">20</span> 39.5 KG
                                    </td>
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

<?php include("include/footer.php");?>