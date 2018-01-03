'use strict';
/**
 * @ngdoc function
 * @name memberByteApp.controller:DashboardCtrl
 * @description
 * # DashboardCtrl
 * Controller of the memberByteApp
 */
angular.module('memberByteApp').controller('OrdersCtrl', function(
    $scope, $http, $location, $mdDialog
) {
	$scope.orderss = [
	{id : 1,},
	{id : 1,},
	{id : 1,},
	{id : 1,},
	{id : 1,},
	];
	console.log($scope.orderss);
});