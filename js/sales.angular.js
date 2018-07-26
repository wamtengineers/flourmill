angular.module('sales', ['ngAnimate', 'angularMoment', 'ui.bootstrap', 'angularjs-datetime-picker','localytics.directives']).controller('salesController',
	function ($scope, $http, $interval, $filter) {
		$scope.customers = [];
		$scope.errors = [];
		$scope.accounts = [];
		$scope.categories = [];
		angular.copy($scope.accounts);
		$scope.processing = false;
		$scope.sales_id = 0;
		$scope.petty_cash = {};
		$scope.items = {
			id: "",
			item_id: "",
			title: "",
		};
		$scope.sales = {
			id: 0,
			datetime_added: '',
			customer_id: '',
			less_weight: '',
			items: [],
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
			$scope.sales.datetime_added = $(".angular-datetimepicker").val();
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
			if( $scope.sales_id > 0 ) {
				$scope.wctAJAX( {action: 'get_sales', id: $scope.sales_id}, function( response ){
					$scope.sales = response;
				});
			}
			else {
				$scope.wctAJAX( {action: 'get_datetime'}, function( response ){
					$scope.sales.datetime_added = JSON.parse( response );
				});
				$scope.sales.items.push( angular.copy( $scope.item ) );
			}
		});
		
		$scope.get_action = function(){
			if( $scope.sales_id > 0 ) {
				return 'Edit';
			}
			else {
				return 'Add New';
			}
		}
		
		$scope.add = function( position ){
			$scope.sales.items.splice(position+1, 0, angular.copy( $scope.item ) );
			$scope.update_grand_total();
		}
		
		$scope.remove = function( position ){
			if( $scope.sales.items.length > 1 ){
				$scope.sales.items.splice( position, 1 );
			}
			else {
				$scope.sales.items = [];
				$scope.sales.items.push( angular.copy( $scope.item ) );
			}
			$scope.update_grand_total();
		}	
		$scope.update_total = function( position ) {
			var quantity = parseFloat( $scope.sales.items[ position ].quantity?$scope.sales.items[ position ].quantity:0 );
			
			$scope.sales.items[ position ].total = ( parseFloat( $scope.sales.items[ position ].packing ) * parseFloat( $scope.sales.items[ position ].unit_price )) * quantity;
			$scope.update_grand_total();
		}
		$scope.update_price = function( position ) {
			var id = $scope.sales.items[ position ].item_id
            var item = $filter('filter')($scope.items, {id: id}, true );
            if( item.length > 0 ) {
                item = item[0];
                $scope.sales.items[ position ].item_id = item.id;
                $scope.sales.items[ position ].unit_price = item.unit_price;
				$scope.update_total(position);
            }
		}	
        $scope.update_grand_total = function(){
			total = 0;
			quantity = 0;
			for( i = 0; i < $scope.sales.items.length; i++ ) {
				total += parseFloat( $scope.sales.items[ i ].total );
				quantity += parseFloat( $scope.sales.items[ i ].quantity?$scope.sales.items[ i ].quantity:0 );
			}
			$scope.sales.total = total;
			$scope.sales.quantity = quantity;
			$scope.update_net_total();
		}
		$scope.update_net_total = function(){
			$scope.sales.net_total = parseFloat( $scope.sales.total ) - parseFloat( $scope.sales.discount );
		}
		$scope.total_items = function(){
			total = 0;
			for( i = 0; i < $scope.sales.items.length; i++ ) {
				total += Number( $scope.sales.items[ i ].quantity );
			}
			return total;
		}
		$scope.grand_total = function(){
			total = 0;
			for( i = 0; i < $scope.sales.items.length; i++ ) {
				total += Number( $scope.sales.items[ i ].total );
			}
			return total;
		}
		$scope.wctAJAX = function( wctData, wctCallback ) {
			wctData.tab = 'addedit';
			wctRequest = {
				method: 'POST',
				url: 'sales_manage.php',
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
		$scope.save_sale = function () {
			$scope.errors = [];
			if( $scope.processing == false ){
				$scope.processing = true;
				data = {action: 'save_sale', sales: JSON.stringify( $scope.sales )};
                console.log(data);
				$scope.wctAJAX( data, function( response ){
					$scope.processing = false;
					if( response.status == 1 ) {
						window.location.href='sales_manage.php?tab=addedit&id='+response.id;
					}
					else{
						$scope.errors = response.error;
					}
				});
			}
		}
	}
);