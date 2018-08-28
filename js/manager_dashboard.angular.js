angular.module('manager', ['ngAnimate', 'angularMoment', 'localytics.directives']).controller('managerController', 
	function ($scope, $http, $interval, $filter) {
		$scope.items = [];
		$scope.sales_orders = [];
		$scope.sales_revalidate = [];
		$scope.purchase_orders = [];
		$scope.transactions = [];
		$scope.expenses = [];
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
		$scope.new_order = {};
		
		$scope.sale_revalidate = angular.copy( $scope.sales_revalidate_placeholder );
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
				
					$scope.transaction = angular.copy( $scope.transaction_placeholder );
					$scope.expense = angular.copy( $scope.expense_placeholder );
				}
			});
			$scope.wctAJAX( {action: 'get_orders', dt: $scope.dt, module: 'sales'}, function( response ){
				$scope.sales_orders = response;
				$scope.update_credit_account( 'sales' );
			});
			$scope.wctAJAX( {action: 'get_sales_revalidate', dt: $scope.dt}, function( response ){
				$scope.sales_revalidate = response;
				//$scope.update_credit_account( 'sales' );
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
		$scope.sum_revalidate = function( sale_revalidate ){
			var total = 0;
			for( var i =0; i < $scope.sales_revalidate.items.length; i++ ) {
				
					total += Number( $scope.sales_revalidate.items[ i ].total_price );
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
		$scope.total_kg_revalidate = function( sale_revalidate ){
			total = 0;
			for( i = 0; i < $scope.sales_revalidate.items.length; i++ ) {
				//total += Number( $scope.sales_revalidate.items[ i ].quantity );
				total +=  ($scope.sales_revalidate.items[i].rate==0?$scope.sales_revalidate.items[i].packing:1) * (Number($scope.sales_revalidate.items[i].quantity)-Number( $scope.sales_revalidate.items[i].less_weight ));
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