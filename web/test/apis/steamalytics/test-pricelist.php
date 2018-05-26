<?php
$url = "http://api.csgo.steamlytics.xyz/v2/pricelist";
$apiKey = "254c9326c460a8ba1a4c550d5da5bb50";

$ch = curl_init($url . "?key=" . $apiKey);

curl_setopt_array($ch, array (
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_SSL_VERIFYPEER => false
));

$result = curl_exec($ch);

if($result){
	var_dump($result);
} else {
	var_dump(curl_error($ch));
}

curl_close($ch);

?>
