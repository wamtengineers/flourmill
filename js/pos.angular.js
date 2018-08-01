angular.module('pos', ['ngAnimate', 'angularMoment', 'localytics.directives']).controller('posController', 
	function ($scope, $http, $interval, $filter) {
		$scope.items = [];

		$scope.sales_orders = [];
		$scope.purchase_orders = [];
		$scope.transactions = [];
		$scope.expenses = []
		$scope.expense_categories = [];
		$scope.credit_accounts = {
			"sales": [],
			"purchase": []	
		}
		$scope.debit_accounts = {
			"sales": [],
			"purchase": []	
		}
		$scope.current_tab = 3;
		$scope.show_tab = function( tab ){
			$scope.current_tab = tab;
			if( $scope.current_tab == 1 && ( $scope.new_order.account_id == "0" || $scope.new_order.account_id == $scope.daily_purchase.id ) ) {
				$scope.new_order.account_id = $scope.daily_sale.id;
			}
			if( $scope.current_tab == 2 && ( $scope.new_order.account_id == "0" || $scope.new_order.account_id == $scope.daily_sale.id )) {
				$scope.new_order.account_id = $scope.daily_purchase.id;
			}
		}
		$scope.overview_tab = {
			'sales': 0,
			'purchase': 0
		};
		
		$scope.accounts = [];
		$scope.account_types = [];
		
		$scope.errors = [];
		$scope.processing = false;
		
		$scope.petty_cash = {};
		$scope.daily_sale = {};
		$scope.daily_purchase = {};
		
		$scope.new_order_placeholder = {
			"id": "",
			"transaction_type": "",
			"transaction_item": "",
			"discount": "",
			"account_id": "0",
			"less_weight": "",
			"payment_amount": "",
			"payment_account_id": "0",
			"status": "",
			"broker_amount": "",
			"broker_account_id": "0",
			"items": [],	
			"fare_of_vehicle" : "",
			"fare_of_vehicle_payment_account_id" : "0",
			"cnf": "",
		};	
		$scope.transaction_placeholder = {
			"id": "",
			"datetime_added": "",
			"account_id": "",
			"reference_id": "",
			"amount": "",
			"details": "",
		};
		$scope.expense_placeholder = {
			"id": "",
			"datetime_added": "",
			"account_id": "",
			"expense_category_id": "",
			"amount": "",
			"details": "",
		};
		$scope.new_order = {};
		$scope.transaction = angular.copy( $scope.transaction_placeholder );
		$scope.expense = angular.copy( $scope.expense_placeholder );
		$scope.updateDate = function(){
			$scope.dt = $(".angular-datepicker").val();
			$scope.$apply();
			$scope.wctAJAX( {action: 'set_dt', date: $scope.dt}, function( response ){});
			$scope.get_records();
		}
		
		$scope.get_field = function( id, array, field ) {
			if( $filter('filter')(array, {id: id}, true).length > 0 ) {
				return $filter('filter')(array, {id: id}, true)[0][ field ];
			}
		}
		$scope.order_item = function( item_id, packing, index ) {
			if( $scope.new_order.items ) {
				if( typeof packing !== 'undefined' ) {
					var search_parameters = {item_id: item_id}
					search_parameters.packing = packing;
					if( typeof index === 'undefined' ) {
						index = 0;
					}
					search_parameters.index = index;
					if( $filter('filter')($scope.new_order.items, search_parameters, true).length > 0 ) {
						return $filter('filter')($scope.new_order.items, search_parameters, true)[0].quantity;
					}
				}
			}
			return 0;
		}
		
		$scope.order_item_add = function( item_id, packing, index, qty ) {
			if( typeof packing !== 'undefined' ) {
				search_parameters = {item_id: item_id}
				search_parameters.packing = packing;
				if( typeof index === 'undefined' ) {
					index = 0;
				}
				search_parameters.index = index;
				if( typeof qty === 'undefined' ) {
					quantity = 1;
				}
				else {
					quantity = Number( qty );
				}
				
				if( quantity > 0 ){
					if( $filter('filter')($scope.new_order.items, search_parameters).length > 0 ) {
						if( typeof qty !== 'undefined' ) {
							$filter('filter')($scope.new_order.items, search_parameters)[0].quantity = quantity;
						}
						else{
							$filter('filter')($scope.new_order.items, search_parameters)[0].quantity++;
						}
					}
					else{
						$scope.new_order.items.push({id: 0, item_id: item_id, packing: packing, index: index, unit_price: 0, quantity: quantity, rate: "0", less_weight: ""});
					}
				}
				else{
					if( $filter('filter')($scope.new_order.items, search_parameters).length > 0 ) {
						index = jQuery.map( $scope.new_order.items, function( obj, i ) {
							if( obj.item_id == item_id && obj.packing == packing ) {
								return i;
							}
						});
						$scope.new_order.items.splice( index, 1 );
					}
				}
			}
			else{
				alert( 'Enter Packing size first' );
			}
		}
		$scope.order_item_remove = function( item_id, packing, index ) { 
		  	search_parameters = {item_id: item_id}
			search_parameters.packing = packing;
			if( typeof index === 'undefined' ) {
				index = 0;
			}
			search_parameters.index = index;
			if( $filter('filter')($scope.new_order.items, search_parameters).length > 0 ) {
				i = jQuery.map( $scope.new_order.items, function( obj, i ) {
					if( obj.item_id == item_id && obj.packing == packing ) {
						return i;
					}
				});
				$scope.new_order.items[ i ].quantity--;
				if( $scope.new_order.items[ i ].quantity == 0 ) {
					$scope.new_order.items.splice( i, 1 );
				}
			}
		}
		
		$scope.more_items = [];		
		$scope.add_another = function( item_id, packing ){
			$scope.more_items.push({
				item_id: item_id,
				packing: packing
			});
		}
		$scope.order_total = function(){
			total = 0;
			if(  typeof $scope.new_order.items!== 'undefined' ) {
				for( var i = 0; i < $scope.new_order.items.length; i++ ) {
					total += (Number( $scope.new_order.items[ i ].quantity )-Number( $scope.new_order.items[ i ].less_weight )) * Number( $scope.new_order.items[ i ].unit_price );
				}
				total -= Number(  $scope.new_order.discount );
			}
			return total;
			
		}
		$scope.save_order = function () {
			if( $scope.processing == false ){
				$scope.processing = true;
				data = {action: 'save_order', type: $scope.current_tab, order: JSON.stringify( $scope.new_order )};
				$scope.wctAJAX( data, function( response ){
					$scope.processing = false;
					if( response.status == 1 ) {
						$scope.print_receipt( response.order.id )
						$scope.new_order = angular.copy( $scope.new_order_placeholder );
						if( $scope.current_tab == 1 ){
							$scope.sales_orders.unshift( response.order );
							//$scope.update_credit_account( 'sales' );
						}
						else{
							$scope.purchase_orders.unshift( response.order );
							//$scope.update_credit_account( 'purchase' );
						}
					}
					else{
						alert(response.message);
					}
				});
			}
		}
		$scope.wctAJAX = function( wctData, wctCallback ) {
			wctRequest = {
				method: 'POST',
				url: 'index.php',
				headers: {'Content-Type': 'application/x-www-form-urlencoded'},
				transformRequest: function(obj) {
					var str = [];
					for(var p in obj){
						str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
					}
					return str.join("&");
				},
				data: wctData
			}
			$http(wctRequest).then(function(wctResponse){
				wctCallback(wctResponse.data);
			}, function () {
				console.log("Error in fetching data");
			});
		}
		angular.element(document).ready(function () {
			$scope.wctAJAX( {action: 'get_dt'}, function( response ){
				$scope.dt = JSON.parse( response );
				$scope.wctAJAX( {action: 'get_products'}, function( response ){
					$scope.items = response;
				});
				$scope.get_records( 1 );
			});
		});
		
		$scope.get_records = function( first_load ){
			$scope.wctAJAX( {action: 'get_accounts'}, function( response ){
				$scope.account_types = response.account_types;
				$scope.accounts = response.accounts;
				if( typeof first_load !== 'undefined' ){
					for( i = 0; i < $scope.accounts.length; i++ ) {
						if( $scope.accounts[ i ].is_petty_cash == 1 ) {
							$scope.petty_cash = $scope.accounts[ i ];
							$scope.new_order_placeholder.payment_account_id = $scope.petty_cash.id
							$scope.new_order_placeholder.fare_of_vehicle_payment_account_id = $scope.petty_cash.id
							$scope.transaction_placeholder.reference_id = $scope.petty_cash.id
							$scope.expense_placeholder.account_id = $scope.petty_cash.id
						}
						if( $scope.accounts[ i ].is_daily_sale == 1 ) {
							$scope.daily_sale = $scope.accounts[ i ];
						}
						if( $scope.accounts[ i ].is_daily_purchase == 1 ) {
							$scope.daily_purchase = $scope.accounts[ i ];
						}
					}
					$scope.new_order = angular.copy( $scope.new_order_placeholder );
					$scope.transaction = angular.copy( $scope.transaction_placeholder );
					$scope.expense = angular.copy( $scope.expense_placeholder );
				}
			});
			$scope.wctAJAX( {action: 'get_orders', dt: $scope.dt, module: 'sales'}, function( response ){
				$scope.sales_orders = response;
				$scope.update_credit_account( 'sales' );
			});
			$scope.wctAJAX( {action: 'get_orders', dt: $scope.dt, module: 'purchase'}, function( response ){
				$scope.purchase_orders = response;
				$scope.update_credit_account( 'purchase' );
			});
			$scope.wctAJAX( {action: 'get_expense_category'}, function( response ){
				$scope.expense_categories = response;
			});
			$scope.wctAJAX( {action: 'get_transactions', dt: $scope.dt}, function( response ){
				$scope.transactions = response;
			});
			$scope.wctAJAX( {action: 'get_expense', dt: $scope.dt}, function( response ){
				$scope.expenses = response;
			});
		}
		
		$scope.get_orders = function( module ){
			if( $scope.overview_tab[module] == 0 ) {
				return $scope[module+'_orders'];
			}
			else if( $scope.overview_tab[module] == 1 ) {
				return $filter('filter')($scope[module+'_orders'], {status: 1});
			}
			else if( $scope.overview_tab[module] == 2 ) {
				return $filter('filter')($scope[module+'_orders'], {status: 2});
			}
			else if( $scope.overview_tab[module] == 4 ) {
				return $filter('filter')($scope[module+'_orders'], {status: 3});
			}
			else if( $scope.overview_tab[module] == 5 ) {
				return $filter('filter')($scope[module+'_orders'], {status: 4});
			}
			else {
				return $filter('filter')($scope[module+'_orders'], {status: 0});
			}
		}
		$scope.add_expense = function(){
			if( $scope.processing == false ) {
				if( $scope.expense.expense_category_id == "" || $scope.expense.account_id == "" || $scope.expense.amount <= 0 ){
					alert("Enter Category, Account and Amount.");
				}
				else{
					$scope.processing = true;
					$scope.wctAJAX( {action: 'add_expense', expense: JSON.stringify($scope.expense)}, function( response ){
						$scope.processing = false;
						if( response.status == 1 ) {
							if( !$scope.expense.id ){
								$scope.expense.unshift(response.expense);
							}
							$scope.expenses = angular.copy( $scope.expense_placeholder );
														
						}
						else{
							alert(response.message);
						}
					});	
				}
			}
		}
		$scope.add_transaction = function(){
			if( $scope.processing == false ) {
				if( $scope.transaction.reference_id == "" || $scope.transaction.account_id == "" || $scope.transaction.amount <= 0 ){
					alert("Enter Account and Amount.");
				}
				if( $scope.transaction.amount <= 0 ){
					alert("Enter Amount.");
				}
				else{
					$scope.processing = true;
					$scope.wctAJAX( {action: 'add_transaction', transaction: JSON.stringify($scope.transaction)}, function( response ){
						$scope.processing = false;
						if( response.status == 1 ) {
							if( !$scope.transaction.id ){
								$scope.transaction.unshift(response.transaction);
							}
							$scope.transactions = angular.copy( $scope.transaction_placeholder );
														
						}
						else{
							alert(response.message);
						}
					});	
				}
			}
		}
		$scope.set_status = function( order_id, status, module ) {
			if( confirm( "Are you sure you want to change the status of this order?" ) ) {
				$scope.wctAJAX( {action: 'update_order', id: order_id, status: status, module: module}, function( response ){
					$filter('filter')($scope[module+"_orders"], {id: order_id})[0].status = status;
					$scope.update_credit_account( module );
				});
			}
		}
		
		$scope.sum = function( array, property ){
			var total = 0;
			for( var i =0; i < array.length; i++ ) {
				if( typeof array[ i ][ property ] !== 'undefined' ) {
					total += Number( array[ i ][ property ] );
				}
			}
			return total;
		}
		$scope.sum_dynamic = function( array, position, property ){
			var total = 0;
			for( var i =0; i <= position; i++ ) {
				if( typeof array[ i ][ property ] !== 'undefined' ) {
					total += Number( array[ i ][ property ] );
				}
			}
			return total;
		}
		$scope.total_kg = function( array ){
			var total = 0;
			if( Array.isArray(array) ) {
				for( var i =0; i < array.length; i++ ) {
					total += $scope.total_kg( array[i] );
				}
			}
			else{
				for( var i =0; i < array.items.length; i++ ) {
					//console.log(Number(array.items[i].quantity));
					total +=  (array.items[i].rate==0?array.items[i].packing:1) * (Number(array.items[i].quantity)-Number( array.items[i].less_weight ));
				}
				total -= Number( array.less_weight );
			}
			return total;
		}
		$scope.total_price = function( array ){
			var total = 0;
			for( var i =0; i < array.length; i++ ) {
				total += $scope.sum( array[ i ].items, 'total_price' )-Number( array[ i ].discount );
			}
			return total;
		}
		$scope.update_credit_account = function( module ){
			for( var i = 0; i < $scope[module+'_orders'].length; i++ ) {
				if( $scope[module+'_orders'][ i ].status != 0 ) {
					var total = $scope.sum( $scope[module+'_orders'][ i ].items, 'total_price' ) - $scope[module+'_orders'][ i ].discount-$scope[module+'_orders'][ i ].payment_amount;
					if( total != 0 ) {
						if( $filter( 'filter' )( $scope.credit_accounts[ module ], {id: $scope[module+'_orders'][ i ].account_id}, true ).length == 0 ) {
							$scope.credit_accounts[ module ].push( {
								id: angular.copy( $scope[module+'_orders'][ i ].account_id ),
								total: total,
							} );
						}
						else{
							$filter( 'filter' )( $scope.credit_accounts[ module ], {id: $scope[module+'_orders'][ i ].account_id}, true )[0].total +=total;
						}
					}
					if( $scope[module+'_orders'][ i ].payment_amount > 0 ) {
						if( $filter( 'filter' )( $scope.debit_accounts[ module ], {id: $scope[module+'_orders'][ i ].payment_account_id}, true ).length == 0 ) {
							$scope.debit_accounts[ module ].push( {
								id: angular.copy( $scope[module+'_orders'][ i ].payment_account_id ),
								total: $scope[module+'_orders'][ i ].payment_amount,
							} );
						}
						else{
							$filter( 'filter' )( $scope.debit_accounts[ module ], {id: $scope[module+'_orders'][ i ].payment_account_id}, true )[0].total += $scope[module+'_orders'][ i ].payment_amount;
						}
					}
				}
			}
			return accounts;
		}
		$scope.print_receipt = function( id ) {
			$("<iframe>")
				.hide()
				.attr("src", "index.php?tab=print_receipt&id="+id)
				.appendTo("body"); 
		}
	}
).directive('ngEnter', function() {
	return function(scope, element, attrs) {
		element.bind("keydown keypress", function(event) {
			if(event.which === 13) {
				scope.$apply(function(){
					scope.$eval(attrs.ngEnter, {'event': event});
				});

				event.preventDefault();
			}
		});
	};
}).directive('ngNumPad', function() {
  	return function(scope, element, attrs) {
    	angular.element(element).numpad({decimalSeparator: '.'});
  	};
});