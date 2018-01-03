angular.module('memberByteLoginApp', [
	'ngSanitize','ngMaterial', 'oitozero.ngSweetAlert', 'angular-loading-bar'
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

.config(['cfpLoadingBarProvider', function(cfpLoadingBarProvider) {
	cfpLoadingBarProvider.includeSpinner = true;
	cfpLoadingBarProvider.includeBar = true;
}])


.controller('LoginIndexCtrl', function ($scope, $mdDialog, $http, $mdToast, SweetAlert) {
var email_id={};
				// Google login
				$scope.loginWithGoogle = function () {
					$http.post('login', {type:"google"})
					.then(function (response) {
						// console.log(response);
						if(response.data.success === false){
							console.log(response.data.error_message);
							// Toast.showToast('Something went wrong');
							return false;
						}
						location.href = response.data.authUrl;
					});
				}

				// Facebook login by sobhan 27-12-17
				$scope.loginWithFacebook = function () {
					$http.post('login', {type:"facebook"}).then(function(response){
						console.log(response);
						if(response.data.success === false){
							console.log(response.data.error_message);
							return false;
						}
						location.href = response.data.authUrl;

					});
				}

				// Basic login with OTP, EMAIL, CHANGE PW
				$scope.loginWithPassword = function () {
					console.log('basic');
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
						function DialogController($scope, $mdDialog, $location, $mdToast, $http, SweetAlert) {
							$scope.send_otp = function () {

								if(!$scope.username){

									$mdToast.show(
								      $mdToast.simple()
								        .textContent("Usename can't be left blank!")
								        .position("bottom right ")
								        .hideDelay(3000)
								    );
									return false;
								}else{
									$scope.isDisabled = true;

									// $cfpLoadingBarProvider.includeSpinner = true;

									$http.post('/check_user', {
										data:$scope.username
									}).then(function(response){
										if(response.data.param == '100' || response.data.param == 200 || response.data.param == 300){
											$scope.isDisabled = false;

											$mdDialog.show({
												title: 'OTP Varification',	
												parent: parentEl,
												targetEvent: $event,
												templateUrl: 'app/views/otp_varification.html',
												locals: {
													otp: $scope.otp,
												},
												controller: DialogController
											});
										}
									});
								}								
							}

							$scope.send_otp_varify = function (){
								if(!$scope.otp){
									$mdToast.show(
						      		$mdToast.simple()
								        .textContent("OTP field can't be left blank!")
								        .position("bottom right ")
								        .hideDelay(3000)
								    );
									return false;
								}else{
									$scope.isDisabled = true;

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
												templateUrl: 'app/views/set_password.html',
												locals: {
													new_password: $scope.new_password,
													confirm_password: $scope.confirm_password
												},
												controller: DialogController
											});
										}

										if(response.data.param == 404){
											alert ("Please provide valid OTP");
											$scope.isDisabled = false;
											return false;
										}
									});
								}
							}

							$scope.change_pw = function () {
								if(!$scope.new_password && !$scope.confirm_password){
									$mdToast.show(
						      		$mdToast.simple()
								        .textContent("Password & Confirm can't be left blank!")
								        .position("bottom right ")
								        .hideDelay(3000)
								    );
									return false;
								}else{
									var pw = $scope.new_password;
									var confirm_pw = $scope.confirm_password;
									var user_email_id = email_id;

									if(pw  != confirm_pw){
										$mdToast.show(
							      		$mdToast.simple()
									        .textContent("Password & Confirm Password should be same!")
									        .position("bottom right ")
									        .hideDelay(3000)
									    );
										return false;
									}else{
										$scope.isDisabled = true;

										$http.post('/change_password',{
											data:{
												"confirm_pw": confirm_pw,
												"user_email_id": user_email_id
											}
										}).then(function(response){
											console.log(response);
											if(response.data.param == 100){
												SweetAlert.swal({   
													title: "Thank You",   
													text: response.data.message,   
													type: "success",     
													confirmButtonColor: "#DD6B55",   
													confirmButtonText: "OK"
												},  function(){ 
													$scope.isDisabled = false; 
													window.location.reload();
													
												});
											}

											if(response.data.param == 200){
												SweetAlert.swal({   
													title: "Thank You",   
													text: response.data.message,   
													type: "success",     
													confirmButtonColor: "#DD6B55",   
													confirmButtonText: "OK"
												},  function(){ 
													$scope.isDisabled = false; 
													window.location.reload();
													
												});
											}

											if(response.data.param == 333){
												SweetAlert.swal({   
													title: "Thank You",   
													text: response.data.error_message,   
													type: "warning",     
													confirmButtonColor: "#DD6B55",   
													confirmButtonText: "OK"
												},  function(){ 
													$scope.isDisabled = false; 
													window.location.reload();
													
												});
											}
										});
									}
								}
							}

							//Basic Login Authenticate

							$scope.loginFormSubmit = function (){

								$http.post('/basic-login', {
									data:{
										email : $scope.username,
										pw : $scope.password
									}
								}).then(function(response){

									if(response.data.success == 'True'){
										var url = response.data.url;

										location.href = url;
									}

									if(response.data.success == 'False'){
										SweetAlert.swal({   
											title: "Oh!",   
											text: response.data.error_message,   
											type: "warning",     
											confirmButtonColor: "#DD6B55",   
											confirmButtonText: "OK"
										},  function(){ 
											$scope.isDisabled = false; 
											// window.location.reload();
											
										});
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