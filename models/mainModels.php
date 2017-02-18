<?php

function addContactMessageModel($params){
    $result = array ("success" => false);
    $c = $params["sql"]["connection"];
    $db = $params["sql"]["db"];
    $data = $params["data"];
    if($c->query("insert into $db.contact values (
        '',
        '".$c->real_escape_string($data["name"])."',
        '".$c->real_escape_string($data["email"])."',
        '".$c->real_escape_string($data["message"])."',
        '".date("Y-m-d H:i:s")."'
    )")){
        $result["success"] = true;
    } else {
        $result["error"] = $c->error;
    }
    
    return $result;
}