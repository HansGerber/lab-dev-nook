<?php

    function indexController() {
        echo getView("home.php");
    }

    function contactController(){
        $data = @$_POST["contact"];
        $result = array();

        if($data){  
            header("location:".makePath("contact"));
            $_SESSION["contact_result"] = addContactMessageModel($data);
        }
        if(isset($result["success"])){
            echo getView("contact-result.php", array(
                "result" => $result
            ));
        } else {
            echo getView("contact.php", array(
                "result" => $result
            ));
        }
    }

    function downloadsController(){
        echo getView("downloads.php");
    }
    
    function linksController(){
        echo getView("links.php");
    }
