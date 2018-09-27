angular.module('addedit', ['ngAnimate', 'angularMoment', 'ui.bootstrap', 'angularjs-datetime-picker','localytics.directives']).controller('addeditController',
	function ($scope, $http, $interval, $filter) {
		$scope.errors = [];
		$scope.accounts = [];
		$scope.account_types = [];
		angular.copy($scope.accounts);
		$scope.processing = false;
		$scope.addedit_id = 0;
		$scope.petty_cash = {};
		$scope.items = {
			id: "",
			title: "",
		};
		$scope.addedit = {
			id: 0,
			datetime_added: '',
			account_id: '0',
			status: '1',
			bill_no: '',
			items: [],
			less_weight: '',
			discount: '',
			brokery_id: 0,
			broker_id: '0',
			broker_amount: '',
			cnf: '0',
			fare_transaction_id: 0,
			fare_of_vehicle: '',
			fare_of_vehicle_payment_account_id: '0',
			transaction_id: 0,
			payment_account_id: '0',
			payment_amount: '',
		};
		$scope.item = {
			"id": "",
			"item_id":"",
			"packing":"",
			"unit_price": "",
			"rate": "0",
			"less_weight": "",
			"quantity": "",
			"total": ""
		};
		$scope.updateDate = function(){
			$scope.addedit.datetime_added = $(".angular-datetimepicker").val();
			$scope.$apply();
		}
		angular.element(document).ready(function () {
			$scope.wctAJAX( {action: 'get_accounts'}, function( response ){
				$scope.accounts = response.accounts;
				$scope.account_types = response.account_types;
				for( i = 0; i < $scope.accounts.length; i++ ) {
					if( $scope.accounts[ i ].is_petty_cash == 1 ) {
						$scope.petty_cash = $scope.accounts[ i ];
						$scope.addedit.payment_account_id = $scope.petty_cash.id
						//$scope.addedit.payment_account_id = $scope.petty_cash.id
					}
				}
				
			});
			$scope.wctAJAX( {action: 'get_items'}, function( response ){
				$scope.items = response;
			});
			if( $scope.addedit_id > 0 ) {
				$scope.wctAJAX( {action: 'get_addedit', id: $scope.addedit_id}, function( response ){
					$scope.addedit = response;
				});
			}
			else {
				$scope.wctAJAX( {action: 'get_datetime'}, function( response ){
					$scope.addedit.datetime_added = JSON.parse( response );
				});
				$scope.addedit.items.push( angular.copy( $scope.item ) );
			}
		});
		
		$scope.get_action = function(){
			if( $scope.addedit_id > 0 ) {
				return 'Edit';
			}
			else {
				return 'Add New';
			}
		}
		$scope.get_title = function(){
			if( $manage_url==['sales_manage.php'] ) {
				return 'Sales';
			}
			else if( $manage_url==['sales_return_manage.php'] ) {
				return 'Sales Return';
			}
			else if( $manage_url==['purchase_manage.php'] ) {
				return 'Purchase';
			}
			else if( $manage_url==['purchase_return_manage.php'] ) {
				return 'Purchase Return';
			}
		}
		
		$scope.add = function( position ){
			$scope.addedit.items.splice(position+1, 0, angular.copy( $scope.item ) );
			$scope.update_grand_total();
		}
		
		$scope.remove = function( position ){
			if( $scope.addedit.items.length > 1 ){
				$scope.addedit.items.splice( position, 1 );
			}
			else {
				$scope.addedit.items = [];
				$scope.addedit.items.push( angular.copy( $scope.item ) );
			}
			$scope.update_grand_total();
		}	
		$scope.update_total = function( position ) {
			var quantity = parseFloat( $scope.addedit.items[ position ].quantity?$scope.addedit.items[ position ].quantity:0 );
			if( $scope.addedit.items[ position ].rate == 1 ) {
				quantity -= Number( $scope.addedit.items[ position ].less_weight );
			}
			$scope.addedit.items[ position ].total = ( parseFloat( $scope.addedit.items[ position ].unit_price )) * quantity;
			$scope.update_grand_total();
		}
        $scope.update_grand_total = function(){
			total = 0;
			quantity = 0;
			for( i = 0; i < $scope.addedit.items.length; i++ ) {
				total += parseFloat( $scope.addedit.items[ i ].total );
				quantity += parseFloat( $scope.addedit.items[ i ].quantity?$scope.addedit.items[ i ].quantity:0 );
			}
			$scope.addedit.total = total;
			$scope.addedit.quantity = quantity;
			$scope.update_net_total();
		}
		$scope.update_net_total = function(){
			$scope.addedit.net_total = parseFloat( $scope.addedit.total ) - parseFloat( $scope.addedit.discount );
		}
		$scope.total_items = function(){
			total = 0;
			for( i = 0; i < $scope.addedit.items.length; i++ ) {
				var weight = Number( $scope.addedit.items[ i ].quantity ) - Number( $scope.addedit.items[ i ].less_weight );
				if( $scope.addedit.items[ i ].rate == "0" ){
					weight *= $scope.addedit.items[ i ].packing;
				}
				total += weight;
			}
			return total;
		}
		$scope.grand_total = function(){
			total = 0;
			for( i = 0; i < $scope.addedit.items.length; i++ ) {
				total += Number( $scope.addedit.items[ i ].total );
			}
			return total;
		}
		$scope.wctAJAX = function( wctData, wctCallback ) {
			wctData.tab = 'addedit';
			wctRequest = {
				method: 'POST',
				url: $manage_url,
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
		$scope.save_addedit = function () {
			$scope.errors = [];
			if( $scope.processing == false ){
				$scope.processing = true;
				data = {action: 'save_addedit', addedit: JSON.stringify( $scope.addedit )};
                console.log(data);
				$scope.wctAJAX( data, function( response ){
					$scope.processing = false;
					if( response.status == 1 ) {
						window.location.href=$manage_url+'?tab=addedit&id='+response.id;
					}
					else{
						$scope.errors = response.error;
					}
				});
			}
		}
	}
);