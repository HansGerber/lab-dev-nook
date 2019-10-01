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

function addUploadModel($params){
    $result = array ("success" => false);
    $c = $params["sql"]["connection"];
    $db = $params["sql"]["db"];
    $data = $params["data"];
    if($c->query("insert into $db.uploads values (
        '',
        '".$c->real_escape_string($data["filename"])."',
        '".$c->real_escape_string($data["fileid"])."',
        '".date("Y-m-d H:i:s")."'
    )")){
        $result["success"] = true;
    } else {
        $result["error"] = $c->error;
    }
    
    return $result;
}

function getUploadsModel($params){
    $result = array ("success" => false);
    $c = $params["sql"]["connection"];
    $db = $params["sql"]["db"];
    if($res = $c->query("select * from $db.uploads order by adddate desc")){
        while($r = $res->fetch_assoc()){
            $result["data"] []= $r;
        }
        $res->free_result();
        $result["success"] = true;
    } else {
        $result["error"] = $c->error;
    }
    
    return $result;
}

function truncateUploadsModel($params) {
    $result = array ("success" => false);
    $c = $params["sql"]["connection"];
    $db = $params["sql"]["db"];
    if($res = $c->query("truncate $db.uploads")){
        $result["success"] = true;
    } else {
        $result["error"] = $c->error;
    }

    return $result;
}
