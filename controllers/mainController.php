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
                $_SESSION["contact_result"] = array("success" => false);
            } else {
                $_SESSION["contact_result"] = addContactMessageModel($data);
                if($_SESSION["contact_result"]["success"] == false){
                    $_SESSION["contact_result"]["error_sent_data"] = $data;
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
