<?php  
	error_reporting(0);
	session_start();
	header("Access-Control-Allow-Origin: *");
	header("Content-Type: application/json; charset=UTF-8");
	//Creating Array for JSON response
	$response = array();
	// Check if we got the field from the user
	if (isset($_REQUEST['k']) && isset($_REQUEST['id'])) {
    	$from_src = trim($_REQUEST['k']);
    	$reader_id = trim($_REQUEST['id']);
    	$pins=trim($_REQUEST['pin']);
	 	require_once "classes/User.php";
		$user = new User();
		$obj=$user->overwritePin($from_src,$reader_id,$pins);
		$response["status"] = 1;
	} 
	else {
	    // If required parameter is missing
	    $response["status"] = 0;
	    $message= "Parameter(s) are missing. Please check the request";
	    // Show JSON response
	}
	echo json_encode($response);
?>