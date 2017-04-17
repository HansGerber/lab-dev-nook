<?php

require_once "../conf.php";

global $_conf;
$sql = $_conf["sql"];
$db = $sql["db"];

if($c = new mysqli($sql["server"], $sql["user"], $sql["pass"])){
    $c->query("create table if not exists $db.contact (
        id int(5) not null primary key auto_increment,
        name varchar(100) not null,
        email varchar(255) not null,
        message text not null,
        adddate datetime not null
     )") or die("failed to create table contact : ".$c->error);
    
    $c->query("create table if not exists $db.uploads (
        id int(5) not null primary key auto_increment,
        filename varchar(100) not null,
        fileid varchar(107) not null unique,
        adddate datetime not null
     )") or die("failed to create table uploads : ".$c->error);
} else {
    die("connection fail");
}
echo "ok";