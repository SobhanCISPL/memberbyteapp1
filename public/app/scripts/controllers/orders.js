'use strict';
/**
 * @ngdoc function
 * @name memberByteApp.controller:OrdersCtrl
 * @description
 * # OrdersCtrl
 * Controller of the memberByteApp
 */
 angular.module('memberByteApp').controller('OrdersCtrl', function(
 	$scope, $http, $location, Toast, $mdDateRangePicker, $filter, Dialog, $rootScope, ApiDataFactory
 	) {
 	var params, startDate, endDate, order_detail;
 	var lessThan3Day = new Date();
 	var toDay = new Date();

 	$scope.loaded_content = true; // loader
 	
 	lessThan3Day.setDate(lessThan3Day.getDate() - 2);
 	$scope.customTemplates = [
 	{
 		name: 'Last 3 Days',
 		dateStart: lessThan3Day,
 		dateEnd: toDay,
 	}
 	];
 	$scope.pickerModel = { selectedTemplate: 'Last 3 Days' };
 	startDate = $filter('date')(lessThan3Day, 'MM/dd/yyyy');
 	endDate = $filter('date')(toDay, 'MM/dd/yyyy');

 	params = {
 			start_date : startDate,
 			end_date: endDate,
 			search_fields: {
 				'email' : $rootScope.user.email
 			},
 		};

 	$rootScope.$on('user-detail-fetched', function(){
 		console.log($rootScope.user.email);
 	});	
 	
 	$scope.orderFilter=function(){
 			startDate = $filter('date')($scope.pickerModel.dateStart, 'MM/dd/yyyy');
 			endDate = $filter('date')($scope.pickerModel.dateEnd, 'MM/dd/yyyy');
 			params.start_date = startDate;
 			params.end_date = endDate;
 			$scope.getOrders();
 		}

 	$scope.getOrders = function(){
 		$scope.loaded_content = true;
 		$scope.loader_circle = false;
 		$http.post('orders', params)
 		.then(function (response) {
 			$scope.loader_circle = true;
 			$scope.loaded_content = false;
 			if(response.data.success === false){
 				Toast.showToast(response.data.message);
 				return false;
 			}
 			$scope.orders = response.data.data.order_ids;
 			$scope.orders_detail = response.data.data.order_details;
 		});
 	}

 	$scope.getOrders();

 	$scope.viewOrder = function(order_id){
 		order_detail = $scope.orders_detail[order_id];
 		console.log(order_detail);
 		Dialog.showOrderViewDialog(order_id, order_detail);
 	} 

 	ApiDataFactory.getOrderOptions().then(function (data) {
 		$scope.options = data.settings;
		console.log(data);
	});

 });