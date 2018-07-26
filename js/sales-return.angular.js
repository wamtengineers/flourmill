angular.module('salesreturn', ['ngAnimate', 'angularMoment', 'ui.bootstrap', 'angularjs-datetime-picker','localytics.directives']).controller('salesreturnController',
	function ($scope, $http, $interval, $filter) {
		$scope.customers = [];
		$scope.errors = [];
		$scope.accounts = [];
		$scope.categories = [];
		angular.copy($scope.accounts);
		$scope.processing = false;
		$scope.sales_return_id = 0;
		$scope.petty_cash = {};
		$scope.items = {
			id: "",
			item_id: "",
			title: "",
		};
		$scope.sales_return = {
			id: 0,
			datetime_added: '',
			customer_id: '',
			items: [],
			less_weight: '',
			quantity: 0,
			total: 0,
			discount: 0,
			net_total: 0,
			customer_payment_id: 0,
			payment_account_id: $scope.petty_cash.id,
			payment_amount: 0
		};
		$scope.item = {
			"id": "",
			"item_id":"",
			"packing":"",
			"unit_price": "",
			"quantity": 0,
			"total": 0
		};
		$scope.updateDate = function(){
			$scope.sales_return.datetime_added = $(".angular-datetimepicker").val();
			$scope.$apply();
		}
		angular.element(document).ready(function () {
			$scope.wctAJAX( {action: 'get_accounts'}, function( response ){
				$scope.accounts = response;
				for( i = 0; i < $scope.accounts.length; i++ ) {
					if( $scope.accounts[ i ].is_petty_cash == 1 ) {
						$scope.petty_cash = $scope.accounts[ i ];
					}
				
				}
				
			});
			$scope.wctAJAX( {action: 'get_customers'}, function( response ){
				$scope.customers = response;
			});
			$scope.wctAJAX( {action: 'get_items'}, function( response ){
				$scope.items = response;
			});
			if( $scope.sales_return_id > 0 ) {
				$scope.wctAJAX( {action: 'get_sales_return', id: $scope.sales_return_id}, function( response ){
					$scope.sales_return = response;
				});
			}
			else {
				$scope.wctAJAX( {action: 'get_datetime'}, function( response ){
					$scope.sales_return.datetime_added = JSON.parse( response );
				});
				$scope.sales_return.items.push( angular.copy( $scope.item ) );
			}
		});
		
		$scope.get_action = function(){
			if( $scope.sales_return_id > 0 ) {
				return 'Edit';
			}
			else {
				return 'Add New';
			}
		}
		
		$scope.add = function( position ){
			$scope.sales_return.items.splice(position+1, 0, angular.copy( $scope.item ) );
			$scope.update_grand_total();
		}
		
		$scope.remove = function( position ){
			if( $scope.sales_return.items.length > 1 ){
				$scope.sales_return.items.splice( position, 1 );
			}
			else {
				$scope.sales_return.items = [];
				$scope.sales_return.items.push( angular.copy( $scope.item ) );
			}
			$scope.update_grand_total();
		}	
		$scope.update_total = function( position ) {
			var quantity = parseFloat( $scope.sales_return.items[ position ].quantity?$scope.sales_return.items[ position ].quantity:0 );
			
			$scope.sales_return.items[ position ].total = ( parseFloat( $scope.sales_return.items[ position ].packing ) * parseFloat( $scope.sales_return.items[ position ].unit_price )) * quantity;
			$scope.update_grand_total();
		}
        $scope.update_grand_total = function(){
			total = 0;
			quantity = 0;
			for( i = 0; i < $scope.sales_return.items.length; i++ ) {
				total += parseFloat( $scope.sales_return.items[ i ].total );
				quantity += parseFloat( $scope.sales_return.items[ i ].quantity?$scope.sales_return.items[ i ].quantity:0 );
			}
			$scope.sales_return.total = total;
			$scope.sales_return.quantity = quantity;
			$scope.update_net_total();
		}
		$scope.update_net_total = function(){
			$scope.sales_return.net_total = parseFloat( $scope.sales_return.total ) - parseFloat( $scope.sales_return.discount );
		}
		$scope.total_items = function(){
			total = 0;
			for( i = 0; i < $scope.sales_return.items.length; i++ ) {
				total += Number( $scope.sales_return.items[ i ].quantity );
			}
			return total;
		}
		$scope.grand_total = function(){
			total = 0;
			for( i = 0; i < $scope.sales_return.items.length; i++ ) {
				total += Number( $scope.sales_return.items[ i ].total );
			}
			return total;
		}
		$scope.wctAJAX = function( wctData, wctCallback ) {
			wctData.tab = 'addedit';
			wctRequest = {
				method: 'POST',
				url: 'sales_return_manage.php',
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
		$scope.save_sale_return = function () {
			$scope.errors = [];
			if( $scope.processing == false ){
				$scope.processing = true;
				data = {action: 'save_sale_return', sales_return: JSON.stringify( $scope.sales_return )};
                console.log(data);
				$scope.wctAJAX( data, function( response ){
					$scope.processing = false;
					if( response.status == 1 ) {
						window.location.href='sales_return_manage.php?tab=addedit&id='+response.id;
					}
					else{
						$scope.errors = response.error;
					}
				});
			}
		}
	}
);