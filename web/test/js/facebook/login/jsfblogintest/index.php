<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 
require_once $_SERVER['DOCUMENT_ROOT']."/../conf.php";
require_once "functions.php";

$_sql = $_conf["sql"];
$_db = $_sql["db"];
$_c = new mysqli($_sql["server"], $_sql["user"], $_sql["pass"]);

if(@$_c->connect_error){
	die("db connection error (".$_c->connect_error.")");
}

function renderLoginModule($pathAfterLogin = ""){
	if(isLoggedIn() === false){
		$data = @$_POST["login"];
		if($data){
			$loginResult = login(array(
				"username" => $data["email"],
				"password" => $data["password"]
			));
			setFlashMessage($loginResult);
			
			header("location: .");
			return "logging in ...";
		} else {
			ob_start();
			?>
			<form class="loginModuleLoginForm" method="post">
				<label>
					Email :
					<input type="text" name="login[email]">
				</label>
				<label>
					Password :
					<input type="text" name="login[password]">
				</label>
				<?php if($pathAfterLogin){ echo '<input type="hidden" name="login[returnPath]" value="'.$pathAfterLogin.'" />'; } ?>
				<input type="submit" value="login" />
				or
				<div style="display:inline-block;margin-left:5px;padding:5px;
				background:rgb(72, 98, 163);color:#fff;" onclick="fbLogin()">Login with Facebook</div>
			</form>
			<?php
			return ob_get_clean();
		}
	} else {
		$data = @$_POST["logout"];
		if($data){
			$loginResult = logout();
			
			setFlashMessage("ausgeloggt");
			header("location: .");
			
			return "logging out ...";
		} else {
			ob_start();
			?>
			<form class="loginModuleLogoutForm" method="post">
				<?php if($pathAfterLogin){ echo '<input type="hidden" name="login[returnPath]" value="'.$pathAfterLogin.'" />'; } ?>
				<input type="submit" name="logout" value="logout" />
			</form>
			<?php
			return ob_get_clean();
		}
	}
}

session_start();

if(@$_GET["_install"] == 1){
	installDB();
}

//
// empty user table
//

/*$_c->query("truncate $_db.fbLoginTest_users");
if($_c->error){ echo $_c->error; }
die();*/

//
// User Test Registration
//

/*
$testUserRegistration = register(array(
	"name" => "test",
	"email" => "test@test.de",
	"password" => "123456",
	"fbAppUserId" => null
));

var_dump($testUserRegistration);
*/

//
// Template
//

require_once "template.php";

$_c->close();

?>