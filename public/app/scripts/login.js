angular.module('memberByteLoginApp', [
	'ngMaterial', 'ngSanitize', 'ngAnimate'
	])
.run(function ($http, $rootScope){
})
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
.factory('Dialog', function ($mdDialog) {
	return {
		alertDialog: function (text) {
			return $mdDialog.show(
				$mdDialog.alert()
				.parent(angular.element(document.querySelector('#popupContainer')))
				.clickOutsideToClose(true)
				.textContent(text)
				.ariaLabel('Alert')
				.ok('Okay!')
				);
		},
		confirmDialog: function (title,content) {
			return $mdDialog.show(
			$mdDialog.confirm()
			.title(title)
			.textContent(content)
			.ariaLabel('Confirmation')
			.ok('Okay')
			);
		}
	};
})
.factory('ApiDataFactory', function ($http, $q) {
	
	return {
		get: function () {
			var deferred = $q.defer();
			$http({
				method: 'POST',
				url: 'app/api-data',
			}).then(function (response) {
				deferred.resolve(response.data);
			})
			.catch(function (response) {
				deferred.reject(response);
			});
			return deferred.promise;
		}
	}
})

.controller('LoginIndexCtrl', function ($scope, $rootScope, $mdDialog, $http, $mdToast, Toast, Dialog, ApiDataFactory) {

	// ApiDataFactory.get().then(function (data) {
	// 	console.log(data);
	// 	$scope.themeSettings = data.data.themeSettings;
	// });
	
	if(ERROR !== '' && ERROR.success === false && ERROR.error_message != ''){
		Dialog.alertDialog(ERROR.error_message);
	}
	var userEmail = '', loginDetail = [];

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
						function DialogController($scope, $mdDialog, $location, $mdToast, $http, Toast, Dialog) {

							$scope.send_otp = function (ev) {

								if(!$scope.username){
									Toast.showToast(APP_MESSAGES.LOGIN.EMAIL_FIELD_BLANK);
									
									$scope.loader = false; // loader hide
									$scope.dialog = false; 

									return false;
								}else{
									userEmail = $scope.username;
									$scope.isDisabled = true;

									$scope.loader = true; // loader hide
									$scope.dialog = true; 

									$http.post('/check-user', {
										data:$scope.username
									}).then(function(response){
										if(response.data.success === false){
											Dialog.confirmDialog('Sorry!', response.data.error_message);
											$scope.isDisabled = false;
											return false;
										}
										loginDetail = response.data.data;
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
										/**/
									});
								}								
							}

							$scope.send_otp_verify = function (ev){
								if(!$scope.otp){
									Toast.showToast(APP_MESSAGES.OTP.BLANK);
									return false;
								}else{
									$scope.isDisabled = true;

									$scope.loader = true; // loader hide
									$scope.dialog = true;

									$http.post('/check-otp', 
									{
										'otp' : $scope.otp,
										'email' : userEmail
									}).then(function(response){

										if(response.data.success === false){
											Toast.showToast(response.data.error_message);
											$scope.isDisabled = false;
											$scope.loader = false; // loader hide
											$scope.dialog = false; 
											return false;
										}
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
									});
								}
							}

							$scope.change_pw = function (ev) {
								if(!$scope.new_password && !$scope.confirm_password){
									Toast.showToast(APP_MESSAGES.PASSWORD.BLANK);
									return false;
								}else{
									console.log(loginDetail);
									var pw = $scope.new_password;
									var confirm_pw = $scope.confirm_password;
									var user_email_id = email_id;

									if(pw != confirm_pw){
										Toast.showToast(APP_MESSAGES.PASSWORD.WARNING);
										return false;
									}else{
										$scope.isDisabled = true;
										$scope.loader = true; // loader hide
										$scope.dialog = true; 

										$http.post('/change-password',{
											data:{
												"confirm_pw" : confirm_pw,
												"user_email_id" : user_email_id,
												"login_detail" : loginDetail,
											}
										}).then(function(response){
											if(response.data.success == true){
												Dialog.confirmDialog('Confirmation', response.data.message);
												$scope.isDisabled = false;
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

									if(response.data.success == false){
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