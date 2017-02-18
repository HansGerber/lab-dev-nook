<?php

function addContactMessageModel($data){
    global $_conf;
    $sql = $_conf["sql"];
    $db = $sql["db"];
    
    $result = array ("success" => false);
    
    if($c = new mysqli($sql["server"], $sql["user"], $sql["pass"])){
        if($c->query("insert into $db.contact values (
            '',
            '".$c->real_escape_string($data["name"])."',
            '".$c->real_escape_string($data["email"])."',
            '".$c->real_escape_string($data["message"])."',
            '".date("Y-m-d H:i:s")."'
        )")){
            $result["success"] = true;
        } else {
            $result["error"] = ($c->connect_error ? $c->connect_error : $c->error);
        }
    }
    
    return $result;
}