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
        
        function write2log($text, $lvl = "info"){
            global $_conf;
            $logFilePath = $_conf["logPath"]."log.txt";
            if(!is_dir($_conf["logPath"])){
                if(!mkdir($_conf["logPath"], 0777)){
                    return false;
                }
            }
            $f = fopen($logFilePath, "a+");
            fwrite($f, "[".date("Y-m-d H:i:s")."][$lvl] $text");
            fclose($f);
            return true;
        }
?>
