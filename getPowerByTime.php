<?php

include("power.php");

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_REQUEST['date']) && isset($_REQUEST['device_id']) && isset($_REQUEST['time_slot'])) {
    $date = $_GET['date'];
    $device_id = $_GET['device_id'];
    $time_slot = $_GET['time_slot'];

    $result = getPowerByTimeSlot($date, $device_id, $time_slot);

    // Return the result as JSON
    header('Content-Type: application/json');
    echo json_encode($result);
    exit();
}
