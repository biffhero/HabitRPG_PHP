<?php
	define("direction", $_POST['direction'], true);
	define("taskId", $_POST['taskId'], true);	
	define("habitTitle", $_POST['habitTitle'], true);

	function habitScoring($userId, $direction, $taskId, $apiToken, $habitTitle, $service, $icon) {
		$url = baseURL . "users/" . $userId . "/tasks/" . $taskId . "/" . $direction;
    	
    	if ($userId == null || $direction == null || $taskId == null || $apiToken == null || $url == null) {
    		echo json_encode(array("error"=>"some parameters are null"));
    	}
    	else {
			$postData = array();
			
			$postData['apiToken'] = $apiToken;
			
			if ($habitTitle != null) {
				$postData['title'] = $habitTitle;
			}
			
			if ($service != null) {
				$postData['service'] = $service;
			}
			
			if ($icon != null) {
				$postData['icon'] = $icon;
			}
			
			$postBody = json_encode($postData);
			
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $postBody);

			$response = curl_exec($curl);
		
			$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

			if ($status == 200) {
				$result = array("result"=>"true","habitRPGData"=>json_decode($response));
			}
			else {
				$result = array("error"=>"unauthorized");
			}	
	
			echo json_encode($result);

			curl_close($curl);
		}
    }
?>