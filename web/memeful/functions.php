<?php
	function memePosts($tags = null){
		$apiUrl = "http://memeful.com/web/ajax/posts?count=10000";
		$curlResult = false;
		$result = null;
		
		$ch = curl_init($apiUrl);
		
		curl_setopt_array($ch, array (
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_RETURNTRANSFER  => true,
		));
		
		$curlResult = curl_exec($ch);
		
		if($curlResult){
			$curlResult = json_decode($curlResult);
			
			if(json_last_error() === JSON_ERROR_NONE){
				
				$result = $curlResult;
				
				if($tags){
					$memesData = array();
					$addMeme = true;
					
					$tags = trim($tags);
					
					$tags = explode(",", $tags);
					$tags = implode("|", $tags);
					
					foreach($result->data as $meme){
						if(preg_match("/$tags/", $meme->tags)){
							$memesData []= $meme;
						}
					}
					
					$result->data = $memesData;
				}
			}
		}
		
		curl_close($ch);
		return $result;
	}
?>