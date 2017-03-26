<?php
	session_start();

	require_once "../conf.php";
	require_once "../url_mapping.php";
	require_once "../functions.php";
        require_once "../models.php";
	require_once "../controllers.php";

        $_exclude_routes_from_maintenance = array (
            "/d2jsp-profile-counter.png",
            "/d2jsp-post-counter.png",
            "/d2jsp-post-counter-count.jpg",
        );

        $_path = getPath();
        
        if($_conf["maintenance"][$_conf["env"]] == true && !in_array($_path, $_exclude_routes_from_maintenance)){
            echo getView("maintenance.php");
        } else {
            if(isset($_url_mapping[$_path])){
                    runController($_url_mapping[$_path]."Controller", array());
            } else {
                    echo getView("404.php");
            }
        }
?>