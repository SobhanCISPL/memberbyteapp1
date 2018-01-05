'use strict';
/**
 * @ngdoc service
 * @name memberByteApp.Dialog
 * @description
 * # Dialog
 * Factory in the memberByteApp.
 */
 angular.module('memberByteApp')
 .factory('Dialog', function ($mdDialog, $http, $route, Toast, $location, $q) {

 	function orderViewDialogController($scope, $mdDialog, order_id, order_detail) {
 		$scope.order_id = order_id;
 		$scope.order_detail = order_detail;
 		$scope.customer_details = {
 			'Customer Id' : order_detail.customer_id,
 			'First Name' : order_detail.first_name,
 			'Last Name' : order_detail.last_name,
 			'Email Address' : order_detail.email_address,
 			'Customers Telephone' : order_detail.customers_telephone,
 		};
 		$scope.billing_details = {
 			'First Name' : order_detail.billing_first_name,
 			'Last Name' : order_detail.billing_last_name,
 			'City' : order_detail.billing_city,
 			'Country' : order_detail.billing_country,
 			'Postcode' : order_detail.billing_postcode,
 			'State' : order_detail.billing_state,
 			'Street address 1' : order_detail.billing_street_address,
 			'Street address 2' : (order_detail.billing_street_address2 != "") ?  order_detail.billing_street_address2 : 'N/A',
 		};
 		$scope.shipping_details = {
 			'First Name' : order_detail.shipping_first_name,
 			'Last Name' : order_detail.shipping_last_name,
 			'City' : order_detail.shipping_city,
 			'Country' : order_detail.shipping_country,
 			'Postcode' : order_detail.shipping_postcode,
 			'State' : order_detail.shipping_state,
 			'Street address 1' : order_detail.shipping_street_address,
 			'Street address 2' : (order_detail.shipping_street_address2 != "") ?  order_detail.shipping_street_address2 : 'N/A',
 			'Shipping Date' : order_detail.shipping_date,
 		};
 		$scope.other_details = {
 			'Campaign ID' : order_detail.campaign_id,
 			'Order Confirmation' : order_detail.order_confirmed,
 			'Total Ammount' : order_detail.order_total,
 			'Shippable' : (order_detail.shippable === "1") ? 'YES' : 'NO',
 			'Upsell Product Quantity' : (order_detail.upsell_product_quantity !== "") ? order_detail.upsell_product_quantity : 'N/A',
 			'Tracking Number' : (order_detail.tracking_number !== "") ? order_detail.tracking_number : 'N/A' ,
 		}
 		$scope.products = order_detail.products;

 		$scope.cancel = function () {
 			$mdDialog.cancel();
 		};
 	}

 	return {
 		confirmationDialog: function ($url, params = null, title) {
 			var deferred = $q.defer();
 			var confirm = $mdDialog.confirm()
 			.title(title ? title : 'Delete')
 			.textContent('Are you sure? This action cannot be undone')
 			.ok('Yes')
 			.cancel('No');
 			return $mdDialog.show(confirm).then(function () {
 				var request = params != null ?  $http.post($url, params) : $http.post($url);
 				request
 				.then(function (response) {
 					deferred.resolve(response.data);
 				})
 				.catch(function (response) {
 					deferred.reject(response);
 				});
 				return deferred.promise;
 			});
 		},
 		rowCopyDialog: function ($url, $data, $redirect_url) {
 			var confirm = $mdDialog.confirm()
 			.title('Copy')
 			.textContent('Are you sure?')
 			.ok('Yes')
 			.cancel('No');

 			return $mdDialog.show(confirm).then(function () {
 				$http.post($url, $data)
 				.then(function (response) {
 					if (response.data.success)
 					{
 						$location.path($redirect_url + response.data.data.id);
 						Toast.showToast('Copied successfully');
 					}
 					else
 					{
 						Toast.showToast(response.data.error_message);

 					}
 				});

 			});
 		},
 		alertDialog: function (content) {
 			return $mdDialog.show(
 				$mdDialog.alert()
 				.parent(angular.element(document.querySelector('#popupContainer')))
 				.clickOutsideToClose(true)
 				.textContent(content)
 				.ariaLabel('Alert')
 				.ok('Got it!')
 				);
 		},
 		showOrderViewDialog: function (order_id, order_detail) {
 			return $mdDialog.show({
 				controller: orderViewDialogController,
 				templateUrl: 'views/order-view-dialog.html',
 				parent: angular.element(document.body),
 				locals : {
 					order_id : order_id,
 					order_detail : order_detail
 				},
 				clickOutsideToClose: true,
					//fullscreen: $scope.customFullscreen // Only for -xs, -sm breakpoints.
				});
 		},
 	};
 });
