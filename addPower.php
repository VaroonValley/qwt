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
        echo $voltage;
        sendMail('voltage = ' . $voltage);
    }
    if ($amp > $env['MAX_AMP'] or $amp < $env['MIN_AMP']) {
        echo $amp;
        sendMail('amp = ' . $amp);
    }
    return $query;
}
