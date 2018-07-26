angular.module('purchase', ['ngAnimate', 'angularMoment', 'ui.bootstrap', 'angularjs-datetime-picker', 'localytics.directives']).controller('purchaseController', 
	function ($scope, $http, $interval, $filter, $window) {
		$scope.items = [];
		$scope.units = [];
		$scope.suppliers = [];
		$scope.accounts = [];
		$scope.errors = [];
		$scope.processing = false;
		$scope.purchase_id = 0;
		$scope.numberMask= "";
		$scope.purchase = {
			id: 0,
			datetime_added: '',
			supplier_id: '',
			items: [],
			discount: 0,
			supplier_payment_id: 0,
			payment_account_id: "",
			payment_amount: 0,
		};
		$scope.item = {
			"id": 0,
			"item_id": "",
			"unit_price": "",
			"quantity": "",
			"total": ""
		};
		$scope.updateDate = function(){
			$scope.purchase.datetime_added = $(".angular-datetimepicker").val();
			$scope.$apply();
		}
		angular.element(document).ready(function () {
			$scope.wctAJAX( {action: 'get_accounts'}, function( response ){
				$scope.accounts = response;
			});
			$scope.wctAJAX( {action: 'get_items'}, function( response ){
				$scope.items = response;
			});
			$scope.wctAJAX( {action: 'get_suppliers'}, function( response ){
				$scope.suppliers = response;
			});
			if( $scope.purchase_id > 0 ) {
				$scope.wctAJAX( {action: 'get_purchase', id: $scope.purchase_id}, function( response ){
					$scope.purchase = response;
				});
			}
			else {
				$scope.wctAJAX( {action: 'get_datetime'}, function( response ){
					$scope.purchase.datetime_added = JSON.parse( response );
				});
				$scope.purchase.items.push( angular.copy( $scope.item ) );
			}
		});
		$scope.get_action = function(){
			if( $scope.purchase_id > 0 ) {
				return 'Edit';
			}
			else {
				return 'Add New';
			}
		}
		
		$scope.add = function( position ){
			$scope.purchase.items.splice(position+1, 0, angular.copy( $scope.item ) );
		}
		
		$scope.remove = function( position ){
			if( $scope.purchase.items.length > 1 ){
				$scope.purchase.items.splice( position, 1 );
			}
			else {
				$scope.purchase.items = [];
				$scope.purchase.items.push( angular.copy( $scope.item ) );
			}
		}	
		$scope.update_total = function( position ) {
			$scope.purchase.items[ position ].total = Number( $scope.purchase.items[ position ].unit_price ) * Number( $scope.purchase.items[ position ].quantity );
		}
		$scope.calc_unit_price = function( position ){
			$scope.purchase.items[ position ].unit_price = Number( $scope.purchase.items[ position ].total ) / Number( $scope.purchase.items[ position ].quantity );
		}
		$scope.total_items = function(){
			total = 0;
			for( i = 0; i < $scope.purchase.items.length; i++ ) {
				total += Number( $scope.purchase.items[ i ].quantity );
			}
			return total;
		}
		$scope.grand_total = function(){
			console.log(total);
			total = 0;
			for( i = 0; i < $scope.purchase.items.length; i++ ) {
				total += Number( $scope.purchase.items[ i ].total );
			}
			return total;
		}
		$scope.update_payment_amount = function(){
			$scope.purchase.payment_amount = $scope.grand_total()-Number( $scope.purchase.discount );
		}
		$scope.wctAJAX = function( wctData, wctCallback ) {
			wctData.tab = 'addedit';
			wctRequest = {
				method: 'POST',
				url: 'purchase_manage.php',
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
		$scope.save_purchase = function () {
			$scope.errors = [];
			if( $scope.processing == false ){
				$scope.processing = true;
				data = {action: 'save_purchase', purchase: JSON.stringify( $scope.purchase )};
				$scope.wctAJAX( data, function( response ){
					$scope.processing = false;
					if( response.status == 1 ) {
						window.location.href='purchase_manage.php?tab=addedit&id='+response.id;
					}
					else{
						$scope.errors = response.error;
					}
				});
			}
		}
	}
);