'use strict';
/**
 * @ngdoc function
 * @name memberByteApp.controller:ProfileInformationCtrl
 * @description
 * # ProfileInformationCtrl
 * Controller of the memberByteApp
 */
 angular.module('memberByteApp').controller('ProfileInformationCtrl', function(
 	$scope, $http, $location , $rootScope, Dialog, Toast
 	) {
 	var params, userDetails, url;
 	userDetails = $rootScope.user; 
 	
 	$scope.resetUser = function(){
 		$scope.user.name = $rootScope.user_name_backup;
 		$scope.user.email = $rootScope.user_email_backup;
 	}
 	$scope.SubmitForm = function(){
 		params = {
 			'old_email' : $rootScope.user_email_backup,
 			'submite_details' : $scope.user
 		};
 		url = 'user-edit';
 		Dialog.confirmationDialog(url, params, 'Edit Profile')
 		.then(function (response) {
 			if (response.success)
 			{
 				$rootScope.user = response.data[0];
 				$rootScope.user_email_backup = response.data[0].email;
 				Toast.showToast(response.message);
 			}
 			else
 			{
 				Toast.showToast(response.error_message);
 			}
 		});
 	}

 });