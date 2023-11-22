<?php

include './../conn.php';


$currentdatetime = new DateTime();
$currentdatetime->sub(new DateInterval('P7D'));
$dateBeforeAWeek = $currentdatetime->format("Y-m-d H:i:s");

$sql = "DELETE FROM q_power WHERE `date` <= '{$dateBeforeAWeek}'";

if ($connection->query($sql) === TRUE) {
    $response = [
        'status' => 'success',
        'message' => 'Records deleted successfully.',
    ];
} else {
    $response = [
        'status' => 'error',
        'message' => 'Error deleting records: ' . $connection->error,
    ];
}

// Output the response as JSON
header('Content-Type: application/json');
echo json_encode($response);

$connection->close();
