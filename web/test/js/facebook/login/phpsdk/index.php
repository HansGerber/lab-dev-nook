<?php
	session_start();

	require_once $_SERVER['DOCUMENT_ROOT']."/../conf.php";

	function registerUser($data){
		global $_c;
		global $_db;
		
		$_c->query("insert into $_db.fbLoginTest_users values (
			'',
			'".$_c->real_escape_string($data["name"])."',
			'".$_c->real_escape_string($data["username"])."',
			'".$_c->real_escape_string(md5($data["password"]))."',
			'".$_c->real_escape_string($data["smAcc"])."'
		)");
		
		if(@$_c->error){
			return array("status" => "error", "error" => $_c->error);
		}
		return array("status" => "ok", "insertId" => $_c->insert_id);
	}
	
	$_sql = $_conf["sql"];
	$_db = $_sql["db"];
	
	$baseUrl = "http://lab.dev-nook.de/test/js/facebook/login/phpsdk/";
	
	$_c = new mysqli($_sql["server"], $_sql["user"], $_sql["pass"]);
	
	if(@$_c->connection_error){
		echo "The database is currently beeing maintained.<br>Please try again a bit later.";
		exit;
	} else {
	
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

					header("location: $baseUrl");

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
					
					registerUser(array(
						"name" => $sessionUserData["name"],
						"username" => $sessionUserData["id"],
						"password" => "",
						"smAcc" => 1
					));
					
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
				$loginUrl = $helper->getLoginUrl($baseUrl, $permissions);
				
				//
				// logged off template - start
				//
				
				echo '<a href="'.$loginUrl.'">Login with Facebook</a>';
				
				//
				// logged off template - end
				//
			
			}
		
		}
		
		$_c->close();
	}
?>