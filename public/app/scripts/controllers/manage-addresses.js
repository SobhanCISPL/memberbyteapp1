'use strict';
/**
 * @ngdoc function
 * @name memberByteApp.controller:ManageAddressesCtrl
 * @description
 * # ManageAddressesCtrl
 * Controller of the memberByteApp
 */
angular.module('memberByteApp').controller('ManageAddressesCtrl', function(
    $scope, $http, $location, $mdDialog, $rootScope
) {
	$scope.addresses = [{
		id : '1',
		city : 'test',
		country : 'US',
		zip : '12345',
		state : 'AL',
		my_address : 'test'
	}];
});