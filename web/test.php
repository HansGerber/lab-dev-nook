<?php
	require_once "../conf.php";
	$sql = $_conf["sql"];
	$result = array();
	if($c = mysqli_connect($sql["server"], $sql["user"], $sql["pass"])){
		$result["DBConnection"] = "OK";
		$c->close();
	} else {
		$result["DBConnection"] = "Error (".$c->error.")";
	}
	
	echo "<h3>Result</h3>\n<pre>";
	foreach($result as $k => $v){
		echo "<b>".$k."</b> : ".$v."<br>";
	}
?>