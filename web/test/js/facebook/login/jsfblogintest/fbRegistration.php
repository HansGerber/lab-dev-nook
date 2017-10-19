<?php

	session_start();

	require_once $_SERVER['DOCUMENT_ROOT']."/../conf.php";
	require_once "functions.php";

	if(isLoggedIn() === false){
		
		$_sql = $_conf["sql"];
		$_db = $_sql["db"];
		$_c = new mysqli($_sql["server"], $_sql["user"], $_sql["pass"]);
		
		if(@$_c->connect_error){
			echo json_encode(array("success" => "false", "error" => "DatabaseConnectionError", "data" => $_c->connect_error));
			exit;
		}
		
		$userData = @$_POST;

		$userRegistrationResult = register(array(
			"name" => $userData["name"],
			"email" => @$userData["email"],
			"fbAppUserId" => $userData["id"]
		));
		
		$_SESSION["user"] = array (
			"id" => null,
			"name" => $userData["name"],
			"email" => @$userData["email"],
			"fbAppUserId" => $userData["id"]
		);
		
		$_c->close();
		
		echo json_encode(array("success" => "true", "error" => "", "data" => $userRegistrationResult));
		exit;
		
	} else {
		echo json_encode(array("success" => "false", "error" => "DatabaseConnectionError"));
		exit;
	}
?>