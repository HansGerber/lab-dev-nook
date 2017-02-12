<?php

    function indexController() {
        echo getView("home.php");
    }

    function contactController(){
        echo getView("contact.php");
    }

    function downloadsController(){
        echo getView("downloads.php");
    }
    
    function linksController(){
        echo getView("links.php");
    }
