<?php

include("conn.php");

function getNextDate($currentDate)
{
    $previousDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
    return $previousDate;
}

function getPowerByDate()
{
    global $connection;
    if (isset($_REQUEST['date'])) {
        $startDate = $_REQUEST['date'];
    } else {
        $startDate = date('Y-m-d');
    }
    $_REQUEST['date'] = $startDate;
    if (isset($_REQUEST['device_id'])) {
        $device_id = $_REQUEST['device_id'];
    } else {
        $device_id = '';
    }
    $endDate = getNextDate($startDate);

    $statement = "SELECT
    FLOOR(HOUR(date) / 2) * 2 AS time_slot_end,
    MAX(voltage) AS voltage,
    MAX(amp) AS amp,
    device_id
    FROM q_power
    WHERE date BETWEEN ? AND ? AND device_id =?
    GROUP BY time_slot_end, device_id
    ORDER BY time_slot_end";

    $query = $connection->prepare($statement);
    $query->bind_param("sss", $startDate, $endDate, $device_id);
    $query->execute();

    $queryResult = $query->get_result();
    $data = array();
    while ($row = mysqli_fetch_assoc($queryResult)) {
        $data[] = $row;
    }

    return $data;
}
