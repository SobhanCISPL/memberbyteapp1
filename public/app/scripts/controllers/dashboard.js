'use strict';
/**
 * @ngdoc function
 * @name memberByteApp.controller:DashboardCtrl
 * @description
 * # DashboardCtrl
 * Controller of the memberByteApp
 */
 angular.module('memberByteApp').controller('DashboardCtrl', function(
 	$scope, $http, $location, Toast, $mdDateRangePicker, $filter
 	) {
 	var params, startDate, endDate, orders, orders_detail;
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
 			'email' : 'poushali.bose@codeclouds.io'
 		},
 	}

 	$scope.orderFilter=function(){
 		startDate = $filter('date')($scope.pickerModel.dateStart, 'MM/dd/yyyy');
 		endDate = $filter('date')($scope.pickerModel.dateEnd, 'MM/dd/yyyy');
 		params.start_date = startDate;
 		params.end_date = endDate;
 		console.log(params);
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
 				Toast.showToast(response.data.error_message);
 				return false;
 			}
 			orders = $scope.orders = response.data.data.order_ids;
 			orders_detail = response.data.data.order_details;
 			console.log(orders_detail);
 		});
 	}

 	$scope.getOrders();
 	
 });