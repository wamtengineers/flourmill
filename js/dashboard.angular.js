angular.module('dashboard', ['ngAnimate', 'angularMoment', 'localytics.directives']).controller('dashboardController', 
	function ($scope, $http, $interval, $filter) {
		$scope.categories = [];
		$scope.orders = [];
		$scope.order_filter = {
			type: "",
			status: 1
		}
		$scope.current_tab = 0;
		$scope.show_tab = function( tab ){
			$scope.current_tab = tab
		}
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
		$scope.item_number = "";
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
		$scope.active_category = 0;
		$scope.active_subcategory = [];
		$scope.new_order = {
			"id": "",
			"discount": 0,
			"account_id": "",
			"pisai_wheat": 0,
			"pisai_cash_balance": false,
			"payment_amount": 0,
			"customer_id": "",
			"items": []	
		};
		
		$scope.change_active_category = function( index ) {
			$scope.active_category = index;
		}
		$scope.change_active_subcategory = function( parent_index, index ) {
			$scope.active_subcategory[parent_index] = index;
		}
		
		$scope.order_item_update = function( product, variation_id ) {
			result = $scope.order_item_add( product, variation_id, $scope.user_input[ product.id ] );
			if( typeof result !== 'undefined' ) {
				$scope.user_input[ product.id ]
			}
		}
		
		$scope.order_item = function( product_id, variation_id ) {
			var search_parameters = {id: product_id}
			if( typeof variation_id !== 'undefined' ) {
				search_parameters.variation_id = variation_id;
			}
			if( $filter('filter')($scope.new_order.items, search_parameters).length > 0 ) {
				return $filter('filter')($scope.new_order.items, search_parameters)[0].quantity;
			}
			else{
				return 0;
			}
		}
		
		$scope.order_item_add = function( product, variation, qty, rtn ) {
			search_parameters = {id: product.id}
			if( typeof variation !== 'undefined' ) {
				search_parameters.variation_id = variation.id;
				variation_id = variation.id;
				unit_price = variation.price;
			}
			else{
				variation_id = 0;
				unit_price = product.unit_price;
			}
			if( typeof qty === 'undefined' ) {
				quantity = 1;
			}
			else {
				quantity = parseFloat( qty );
			}
			
			if( variation_id > 0 && variation.quantity >= quantity || variation_id == 0 && product.quantity >= quantity || product.type==2 || 1 ) {
				if( quantity > 0 ){
					if( $filter('filter')($scope.new_order.items, search_parameters).length > 0 ) {
						if( typeof qty !== 'undefined' ) {
							if( product.type!=2 ) {
								product.quantity+=$filter('filter')($scope.new_order.items, search_parameters)[0].quantity
							}
							$filter('filter')($scope.new_order.items, search_parameters)[0].quantity = quantity;				
						}
						else {
							$filter('filter')($scope.new_order.items, search_parameters)[0].quantity++;
						}
					}
					else{
						var title = product.title;
						if( variation_id > 0 ) {
							title += " (";
							for( var i=0; i < variation.attributes.length; i++ ){
								title += " "+variation.attributes[i].attribute_value;
							}
							title += " )";
						}
						$scope.new_order.items.push({id: product.id, variation_id: variation_id, title: title, unit_price: unit_price, quantity: quantity, type: product.type});
					}
					if( product.type!=2 ) {
						product.quantity-=quantity;
						if( variation_id > 0 ) {
							variation.quantity-=quantity;
						}
					}
				}
				else{
					if( $filter('filter')($scope.new_order.items, search_parameters).length > 0 ) {
						index = jQuery.map( $scope.new_order.items, function( obj, i ) {
							if( obj.id == product.id && obj.variation_id == variation_id ) {
								return i;
							}
						});
						$scope.new_order.items.splice( index, 1 );
					}
				}
			}
			else {
				if( typeof qty === 'undefined' ) {
					alert( "No more items available." );
				}
				else {
					return variation_id > 0?variation.quantity:product.quantity;
				}
			}	
		}
		$scope.order_item_remove = function( product, variation ) { 
		  	search_parameters = {id: product.id}
			if( typeof variation !== 'undefined' ) {
				search_parameters.variation_id = variation.id;
				variation_id = variation.id;
			}
			else{
				variation_id = 0;
			}
			if( $filter('filter')($scope.new_order.items, search_parameters).length > 0 ) {
				$filter('filter')($scope.new_order.items, search_parameters)[0].quantity--;
				if( $filter('filter')($scope.new_order.items, search_parameters)[0].quantity == 0 ){
					index = jQuery.map( $scope.new_order.items, function( obj, i ) {
						if( obj.id == product.id && obj.variation_id == variation_id ) {
							return i;
						}
					});
					$scope.new_order.items.splice( index, 1 );
					product.quantity++;
					if( variation_id > 0 ) {
						variation.quantity++;
					}
				}
			}
		}
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
		$scope.wctAJAX( {action: 'get_products'}, function( response ){
			$scope.categories = response;
		});
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
		$scope.wctAJAX( {action: 'get_orders'}, function( response ){
			$scope.orders = response;
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
		$scope.order_total_items = function( order ) {
			if( typeof order === "undefined" ){
				order = $scope.new_order;
			}
			total = 0;
			for( i = 0; i < order.items.length; i++ ) {
				total += order.items[ i ].quantity;
			}
			return total;
		}
		$scope.order_total = function( order ) {
			if( typeof order === "undefined" ){
				order = $scope.new_order;
			}
			total = 0;
			for( i = 0; i < order.items.length; i++ ) {
				if( typeof order.pisai_wheat === "undefined" || parseFloat(order.pisai_wheat)==0 || (parseFloat(order.pisai_wheat)>0 && order.items[ i ].type==2 && !order.pisai_cash_balance) ) {
					total += (parseFloat(order.items[ i ].unit_price) * parseFloat(order.items[ i ].quantity));
				}
			}
			if( typeof order.discount !== "undefined" ){
				total -= parseFloat(order.discount);
			}
			return total;
		}
		$scope.orders_total_items = function( orders ) {
			total = 0;
			for( i = 0; i < orders.length; i++ ) {
				for( j = 0; j < orders[i].items.length; j++ ) {
					total += orders[ i ].items[ j ].quantity;
				}
			}
			return total;
		}
		$scope.orders_total_kg = function( orders, category_id ) {
			total = 0;
			for( i = 0; i < orders.length; i++ ) {
				for( j = 0; j < orders[i].items.length; j++ ) {
					if( orders[ i ].items[ j ].category == category_id ) {
						total += orders[ i ].items[ j ].unit;
					}
				}
			}
			return total;
		}
		$scope.orders_item_total = function( orders, category_id ) {
			total = 0;
			for( i = 0; i < orders.length; i++ ) {
				for( j = 0; j < orders[i].items.length; j++ ) {
					if( orders[ i ].items[ j ].category == category_id ) {
						total = orders[ i ].items[ j ].unit_price * orders[ i ].items[ j ].quantity;
					}
				}
			}
			return total;
		}
		$scope.orders_total_product_kg = function( orders, product_id ) {
			total = 0;
			for( i = 0; i < orders.length; i++ ) {
				for( j = 0; j < orders[i].items.length; j++ ) {
					if( orders[ i ].items[ j ].id == product_id ) {
						total += orders[ i ].items[ j ].quantity;
					}
				}
			}
			return total;
		}
		$scope.orders_total_product_price = function( orders, product_id ) {
			total = 0;
			for( i = 0; i < orders.length; i++ ) {
				for( j = 0; j < orders[i].items.length; j++ ) {
					if( orders[ i ].items[ j ].id == product_id ) {
						total += orders[ i ].items[ j ].unit_price * orders[ i ].items[ j ].quantity;
					}
				}
			}
			return total;
		}
		$scope.orders_total = function( orders, type ) {
			total = 0;
			for( i = 0; i < orders.length; i++ ) {
				if( typeof type === 'undefined' || ( type == 0 && orders[ i ].account_id == "0" ) || ( type != 0 && orders[ i ].account_id != "0" ) ) {
					for( j = 0; j < orders[i].items.length; j++ ) {
						
							total += (parseFloat(orders[ i ].items[ j ].unit_price) * parseFloat(orders[ i ].items[ j ].quantity));
					}
					if( typeof orders[ i ].discount !== "undefined" ){
						total -= parseFloat(orders[ i ].discount);
					}
				}
			}
			return total;
		}
		$scope.cash_in_hand = function(){
			var total = $scope.transaction_total( $scope.petty_cash.id, $scope.petty_cash.balance );
			total += $scope.orders_total($scope.orders, 0);
			total += $scope.customer_payment_total(0);
			total -= $scope.supplier_payment_total(0);
			total -= $scope.expense_total(0);
			total -= $scope.wheat_purchase_total(0);
			return total;
		}
		$scope.print_receipt = function( id ) {
			$("<iframe>")
				.hide()
				.attr("src", "index.php?tab=print_receipt&id="+id)
				.appendTo("body"); 
		}
		$scope.add_item_with_id = function(){
			if( $scope.item_number != "" ){
				$scope.errors = [];
				id = parseInt($scope.item_number);
				for( var i = 0; i < $scope.categories.length; i++ ) {
					for( var j = 0; j < $scope.categories[i].categories.length; j++ ) {
						for( var k = 0; k < $scope.categories[i].categories[j].categories.length; k++ ) {
							for( var l = 0; l < $scope.categories[i].categories[j].categories[k].products.length; l++ ) {
								if( id > 10000 && $scope.categories[i].categories[j].categories[k].products[l].type == 1 ) {
									var v_id = id - 10000;
									for( var m = 0; m < $scope.categories[i].categories[j].categories[k].products[l].variations.length; m++ ) {
										if( v_id == $scope.categories[i].categories[j].categories[k].products[l].variations[m].id ) {
											$scope.item_number = '';
											$scope.order_item_add( $scope.categories[i].categories[j].categories[k].products[l], $scope.categories[i].categories[j].categories[k].products[l].variations[m] );
											return;
										}
									}
								}
								else {
									if( id == $scope.categories[i].categories[j].categories[k].products[l].id ) {
										$scope.item_number = '';
										$scope.order_item_add( $scope.categories[i].categories[j].categories[k].products[l] );
										return;
									}
								}		
							}
						}					
					}
				}
				$scope.errors.push( "No item found." );
			}
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