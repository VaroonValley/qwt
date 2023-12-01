<?php
$date = isset($_REQUEST["date"]) ? $_REQUEST["date"] : date("Y-m-d");
$deviceID = $_REQUEST['device_id'];
$today = date('Y-m-d');
$formattedDate = date('d-m-Y l', strtotime($date));
$isToday = ($date == $today) ? 'Today': '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Document</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/popupbox.css">
</head>

   


<body class='lg_container'>
<h1>Power Chart</h1>
<div class="container">
    <p class="title">Model ID: <?= $deviceID ?></p>
    <p class="date"><?php echo $isToday . '&nbsp;&nbsp;' . date('l, d F Y', strtotime($date)) ?></p>
    
    <div class="navigation">
        <div>
            <?php if ($date > date('Y-m-d', strtotime('- 6 day'))) { ?>
                <a href="/chart.php?device_id=<?php echo $deviceID ?>&date=<?php echo date('Y-m-d', strtotime($date . '- 1 day')) ?>" class="link-button">Previous Day</a>
            <?php } ?>
        </div>
        <div>
            <?php if ($date < date('Y-m-d')) { ?>
                <a href="/chart.php?device_id=<?php echo $deviceID ?>&date=<?php echo date('Y-m-d', strtotime($date . '+ 1 day')) ?>" class="link-button">Next Day</a>
            <?php } ?>
        </div>
    </div>

    <div class="charts-container">
        <div class="chart">
            <canvas id="voltage" class="canvas"></canvas>
        </div>
        <div class="chart">
            <canvas id="amp" class="canvas"></canvas>
        </div>
    </div>
    </div>

    <div id="chartPopUp" class="overlay">
        <p> Model ID : <?= $deviceID ?> </p>
        <p><?php echo $isToday . '&nbsp;&nbsp;' . date('l, d F Y', strtotime($date)) ?></p>
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <div class="overlay-content">
            <canvas id='chartBySlot' class='canvasPopUp'></canvas>
        </div>
    </div>

    <script src="./js/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="module" src="./chartVariables.js"></script>
    <script type="module" src="./js/index.js"></script>

    <script>
        function closeNav() {
            document.getElementById("chartPopUp").style.display = "none";
        }
    </script>
</body>

</html>