<?php
	session_start();

	require_once "../conf.php";
	require_once "../url_mapping.php";
	require_once "../functions.php";
        require_once "../models.php";
	require_once "../controllers.php";

        if($_conf["maintenance"][$_conf["env"]] == true){
            echo getView("maintenance.php");
        } else {
            $_path = getPath();

            if(isset($_url_mapping[$_path])){
                    runController($_url_mapping[$_path]."Controller", array());
            } else {
                    echo getView("404.php");
            }
        }
?>