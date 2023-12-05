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
    <!-- Bookstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

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
        <nav class="navbar navbar-light bg-light">
            <a class="navbar-brand" href="#">
                <img src="/docs/4.0/assets/brand/bootstrap-solid.svg" width="30" height="30" class="d-inline-block align-top" alt="">
                Bootstrap
            </a>
        </nav>
        <p> Model ID : <?= $deviceID ?> </p>
        <p><?php echo $isToday . '&nbsp;&nbsp;' . date('l, d F Y', strtotime($date)) ?></p>

        <select id="chartTypeSelect" class="form-select" aria-label="Default select example">
            <option selected>Select Chart Type</option>
            <option value="line">Line</option>
            <option value="bar">Bar</option>
        </select>
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <div class="overlay-content">
            <canvas id='chartBySlot' class='canvasPopUp'></canvas>
        </div>
    </div>

    <script src="./js/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="module" src="./chartVariables.js"></script>
    <script type="module" src="./js/index.js"></script>

    <!-- //bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <script>
                function closeNav() {
            document.getElementById("chartPopUp").style.display = "none";
            document.getElementById("chartTypeSelect").selectedIndex = 0;
        }
    </script>
</body>

</html>