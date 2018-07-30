angular.module('gatepass', ['ngAnimate', 'angularMoment', 'localytics.directives']).controller('gatepassController', 
	function ($scope, $http, $interval, $filter) {
		$scope.items = [];

		$scope.sales_orders = [];
		$scope.credit_accounts = {
			"sales": []
		}
		$scope.debit_accounts = {
			"sales": []
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
			'sales': 0
		};
		
		$scope.accounts = [];
		$scope.account_types = [];
		
		$scope.errors = [];
		$scope.processing = false;
		
		$scope.petty_cash = {};
		$scope.daily_sale = {};
		

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
		$scope.wctAJAX = function( wctData, wctCallback ) {
			wctRequest = {
				method: 'POST',
				url: 'gatepass.php',
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
							
						}
						if( $scope.accounts[ i ].is_daily_sale == 1 ) {
							$scope.daily_sale = $scope.accounts[ i ];
						}
						
					}
				}
			});
			$scope.wctAJAX( {action: 'get_orders', dt: $scope.dt, module: 'sales'}, function( response ){
				$scope.sales_orders = response;
				$scope.update_credit_account( 'sales' );
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
			else {
				return $filter('filter')($scope[module+'_orders'], {status: 0});
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
    	angular.element(element).numpad();
  	};
});