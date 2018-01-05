'use strict';
/**
 * @ngdoc function
 * @name memberByteApp.controller:OrderTrackingCtrl
 * @description
 * # OrderTrackingCtrl
 * Controller of the memberByteApp
 */
 angular.module('memberByteApp').controller('OrderTrackingCtrl', function(
 	$scope, $http, $location, Toast, $mdDateRangePicker, $filter
 	) {
 	$scope.default_preview = true; // default preview  show
 	$scope.loaded_content = true; // loaded content hide
 	$scope.track = function(tracking_id){
 		$scope.default_preview = false;
 		$scope.loader_circle = true;
 		if(tracking_id){
 			$scope.loader_circle = false;
 			$scope.loaded_content = false;
 		}
 		else{
 			Toast.showToast('Please Enter Tracking Id');
 			$scope.initialViewSet();
 			return false;
 		}
 	}

 	//clear tracking id input
 	$scope.$watch("tracking_id", function(newValue, oldValue){
 		if(!newValue){
 			$scope.initialViewSet();
 			return false;
 		}
 	});

 	$scope.initialViewSet = function(){
 		$scope.default_preview = true; // default preview show
 			$scope.loader_circle = false; // loader hide
 			$scope.loaded_content = true; // loaded content hide
 		}
 	});