angular.module('memberByteLoginApp', [
	'ngMaterial', 'ngSanitize'
	])
.config(function ($mdThemingProvider) {
	$mdThemingProvider
	.theme('default')
	.primaryPalette('blue', {
		'default': '800'
	})
	.accentPalette('blue', {
		'default': '800'
	});
})
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
})

.controller('LoginIndexCtrl', function ($scope, $mdDialog, $http, $mdToast, Toast) {

	if(ERROR !== '' && ERROR.success === false && ERROR.error_message != ''){
		Toast.showToast(ERROR.error_message);
	}

	$scope.thirdPartyLogin = function(login_driver){
		$http.post('login', {type:login_driver})
		.then(function (response) {
			if(response.data.success === false){
				Toast.showToast(response.data.error_message);
				return false;
			}
			location.href = response.data.authUrl;
		});
	}
				// Google login
				$scope.loginWithGoogle = function () {
					$scope.thirdPartyLogin("google");
				}
				// Facebook login  by sobhan
				$scope.loginWithFacebook = function () {
					$scope.thirdPartyLogin("facebook");
				}

				// Basic login with OTP, EMAIL, CHANGE PW by sobhan
				$scope.loginWithPassword = function () {
					$scope.showDialog = function($event) {
						var parentEl = angular.element(document.body);
						$mdDialog.show({
							title: 'Authentication Require',	
							parent: parentEl,
							targetEvent: $event,
							templateUrl: 'app/views/login-dialog.html',
							locals: {
								username: $scope.username,
								password: $scope.password
							},
							controller: DialogController
						});
						function DialogController($scope, $mdDialog, $location, $mdToast, $http, Toast) {

							$scope.send_otp = function (ev) {
								if(!$scope.username){
									Toast.showToast("Usename can't be left blank!");
									
									$scope.loader = false; // loader hide
									$scope.dialog = false; 

									return false;
								}else{
									$scope.isDisabled = true;

									$scope.loader = true; // loader hide
									$scope.dialog = true; 

									$http.post('/check_user', {
										data:$scope.username
									}).then(function(response){
										if(response.data.param == '100' || response.data.param == 200 || response.data.param == 300){
											$scope.isDisabled = false;

											$mdDialog.show({
												title: 'OTP Varification',	
												parent: parentEl,
												targetEvent: $event,
												templateUrl: 'app/views/otp-varification.html',
												locals: {
													otp: $scope.otp,
												},
												controller: DialogController
											});
										}

										if(response.data.param == 400) {
											var confirm = $mdDialog.confirm()
										          .title('Sorry!')
										          .textContent(response.data.error_message)
										          .ariaLabel('Lucky day')
										          .targetEvent(ev)
										          .ok('Okay');

										    $mdDialog.show(confirm).then(function() {
										    	$scope.isDisabled = false; 
										      	location.reload();
										    });
										}
									});
								}								
							}

							$scope.send_otp_varify = function (ev){
								if(!$scope.otp){
									Toast.showToast("OTP field can't be left blank!");
									return false;
								}else{
									$scope.isDisabled = true;

									$scope.loader = true; // loader hide
									$scope.dialog = true;

									$http.post('/check_otp', {
										data:$scope.otp
									}).then(function(response){

										if(response.data.param == "100"){
											$scope.isDisabled = false;

											email_id = response.data.user_details[0].email_id;

											$mdDialog.show({
												title: 'Generate Password',	
												parent: parentEl,
												targetEvent: $event,
												templateUrl: 'app/views/set-password.html',
												locals: {
													new_password: $scope.new_password,
													confirm_password: $scope.confirm_password
												},
												controller: DialogController
											});
										}

										if(response.data.param == 404){
											Toast.showToast("Please provide valid OTP");
											$scope.isDisabled = false;

											$scope.loader = false; // loader hide
 											$scope.dialog = false; 

											return false;
										}
									});
								}
							}

							$scope.change_pw = function (ev) {
								if(!$scope.new_password && !$scope.confirm_password){
									Toast.showToast("Password & Confirm can't be left blank!");
									return false;
								}else{
									var pw = $scope.new_password;
									var confirm_pw = $scope.confirm_password;
									var user_email_id = email_id;

									if(pw  != confirm_pw){
										Toast.showToast("Password & Confirm Password should be same!");
										return false;
									}else{
										$scope.isDisabled = true;
										$scope.loader = true; // loader hide
										$scope.dialog = true; 


										$http.post('/change_password',{
											data:{
												"confirm_pw": confirm_pw,
												"user_email_id": user_email_id
											}
										}).then(function(response){
											if(response.data.param == 100){
												
												var confirm = $mdDialog.confirm()
											          .title('Confirmation!')
											          .textContent(response.data.message)
											          .ariaLabel('Lucky day')
											          .targetEvent(ev)
											          .ok('Okay');

											    $mdDialog.show(confirm).then(function() {
											    	$scope.isDisabled = false; 
											      	location.reload();
											    });
											}

											if(response.data.param == 200){

												var confirm = $mdDialog.confirm()
											          .title('Confirmation!')
											          .textContent(response.data.message)
											          .ariaLabel('Lucky day')
											          .targetEvent(ev)
											          .ok('Okay');

											    $mdDialog.show(confirm).then(function() {
											    	$scope.isDisabled = false; 
											      	location.reload();
											    });
											}

											if(response.data.param == 333){

												var confirm = $mdDialog.confirm()
											          .title('Sorry!')
											          .textContent(response.data.error_message)
											          .ariaLabel('Lucky day')
											          .targetEvent(ev)
											          .ok('Okay');

											    $mdDialog.show(confirm).then(function() {
											    	$scope.isDisabled = false; 
											      	location.reload();
											    });
											}
										});
									}
								}
							}

							//Basic Login Authenticate

							$scope.loginFormSubmit = function (ev){
								$scope.loader = true; // loader hide
								$scope.dialog = true; 

								$http.post('/basic-login', {
									data:{
										email : $scope.username,
										pw : $scope.password
									}
								}).then(function(response){

									if(response.data.success == true){
										var url = response.data.url;

										location.href = url;
									}

									if(response.data.param == 404){
										Toast.showToast(response.data.error_message);
										$scope.isDisabled = false;

										$scope.loader = false; // loader hide
										$scope.dialog = false; 

										return false;
									}
								});
							}

							$scope.closeDialog = function() {
								$mdDialog.hide();
							}
						}
					}
					$scope.showDialog();
				};

			});