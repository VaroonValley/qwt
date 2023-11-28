<?php
include("conn.php");
include("mail.php");

function addPower($device_id, $voltage, $amp)
{
    global $connection;

    $env = parse_ini_file(".env");

    $insertStatement = "INSERT INTO q_power (device_id, voltage, amp, date) 
                    VALUES ('$device_id', $voltage, $amp, CURTIME())";

    $query = mysqli_query($connection, $insertStatement);
    if ($voltage > $env['MAX_VOLTAGE'] or $voltage < $env['MIN_VOLTAGE']) {
        // Log error
        error_log("Voltage out of range: $voltage", 0);
        // Send email notification
        sendMail('voltage = ' . $voltage);
    }
    if ($amp > $env['MAX_AMP'] or $amp < $env['MIN_AMP']) {
        // Log error
        error_log("Ampere out of range: $amp", 0);
        // Send email notification
        sendMail('amp = ' . $amp);
    }
    return $query;
}

if (isset($_REQUEST['id']) && isset($_REQUEST['voltage']) && isset($_REQUEST['amp'])) {
    $device_id = trim($_REQUEST['device_id']);
    $voltage = trim($_REQUEST['voltage']);
    $amp = trim($_REQUEST['amp']);

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
