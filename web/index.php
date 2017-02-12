<?php
	session_start();

	require_once "../conf.php";
	require_once "../url_mapping.php";
	require_once "../functions.php";
        require_once "../models.php";
	require_once "../controllers.php";
	
	$_path = getPath();
	
	if(isset($_url_mapping[$_path])){
		$controller = $_url_mapping[$_path]."Controller";
                if(function_exists($controller)){
                    call_user_func_array($controller, array());
                } else {
                    throw new \Exception("Controller '$controller' not found.");
                }
	} else {
		echo getView("404.php");
	}
?>