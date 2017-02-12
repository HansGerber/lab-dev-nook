<?php
	
	function getBasePath(){
		return str_replace("/".basename($_SERVER["PHP_SELF"]), "", $_SERVER["PHP_SELF"]);
	}

	function getPath(){
		return preg_replace("/^".str_replace("/", "\/", preg_quote(getBasePath()))."/", "", $_SERVER["REQUEST_URI"]);
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
