<?php
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
			return array("satus" => "error", "error" => $_c->error);
		} else {
			if(!$_c->query("drop table if exists $_db.fbLoginTest_sessions")){
				return array("satus" => "error", "error" => $_c->error);
			}
		}
		
		if(!$_c->query("create table $_db.fbLoginTest_users (
				id int(8) not null primary key auto_increment,
				name varchar(100) not null,
				email varchar(255) unique,
				password varchar(32),
				fbAppUserId varchar(20)
			)")){
			return array("satus" => "error", "error" => $_c->error);
		}
		
		if(!$_c->query("create table $_db.fbLoginTest_sessions (
				id int(6) not null primary key auto_increment,
				hash varchar(32) not null,
				expires datetime not null
			)")){
			return array("satus" => "error", "error" => $_c->error);
		}
		
		return array("satus" => "ok", "error" => "");
	}

	function register($data){
		global $_c;
		global $_db;
		
		$sqlRegistrationQuery = "insert into $_db.fbLoginTest_users (
			id,
			name";
		if(isset($data["email"])){
			$sqlRegistrationQuery .= ",email";
		}
		if(isset($data["password"])){
			$sqlRegistrationQuery .= ",password";
		}
		if(isset($data["fbAppUserId"])){
			$sqlRegistrationQuery .= ",fbAppUserId";
		}
		$sqlRegistrationQuery .= ")
			values (
			'',
			'".$_c->real_escape_string($data["name"])."'";
		if(isset($data["email"])){
			$sqlRegistrationQuery .= ",'".$_c->real_escape_string($data["email"])."'";
		}
		if(isset($data["password"])){
			$sqlRegistrationQuery .= ",'".$_c->real_escape_string(md5($data["password"]))."'";
		}
		if(isset($data["fbAppUserId"])){
			$sqlRegistrationQuery .= ",'".$_c->real_escape_string($data["fbAppUserId"])."'";
		}	
		$sqlRegistrationQuery .= ")";
		
		$_c->query($sqlRegistrationQuery);
		
		if(@$_c->error){
			return array("status" => "error", "error" => $_c->error, "query" => $sqlRegistrationQuery);
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
							$fetchedUserData = $user->fetch_assoc();
							if(is_null($fetchedUserData["fbAppUserId"])){
								$_SESSION["user"] = $fetchedUserData;
								$user->free_result();
								return "ok";
							}
						}
						return "wrongCredentials";
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
?>