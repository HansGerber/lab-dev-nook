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
        
        function getConf($key = null){
            global $_conf;
            if($key){
                if(isset($_conf[$key])){
                    return $_conf[$key];
                }
            } else {
                return $_conf;
            }
            return null;
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
            fwrite($f, "[".date("Y-m-d H:i:s")."][$lvl] $text".PHP_EOL);
            fclose($f);
            return true;
        }
        
        function runModel($name, $data = array()){
            global $_conf;
            $sql = $_conf["sql"];
            $result = array ("success" => false);
            $modelParams = array("params" => array (
                "sql" => $sql,
                "data" => $data
            ));

            $modelName = $name.'Model';
            if(function_exists($modelName)){
                if($c = new mysqli($sql["server"], $sql["user"], $sql["pass"])){
                    $modelParams["params"]["sql"]["connection"] = $c;
                    $result = call_user_func_array($modelName, $modelParams);
                    $c->close();
                } else {
                    $result["error"] = ($c->connect_error ? $c->connect_error : $c->error);
                }
            } else {
                throw new \Exception("Model '$modelName' does not exist!");
            }
            return $result;
        }
        
        function printCaptcha(){
            $conf = getConf();
            $sitekey = @$conf["recaptcha"][$conf["env"]]["sitekey"];
            if($sitekey){
                echo '<script src="https://www.google.com/recaptcha/api.js"></script>'."\n";
                echo '<div class="g-recaptcha" data-sitekey="'.$sitekey.'"></div>';
            }
        }
        
        function verifyCaptcha(){
            $conf = getConf();
            $captchaResponse = @$_POST["g-recaptcha-response"];
            if($captchaResponse){
                $ch = curl_init("https://www.google.com/recaptcha/api/siteverify");
                curl_setopt_array($ch, array(
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_POSTFIELDS => http_build_query(array(
                        "secret" => @$conf["recaptcha"][$conf["env"]]["secret"],
                        "response" => $captchaResponse
                    ))
                ));

                $resultData = curl_exec($ch);
                write2log("verifyCaptcha() : ".$resultData);
                if($resultData === false){
                    write2log("verifyCaptcha() curl_error : ".curl_error($ch), "error");
                    return false;
                } else {
                    $resultData = json_decode($resultData);
                    if($resultData->success == false){
                        write2log("verifyCaptcha() : ".json_encode($resultData));
                        return false;
                    }
                }
                curl_close($ch);
                return true;
            }
            return false;
        }
?>
