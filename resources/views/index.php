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

	<script src="app/bower_components/jquery/dist/jquery.min.js"></script>
	<script src="app/scripts/initial.js"></script>

	<script type="text/javascript">
		APP_MESSAGES = <?php echo json_encode(__('messages'));?>;
		ERROR = <?php echo (isset($data) ? json_encode($data) : "''");?>;
	</script>

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
					<md-list-item class="md-2-line" ng-click="loginWithGoogle()">
						<md-icon md-svg-src="app/images/google-icon.svg" style="margin-right: 20px;"></md-icon>
						<div class="md-list-item-text" layout="column">
							<h3>Google</h3>
							<div class="hint">Login with a Google account</div>
						</div>
					</md-list-item>
					<md-divider></md-divider>
					<md-list-item class="md-2-line"  ng-click="loginWithFacebook()">
						<md-icon md-svg-src="app/images/facebook-icon.svg" style="margin-right: 20px;"></md-icon>
						<div class="md-list-item-text" layout="column">
							<h3>Facebook</h3>
							<div class="hint">Login with a Facebook account</div>
						</div>
					</md-list-item>
					<md-divider></md-divider>
					<md-list-item class="md-2-line"  ng-click="loginWithPassword()">
						<md-icon class="material-icons" style="font-size: 28px;color: #FFB300;margin-right: 20px;">&#xE88D;</md-icon>
						<div class="md-list-item-text" layout="column">
							<h3>Basic</h3>
							<div class="hint">Login with username/password combination</div>
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
		
		<!--angularjs-material-->
		<script src="app/bower_components/angular-material/angular-material.min.js"></script>
		<script src="app/bower_components/angular-aria/angular-aria.min.js"></script>
		<!--angularjs-script-->
		<script src="app/scripts/login.js"></script>
	</body>
	</html>