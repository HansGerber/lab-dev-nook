<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 
require_once $_SERVER['DOCUMENT_ROOT']."/../conf.php";

$_sql = $_conf["sql"];
$_db = $_sql["db"];
$_c = new mysqli($_sql["server"], $_sql["user"], $_sql["pass"]);

if(@$_c->connect_error){
	die("db connection error (".$_c->connect_error.")");
}

function setFlashMessage($msg){
	$_SESSION["flashMessage"] = $msg;
}

function getFlashMessage(){
	$result = "";
	if(isset($_SESSION["flashMessage"])){
		$result = $_SESSION["flashMessage"];
		unset($_SESSION["flashMessage"]);
	}
	return $result;
}

function hasFlashMessage(){
	if(isset($_SESSION["flashMessage"])){
		return true;
	}
	return false;
}

function installDB(){
    global $_c;
	global $_db;
    
    if(!$_c->query("drop table if exists $_db.fbLoginTest_users")){
        return array("status" => "error", "error" => $_c->error);
    } else {
        if(!$_c->query("drop table if exists $_db.fbLoginTest_sessions")){
            return array("status" => "error", "error" => $_c->error);
        }
    }
    
    if(!$_c->query("create table $_db.fbLoginTest_users (
            id int(8) not null primary key auto_increment,
            name varchar(100) not null,
            email varchar(255) not null unique,
            password varchar(32),
            smAcc tinyint(1) not null,
            fbUserId varchar(15)
        )")){
        return array("status" => "error", "error" => $_c->error);
    }
    
    if(!$_c->query("create table $_db.fbLoginTest_sessions (
            id int(6) not null primary key auto_increment,
            hash varchar(32) not null,
            expires datetime not null
        )")){
        return array("status" => "error", "error" => $_c->error);
    }
    
    return array("status" => "ok", "error" => "");
}

function register($data){
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

function login($data){
	global $_c;
	global $_db;
	if(isLoggedIn() === false){
			
			if(isset($data["username"]) && isset($data["password"])){
				if($user = $_c->query("select * from $_db.fbLoginTest_users
				where email='".$data["username"]."' and password='".md5($data["password"])."'")){
					
					if($user->num_rows == 1){
						$_SESSION["user"] = $user->fetch_assoc();
						$user->free_result();
						return "ok";
					} else {
						return "wrongCredentials";
					}
				}
				return "sqlError; ".$_c->error;
			}
			return "noData";
	}
	return "noAction";
}

function isLoggedIn(){
	if(isset($_SESSION["user"])){
		return true;
	}
	return false;
}

function logout(){
	setcookie('acc', '', time() - 3600);
	if(isLoggedIn()){
		unset($_SESSION["user"]);
	}
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
	"username" => "test@test.de",
	"password" => "123456",
	"smAcc" => 0
));

var_dump($testUserRegistration);
*/

//
// Template
//

require_once "template.php";

$_c->close();

?>