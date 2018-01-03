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
			templateUrl: 'views/orders.html',
			controller: 'OrdersCtrl',
			controllerAs: 'orders',
			title: 'Profile'
		})
		.when('/order_tracking', {
			templateUrl: 'views/order_tracking.html',
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
		.primaryPalette('blue', {
			'default': '800'
		})
		.accentPalette('blue', {
			'default': '500'
		});

})
.run(function ($rootScope, $location, $http, $route, $window) {
	
	$rootScope.goBack = function () {
		history.back();
	};

	$rootScope.$on('$routeChangeSuccess', function (event, current, previous) {
		if (current.hasOwnProperty('$$route')) {
			$rootScope.page_title = current.$$route.title;
		}
	});

})
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
});
