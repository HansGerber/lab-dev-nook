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
                        $uploadfile = "uploads/".$fileId;
                        if(move_uploaded_file($_FILES['uploadFile']['tmp_name'], $uploadfile)){
                            $data = array (
                                "filename" => $fileName,
                                "fileid" => $fileId,
                            );
                            $upLoadToDatabase = runModel("addUpload", $data);
                            print_r($data);
                            print_r($upLoadToDatabase);
                            die("success");
                        } else {
                            die("copyFail");
                        }
                } else {
                        die("noData");
                }
        }

        ?>
        <style>
        * {
                box-sizing:border-box;
                font:100%/1.5 arial;
        }
        #progress,
        #estimated,
        #inProgress,
        #success,
        #error {
                display:none;	
        }
        #progress_wrap {
                width:500px;
                background:#ddd;
        }
        #progress {
                background:#555;
                height:50px;
                position:relative;
        }
        #progress div {
                padding:5px;
                top:10px;
                left:10px;
                color:#55;
                background:#fff;
                border:solid 1px #555;
                position:absolute;
                font-size:150%;
        }
        #estimated,
        #inProgress,
        #success,
        #error {
                color:#fff;
                padding:10px;
                width:500px;
        }
        #estimated {
                background:#999;
        }
        #inProgress {
                background:#05a;
        }
        #success {
                background:#5a0;
        }
        #error {
                background:#a00;
        }
        </style>
        <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
        <form id="uploadForm" method="post" enctype="multipart/form-data">
                <input type="file" name="uploadFile" id="uploadFile" />
                <input type="submit" value="upload" id="sendUploadForm" />
        </form>
        <div id="progress_wrap">
                <div id="progress"><div></div></div>
        </div>
        <div id="estimated">
                <span class="dataLeft"></span> Bytes uploaded.<br>
                Approximately <span class="seconds"></span> seconds left.
        </div>
        <div id="inProgress">
                Upload in progress ...
        </div>
        <div id="success">
                Upload successfull :) !
        </div>
        <div id="error">
                Upload failed :( !<br>
                Please try again a bit later.
        </div>
        <script type="text/javascript">

                function objDump(obj) {
                        var out = "";
                        for(p in obj){
                                out += p + " : " + obj[p] + "; ";
                        }
                        return out;
                }

                var currentlyLoaded = 0;
                var estimateTimerRunning = false;
                var firstEstimationDone = false;
                var fadeSpeed = 100;

                $("#sendUploadForm").click(function(e) {
                        var formData = new FormData($("#uploadForm")[0]);
                        e.preventDefault();
                        $.ajax({
                            xhr: function() {
                                    var xhr = new window.XMLHttpRequest();
                                    $("#inProgress").fadeIn(fadeSpeed);
                                    $("#progress").stop().fadeIn(fadeSpeed);
                                    $("#estimated").stop().fadeIn(fadeSpeed);
                                    xhr.upload.addEventListener("progress", function(evt) {
                                            var completedPercentage = Math.round(evt.loaded / evt.total * 100);
                                            $("#progress").width(completedPercentage * 5);
                                            $("#progress div").html(completedPercentage + "%");
                                            $("#estimated .dataLeft").html((evt.total - evt.loaded) + " / " + evt.total);
                                            currentlyLoaded = evt.loaded;
                                            if(estimateTimerRunning == false){
                                                    estimateTimerRunning = true;
                                                    setTimeout(function() {
                                                            estimateTimerRunning = false;
                                                            var secondsRemaining = (evt.total - evt.loaded) / (currentlyLoaded - evt.loaded);
                                                            if(secondsRemaining !== Infinity){
                                                                    $("#estimated .seconds").html(Math.round((evt.total - evt.loaded) / (currentlyLoaded - evt.loaded) / 2));
                                                            }
                                                            firstEstimationDone = true;
                                                    }, 500);
                                            }
                                    }, false);
                                    return xhr;
                            },
                            method: 'POST',
                            url: '',
                            data: formData,
                            // THIS MUST BE DONE FOR FILE UPLOADING
                            cache: false,
                            contentType: false,
                            processData: false,
                            // ... Other options like success and etc
                            success: function(responseData){
                                    $("#progress, #estimated").stop().fadeOut(fadeSpeed);
                                    $("#inProgress").stop().fadeOut(fadeSpeed, function() {
                                            if(responseData === "success"){
                                                    $("#success").fadeIn(fadeSpeed).delay(2000).fadeOut(fadeSpeed);
                                            } else {
                                                    $("#error").fadeIn(fadeSpeed).delay(2000).fadeOut(fadeSpeed);
                                            }
                                    });
                            },
                            error: function() {
                                    $("#progress, #estimated").stop().fadeOut(fadeSpeed);
                                    $("#inProgress").stop().fadeOut(fadeSpeed, function() {
                                            $("#error").fadeIn(fadeSpeed).delay(2000).fadeOut(fadeSpeed);
                                    });
                            }
                    });
                    return false;
            });

        </script>
        <?php
    }