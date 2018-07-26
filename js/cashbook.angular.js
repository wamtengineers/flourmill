angular.module('cashbook', ['ngAnimate', 'angularMoment', 'localytics.directives']).controller('cashbookController', 
	function ($scope, $http, $interval, $filter) {

		$scope.customers = [];
		$scope.suppliers = [];
		$scope.expenses = [];
		$scope.transactions = [];
		$scope.wheat_purchases = [];
		$scope.cash_purchases = [];
		$scope.customer_payments = [];
		$scope.supplier_payments = [];
		$scope.errors = [];
		$scope.processing = false;
		$scope.user_input = {};
		$scope.new_expense = {
			"details": "",
			"amount": "",
			"account_id": "",
			"expense_category_id": ""
		};
		$scope.new_wheat_purchase = {
			"gross_weight": "",
			"deduction_weight": "",
			"net_weight": "",
			"wheat_price": ""
		};
		$scope.new_transaction = {
			"account_id": "",
			"reference_id": "",
			"amount": "",
			"details": ""
		};
		$scope.customer_payments = [];
		$scope.new_customer_payment_placeholder = {
			"customer_id": "",
			"details": "",
			"amount": "",
			"account_id": ""
		}
		$scope.new_customer_payment = {
			"supplier_id": "",
			"amount": "",
			"account_id": "",
			"details": ""
		};
		$scope.new_supplier_payment = {
			"supplier_id": "",
			"amount": "",
			"account_id": "",
			"details": ""
		};
		$scope.accounts = [];
		$scope.petty_cash = {};
		$scope.expense_categories = [];
		
		$scope.save_order = function () {
			if( $scope.processing == false ){
				$scope.processing = true;
				data = {action: 'save_order', order: JSON.stringify( $scope.new_order )};
				$scope.wctAJAX( data, function( response ){
					$scope.processing = false;
					if( response.status == 1 ) {
						$scope.orders.unshift( response.order );
						if( typeof response.wheat_purchase !== 'undefined' ) {
							$scope.wheat_purchases.unshift( response.wheat_purchase );
						}
						$scope.print_receipt( response.order.id )
						$scope.new_order = {
							"id": "",
							"discount": 0,
							"account_id": "0",
							"customer_id": "",
							"pisai_wheat": 0,
							"pisai_cash_balance": false,
							"items": []	
						};
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
		$scope.wctAJAX( {action: 'get_customers'}, function( response ){
			$scope.customers = response;
			for( i = 0; i < $scope.customers.length; i++ ) {
				if( $scope.customers[ i ].is_daily_sale == "1" ) {
					$scope.daily_sale = $scope.customers[ i ];
					$scope.new_order.customer_id = $scope.daily_sale.id
					$scope.new_customer_payment_placeholder.customer_id = $scope.daily_sale.id
					break;
				}
			}
		});
		$scope.wctAJAX( {action: 'get_suppliers'}, function( response ){
			$scope.suppliers = response;
		});
		$scope.wctAJAX( {action: 'get_expense'}, function( response ){
			$scope.expenses = response;
		});
		$scope.wctAJAX( {action: 'get_customer_payment'}, function( response ){
			$scope.customer_payments = response;
		});
		$scope.wctAJAX( {action: 'get_supplier_payment'}, function( response ){
			$scope.supplier_payments = response;
		});
		$scope.wctAJAX( {action: 'get_cash_purchase'}, function( response ){
			$scope.wheat_purchases = response;
		});
		$scope.wctAJAX( {action: 'get_accounts'}, function( response ){
			$scope.accounts = response;
			for( i = 0; i < $scope.accounts.length; i++ ) {
				if( $scope.accounts[ i ].is_petty_cash == 1 ) {
					$scope.petty_cash = $scope.accounts[ i ];
					$scope.new_order.account_id = $scope.petty_cash.id
					$scope.new_order = angular.copy( $scope.new_order );
					break;
				}
			}
		});
		$scope.wctAJAX( {action: 'get_expense_category'}, function( response ){
			$scope.expense_categories = response;
		});
		$scope.wctAJAX( {action: 'get_transaction'}, function( response ){
			$scope.transactions = response;
		});
		$scope.add_expense = function(){
			if( $scope.processing == false ) {
				if( $scope.new_expense.expense_category_id == "" || $scope.new_expense.account_id == "" || $scope.new_expense.amount <= 0 ){
					alert("Enter Expense Category, Account and Amount.");
				}
				else{
					$scope.processing = true;
					$scope.wctAJAX( {action: 'add_expense', expense: JSON.stringify($scope.new_expense)}, function( response ){
						$scope.processing = false;
						if( response.status == 1 ) {
							$scope.new_expense = {
								"details": "",
								"amount": 0,
								"account_id": "",
								"expense_category_id": ""
							};
							$scope.expenses.unshift(response.expense);
						}
						else{
							alert(response.message);
						}
					});	
				}
			}
		}
		$scope.expense_total = function( type ) {
			total = 0;
			for( i = 0; i < $scope.expenses.length; i++ ) {
				if( typeof type === 'undefined' || ( type == 0 && $scope.expenses[ i ].account_id == $scope.petty_cash.id ) || ( type != 0 && $scope.expenses[ i ].account_id != $scope.petty_cash.id ) ) {
					total += parseFloat($scope.expenses[ i ].amount);
				}
			}
			return total;
		}
		$scope.add_customer_payment = function(){
			if( $scope.processing == false ) {
				if( $scope.new_customer_payment.customer_id == "" || $scope.new_customer_payment.account_id == "" || $scope.new_customer_payment.amount <= 0 ){
					alert("Enter Customer, Account and Amount.");
				}
				else{
					$scope.processing = true;
					$scope.wctAJAX( {action: 'add_customer_payment', customer_payment: JSON.stringify($scope.new_customer_payment)}, function( response ){
						$scope.processing = false;
						if( response.status == 1 ) {
							$scope.new_customer_payment = {
								"customer_id": "",
								"amount": "",
								"account_id": "",
								"details": ""
							};
							$scope.customer_payments.unshift(response.customer_payment);
						}
						else{
							alert(response.message);
						}
					});	
				}
			}
		}
		$scope.customer_payment_total = function(type) {
			total = 0;
			for( i = 0; i < $scope.customer_payments.length; i++ ) {
				if( typeof type === 'undefined' || ( type == 0 && $scope.customer_payments[ i ].account_id == $scope.petty_cash.id ) || ( type != 0 && $scope.customer_payments[ i ].account_id != $scope.petty_cash.id ) ) {
					total += parseFloat($scope.customer_payments[ i ].amount);
				}
			}
			return total;
		}
		$scope.add_supplier_payment = function(){
			if( $scope.processing == false ) {
				if( $scope.new_supplier_payment.supplier_id == "" || $scope.new_supplier_payment.account_id == "" || $scope.new_supplier_payment.amount <= 0 ){
					alert("Enter Supplier, Account and Amount.");
				}
				else{
					$scope.processing = true;
					$scope.wctAJAX( {action: 'add_supplier_payment', supplier_payment: JSON.stringify($scope.new_supplier_payment)}, function( response ){
						$scope.processing = false;
						if( response.status == 1 ) {
							$scope.new_supplier_payment = {
								"supplier_id": "",
								"amount": "",
								"account_id": "",
								"details": ""
							};
							$scope.supplier_payments.unshift(response.supplier_payment);
						}
						else{
							alert(response.message);
						}
					});	
				}
			}
		}
		$scope.supplier_payment_total = function(type) {
			total = 0;
			for( i = 0; i < $scope.supplier_payments.length; i++ ) {
				if( typeof type === 'undefined' || ( type == 0 && $scope.supplier_payments[ i ].account_id == $scope.petty_cash.id ) || ( type != 0 && $scope.supplier_payments[ i ].account_id != $scope.petty_cash.id ) ) {
					total += parseFloat($scope.supplier_payments[ i ].amount);
				}
			}
			return total;
		}
		$scope.add_transaction = function(){
			if( $scope.processing == false ) {
				$scope.new_transaction.amount=parseFloat($scope.new_transaction.amount);
				if( $scope.new_transaction.account_id == "" || $scope.new_transaction.reference_id == "" ||  $scope.new_transaction.amount<= 0 ){
					alert("Select Accounts and Amount.");
				}
				else{
					$scope.processing = true;
					$scope.wctAJAX( {action: 'add_transaction', transaction: JSON.stringify($scope.new_transaction)}, function( response ){
						$scope.processing = false;
						if( response.status == 1 ) {
							$scope.new_transaction = {
								"account_id": "",
								"reference_id": "",
								"amount": "",
								"details": ""								
							};
							$scope.transactions.unshift(response.transaction);
						}
						else{
							alert(response.message);
						}
					});	
				}
			}
		}
		$scope.transaction_total = function(account_id, balance) {
			total = balance;
			for( i = 0; i < $scope.transactions.length; i++ ) {
				if( $scope.transactions[ i ].account_id == account_id ) {
					total -= parseFloat($scope.transactions[ i ].amount);
				}
				if( $scope.transactions[ i ].reference_id == account_id ) {
					total += parseFloat($scope.transactions[ i ].amount);
				}
			}
			return total;
		}
		$scope.add_wheat_purchase = function(){
			if( $scope.processing == false ) {
				if( $scope.new_wheat_purchase.gross_weight == "" ){
					alert("Enter Gross Weight");
				}
				else{
					$scope.processing = true;
					$scope.wctAJAX( {action: 'add_wheat_purchase', wheat_purchase: JSON.stringify($scope.new_wheat_purchase)}, function( response ){
						$scope.processing = false;
						if( response.status == 1 ) {
							$scope.new_wheat_purchase = {
								"gross_weight": "",
								"deduction_weight": "",
								"net_weight": "",
								"wheat_price": ""
							};
							$scope.wheat_purchases.unshift(response.wheat_purchase);
						}
						else{
							alert(response.message);
						}
					});	
				}
			}
		}
		$scope.wheat_purchase_total = function() {
			total = 0;
			for( i = 0; i < $scope.wheat_purchases.length; i++ ) {
				total += parseFloat($scope.wheat_purchases[ i ].amount);
			}
			return total;
		}
		$scope.wheat_purchase_gross_weight = function() {
			total = 0;
			for( i = 0; i < $scope.wheat_purchases.length; i++ ) {
				total += parseFloat($scope.wheat_purchases[ i ].gross_weight);
			}
			return total;
		}
		$scope.wheat_purchase_net_weight = function() {
			total = 0;
			for( i = 0; i < $scope.wheat_purchases.length; i++ ) {
				total += parseFloat($scope.wheat_purchases[ i ].net_weight);
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
    });