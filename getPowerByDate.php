<?php

include("power.php");

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_REQUEST['date']) && isset($_REQUEST['device_id'])) {
    $date = $_GET['date'];
    $device_id = $_GET['device_id'];

    $result = getMaxPowerByDate($date, $device_id);

    // Return the result as JSON
    header('Content-Type: application/json');
    echo json_encode($result);
    exit();
}
