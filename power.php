<?php

use function PHPSTORM_META\map;

include("conn.php");

function getNextDate($currentDate)
{
    $previousDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
    return $previousDate;
}

function getPowerByDate($date, $device_id)
{
    global $connection;
    $endDate = getNextDate($date);

    $statement = "SELECT
    FLOOR(HOUR(date) / 2) * 2 AS time_slot_end,
    voltage AS voltage,
    amp AS amp,
    device_id,
    date
    FROM q_power
    WHERE date BETWEEN ? AND ? AND device_id =?
    ORDER BY date";

    $query = $connection->prepare($statement);
    $query->bind_param("sss", $date, $endDate, $device_id);
    $query->execute();

    $queryResult = $query->get_result();
    $data = array();
    while ($row = mysqli_fetch_assoc($queryResult)) {
        $data[] = $row;
    }
    return $data;
}

function getMaxPowerByDate($date, $device_id)
{
    $maxValues = [];
    $fetchedData = getPowerByDate($date, $device_id);

    foreach ($fetchedData as $item) {
        $timeSlotEnd = $item['time_slot_end'];
        $voltage = $item['voltage'];
        $amp = $item['amp'];

        if (!isset($maxValues[$timeSlotEnd])) {
            $maxValues[$timeSlotEnd] = [
                'max_voltage' => $voltage,
                'max_amp' => $amp
            ];
        } else {
            $maxValues[$timeSlotEnd]['max_voltage'] = max($maxValues[$timeSlotEnd]['max_voltage'], $voltage);
            $maxValues[$timeSlotEnd]['max_amp'] = max($maxValues[$timeSlotEnd]['max_amp'], $amp);
        }
    }

    return $maxValues;
}

function getPowerByTimeSlot($date, $device_id, $time_slot)
{
    $fetchedData = getPowerByDate($date, $device_id);

    $data = array();

    foreach ($fetchedData as $item) {
        if ($time_slot == $item['time_slot_end']) {
            $data[] = $item;
        }
    }

    return $data;
}
