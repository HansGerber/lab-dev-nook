<?php
	session_start();

	$baseUrl = "http://lab.dev-nook.de/test/js/facebook/login/phpsdk/";
	
	if(isset($_SESSION["fb_user"])){
		
		$action = @$_POST["action"];
		
		//
		// logged in template - start
		//
		
		switch($action){
			case 'logout':
			
				header("location: $baseUrl");
				unset($_SESSION["fb_user"]);
				echo "logging you out ...";
			
			break;
			default:
			
				?>
					<form method="post" class="logoutForm">
						<input type="hidden" name="action" value="logout">
						<input type="submit" value="logout">
					</form>
					<h1>Welcome!</h1>
					<strong>User Data</strong>
					<div>
						<?php echo json_encode($_SESSION["fb_user"]["user_info"]); ?>
					</div>
				<?php
			
			break;
		}
		
		//
		// logged in template - end
		//
		
	} else {
		
		require_once "../../vendor/autoload.php";
		
		$fb = new \Facebook\Facebook([
			'app_id' => '1265075126870179',
			'app_secret' => 'bdabdd3134f01acd1fa742897321a7b7',
			'default_graph_version' => 'v2.10',
		]);
		
		$helper = $fb->getRedirectLoginHelper();
		
		$code = @$_GET["code"];
	
		if($code){
			
			try {
				$accessToken = $helper->getAccessToken();
				
			} catch(Facebook\Exceptions\FacebookResponseException $e) {

				echo 'Graph returned an error: ' . $e->getMessage();
				exit;
			} catch(Facebook\Exceptions\FacebookSDKException $e) {
				
				echo 'Facebook SDK returned an error: ' . $e->getMessage();
				exit;
			}

			if (isset($accessToken)) {

				header("location: /test/js/facebook/login/phpsdk/");

				try {
					$userData = $fb->get('/me', $accessToken);
					$userData = $userData->getGraphUser();
					
					$sessionUserData = array(
						"id" => $userData->getId(),
						"name" => $userData->getName(),
						"email" => $userData->getEmail(),
					);

				} catch (\Exception $e) {
					$sessionUserData = array("error" => "Some error occured");
				}
				
				$_SESSION["fb_user"] = array(
					"access_token" => $accessToken,
					"user_info" => $sessionUserData,
				);
				
				echo "Logging you in ...";
				
			} elseif ($helper->getError()) {

				echo "ERROR : ".$helper->getError();
				exit;
			}
			
		} else {
		
			$permissions = ['email'];
			$loginUrl = $helper->getLoginUrl('http://lab.dev-nook.de/test/js/facebook/login/phpsdk/', $permissions);
			
			//
			// logged off template - start
			//
			
			echo '<a href="'.$loginUrl.'">Login with Facebook</a>';
			
			//
			// logged off template - end
			//
		
		}
	
	}
	
	/*try {
		// Get the \Facebook\GraphNodes\GraphUser object for the current user.
		// If you provided a 'default_access_token', the '{access-token}' is optional.
		$response = $fb->get('/me', '{access-token}');
	} catch(\Facebook\Exceptions\FacebookResponseException $e) {
		// When Graph returns an error
		echo 'Graph returned an error: ' . $e->getMessage();
		exit;
	} catch(\Facebook\Exceptions\FacebookSDKException $e) {
		// When validation fails or other local issues
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}

	$me = $response->getGraphUser();
	echo 'Logged in as ' . $me->getName();*/
?>