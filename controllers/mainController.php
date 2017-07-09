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

    function uploadTestController(){
        
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                if(isset($_FILES["uploadFile"])){
                        $fileName = str_replace(' ', '_', $_FILES['uploadFile']['name']);
                        $fileId = rand(1000000, 9999999)."_".$fileName;
                        $uploadfile = "uploads/".$fileId."_";
                        if(move_uploaded_file($_FILES['uploadFile']['tmp_name'], $uploadfile)){
                            $data = array (
                                "filename" => $fileName,
                                "fileid" => $fileId,
                            );
                            $upLoadToDatabase = runModel("addUpload", $data);
                            die("success");
                        } else {
                            die("copyFail");
                        }
                } else {
                        die("noData");
                }
        }

        echo getView("upload-test.php");
    }
    
    function uploadListTestController(){
        $uploads = runModel("getUploads");
        
        echo getView("upload-list-test.php", array (
            "uploads" => $uploads,
        ));
    }