<?php

        function apiPingController(){
            global $_conf;
            $sql = $_conf["sql"];

            $result = array("success" => false, "error" => "");
            try {
                if($c = mysqli_connect($sql["server"], $sql["user"], $sql["pass"])){
                    if(strlen($c->error) == 0){
                        $result["success"] = true;
                    } else {
                        $result["error"] = $c->error;
                    }
                    $c->close();
                }
            } catch(\Exception $e){
                $result["error"] = $e->getMessage();
            }
            echo json_encode($result);
        }
