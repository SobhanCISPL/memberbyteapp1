'use strict';
/**
 * @ngdoc service
 * @name memberByteApp.Dialog
 * @description
 * # Dialog
 * Factory in the memberByteApp.
 */
angular.module('memberByteApp')
	.factory('Dialog', function ($mdDialog, $http, $route, Toast, $location) {

		return {
			confirmationDialog: function ($url, title) {
				var confirm = $mdDialog.confirm()
					.title(title ? title : 'Delete')
					.textContent('Are you sure? This action cannot be undone')
					.ok('Yes')
					.cancel('No');
				return $mdDialog.show(confirm).then(function () {
					$http.post($url)
						.then(function (response) {
							if (response.data.success)
							{
								$route.reload();
								Toast.showToast(response.data.message ? response.data.message : 'Deleted successfully');
							}
							else
							{
								Toast.showToast(response.data.error_message);
							}
						});
				});
			},
			showCopyDialog: function ($url, $data, $redirect_url) {
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
		};
	});
