<?php

require_once './classes/Connector.php';
require_once './classes/MailSender.php';

$connector = new Connector();
$myDBcon = $connector->myDBcon;

// Create an instance of the MailSender class
$mailSender = new MailSender();

function addPower($device_id, $voltage, $amp)
{
    global $myDBcon, $mailSender;

    // Check if the database connection is successful
    if (!$myDBcon) {
        error_log("Database connection failed");
        return false;
    }

    $env = parse_ini_file(".env");

    $insertStatement = "INSERT INTO q_power (device_id, voltage, amp, date) 
                    VALUES (?, ?, ?, CURTIME())";

    $query = $myDBcon->prepare($insertStatement);

    // Check for statement preparation errors
    if (!$query) {
        error_log("Error in preparing the statement: " . $myDBcon->error);
        return false;
    }

    // Bind parameters
    $query->bind_param("sdd", $device_id, $voltage, $amp);

    // Execute the query
    if (!$query->execute()) {
        error_log("Error in executing the query: " . $query->error);
        return false; // or handle the error in an appropriate way
    }

    // Close the statement
    $query->close();

    if ($voltage > $env['MAX_VOLTAGE'] || $voltage < $env['MIN_VOLTAGE']) {
        // Log error
        error_log("Voltage out of range: $voltage", 0);
        // Send email notification
        $mailSender->sendMail('your_email@example.com', 'Voltage Out of Range', 'Voltage is out of range: ' . $voltage);
    }
    if ($amp > $env['MAX_AMP'] || $amp < $env['MIN_AMP']) {
        // Log error
        error_log("Ampere out of range: $amp", 0);
        // Send email notification
        $mailSender->sendMail('your_email@example.com', 'Ampere Out of Range', 'Ampere is out of range: ' . $amp);
    }

    return true;
}

// Check if the required parameters are set
if (isset($_REQUEST['k']) && isset($_REQUEST['id']) && isset($_REQUEST['pvoltage']) && isset($_REQUEST['pcurrent'])) {
    $device_id = trim($_REQUEST['device_id']);
    $voltage = trim($_REQUEST['voltage']);
    $amp = trim($_REQUEST['amp']);

    // Call the addPower function
    $result = addPower($device_id, $voltage, $amp);

    // Check if the query was successful
    if ($result) {
        $response["status"] = 1;
        $response["message"] = "Data added successfully";
    } else {
        $response["status"] = 0;
        $response["message"] = "Failed to add data. Please check your input";
    }
} else {
    // If required parameter is missing
    $response["status"] = 0;
    $response["message"] = "Parameter(s) are missing. Please check the request";
}

// Show JSON response
echo json_encode($response);
?>