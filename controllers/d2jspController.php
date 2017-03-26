<?php
    function d2jspProfileCounterController(){
        // prevent caching in all clients
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
        header("Pragma: no-cache"); // HTTP 1.0.
        header("Expires: 0"); // Proxies.
        
        //handle the counter
        $counterFile = "../counter-d2jsp-profile.txt";
        if(!file_exists($counterFile)){ touch($counterFile); }
        $counterVal = file_get_contents($counterFile) * 1;
        if(!isset($_SESSION["d2jspProfileVisited"])){
            if(@$_SERVER['HTTP_REFERER'] && preg_match("/^https?\:\/\/forums.d2jsp.org/", $_SERVER['HTTP_REFERER']) == 1){
                $_SESSION["d2jspProfileVisited"] = true;
                $counterVal++;
                file_put_contents($counterFile, $counterVal);
            }
        }
        
        //create and send the image
        $ttffile = "assets/DejaVuSans-Bold.ttf";
        header("content-type: image/png");
        $im = imagecreatetruecolor(320, 40);
        $text_color = imagecolorallocate($im, 0, 255, 0);
        if(file_exists($ttffile)){
            imagettftext($im, 18, 0, 10, 28, $text_color, $ttffile, "$counterVal");
        } else {
            imagestring($im, 5, 10, 10, "$counterVal", $text_color);
        }
        imagepng($im);
        imagedestroy($im);
    }
    
    function d2jspPostCounterController(){
        // prevent caching in all clients
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
        header("Pragma: no-cache"); // HTTP 1.0.
        header("Expires: 0"); // Proxies.
        
        //get the counter
        $counterFile = "../counter-d2jsp-posts.txt";
        if(!file_exists($counterFile)){ touch($counterFile); }
        $counterVal = file_get_contents($counterFile) * 1;
        
        //create and send the image
        $ttffile = "assets/DejaVuSans-Bold.ttf";
        header("content-type: image/png");
        $im = imagecreatetruecolor(320, 40);
        $text_color = imagecolorallocate($im, 0, 255, 0);
        if(file_exists($ttffile)){
            imagettftext($im, 18, 0, 10, 28, $text_color, $ttffile, "$counterVal");
        } else {
            imagestring($im, 5, 10, 10, "$counterVal", $text_color);
        }
        imagepng($im);
        imagedestroy($im);
    }
    
    function d2jspPostCounterCountController(){
        // prevent caching in all clients
        header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
        header("Pragma: no-cache"); // HTTP 1.0.
        header("Expires: 0"); // Proxies.
        
        $counterFile = "../counter-d2jsp-posts.txt";
        if(!file_exists($counterFile)){ touch($counterFile); }
        $counterVal = file_get_contents($counterFile) * 1;
        if(
            @$_SERVER['HTTP_REFERER'] &&
            preg_match("/^https?\:\/\/forums.d2jsp.org/", $_SERVER['HTTP_REFERER']) == 1 &&
            preg_match("/https?\:\/\/forums.d2jsp.org\/(user|pm)\.php/", $_SERVER['HTTP_REFERER']) != 1
        ){
            $counterVal++;
            file_put_contents($counterFile, $counterVal);
        }
        
        //create and send the image
        $im = @imagecreatefromjpeg("assets/images/d2jsp-signature.jpg");
        if(!$im){
            header("content-type: image/png");
            $im = imagecreatetruecolor(1, 1);
            $color = imagecolorallocatealpha($im, 0, 0, 0, 0);
            imagefill($im, 0, 0, $color);
            imagepng($im);
        } else {
            header("content-type: image/jpg");
            imagejpeg($im);
        }
    }
?>
