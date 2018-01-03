<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>MemberByte</title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width">
	<!-- Angular Material style sheet -->
	<link rel="stylesheet" href="app/bower_components/angular-material/angular-material.min.css">
	<link rel='stylesheet' href='app/bower_components/font-awesome/css/font-awesome.min.css'>
	<!-- endbower -->
	<!-- endbuild -->
	<!-- build:css(.tmp) styles/main.css -->
	<link rel="stylesheet" href="app/styles/material-icon.css">
	<link rel="stylesheet" href="app/styles/main.css">

	<!--Font-->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,400italic">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons"
	rel="stylesheet">
</head>
<body ng-app="memberByteLoginApp" ng-controller="LoginIndexCtrl" class="zoom-body f1f1f1">
		<!--[if lte IE 8]>
		  <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
		<![endif]-->

		<!---->
		<div layout="column" class="login-div" layout-align="center center">

			<h1 class="md-headline">MemberByte</h1>
			<md-content class="f1f1f1 login-md-content" md-whiteframe="3">
				<md-list flex>
					<md-subheader class="md-no-sticky">Choose an option to authenticate</md-subheader>
					<md-list-item class="md-2-line"  ng-click="loginWithGoogle()">
						<i flex="10" class="material-icons">&#xE326;</i>

						<div class="md-list-item-text" layout="column">
							<h3>Google</h3>
							<h4>Login with a Google account</h4>
						</div>
					</md-list-item>
					<md-divider></md-divider>
					<md-list-item class="md-2-line"  ng-click="loginWithFacebook()">
						<i flex="10" class="material-icons">&#xE326;</i>

						<div class="md-list-item-text" layout="column">
							<h3>Facebook</h3>
							<h4>Login with a Facebook account</h4>
						</div>
					</md-list-item>
					<md-divider></md-divider>
					<md-list-item class="md-2-line"  ng-click="loginWithPassword()">
						<i flex="10" class="material-icons">&#xE88D;</i>

						<div class="md-list-item-text" layout="column">
							<h3>Basic</h3>
							<h4>Login with username/password combination</h4>
						</div>
					</md-list-item>
				</md-list>
			</md-content>

		</div>

		<!-- build:js(.) scripts/vendor.js -->
		<!-- bower:js -->
		<script src="app/bower_components/angular/angular.min.js"></script>
		<script src="app/bower_components/angular-animate/angular-animate.min.js"></script>
		<script src="app/bower_components/angular-sanitize/angular-sanitize.min.js"></script>

		<script src="app/ng-sweet-alert.js"></script>
		<script src="app/SweetAlert.min.js"></script>
		<link rel="stylesheet" href="app/sweet-alert.css">

		<!--angularjs-material-->
		<script src="app/bower_components/angular-material/angular-material.min.js"></script>
		<script src="app/bower_components/angular-aria/angular-aria.min.js"></script>
		<!--angularjs-script-->
		<script src="app/scripts/login.js"></script>

		<link rel='stylesheet' href='//cdnjs.cloudflare.com/ajax/libs/angular-loading-bar/0.9.0/loading-bar.min.css' type='text/css' media='all' />
		
 		<script type='text/javascript' src='//cdnjs.cloudflare.com/ajax/libs/angular-loading-bar/0.9.0/loading-bar.min.js'></script>

	</body>
	</html>