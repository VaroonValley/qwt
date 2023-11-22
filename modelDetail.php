<?php

include("conn.php");

$id = $_GET['id'];

if ($id) {
    $stmt = $connection->prepare("SELECT voltage, amp FROM q_power WHERE device_id = ?  ORDER BY `date` DESC LIMIT 1");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $stmt->bind_result($voltage, $amp);
    $stmt->fetch();

    echo "Last voltage for id $id: $voltage, Last amp for id $id: $amp";

    $stmt->close();
} else {
    echo "No id provided";
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
<div class="flex-container">
        <a href="/chart.php?device_id=<?= $id?>" class='link-button'><?= $voltage ? $voltage : 'Null'  ?></a>
        <a href="/chart.php?device_id=<?= $id?>" class='link-button'><?= $amp ? $amp : 'Null' ?></a>
    </div>
</body>

</html>