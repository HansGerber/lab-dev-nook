<?php
	
	function getBasePath(){
		return str_replace("/".basename($_SERVER["PHP_SELF"]), "", $_SERVER["PHP_SELF"]);
	}
	
	function getPath(){
		return str_replace(getBasePath(), "", $_SERVER["REDIRECT_URL"]);
	}
	
	function makePath($path = ""){
		return getBasePath()."/$path";
	}
	
	function getView($path, $params = array()){
		extract($params);
		ob_start();
		require_once "../views/$path";
		return ob_get_clean();
	}
?>