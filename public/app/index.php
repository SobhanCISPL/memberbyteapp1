<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>MemberByte</title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width">
	<!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
	<!-- build:css(.) styles/vendor.css -->

	<!-- bower:css -->

	<link rel='stylesheet' href='bower_components/font-awesome/css/font-awesome.min.css'>
	<link rel='stylesheet' href='bower_components/textAngular/dist/textAngular.css'>
	<link rel='stylesheet' href='bower_components/md-date-range-picker/dist/md-date-range-picker.css'>
	<!-- endbower -->
	<!-- endbuild -->
	<!-- build:css(.tmp) styles/main.css -->
	<link rel="stylesheet" href="styles/main.css">
	<link rel="stylesheet" href="styles/material-icon.css">

	<!--Font-->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,400italic">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons"
	rel="stylesheet">
	<!-- Angular Material style sheet -->
	<link rel="stylesheet" href="bower_components/angular-material/angular-material.min.css">
	<style type="text/css">
	.ctrl-btns .md-icon-button {}
	[ng\:cloak], [ng-cloak], [data-ng-cloak], [x-ng-cloak], .ng-cloak, .x-ng-cloak {
		display: none !important;
	}
	.pull-right{
		text-align: right;
	}
	.zoom-body {
		zoom: 90%;
		/*background: red;*/
	}
	.f1f1f1 {
		background: #f1f1f1;
	}

</style>
<!-- endbuild -->

</head>
<body ng-app="memberByteApp" ng-controller="MainCtrl" class="zoom-body f1f1f1" ng-cloak>
		<!--[if lte IE 8]>
		  <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
		<![endif]-->

		<!---sidebar-->
		<md-sidenav class="md-sidenav-left md-whiteframe-z2"
		md-component-id="left" >

		<md-toolbar class="md-toolbar-header">
			<span flex></span>
			<div layout="row" class="md-toolbar-tools-bottom">
				<md-list>
					<md-list-item class="md-2-line ">
						<img ng-src="{{user.img}}" class="toolbar-avatar md-avatar" alt="" />
						<div class="md-list-item-text" layout="column">
							<div style="margin-top: 0px;font-size: 14px;font-weight: 600;">{{user.name}}</div>
							<div>{{user.email}}</div>
						</div>
					</md-list-item>
				</md-list>

			</div>
		</md-toolbar>

		<md-content>
			<md-list>
				<div ng-repeat="menu in submenu">
					<md-list-item
					class="md-2-line menu-list" ng-click="open(menu.href, $index, menu.menu_id)" ng-class="{selectedIndex: selectedIndex === menu.menu_id}" ng-if="!menu.submenu">
					<i class="material-icons menu-icon">{{menu.icon}}</i>
					<div class="md-list-item-text">
						<h3 class="menu-text-h3">{{menu.name}}</h3>
					</div>
				</md-list-item>
				<md-list-item
				class="md-2-line menu-list disabled-menu" ng-if="menu.submenu" ng-click="open()" ng-disabled="true">
				<i class="material-icons menu-icon">{{menu.icon}}</i>
				<div class="md-list-item-text">
					<h3 class="menu-text-h3">{{menu.name}}</h3>
				</div>
			</md-list-item>
			<md-list ng-if="menu.submenu">
				<div ng-repeat="submenu in menu.submenu">
					<md-list-item
					class="md-2-line menu-list" ng-click="open(submenu.href, $index, submenu.menu_id)" ng-class="{selectedIndex: selectedIndex === submenu.menu_id}">
					<i class="material-icons menu-icon">{{submenu.icon}}</i>
					<div class="md-list-item-text">
						<h3 class="submenu-text">{{submenu.name}}</h3>
					</div>
				</md-list-item>
			</div>
		</md-list>
	</div>
</md-list>
</md-content>
</md-sidenav>

<!---->
<div layout="column" style="height: 100%">
	<md-toolbar class="head-toolbar" ng-show="!showSearch">
		<div layout="row" flex layout-align="center center">
			<div flex="20">
				<div class="md-toolbar-tools">
					<md-button class="md-icon-button" aria-label="Side Panel" ng-click="toggleLeft()">
						<md-icon class="md-default-theme" class="material-icons">&#xE5D2;</md-icon>
					</md-button>
					<h2>{{page_title}}</h2>
				</div>
			</div>
			<div flex="60" layout="row" layout-align="end center">
			</div>
			<div flex="20"></div>
		</div>
	</md-toolbar>
	<md-content  class="f1f1f1" scroll>
		<div layout="row" layout-align="center center">
			<div ng-view flex="60"></div>
		</div>

		<div layout="row" layout-align="center" layout-padding>
			<br><br>
			<div>Created with <span style="color: #e611d4">❤</span> by CodeClouds</div>
		</div>
	</md-content>

</div>

<!-- build:js(.) scripts/vendor.js -->
<!-- bower:js -->
<script src="bower_components/jquery/dist/jquery.min.js"></script>
<script src="bower_components/angular/angular.min.js"></script>
<script src="bower_components/angular-animate/angular-animate.min.js"></script>
<script src="bower_components/angular-cookies/angular-cookies.min.js"></script>
<script src="bower_components/angular-resource/angular-resource.min.js"></script>
<script src="bower_components/angular-route/angular-route.min.js"></script>
<script src="bower_components/angular-sanitize/angular-sanitize.min.js"></script>
<script src='bower_components/textAngular/dist/textAngular-rangy.min.js'></script>
<script src='bower_components/textAngular/dist/textAngular-sanitize.min.js'></script>
<script src='bower_components/textAngular/dist/textAngular.min.js'></script>
<script src='bower_components/textAngular/dist/textAngular.min.js'></script>
<script src='bower_components/ngclipboard-master/dist/clipboard.min.js'></script>
<script src='bower_components/ngclipboard-master/dist/ngclipboard.js'></script>
<script src='bower_components/md-date-range-picker/dist/md-date-range-picker.js'></script>

<!--angularjs-material-->
<script src="bower_components/angular-material/angular-material.min.js"></script>
<script src="bower_components/angular-aria/angular-aria.min.js"></script>
<!-- endbower -->
<!-- endbuild -->
<script src="https://cdn.jsdelivr.net/angular-material-icons/0.4.0/angular-material-icons.min.js"></script>
<!-- build:js({.tmp,app}) scripts/scripts.js -->
<script src="scripts/app.js"></script>
<script src="scripts/controllers/main.js"></script>
<script src="scripts/controllers/dashboard.js"></script>
<script src="scripts/controllers/orders.js"></script>
<script src="scripts/controllers/tracking.js"></script>
<script src="scripts/services/toast.js"></script>
<!-- endbuild -->
</body>
</html>
