'use strict';

/**
 * @ngdoc service
 * @name memberByteApp.Toast
 * @description
 * # Toast
 * Factory in the memberByteApp.
 */
angular.module('memberByteApp')
	.factory('Toast', function ($mdToast) {
		return {
			showToast: function (text) {
				return $mdToast.show(
					$mdToast.simple()
					.textContent(text)
					.position('bottom right')
					.hideDelay(3000)
					);
			}
		};
	});
