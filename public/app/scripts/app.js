'use strict';
/**
 * @ngdoc overview
 * @name memberByteApp
 * @description
 * # memberByteApp
 *
 * Main module of the application.
 */
 angular.module('memberByteApp', [
 	'ngAnimate', 'ngCookies', 'ngResource', 'ngRoute', 'ngSanitize',
 	'ngMaterial', 'ngclipboard', 'ngMdIcons', 'ngMaterialDateRangePicker',
 	])
 .config(function ($routeProvider, $provide, $mdThemingProvider) {
 	$routeProvider
 	.when('/dashboard', {
 		templateUrl: 'views/dashboard.html',
 		controller: 'DashboardCtrl',
 		controllerAs: 'dashboard',
 		title: 'Dashboard'
 	})
 	.when('/orders', {
 		templateUrl: 'views/orders.html',
 		controller: 'OrdersCtrl',
 		controllerAs: 'orders',
 		title: 'My Orders'
 	})
 	.when('/profile', {
 		templateUrl: 'views/profile-information.html',
 		controller: 'ProfileInformationCtrl',
 		controllerAs: 'profile',
 		title: 'Profile'
 	})
 	.when('/manage-addresses', {
 		templateUrl: 'views/manage-addresses.html',
 		controller: 'ManageAddressesCtrl',
 		controllerAs: 'manage-addresses',
 		title: 'Manage Addresses'
 	})
 	.when('/address-add', {
 		templateUrl: 'views/add-addresses.html',
 		controller: 'ManageAddressesCtrl',
 		controllerAs: 'manage-addresses',
 		title: 'Manage Addresses'
 	})
 	.when('/order-tracking', {
 		templateUrl: 'views/order-tracking.html',
 		controller: 'OrderTrackingCtrl',
 		controllerAs: 'orderstrack',
 		title: 'Order Tracking'
 	}) 	
 	.otherwise({
 		redirectTo: '/dashboard',
 		title: 'dashboard'
 	});

 	$mdThemingProvider
 	.theme('default')
 	.primaryPalette('amber', {
 		'default': '600'
 	})
 	.accentPalette('yellow', {
 		'default': '500'
 	});

 })
 .run(function ($rootScope, $location, $http, $route, $window) {
 	$rootScope.goBack = function () {
		history.back();
	};

 	$rootScope.$on('$routeChangeSuccess', function (event, current, previous) {
 		if (current.hasOwnProperty('$$route')) {
 			$rootScope.page_title = current.$$route.title; // page title set
 		}
 	});

 	$http.post('user')
 	.then(function (response) {
 		$rootScope.user = response.data.data[0];
 		$rootScope.user_email_backup = response.data.data[0].email;
 		$rootScope.user_name_backup = response.data.data[0].name;
 		$rootScope.$broadcast('user-detail-fetched');
 	});

 })
 //'scroll' directive for shadow effect on scroll
 .directive("scroll", function ($window) { 
 	return function (scope, element, attrs) {
 		var head = angular.element(document.getElementsByClassName("head-toolbar"));
 		element.on("scroll", function () {
 			var scrollTop = element.scrollTop();
 			if (scrollTop == 0) {
 				head.removeClass('dropShadow');
 			} else {
 				head.addClass('dropShadow');
 			}
 		});
 	};
 })
  //merchant data from 201clicks
.factory('ApiDataFactory', function ($http, $q) {
	
	return {
		getOrderOptions: function () {
			var deferred = $q.defer();
			$http({
				method: 'POST',
				url: 'api-data/order-options',
			}).then(function (response) {
				deferred.resolve(response.data);
			})
			.catch(function (response) {
				deferred.reject(response);
			});
			return deferred.promise;
		}
	}
});
