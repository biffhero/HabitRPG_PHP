<?php
	/*
	A PHP class for HabitRPG API
	Author: Rudd Fawcett
	URL: http://ruddfawcett.com, http://github.com/ruddfawcett
	Last Commit: 2/21/2013
	Version: 1.1
	*/

class HabitRPG {
	public $userId;
	public $apiToken;
	public $url;
	
	public function __construct ($userId, $apiToken) {
		$this->userId = $userId;
		$this->apiToken = $apiToken;
		$this->url = "https://habitrpg.com/v1/users/";
		
		if(!extension_loaded("cURL")) {
			throw new Exception("This HabitRPG PHP API class requires cURL in order to work.");
		}
	}
	
	// habitScoring function, allows users to up or down tasks by ids
	// takes array as parameter - $scoringParams which is required to
	// contain taskId and direction of of scoring
	
	public function habitScoring($scoringParams) {
		if(is_array($scoringParams)) {
			if(!empty($scoringParams['taskId']) && !empty($scoringParams['direction'])) {
				$scoringEndpoint=$this->url.$this->userId."/tasks/".$scoringParams['taskId']."/".$scoringParams['direction'];
				$scoringPostBody=array();
				$scoringPostBody['apiToken']=$this->apiToken;
				if(!empty($scoringParams['title'])) {
					$scoringPostBody['title']=$scoringParams['title'];
				}
				if(!empty($scoringParams['service'])) {
					$scoringPostBody['service']=$scoringParams['service'];
				}
				if(!empty($scoringParams['icon'])) {
					$scoringPostBody['icon']=$scoringParams['icon'];
				}
				
				$scoringPostBody=json_encode($scoringPostBody);
				
				return $this->curl($scoringEndpoint,$scoringPostBody);
			}
			else {
				return json_encode(array("error"=>"some parameters are null"));
			}
		}
		else {
			return json_encode(array("error"=>"habitScoring takes an array"));
		}
	}
	
	// A cURL function to handle all curls which require POSTs.
	// Will add switch and eliminate future exceptions of DELETE, PUT, etc.
	// merging all into a curl function
	// curl takes endpoint and postBody from any other function
	
	private function curl($endpoint,$postBody) {
		$curl = curl_init($endpoint);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postBody);

		$habitRPGResponse = curl_exec($curl);
		$habitRPGHTTPCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
		curl_close($curl);
		
		if ($habitRPGHTTPCode == 200) {
			return json_encode(array("result"=>"true","habitRPGData"=>json_decode($habitRPGResponse)));
		}
		else {
			return str_replace('\/','/',json_encode(array("error"=>"the cURL returned a non 200 http code","httpCode"=>$habitRPGHTTPCode,"endpoint"=>$endpoint,"dataSent"=>json_decode($postBody))));
		}
	}
}
?>