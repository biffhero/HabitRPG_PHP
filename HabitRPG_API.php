<?php
	define("baseURL", "https://habitrpg.com/v1/", true);
	define("apiToken", $_POST['apiToken'], true);
	define("userId", $_POST['userId'], true);	
	define("service", $_POST['service'], true);	
	define("icon", $_POST['icon'], true);
	
	require('HabitRPG_API_HabitScoring.php');
	
	switch ($_POST['function']) {
		case "habitScoring":
			habitScoring(userId, direction, taskId, apiToken, habitTitle, service, icon);
		break;
		default:		
			echo json_encode(array("error"=>"inavild function"));
		break;
	}

?>