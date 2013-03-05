<?php
	/*
	A PHP class for HabitRPG API
	Author: Rudd Fawcett
	URL: http://ruddfawcett.com, http://github.com/ruddfawcett
	Last Commit:3/5/2013
	Version: 1.3
	*/

class HabitRPG {
	public $userId;
	public $apiToken;
	public $apiURL;
	
	public function __construct ($userId, $apiToken) {
		
		// Check to see if this is the latest version of the class
		
		$currentVersion = "1.3";
		
		if ($this->currentVersion() != $currentVersion) {
			throw new Exception("Please update to the latest version of this PHP wrapper (version ".$this->currentVersion().")! It most likely has updated functions and is better!  Here's a link: http://github.com/ruddfawcett/HabitRPG_PHP");
		}
		
		$this->userId = $userId;
		$this->apiToken = $apiToken;
		$this->apiURL = "https://habitrpg.com/api/v1/user";
		
		if(!extension_loaded("cURL")) {
			throw new Exception("This HabitRPG PHP API class requires cURL in order to work.");
		}
	}
	
	// Grabs the current version of the PHP API, and compares it with this version...
	private function currentVersion() {
		$JSON = json_decode(file_get_contents("http://ruddfawcett.com/projects/HabitRPG_PHP/currentVersion.json"),true);
		$currentVersion = $JSON['currentVersion'];
		
		return $currentVersion;
	}
	
	// habitScoring function, allows users to up or down tasks by ids
	// takes array as parameter - $scoringParams which is required to
	// contain taskId and direction of of scoring
	
	public function newTask($newTaskParams) {
		if(is_array($newTaskParams)) {
			if(!empty($newTaskParams['type']) && !empty($newTaskParams['title']) && !empty($newTaskParams['text'])) {
				$newTaskParamsEndpoint=$this->apiURL."/task";
				$newTaskPostBody=array();
				$newTaskPostBody['type'] = $newTaskParams['type'];
				if(!empty($newTaskParams['value'])) {
					$newTaskPostBody['value']=$newTaskParams['value'];
				}
				if(!empty($newTaskParams['note'])) {
					$newTaskPostBody['note']=$newTaskParams['note'];
				}
				
				$newTaskPostBody=json_encode($newTaskPostBody);
				
				return $this->curl($newTaskParamsEndpoint,"POST",$newTaskPostBody);
			}
			else {
				throw new Exception("Required keys of $newTaskParams are null.");
			}
		}
		else {
			throw new Exception("newTask takes an array as it's parameter.");
		}
	}
	
	// Grabs all of the information about a user on HabitRPG
	
	public function userStats() {
		return $this->curl($this->apiURL,"GET",NULL);
	}
	
	// Grabs all of the user's tasks on HabitRPG
	
	public function userTasks($userTasksType=NULL) {
		$userTasksEndpoint=$this->apiURL."/tasks";
		if($userTasksType != NULL) {
			$userTasksEndpoint=$this->apiURL."/tasks?type=".$userTasksType;
		}
			return $this->curl($userTasksEndpoint,"GET",NULL);
	}	
	
	// Grabs specific details of a task for an HabitRPG user
	
	public function userGetTask($taskId) {
		if(!empty($taskId)) {
			$userGetTaskEndpoint=$this->apiURL."/task/".$taskId;
			
			return $this->curl($userGetTaskEndpoint,"GET");
		}
		else {
			throw new Exception("userGetTask needs a value as it's parameter.");
		}
	}
	
	public function updateTask($updateParams) {
		if(is_array($updateParams)) {
			if(!empty($updateParams['taskId']) && !empty($updateParams['text'])) {
				$updateParamsEndpoint=$this->apiURL."/task/".$updateParams['taskId'];
				$updateTaskPostBody=array();
				$updateTaskPostBody['text'] = $updateParams['text'];
				
				$updateTaskPostBody=json_encode($updateTaskPostBody);
				
				return $this->curl($updateParamsEndpoint,"PUT",$updateTaskPostBody);
			}
			else {
				throw new Exception("Required keys of $updateParams are null.");
			}
		}
		else {
			throw new Exception("updateTask takes an array as it's parameter.");
		}
	}
	
	// A cURL function to handle all curls which require POSTs.
	// Will add switch and eliminate future exceptions of DELETE, PUT, etc.
	// merging all into a curl function
	// curl takes endpoint and postBody from any other function
	
	private function curl($endpoint,$curlType,$postBody) {
		$curl = curl_init();
		$curlArray = array(
							CURLOPT_RETURNTRANSFER => true, 
							CURLOPT_HEADER => false, 
							CURLOPT_ENCODING => "gzip",
							CURLOPT_HTTPHEADER => array(
														"Content-type: application/json",
														"x-api-user:".$this->userId,
														"x-api-key:".$this->apiToken),
							CURLOPT_URL => $endpoint);
		switch($curlType) {
			case "POST":
				$curlArray[CURLOPT_POSTFIELDS] = $postBody;
				$curlArray[CURLOPT_POST] = true;
				curl_setopt_array($curl, $curlArray);
				break;
			case "GET":
				curl_setopt_array($curl, $curlArray);
				break;
			case "PUT":
				$curlArray[CURLOPT_CUSTOMREQUEST] = "PUT";				
				$curlArray[CURLOPT_POSTFIELDS] = $postBody;
				curl_setopt_array($curl, $curlArray);
				break;
			case "DELETE":
				break;
			default:
				throw new Exception("Please use a valid method as the cURL type.");
		}
		
		$habitRPGResponse = curl_exec($curl);
		$habitRPGHTTPCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
		curl_close($curl);
		
		if ($habitRPGHTTPCode == 200) {
			return array("result"=>true,"habitRPGData"=>json_decode($habitRPGResponse,true));
		}
		else {
		$habitRPGResponse = json_decode($habitRPGResponse,true);
			return array("error"=>$habitRPGResponse['err'],"details"=>array("httpCode"=>$habitRPGHTTPCode,"endpoint"=>$endpoint,"dataSent"=>json_decode($postBody,true)));
		}
	}
}
?>