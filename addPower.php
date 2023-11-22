<?php
include("conn.php");
include("mail.php");

function addPower($device_id, $voltage, $amp)
{
    global $connection;

    $insertStatement = "INSERT INTO q_power (device_id, voltage, amp, date) 
                    VALUES ('$device_id', $voltage, $amp, CURTIME())";

    $query = mysqli_query($connection, $insertStatement);
    if ($voltage > 230 or $voltage < 220) {
        sendMail('voltage = ' . $voltage);
    }
    if ($amp > 100 or $amp < 80) {
        sendMail('amp = ' . $amp);
    }
    // if ($query) {
    //     $lastInsertedId = mysqli_insert_id($connection);

    //     $fetchInsertedData = "SELECT * FROM q_power WHERE id = $lastInsertedId";
    //     $result = mysqli_query($connection, $fetchInsertedData);

    //     if ($result) {
    //         $insertedData = mysqli_fetch_assoc($result);
    //         return $insertedData;
    //     }
    // }
    return $query;
}
