<?php

    function indexController() {
        echo getView("home.php");
    }

    function contactController(){
        $data = @$_POST["contact"];
        $result = array();

        if($data){
            header("location:".makePath("contact"));
            if(!@$data["name"] || !@$data["email"] || !@$data["message"]){
                $_SESSION["contact_result"] = array("success" => false, "error" => "emptyData");
            } else {
                if(verifyCaptcha() == false){
                    $_SESSION["contact_result"] = array("success" => false, "error" => "invalidCaptcha");
                } else {
                    $_SESSION["contact_result"] = runModel("addContactMessage", $data);
                    if($_SESSION["contact_result"]["success"] != true){
                        $_SESSION["contact_result"]["error_sent_data"] = $data;
                    }
                }
            }
        } else {
            if(isset($_SESSION["contact_result"])){
                $result = $_SESSION["contact_result"];
                unset($_SESSION["contact_result"]);
            }
        }
        if(isset($result["success"])){
            if(isset($result["error"])){
                write2log("contactController() : saving contact data failed (".@$result["error"].") (data : ".  @json_encode($result["error_sent_data"]).")", "error");
            }
            echo getView("contact-result.php", array(
                "result" => $result
            ));
        } else {
            echo getView("contact.php", array(
                "result" => $result,
            ));
        }
    }
    
    function gamesController(){
        echo getView("games.php");
    }

    function downloadsController(){
        echo getView("downloads.php");
    }
    
    function linksController(){
        echo getView("links.php");
    }

    function d2jspCounterController(){
        // prevent caching in all clients
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
        header("Pragma: no-cache"); // HTTP 1.0.
        header("Expires: 0"); // Proxies.
        
        //handle the counter
        $counterFile = "../counter.txt";
        if(!file_exists($counterFile)){ touch($counterFile); }
        $counterVal = file_get_contents($counterFile) * 1;
        if(!isset($_SESSION["d2jspVisited"])){
            if(@$_SERVER['HTTP_REFERER'] && preg_match("/^https?\:\/\/forums.d2jsp.org/", $_SERVER['HTTP_REFERER']) == 1){
                $_SESSION["d2jspVisited"] = true;
                $counterVal++;
                file_put_contents($counterFile, $counterVal);
            }
        }
        
        //create and send the image
        $ttffile = "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf";
        header("content-type: image/png");
        $im = imagecreatetruecolor(300, 40);
        $text_color = imagecolorallocate($im, 0, 255, 0);
        if(file_exists($ttffile)){
            imagettftext($im, 24, 0, 2, 2, $text_color, $ttffile, "Visitors : ");
        } else {
            imagestring($im, 5, 2, 2, "Visitors : $counterVal", $text_color);
        }
        imagepng($im);
        imagedestroy($im);
    }