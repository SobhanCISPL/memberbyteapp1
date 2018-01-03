'use strict';

/**
 * @ngdoc function
 * @name memberByteApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the memberByteApp
 */
 angular.module('memberByteApp')
 .controller('MainCtrl', function ($scope, $location, $mdSidenav, $mdToast, $rootScope, $route, $http) {

 	$scope.counter = 2;
		$scope.selectedIndex = 0; //set 0 index menu active by default

		$scope.appName = 'MemberByteApp';
		$scope.toggleLeft = buildToggler('left');

		function buildToggler(componentId) {
			return function () {
				$mdSidenav(componentId).toggle();
			}
		}

		$scope.open = function (state, index, menu_id) {
			/*menu active*/
			if ($scope.selectedIndex === 0) {
				$scope.selectedIndex = menu_id;
			}
			else if ($scope.selectedIndex === menu_id) {
				$scope.selectedIndex = menu_id;
			}
			else {
				$scope.selectedIndex = menu_id;
			}
			/*menu active*/
			$scope.toggleLeft();

			if(state === 'logout'){
				$http.get('logout')
				.then(function (response) {
					if(response.data.success === true){
						location.href = response.data.url;
					}
				});
			}else{
				$location.path(state);
			}
		}

		$scope.isActive = function (view) {
			return view === $location.path();
		}

		$scope.user = {
			name : 'Poushali Bose',
			email: 'poushali.bose@codeclouds.io',
			img	 : 'https://lh4.googleusercontent.com/-hmSiUy3319Y/AAAAAAAAAAI/AAAAAAAAABs/6LekJKbeMYc/photo.jpg?sz=50'
		}

		/**
		* NAME: name to show
		* ICON: icon to show
		* MENU_ID: menu id to identify menu fot active deactive
		* HREF: redirect url
		 **/
		$scope.submenu = [
		{
			'name' : 'Dashboard',
			'icon' : 'dashboard',
			'menu_id' : 0,
			'href' : 'dashboard',	
		},
		{
			'name' : 'My Orders',
			'icon' : 'shopping_cart',
			'menu_id' : 1,
			'href' : 'orders',	
		},
		{
			'name' : 'Order Tracking',
			'icon' : 'room',
			'menu_id' : 2,
			'href' : 'order_tracking',	
		},
		{
			'name' : 'Account Settings',
			'icon' : 'account_circle',
			'menu_id' : 3,
			'submenu' : [{
				'name' : 'Profile Information',
				'menu_id' : 30,
				'href' : 'profile'
			},	
			{
				'name' : 'Manage Addresses',
				'menu_id' : 31,
				'href' : ''
			}]	
		},
		{
			'name' : 'Logout',
			'icon' : 'power_settings_new',
			'menu_id' : 4,
			'href' : 'logout',
		}
		];
	});