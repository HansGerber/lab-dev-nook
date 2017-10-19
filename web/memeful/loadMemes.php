<?php
	require_once "functions.php";
	
	header("content-type: application/json");
	
	echo json_encode(memePosts((@$_GET["tag"] != "" ? $_GET["tag"] : "")));
?>